<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnrollmentController extends Controller
{
    private function getUser()
    {
        $userId = session('user_id');
        if (! $userId) {
            abort(response()->json(['status' => 'error', 'message' => 'Belum login'], 401));
        }
        $user = User::find($userId);
        if (! $user) {
            abort(response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 401));
        }
        return $user;
    }

    /** POST /api/pelajar/enroll/{programId} */
    public function enroll(string $programId)
    {
        $user = $this->getUser();
        $program = Program::published()->findOrFail($programId);

        // Cek apakah sudah enroll
        if ($program->enrollments()->where('pelajar_id', $user->id)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kamu sudah terdaftar di program ini.',
            ], 422);
        }

        // Cek kuota
        if ($program->isFull()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kuota program sudah penuh.',
            ], 422);
        }

        Enrollment::create([
            'id'          => (string) Str::uuid(),
            'pelajar_id'  => $user->id,
            'program_id'  => $program->id,
            'status'      => 'aktif',
            'enrolled_at' => now(),
        ]);

        // Notifikasi ke Mitra
        \App\Models\Notification::create([
            'id' => (string) Str::uuid(),
            'user_id' => $program->mitra->user_id,
            'type' => 'new_enrollment',
            'data' => [
                'title' => 'Peserta Baru: ' . $user->nama_lengkap,
                'message' => 'Mendaftar ke program ' . $program->judul,
                'link' => '/pages/mitra/programs/' . $program->id
            ]
        ]);

        // Notifikasi ke Mentor yang di-assign
        foreach ($program->mentors as $mentor) {
            \App\Models\Notification::create([
                'id' => (string) Str::uuid(),
                'user_id' => $mentor->user_id,
                'type' => 'new_enrollment',
                'data' => [
                    'title' => 'Peserta Baru: ' . $user->nama_lengkap,
                    'message' => 'Mendaftar ke program ' . $program->judul,
                    'link' => '/pages/mentor/dashboard'
                ]
            ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Berhasil mendaftar ke program!',
        ], 201);
    }

    /** GET /api/pelajar/enrollments — daftar program yang diikuti */
    public function myEnrollments()
    {
        $user = $this->getUser();

        $enrollments = $user->enrollments()
            ->with(['program.mitra', 'program.tasks'])
            ->orderByDesc('enrolled_at')
            ->get()
            ->map(function ($e) {
                return [
                    'id'           => $e->id,
                    'program_id'   => $e->program_id,
                    'judul'        => $e->program->judul,
                    'perusahaan'   => $e->program->mitra->nama_usaha,
                    'bidang'       => $e->program->bidang,
                    'status'       => $e->status,
                    'progress'     => $e->progressPercent(),
                    'total_tasks'  => $e->program->tasks->count(),
                    'enrolled_at'  => $e->enrolled_at?->format('d M Y'),
                ];
            });

        return response()->json(['status' => 'success', 'data' => $enrollments]);
    }

    /** GET /api/pelajar/enrollments/{id} — detail enrollment dengan tasks & submission */
    public function showEnrollment(string $id)
    {
        $user = $this->getUser();

        $enrollment = Enrollment::with(['program.mitra', 'program.tasks', 'submissions'])
            ->where('pelajar_id', $user->id)
            ->findOrFail($id);

        $tasks = $enrollment->program->tasks->map(function ($task) use ($enrollment) {
            $submission = $enrollment->submissions->where('task_id', $task->id)->first();

            // Cek apakah ada task sebelumnya (urutan lebih kecil) yang belum disetujui
            $isLocked = $enrollment->program->tasks
                ->where('urutan', '<', $task->urutan)
                ->contains(function ($prevTask) use ($enrollment) {
                    $prevSubmission = $enrollment->submissions->where('task_id', $prevTask->id)->first();
                    return !$prevSubmission || $prevSubmission->status !== 'disetujui';
                });

            return [
                'id'        => $task->id,
                'judul'     => $task->judul,
                'deskripsi' => $task->deskripsi,
                'deadline'  => $task->deadline?->format('d M Y H:i'),
                'urutan'    => $task->urutan,
                'is_locked' => $isLocked,
                'submission'=> $submission ? [
                    'id'       => $submission->id,
                    'status'   => $submission->status,
                    'feedback' => $submission->feedback,
                    'nilai'    => $submission->nilai,
                    'file_url' => $submission->file_url,
                ] : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'          => $enrollment->id,
                'judul'       => $enrollment->program->judul,
                'perusahaan'  => $enrollment->program->mitra->nama_usaha,
                'status'      => $enrollment->status,
                'progress'    => $enrollment->progressPercent(),
                'tasks'       => $tasks,
            ],
        ]);
    }
}
