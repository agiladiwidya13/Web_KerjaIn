<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MitraController extends Controller
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

    /** GET /api/mitra/profile */
    public function show()
    {
        $user = $this->getUser();
        $m    = $user->mitra;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nama_lengkap'    => $user->nama_lengkap,
                'email'           => $user->email,
                'foto_profil'     => $user->foto_profil,
                'nama_usaha'      => $m->nama_usaha,
                'bidang_usaha'    => $m->bidang_usaha,
                'kota'            => $m->kota,
                'kontak_bisnis'   => $m->kontak_bisnis,
                'email_domain'    => $m->email_domain,
                'logo_perusahaan' => $m->logo_perusahaan,
                'website'         => $m->website,
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
            'website'       => 'nullable|string|max:255',
            'nama_lengkap'  => 'nullable|string|max:255',
        ]);

        $user = $this->getUser();
        $user->mitra->update([
            'nama_usaha'    => $request->nama_usaha,
            'bidang_usaha'  => $request->bidang_usaha,
            'kota'          => $request->kota,
            'kontak_bisnis' => $request->kontak_bisnis,
            'website'       => $request->website,
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

        if (! Hash::check($request->password_confirm, $user->password)) {
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

    /** GET /api/mitra/dashboard */
    public function dashboard()
    {
        $user = $this->getUser();
        $m    = $user->mitra;

        // Program milik mitra
        $programs = $m->programs()
            ->withCount('enrollments')
            ->get()
            ->map(function ($p) {
                return [
                    'id'          => $p->id,
                    'judul'       => $p->judul,
                    'bidang'      => $p->bidang,
                    'status'      => $p->status,
                    'kuota'       => $p->kuota,
                    'enrolled'    => $p->enrollments_count,
                    'tanggal_mulai'   => $p->tanggal_mulai?->format('d M Y'),
                    'tanggal_selesai' => $p->tanggal_selesai?->format('d M Y'),
                ];
            });

        // Mentor terafiliasi
        $mentorCount = $m->mentors()->count();

        // Total pelamar baru (enrollment aktif)
        $newApplicants = 0;
        foreach ($m->programs as $prog) {
            $newApplicants += $prog->enrollments()->where('status', 'aktif')
                ->where('enrolled_at', '>=', now()->subDays(7))
                ->count();
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'nama'           => $user->nama_lengkap,
                'nama_usaha'     => $m->nama_usaha,
                'email_domain'   => $m->email_domain,
                'programs'       => $programs,
                'total_programs' => $programs->count(),
                'total_mentors'  => $mentorCount,
                'new_applicants' => $newApplicants,
            ],
        ]);
    }

    /** GET /api/mitra/mentors — daftar mentor terafiliasi */
    public function mentors()
    {
        $user = $this->getUser();
        $mentors = \App\Models\Mentor::with('user')
            ->where('mitra_id', $user->mitra->id)
            ->get()
            ->map(function ($m) {
                return [
                    'id'    => $m->id,
                    'nama'  => $m->user->nama_lengkap,
                    'email' => $m->user->email,
                    'profesi'=> $m->profesi,
                    'tahun_pengalaman'=> $m->tahun_pengalaman,
                ];
            });

        return response()->json(['status' => 'success', 'data' => $mentors]);
    }

    /** GET /api/mitra/candidates */
    public function searchCandidates(Request $request)
    {
        $user = $this->getUser();
        
        $query = \App\Models\Pelajar::with(['user', 'portfolios.enrollment.program.mitra']);

        // Filter berdasarkan universitas
        if ($request->filled('universitas')) {
            $query->where('universitas', 'like', '%' . $request->universitas . '%');
        }

        // Filter berdasarkan jurusan
        if ($request->filled('jurusan')) {
            $query->where('jurusan', 'like', '%' . $request->jurusan . '%');
        }

        $candidates = $query->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'nama' => $p->user->nama_lengkap,
                'universitas' => $p->universitas,
                'jurusan' => $p->jurusan,
                'total_poin' => $p->total_poin,
                'total_portofolio' => $p->portfolios->count(),
                'bio' => \Illuminate\Support\Str::limit($p->bio, 80)
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $candidates
        ]);
    }

    /** GET /api/mitra/dashboard-charts */
    public function dashboardCharts()
    {
        $user = $this->getUser();
        $m    = $user->mitra;
        $programIds = $m->programs()->pluck('id');

        // 1. Peserta per Program (Bar Chart)
        $perProgram = $m->programs()
            ->withCount('enrollments')
            ->get()
            ->map(fn($p) => [
                'label' => \Illuminate\Support\Str::limit($p->judul, 20),
                'value' => $p->enrollments_count,
            ]);

        // 2. Tren Pendaftaran Bulanan - 6 bulan terakhir (Line Chart)
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $monthlyRaw = Enrollment::whereIn('program_id', $programIds)
            ->where('enrolled_at', '>=', $sixMonthsAgo)
            ->select(DB::raw("DATE_FORMAT(enrolled_at, '%Y-%m') as bulan"), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $monthlyTrend = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key   = Carbon::now()->subMonths($i)->format('Y-m');
            $label = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $monthlyTrend->push([
                'label' => $label,
                'value' => $monthlyRaw->has($key) ? $monthlyRaw[$key]->total : 0,
            ]);
        }

        // 3. Distribusi Status Program (Doughnut Chart)
        $statusDist = $m->programs()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(fn($s) => [
                'label' => ucfirst($s->status),
                'value' => $s->total,
            ]);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'per_program'    => $perProgram,
                'monthly_trend'  => $monthlyTrend,
                'status_dist'    => $statusDist,
            ],
        ]);
    }

    /** POST /api/mitra/upload-logo */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $this->getUser();
        $mitra = $user->mitra;
        if (! $mitra) {
            return response()->json(['status' => 'error', 'message' => 'Mitra tidak ditemukan'], 404);
        }

        // Hapus logo lama jika ada
        if ($mitra->logo_perusahaan && file_exists(public_path($mitra->logo_perusahaan))) {
            @unlink(public_path($mitra->logo_perusahaan));
        }

        $filename = 'logo_' . $mitra->id . '_' . time() . '.' . $request->logo->extension();
        $request->logo->move(public_path('uploads/logos'), $filename);
        $path = 'uploads/logos/' . $filename;

        $mitra->update(['logo_perusahaan' => $path]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Logo perusahaan berhasil diupload!',
            'path'    => $path,
        ]);
    }
}
