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
     *
     * Logika afiliasi Mentor–Mitra:
     * - Mitra: sistem menyimpan email_domain dari email pendaftaran
     * - Mentor: sistem mengekstrak domain email, mencocokkan dengan tabel mitra
     */
    public function register(Request $request)
    {
        $rules = [
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|min:8',
            'role'         => 'required|in:pelajar,mentor,mitra',
        ];

        // Validasi tambahan untuk Mitra
        if ($request->role === 'mitra') {
            $rules['nama_usaha'] = 'required|string|max:255';
        }

        $request->validate($rules);

        // [*] Untuk Mentor: validasi email domain vs Mitra terdaftar
        if ($request->role === 'mentor') {
            $domain = Mitra::extractDomain($request->email);
            $mitra  = Mitra::findByDomain($domain);

            if (! $mitra) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Registrasi gagal. Domain email @{$domain} tidak terdaftar di perusahaan mitra manapun. Hubungi perusahaan Anda untuk mendaftar sebagai Mitra terlebih dahulu.",
                ], 422);
            }
        }

        // [*] Untuk Mitra: cek apakah domain sudah dipakai mitra lain
        if ($request->role === 'mitra') {
            $domain = Mitra::extractDomain($request->email);
            $existing = Mitra::findByDomain($domain);

            if ($existing) {
                return response()->json([
                    'status'  => 'error',
                    'message' => "Domain email @{$domain} sudah terdaftar oleh perusahaan lain.",
                ], 422);
            }
        }

        $user = User::create([
            'id'           => (string) Str::uuid(),
            'nama_lengkap' => $request->nama_lengkap,
            'email'        => $request->email,
            'password'     => Hash::make($request->password),
            'role'         => $request->role,
        ]);

        match ($request->role) {
            'pelajar' => Pelajar::create([
                'id'      => (string) Str::uuid(),
                'user_id' => $user->id,
            ]),
            'mentor' => Mentor::create([
                'id'        => (string) Str::uuid(),
                'user_id'   => $user->id,
                'mitra_id'  => $mitra->id,                          // [*] Link ke Mitra
                'perusahaan'=> $mitra->nama_usaha,                  // Display name
            ]),
            'mitra' => Mitra::create([
                'id'           => (string) Str::uuid(),
                'user_id'      => $user->id,
                'nama_usaha'   => $request->nama_usaha,
                'email_domain' => Mitra::extractDomain($request->email), // [*] Auto-extract
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

        if (! Hash::check($request->password, $user->password)) {
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

        // [*] Tambahan session data untuk mentor
        if ($user->role === 'mentor' && $roleModel?->mitra_id) {
            session(['mitra_id' => $roleModel->mitra_id]);
        }

        $redirect = match ($user->role) {
            'pelajar' => '/pages/pelajar/dashboard',
            'mentor'  => '/pages/mentor/dashboard',
            'mitra'   => '/pages/mitra/dashboard',
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

        $user = User::find(session('user_id'));
        if (! $user) {
            $request->session()->flush();
            return response()->json(['status' => 'error', 'loggedIn' => false], 401);
        }

        return response()->json([
            'status'   => 'success',
            'loggedIn' => true,
            'user'     => [
                'id'       => $user->id,
                'nama'     => $user->nama_lengkap,
                'email'    => $user->email,
                'role'     => $user->role,
                'role_id'  => session('role_id'),
                'mitra_id' => session('mitra_id'),
                'foto_profil' => $user->foto_profil ? asset($user->foto_profil) : null,
            ],
        ]);
    }

    /**
     * POST /api/change-password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        $user = User::find(session('user_id'));

        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Password saat ini salah'], 403);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['status' => 'success', 'message' => 'Password berhasil diubah!']);
    }

    /**
     * POST /api/upload-foto
     */
    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::find(session('user_id'));
        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'User tidak ditemukan'], 404);
        }

        // Hapus foto lama jika ada
        if ($user->foto_profil && file_exists(public_path($user->foto_profil))) {
            unlink(public_path($user->foto_profil));
        }

        $filename = 'profile_' . $user->id . '_' . time() . '.' . $request->foto->extension();
        $request->foto->move(public_path('uploads/profiles'), $filename);
        $path = 'uploads/profiles/' . $filename;

        $user->update(['foto_profil' => $path]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Foto profil berhasil diupload!',
            'path'    => $path,
        ]);
    }
}
