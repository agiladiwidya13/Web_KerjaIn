<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PelajarController extends Controller
{
    private function getUser()
    {
        return User::find(session('user_id'));
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

        if (! Hash::check($request->password_confirm, $user->password_hash)) {
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
}
