<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MitraController extends Controller
{
    private function getUser()
    {
        return User::find(session('user_id'));
    }

    /** GET /api/mitra/profile */
    public function show()
    {
        $user = $this->getUser();
        $m    = $user->mitra;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nama_lengkap'  => $user->nama_lengkap,
                'email'         => $user->email,
                'foto_profil'   => $user->foto_profil,
                'nama_usaha'    => $m->nama_usaha,
                'bidang_usaha'  => $m->bidang_usaha,
                'kota'          => $m->kota,
                'kontak_bisnis' => $m->kontak_bisnis,
            ],
        ]);
    }

    /** POST /api/mitra/update */
    public function update(Request $request)
    {
        $request->validate([
            'nama_usaha'    => 'required|string|max:255',
            'bidang_usaha'  => 'nullable|string|max:255',
            'kota'          => 'nullable|string|max:255',
            'kontak_bisnis' => 'nullable|string|max:255',
            'nama_lengkap'  => 'nullable|string|max:255',
        ]);

        $user = $this->getUser();
        $user->mitra->update([
            'nama_usaha'    => $request->nama_usaha,
            'bidang_usaha'  => $request->bidang_usaha,
            'kota'          => $request->kota,
            'kontak_bisnis' => $request->kontak_bisnis,
        ]);

        if ($request->filled('nama_lengkap')) {
            $user->update(['nama_lengkap' => $request->nama_lengkap]);
            session(['nama' => $request->nama_lengkap]);
        }

        return response()->json(['status' => 'success', 'message' => 'Profil mitra berhasil diperbarui!']);
    }

    /** POST /api/mitra/delete */
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
            'message'  => 'Akun mitra berhasil dihapus.',
            'redirect' => '/',
        ]);
    }
}
