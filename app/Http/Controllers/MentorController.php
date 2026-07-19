<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

    /**
     * GET /api/mentor/explore-programs
     * List programs from the mentor's affiliated Mitra.
     * Mentor sees only published programs with active registration period.
     */
    public function explorePrograms()
    {
        $user = $this->getUser();
        $mentor = $user->mentor;

        if (! $mentor || ! $mentor->mitra_id) {
            return response()->json(['status' => 'error', 'message' => 'Anda belum terafiliasi dengan perusahaan manapun.'], 403);
        }

        $programs = \App\Models\Program::where('mitra_id', $mentor->mitra_id)
            ->where('status', 'published')
            ->where('registrasi_mulai', '<=', now())
            ->where('registrasi_selesai', '>=', now())
            ->with('mitra')
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($p) use ($mentor) {
                // Check if mentor has already applied
                $application = $p->mentors()->where('mentor_id', $mentor->id)->first();
                
                return [
                    'id'           => $p->id,
                    'judul'        => $p->judul,
                    'deskripsi'    => Str::limit($p->deskripsi, 150),
                    'bidang'       => $p->bidang,
                    'perusahaan'   => $p->mitra->nama_usaha,
                    'kuota'        => $p->kuota,
                    'enrolled'     => $p->enrollments_count,
                    'registrasi_mulai'   => $p->registrasi_mulai?->format('d M Y'),
                    'registrasi_selesai' => $p->registrasi_selesai?->format('d M Y'),
                    'tanggal_mulai'      => $p->tanggal_mulai?->format('d M Y'),
                    'tanggal_selesai'    => $p->tanggal_selesai?->format('d M Y'),
                    'is_registration_open' => $p->isRegistrationOpen(),
                    'application_status'   => $application ? $application->pivot->status : null,
                ];
            });

        return response()->json(['status' => 'success', 'data' => $programs]);
    }

    /**
     * POST /api/mentor/apply/{programId}
     * Mentor applies to a program. Must be during active registration period.
     */
    public function applyToProgram(string $programId)
    {
        $user = $this->getUser();
        $mentor = $user->mentor;

        $program = \App\Models\Program::where('id', $programId)
            ->where('status', 'published')
            ->firstOrFail();

        // BL-D04: Mentor can only apply to programs from their affiliated Mitra
        if ($program->mitra_id !== $mentor->mitra_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda hanya dapat mendaftar ke program dari perusahaan yang terafiliasi.',
            ], 403);
        }

        // BL-P07: Check registration period is active
        if (! $program->isRegistrationOpen()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Periode pendaftaran belum dibuka atau sudah berakhir.',
            ], 422);
        }

        // BL-A02: Check duplicate application
        if ($program->mentors()->where('mentor_id', $mentor->id)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah mendaftar ke program ini.',
            ], 422);
        }

        // Create application
        $program->mentors()->attach($mentor->id, [
            'id'         => (string) Str::uuid(),
            'status'     => 'pending',
            'applied_at' => now(),
        ]);

        // Notify Mitra
        \App\Models\Notification::create([
            'id'      => (string) Str::uuid(),
            'user_id' => $program->mitra->user_id,
            'type'    => 'mentor_application',
            'data'    => [
                'title'   => 'Lamaran Mentor Baru',
                'message' => $user->nama_lengkap . ' mendaftar sebagai mentor pada program ' . $program->judul,
                'link'    => '/pages/mitra/programs/' . $program->id,
            ],
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Lamaran berhasil dikirim! Menunggu persetujuan dari perusahaan.',
        ], 201);
    }

    /**
     * POST /api/mentor/cancel-application/{programId}
     * Cancel a pending application. Only allowed if status is 'pending'.
     */
    public function cancelApplication(string $programId)
    {
        $user = $this->getUser();
        $mentor = $user->mentor;

        $program = \App\Models\Program::findOrFail($programId);

        $application = $program->mentors()->where('mentor_id', $mentor->id)->first();

        if (! $application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Lamaran tidak ditemukan.',
            ], 404);
        }

        // BL-A03: Cancel only allowed when status is 'pending'
        if ($application->pivot->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Lamaran tidak dapat dibatalkan karena sudah diproses.',
            ], 403);
        }

        // Hard delete the application
        $program->mentors()->detach($mentor->id);

        return response()->json([
            'status'  => 'success',
            'message' => 'Lamaran berhasil dibatalkan.',
        ]);
    }

    /**
     * GET /api/mentor/my-applications
     * List all of this mentor's applications with their statuses.
     */
    public function myApplications()
    {
        $user = $this->getUser();
        $mentor = $user->mentor;

        $applications = $mentor->applications()
            ->with('mitra')
            ->orderByDesc('program_mentors.applied_at')
            ->get()
            ->map(function ($program) {
                return [
                    'id'             => $program->id,
                    'judul'          => $program->judul,
                    'bidang'         => $program->bidang,
                    'perusahaan'     => $program->mitra->nama_usaha,
                    'status'         => $program->pivot->status,
                    'applied_at'     => $program->pivot->applied_at ? Carbon::parse($program->pivot->applied_at)->format('d M Y H:i') : null,
                    'reviewed_at'    => $program->pivot->reviewed_at ? Carbon::parse($program->pivot->reviewed_at)->format('d M Y H:i') : null,
                    'tanggal_mulai'  => $program->tanggal_mulai?->format('d M Y'),
                    'tanggal_selesai'=> $program->tanggal_selesai?->format('d M Y'),
                ];
            });

        return response()->json(['status' => 'success', 'data' => $applications]);
    }
}
