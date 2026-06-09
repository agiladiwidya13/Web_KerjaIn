<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MentorController extends Controller
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

    /** GET /api/mentor/profile */
    public function show()
    {
        $user = $this->getUser();
        $m    = $user->mentor;

        // [*] Load afiliasi mitra
        $mitraData = null;
        if ($m->mitra_id) {
            $mitra = $m->mitra;
            $mitraData = [
                'nama_usaha'      => $mitra->nama_usaha,
                'email_domain'    => $mitra->email_domain,
                'logo_perusahaan' => $mitra->logo_perusahaan,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nama_lengkap'     => $user->nama_lengkap,
                'email'            => $user->email,
                'foto_profil'      => $user->foto_profil,
                'profesi'          => $m->profesi,
                'perusahaan'       => $m->perusahaan,
                'tahun_pengalaman' => $m->tahun_pengalaman,
                'bio_keahlian'     => $m->bio_keahlian,
                'mitra'            => $mitraData,
            ],
        ]);
    }

    /** POST /api/mentor/update */
    public function update(Request $request)
    {
        $request->validate([
            'profesi'          => 'required|string|max:255',
            'perusahaan'       => 'required|string|max:255',
            'tahun_pengalaman' => 'nullable|integer|min:0',
            'bio_keahlian'     => 'nullable|string',
            'nama_lengkap'     => 'nullable|string|max:255',
        ]);

        $user = $this->getUser();
        $user->mentor->update([
            'profesi'          => $request->profesi,
            'perusahaan'       => $request->perusahaan,
            'tahun_pengalaman' => $request->tahun_pengalaman ?? 0,
            'bio_keahlian'     => $request->bio_keahlian,
        ]);

        if ($request->filled('nama_lengkap')) {
            $user->update(['nama_lengkap' => $request->nama_lengkap]);
            session(['nama' => $request->nama_lengkap]);
        }

        return response()->json(['status' => 'success', 'message' => 'Profil mentor berhasil diperbarui!']);
    }

    /** POST /api/mentor/delete */
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
            'message'  => 'Akun mentor berhasil dihapus.',
            'redirect' => '/',
        ]);
    }

    /** GET /api/mentor/dashboard */
    public function dashboard()
    {
        $user   = $this->getUser();
        $mentor = $user->mentor;

        // [*] Program yang di-handle mentor ini (via afiliasi)
        $programs = $mentor->programs()
            ->with('mitra')
            ->get()
            ->map(function ($p) {
                return [
                    'id'         => $p->id,
                    'judul'      => $p->judul,
                    'perusahaan' => $p->mitra->nama_usaha,
                    'bidang'     => $p->bidang,
                    'status'     => $p->status,
                    'enrollments'=> $p->enrollments()->count(),
                ];
            });

        // Submission menunggu review
        $pendingCount = Submission::where('status', 'menunggu')
            ->whereHas('enrollment.program', function ($q) use ($mentor) {
                $q->where('mitra_id', $mentor->mitra_id);
            })
            ->count();

        // Total yang sudah di-review
        $reviewedCount = $mentor->reviewedSubmissions()->count();

        // Afiliasi info
        $afiliasi = null;
        if ($mentor->mitra) {
            $afiliasi = [
                'nama_usaha'      => $mentor->mitra->nama_usaha,
                'logo_perusahaan' => $mentor->mitra->logo_perusahaan,
            ];
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nama'          => $user->nama_lengkap,
                'programs'      => $programs,
                'pending_review'=> $pendingCount,
                'total_reviewed'=> $reviewedCount,
                'afiliasi'      => $afiliasi,
            ],
        ]);
    }
}
