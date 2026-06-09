<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\User;
use App\Models\Portfolio;
use App\Models\Certificate;
use App\Models\PoinLog;
use App\Models\Notification;
use App\Models\Pelajar;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubmissionController extends Controller
{
    // ── Pelajar: Submit Task ─────────────────────────────────

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

    /** POST /api/pelajar/submit */
    public function submit(Request $request)
    {
        $request->validate([
            'enrollment_id' => 'required|uuid',
            'task_id'       => 'required|uuid',
            'catatan'       => 'nullable|string',
            'file'          => 'nullable|file|max:10240', // max 10 MB
        ]);

        $user = $this->getUser();

        // Verifikasi enrollment milik pelajar ini
        $enrollment = Enrollment::with('program.mentors')->where('id', $request->enrollment_id)
            ->where('pelajar_id', $user->id)
            ->where('status', 'aktif')
            ->firstOrFail();

        // Cek apakah sudah submit task ini
        $existing = Submission::where('enrollment_id', $enrollment->id)
            ->where('task_id', $request->task_id)
            ->first();

        if ($existing && $existing->status !== 'revisi') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kamu sudah mengirim submission untuk task ini.',
            ], 422);
        }

        // Handle file upload
        $fileUrl = null;
        if ($request->hasFile('file')) {
            $filename = 'sub_' . $user->id . '_' . time() . '.' . $request->file->extension();
            $request->file->move(public_path('uploads/submissions'), $filename);
            $fileUrl = 'uploads/submissions/' . $filename;
        }

        if ($existing) {
            // Re-submit setelah revisi
            $existing->update([
                'file_url' => $fileUrl ?? $existing->file_url,
                'catatan'  => $request->catatan,
                'status'   => 'menunggu',
                'feedback' => null,
                'nilai'    => null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ]);

            // Notify Mentors (Resubmit/Revisi)
            foreach ($enrollment->program->mentors as $mentor) {
                Notification::create([
                    'id' => (string) Str::uuid(),
                    'user_id' => $mentor->user_id,
                    'type' => 'submission_revision',
                    'data' => [
                        'title' => 'Tugas Direvisi: ' . $user->nama_lengkap,
                        'message' => 'Mengirim ulang tugas untuk program ' . $enrollment->program->judul,
                        'link' => '/pages/mentor/submissions'
                    ]
                ]);
            }

            return response()->json(['status' => 'success', 'message' => 'Submission berhasil dikirim ulang!']);
        }

        Submission::create([
            'id'            => (string) Str::uuid(),
            'enrollment_id' => $enrollment->id,
            'task_id'       => $request->task_id,
            'file_url'      => $fileUrl,
            'catatan'       => $request->catatan,
            'status'        => 'menunggu',
        ]);

        // Notify Mentors (Baru Submit)
        foreach ($enrollment->program->mentors as $mentor) {
            Notification::create([
                'id' => (string) Str::uuid(),
                'user_id' => $mentor->user_id,
                'type' => 'new_submission',
                'data' => [
                    'title' => 'Tugas Masuk: ' . $user->nama_lengkap,
                    'message' => 'Telah mengumpulkan tugas untuk program ' . $enrollment->program->judul,
                    'link' => '/pages/mentor/submissions'
                ]
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Submission berhasil dikirim!'], 201);
    }

    // ── Mentor: Review Submission ────────────────────────────

    /**
     * GET /api/mentor/submissions
     *
     * [*] Mentor hanya bisa melihat submission dari program perusahaannya.
     */
    public function mentorSubmissions(Request $request)
    {
        $user   = $this->getUser();
        $mentor = $user->mentor;

        $query = Submission::whereHas('enrollment.program', function ($q) use ($mentor) {
                // [*] Hanya program dari perusahaan mentor
                $q->where('mitra_id', $mentor->mitra_id);
            })
            ->with(['task', 'enrollment.pelajar', 'enrollment.program']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->orderByDesc('created_at')
            ->get()
            ->map(function ($s) {
                return [
                    'id'          => $s->id,
                    'task_judul'  => $s->task->judul,
                    'program'     => $s->enrollment->program->judul,
                    'pelajar'     => $s->enrollment->pelajar->nama_lengkap,
                    'status'      => $s->status,
                    'catatan'     => $s->catatan,
                    'file_url'    => $s->file_url,
                    'feedback'    => $s->feedback,
                    'nilai'       => $s->nilai,
                    'created_at'  => $s->created_at?->format('d M Y H:i'),
                ];
            });

        return response()->json(['status' => 'success', 'data' => $submissions]);
    }

    /**
     * POST /api/mentor/submissions/{id}/review
     *
     * [*] Hanya bisa mereview submission dari program perusahaannya.
     */
    public function review(Request $request, string $id)
    {
        $request->validate([
            'status'   => 'required|in:disetujui,revisi',
            'feedback' => 'nullable|string',
            'nilai'    => 'nullable|integer|min:0|max:100',
        ]);

        $user   = $this->getUser();
        $mentor = $user->mentor;

        $submission = Submission::whereHas('enrollment.program', function ($q) use ($mentor) {
                $q->where('mitra_id', $mentor->mitra_id);
            })
            ->findOrFail($id);

        $submission->update([
            'status'      => $request->status,
            'feedback'    => $request->feedback,
            'nilai'       => $request->status === 'disetujui' ? $request->nilai : null,
            'reviewed_by' => $mentor->id,
            'reviewed_at' => now(),
        ]);

        // Fase 4: Gamifikasi (Poin)
        if ($request->status === 'disetujui' && $request->nilai > 0) {
            PoinLog::create([
                'id' => (string) Str::uuid(),
                'pelajar_id' => $submission->enrollment->pelajar_id,
                'jumlah' => (int) $request->nilai,
                'keterangan' => 'Menyelesaikan task: ' . $submission->task->judul,
                'referensi_type' => 'submission',
                'referensi_id' => $submission->id,
                'created_at' => now(),
            ]);
            
            $pelajarModel = Pelajar::where('user_id', $submission->enrollment->pelajar_id)->first();
            if ($pelajarModel) {
                $pelajarModel->increment('total_poin', (int) $request->nilai);
            }
        }

        // Fase 4: Notifikasi
        Notification::create([
            'id' => (string) Str::uuid(),
            'user_id' => $submission->enrollment->pelajar_id,
            'type' => 'submission_reviewed',
            'data' => [
                'title' => 'Tugas ' . $submission->task->judul . ' telah direview',
                'message' => 'Status: ' . ucfirst($request->status) . '. Nilai: ' . ($request->nilai ?? 0),
                'link' => '/pages/pelajar/enrollments/' . $submission->enrollment_id
            ]
        ]);

        // Cek apakah semua task disetujui → enrollment selesai
        if ($request->status === 'disetujui') {
            $this->checkEnrollmentCompletion($submission->enrollment_id);
        }

        return response()->json([
            'status'  => 'success',
            'message' => $request->status === 'disetujui'
                ? 'Submission disetujui!'
                : 'Submission perlu direvisi.',
        ]);
    }

    /**
     * Cek apakah semua task dalam enrollment sudah disetujui.
     * Jika ya, ubah status enrollment menjadi 'selesai'.
     */
    private function checkEnrollmentCompletion(string $enrollmentId)
    {
        $enrollment = Enrollment::with(['program.tasks', 'submissions'])->find($enrollmentId);
        if (! $enrollment) return;

        $totalTasks    = $enrollment->program->tasks->count();
        $approvedTasks = $enrollment->submissions->where('status', 'disetujui')->count();

        if ($totalTasks > 0 && $approvedTasks >= $totalTasks) {
            $enrollment->update([
                'status'    => 'selesai',
                'selesai_at'=> now(),
            ]);

            // Generate Portfolio (Fase 3)
            Portfolio::firstOrCreate(
                ['enrollment_id' => $enrollment->id],
                [
                    'id' => (string) Str::uuid(),
                    'pelajar_id' => $enrollment->pelajar_id,
                    'is_public' => true,
                ]
            );

            // Generate Certificate (Fase 3)
            Certificate::firstOrCreate(
                ['enrollment_id' => $enrollment->id],
                [
                    'id' => (string) Str::uuid(),
                    'nomor_sertifikat' => 'KJ-' . strtoupper(Str::random(8)),
                    'issued_by' => $enrollment->program->mitra_id,
                    'issued_at' => now(),
                ]
            );
        }
    }
}
