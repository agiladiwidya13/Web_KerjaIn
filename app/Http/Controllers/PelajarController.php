<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PelajarController extends Controller
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

    /** GET /api/pelajar/profile */
    public function show()
    {
        $user = $this->getUser();
        $p    = $user->pelajar;

        if (! $p) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nama_lengkap' => $user->nama_lengkap,
                'email'        => $user->email,
                'foto_profil'  => $user->foto_profil,
                'universitas'  => $p->universitas,
                'jurusan'      => $p->jurusan,
                'angkatan'     => $p->angkatan,
                'bio'          => $p->bio,
                'total_poin'   => $p->total_poin,
            ],
        ]);
    }

    /** POST /api/pelajar/update */
    public function update(Request $request)
    {
        $request->validate([
            'universitas'  => 'required|string|max:255',
            'jurusan'      => 'required|string|max:255',
            'angkatan'     => 'required|integer|between:2000,2099',
            'bio'          => 'nullable|string',
            'nama_lengkap' => 'nullable|string|max:255',
        ]);

        $user = $this->getUser();
        $user->pelajar->update([
            'universitas' => $request->universitas,
            'jurusan'     => $request->jurusan,
            'angkatan'    => $request->angkatan,
            'bio'         => $request->bio,
        ]);

        if ($request->filled('nama_lengkap')) {
            $user->update(['nama_lengkap' => $request->nama_lengkap]);
            session(['nama' => $request->nama_lengkap]);
        }

        return response()->json(['status' => 'success', 'message' => 'Profil berhasil diperbarui!']);
    }

    /** POST /api/pelajar/delete */
    public function destroy(Request $request)
    {
        $request->validate(['password_confirm' => 'required']);

        $user = $this->getUser();

        if (! Hash::check($request->password_confirm, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Password salah, penghapusan dibatalkan'], 403);
        }

        $user->delete();
        $request->session()->flush();

        return response()->json([
            'status'   => 'success',
            'message'  => 'Akun pelajar berhasil dihapus.',
            'redirect' => '/',
        ]);
    }

    /** GET /api/pelajar/dashboard */
    public function dashboard()
    {
        $user = $this->getUser();
        $p    = $user->pelajar;

        if (! $p) {
            return response()->json(['status' => 'error', 'message' => 'Data pelajar tidak ditemukan'], 404);
        }

        // Active enrollments
        $enrollments = $user->enrollments()
            ->with(['program.mitra', 'program.tasks'])
            ->where('status', 'aktif')
            ->get()
            ->map(function ($e) {
                return [
                    'id'           => $e->id,
                    'program'      => $e->program->judul,
                    'perusahaan'   => $e->program->mitra->nama_usaha,
                    'bidang'       => $e->program->bidang,
                    'progress'     => $e->progressPercent(),
                    'total_tasks'  => $e->program->tasks->count(),
                    'enrolled_at'  => $e->enrolled_at?->format('d M Y'),
                ];
            });

        // Completed count
        $completed = $user->enrollments()->where('status', 'selesai')->count();

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nama'              => $user->nama_lengkap,
                'total_poin'        => $p->total_poin ?? 0,
                'certificate_count' => $p->certificates()->count(),
                'portfolio_count'   => $p->portfolios()->count(),
                'enrollments'       => $enrollments,
                'completed'         => $completed,
            ],
        ]);
    }

    /** GET /profil/{id} (Web Route) */
    public function publicProfile($id)
    {
        $pelajar = \App\Models\Pelajar::with(['user', 'portfolios.enrollment.program.mitra', 'certificates.enrollment.program'])
            ->findOrFail($id);

        return view('pages.pelajar.public-profile', compact('pelajar'));
    }

    /** GET /api/pelajar/leaderboard */
    public function leaderboard()
    {
        $leaderboard = \App\Models\Pelajar::with(['user.badges.badge'])
            ->orderByDesc('total_poin')
            ->limit(10)
            ->get()
            ->map(function ($p, $index) {
                return [
                    'rank' => $index + 1,
                    'id' => $p->id,
                    'nama' => $p->user->nama_lengkap,
                    'universitas' => $p->universitas,
                    'total_poin' => $p->total_poin,
                    'badges' => $p->user->badges->map(fn($ub) => [
                        'nama' => $ub->badge->nama,
                        'icon' => $ub->badge->icon_url
                    ])
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $leaderboard
        ]);
    }

    /**
     * GET /api/pelajar/certificates
     * List all certificates earned by this student.
     */
    public function certificates()
    {
        $user = $this->getUser();

        $certificates = \App\Models\Certificate::whereHas('enrollment', function ($q) use ($user) {
                $q->where('pelajar_id', $user->id);
            })
            ->with(['enrollment.program.mitra'])
            ->orderByDesc('issued_at')
            ->get()
            ->map(function ($cert) {
                return [
                    'id'                => $cert->id,
                    'nomor_sertifikat'  => $cert->nomor_sertifikat,
                    'program'           => $cert->enrollment->program->judul,
                    'perusahaan'        => $cert->enrollment->program->mitra->nama_usaha,
                    'logo'              => $cert->enrollment->program->mitra->logo_perusahaan,
                    'issued_at'         => $cert->issued_at?->format('d M Y'),
                    'pdf_url'           => $cert->pdf_url,
                ];
            });

        return response()->json(['status' => 'success', 'data' => $certificates]);
    }

    /**
     * GET /api/pelajar/portfolios
     * List all portfolios with task details.
     * Portfolio = collection of completed program tasks with descriptions.
     */
    public function portfolios()
    {
        $user = $this->getUser();

        $portfolios = \App\Models\Portfolio::where('pelajar_id', $user->id)
            ->with([
                'enrollment.program.mitra',
                'enrollment.program.tasks',
                'enrollment.submissions',
            ])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($portfolio) {
                $enrollment = $portfolio->enrollment;
                $program = $enrollment->program;

                // Build task list with descriptions and submission data
                $tasks = $program->tasks->map(function ($task) use ($enrollment) {
                    $submission = $enrollment->submissions->where('task_id', $task->id)->first();
                    return [
                        'id'        => $task->id,
                        'judul'     => $task->judul,
                        'deskripsi' => $task->deskripsi,
                        'urutan'    => $task->urutan,
                        'status'    => $submission ? $submission->status : null,
                        'nilai'     => $submission ? $submission->nilai : null,
                        'file_url'  => $submission ? $submission->file_url : null,
                    ];
                });

                return [
                    'id'          => $portfolio->id,
                    'program'     => $program->judul,
                    'perusahaan'  => $program->mitra->nama_usaha,
                    'bidang'      => $program->bidang,
                    'logo'        => $program->mitra->logo_perusahaan,
                    'is_public'   => $portfolio->is_public,
                    'created_at'  => $portfolio->created_at?->format('d M Y'),
                    'tasks'       => $tasks,
                ];
            });

        return response()->json(['status' => 'success', 'data' => $portfolios]);
    }
}
