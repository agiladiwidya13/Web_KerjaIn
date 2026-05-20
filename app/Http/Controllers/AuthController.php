<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Models\Mitra;
use App\Models\Pelajar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * POST /api/register
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:8',
            'role'         => 'required|in:pelajar,mentor,mitra',
        ]);

        $user = User::create([
            'id'            => (string) Str::uuid(),
            'nama_lengkap'  => $request->nama_lengkap,
            'email'         => $request->email,
            'password_hash' => Hash::make($request->password),
            'role'          => $request->role,
        ]);

        match ($request->role) {
            'pelajar' => Pelajar::create(['id' => (string) Str::uuid(), 'user_id' => $user->id]),
            'mentor'  => Mentor::create(['id' => (string) Str::uuid(), 'user_id' => $user->id]),
            'mitra'   => Mitra::create([
                'id'         => (string) Str::uuid(),
                'user_id'    => $user->id,
                'nama_usaha' => $request->nama_usaha ?? 'Belum diisi',
            ]),
        };

        return response()->json([
            'status'  => 'success',
            'message' => 'Akun berhasil dibuat! Silakan login.',
            'role'    => $user->role,
        ], 201);
    }

    /**
     * POST /api/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'role'     => 'required|in:pelajar,mentor,mitra',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'Email tidak ditemukan'], 401);
        }

        if (! Hash::check($request->password, $user->password_hash)) {
            return response()->json(['status' => 'error', 'message' => 'Password salah'], 401);
        }

        if ($user->role !== $request->role) {
            $label = ['pelajar' => 'Mahasiswa', 'mentor' => 'Mentor', 'mitra' => 'Perusahaan'];
            return response()->json([
                'status'  => 'error',
                'message' => "Akun ini bukan akun {$label[$request->role]}. Silakan gunakan form login yang sesuai.",
            ], 403);
        }

        // Ambil role_id
        $roleModel = match ($user->role) {
            'pelajar' => $user->pelajar,
            'mentor'  => $user->mentor,
            'mitra'   => $user->mitra,
        };

        // Simpan ke session
        session([
            'user_id'  => $user->id,
            'nama'     => $user->nama_lengkap,
            'email'    => $user->email,
            'role'     => $user->role,
            'role_id'  => $roleModel?->id,
        ]);

        $redirect = match ($user->role) {
            'pelajar' => '/pages/pelajar/profile',
            'mentor'  => '/pages/mentor/profile',
            'mitra'   => '/pages/mitra/profile',
            default   => '/',
        };

        return response()->json([
            'status'   => 'success',
            'message'  => 'Login berhasil!',
            'role'     => $user->role,
            'nama'     => $user->nama_lengkap,
            'redirect' => $redirect,
        ]);
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request)
    {
        $request->session()->flush();

        return response()->json(['status' => 'success', 'message' => 'Logout berhasil']);
    }

    /**
     * GET /api/session
     */
    public function getSession(Request $request)
    {
        if (! session('user_id')) {
            return response()->json(['status' => 'error', 'loggedIn' => false], 401);
        }

        return response()->json([
            'status'   => 'success',
            'loggedIn' => true,
            'user'     => [
                'id'      => session('user_id'),
                'nama'    => session('nama'),
                'email'   => session('email'),
                'role'    => session('role'),
                'role_id' => session('role_id'),
            ],
        ]);
    }
}
