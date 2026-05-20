<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MentorController extends Controller
{
    private function getUser()
    {
        return User::find(session('user_id'));
    }

    /** GET /api/mentor/profile */
    public function show()
    {
        $user = $this->getUser();
        $m    = $user->mentor;

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

        if (! Hash::check($request->password_confirm, $user->password_hash)) {
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
}
