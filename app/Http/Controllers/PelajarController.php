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
                'nama'        => $user->nama_lengkap,
                'total_poin'  => $p->total_poin ?? 0,
                'enrollments' => $enrollments,
                'completed'   => $completed,
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
}
