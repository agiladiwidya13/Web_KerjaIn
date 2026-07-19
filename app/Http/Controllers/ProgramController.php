<?php

namespace App\Http\Controllers;

use App\Models\Mentor;
use App\Models\Program;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProgramController extends Controller
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

    // ── Mitra: CRUD Program ──────────────────────────────────

    /** GET /api/mitra/programs */
    public function index()
    {
        $user = $this->getUser();
        $programs = $user->mitra->programs()
            ->withCount('enrollments')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['status' => 'success', 'data' => $programs]);
    }

    /** POST /api/mitra/programs */
    public function store(Request $request)
    {
        $request->validate([
            'judul'              => 'required|string|max:255',
            'deskripsi'          => 'nullable|string',
            'bidang'             => 'nullable|string|max:100',
            'kuota'              => 'nullable|integer|min:1',
            'registrasi_mulai'   => 'nullable|date',
            'registrasi_selesai' => 'nullable|date|after_or_equal:registrasi_mulai',
            'tanggal_mulai'      => 'nullable|date',
            'tanggal_selesai'    => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $user = $this->getUser();

        $program = Program::create([
            'id'                 => (string) Str::uuid(),
            'mitra_id'           => $user->mitra->id,
            'judul'              => $request->judul,
            'deskripsi'          => $request->deskripsi,
            'bidang'             => $request->bidang,
            'status'             => 'draft',
            'kuota'              => $request->kuota,
            'registrasi_mulai'   => $request->registrasi_mulai,
            'registrasi_selesai' => $request->registrasi_selesai,
            'tanggal_mulai'      => $request->tanggal_mulai,
            'tanggal_selesai'    => $request->tanggal_selesai,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Program berhasil dibuat!',
            'data'    => $program,
        ], 201);
    }

    /** GET /api/mitra/programs/{id} */
    public function show(string $id)
    {
        $user = $this->getUser();
        $program = Program::with(['tasks', 'mentors.user', 'enrollments.pelajar'])
            ->where('id', $id)
            ->where('mitra_id', $user->mitra->id)
            ->firstOrFail();

        return response()->json(['status' => 'success', 'data' => $program]);
    }

    /** POST /api/mitra/programs/{id}/update */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'judul'              => 'required|string|max:255',
            'deskripsi'          => 'nullable|string',
            'bidang'             => 'nullable|string|max:100',
            'kuota'              => 'nullable|integer|min:1',
            'registrasi_mulai'   => 'nullable|date',
            'registrasi_selesai' => 'nullable|date|after_or_equal:registrasi_mulai',
            'tanggal_mulai'      => 'nullable|date',
            'tanggal_selesai'    => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $user = $this->getUser();
        $program = Program::where('id', $id)
            ->where('mitra_id', $user->mitra->id)
            ->firstOrFail();

        $program->update($request->only([
            'judul', 'deskripsi', 'bidang', 'kuota',
            'registrasi_mulai', 'registrasi_selesai',
            'tanggal_mulai', 'tanggal_selesai',
        ]));

        return response()->json(['status' => 'success', 'message' => 'Program berhasil diperbarui!']);
    }

    /** POST /api/mitra/programs/{id}/publish */
    public function publish(string $id)
    {
        $user = $this->getUser();
        $program = Program::where('id', $id)
            ->where('mitra_id', $user->mitra->id)
            ->firstOrFail();

        if (! $program->registrasi_mulai || ! $program->registrasi_selesai || ! $program->tanggal_mulai || ! $program->tanggal_selesai) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Semua periode (pendaftaran dan program) harus diisi sebelum dipublikasikan.',
            ], 422);
        }

        // Validate date ordering: registrasi_selesai <= tanggal_mulai
        if ($program->registrasi_selesai > $program->tanggal_mulai) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Periode pendaftaran harus berakhir sebelum atau pada tanggal mulai program.',
            ], 422);
        }

        if ($program->tasks()->count() < 3) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Program minimal harus memiliki 3 tugas (tasks) sebelum dapat dipublikasikan.',
            ], 422);
        }

        $program->update(['status' => 'published']);

        return response()->json(['status' => 'success', 'message' => 'Program berhasil dipublikasi!']);
    }

    /** POST /api/mitra/programs/{id}/close */
    public function close(string $id)
    {
        $user = $this->getUser();
        $program = Program::where('id', $id)
            ->where('mitra_id', $user->mitra->id)
            ->firstOrFail();

        $program->update(['status' => 'closed']);

        return response()->json(['status' => 'success', 'message' => 'Program berhasil ditutup!']);
    }

    /** POST /api/mitra/programs/{id}/delete */
    public function destroy(string $id)
    {
        $user = $this->getUser();
        $program = Program::where('id', $id)
            ->where('mitra_id', $user->mitra->id)
            ->firstOrFail();

        $program->delete();

        return response()->json(['status' => 'success', 'message' => 'Program berhasil dihapus!']);
    }

    /**
     * POST /api/mitra/programs/{id}/assign-mentor
     *
     * [*] Mentor hanya bisa di-assign jika terafiliasi dengan mitra yang sama.
     */
    public function assignMentor(Request $request, string $id)
    {
        $request->validate(['mentor_id' => 'required|uuid']);

        $user = $this->getUser();
        $program = Program::where('id', $id)
            ->where('mitra_id', $user->mitra->id)
            ->firstOrFail();

        // [*] Validasi: mentor harus terafiliasi dengan mitra ini
        $mentor = Mentor::where('id', $request->mentor_id)
            ->where('mitra_id', $user->mitra->id)
            ->first();

        if (! $mentor) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mentor ini tidak terafiliasi dengan perusahaan Anda.',
            ], 403);
        }

        // Cek apakah sudah di-assign
        if ($program->mentors()->where('mentor_id', $mentor->id)->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Mentor sudah di-assign ke program ini.',
            ], 422);
        }

        $program->mentors()->attach($mentor->id, [
            'id'          => (string) Str::uuid(),
            'assigned_at' => now(),
        ]);

        \App\Models\Notification::create([
            'id' => (string) Str::uuid(),
            'user_id' => $mentor->user_id,
            'type' => 'assigned_program',
            'data' => [
                'title' => 'Program Baru: ' . $program->judul,
                'message' => 'Anda ditugaskan sebagai mentor pada program ini.',
                'link' => '/pages/mentor/dashboard'
            ]
        ]);

        return response()->json(['status' => 'success', 'message' => 'Mentor berhasil di-assign!']);
    }

    // ── Pelajar: Browse Program ──────────────────────────────

    /** GET /api/programs — browse program yang published */
    public function browse(Request $request)
    {
        $query = Program::published()
            ->with('mitra')
            ->withCount('enrollments');

        // BL-P08: Only show programs with active registration period
        $query->where('registrasi_mulai', '<=', now())
              ->where('registrasi_selesai', '>=', now());

        // Filter bidang
        if ($request->filled('bidang')) {
            $query->where('bidang', 'like', '%' . $request->bidang . '%');
        }

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qb) use ($q) {
                $qb->where('judul', 'like', "%{$q}%")
                   ->orWhereHas('mitra', fn($m) => $m->where('nama_usaha', 'like', "%{$q}%"));
            });
        }

        $programs = $query->orderByDesc('created_at')->get()->map(function ($p) {
            return [
                'id'           => $p->id,
                'judul'        => $p->judul,
                'deskripsi'    => Str::limit($p->deskripsi, 150),
                'bidang'       => $p->bidang,
                'perusahaan'   => $p->mitra->nama_usaha,
                'logo'         => $p->mitra->logo_perusahaan,
                'cover_image'  => $p->cover_image ? asset($p->cover_image) : null,
                'kuota'        => $p->kuota,
                'enrolled'     => $p->enrollments_count,
                'registrasi_mulai'   => $p->registrasi_mulai?->format('d M Y'),
                'registrasi_selesai' => $p->registrasi_selesai?->format('d M Y'),
                'tanggal_mulai'   => $p->tanggal_mulai?->format('d M Y'),
                'tanggal_selesai' => $p->tanggal_selesai?->format('d M Y'),
            ];
        });

        return response()->json(['status' => 'success', 'data' => $programs]);
    }

    /** GET /api/programs/{id} — detail program untuk pelajar */
    public function detail(string $id)
    {
        $program = Program::published()
            ->with(['mitra', 'tasks'])
            ->withCount('enrollments')
            ->findOrFail($id);

        // Cek apakah pelajar sudah enroll
        $enrolled = false;

        $approvedMentors = $program->mentors()
            ->wherePivot('status', 'disetujui')
            ->with('user')
            ->get();
        if (session('role') === 'pelajar') {
            $enrolled = $program->enrollments()
                ->where('pelajar_id', session('user_id'))
                ->exists();
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'           => $program->id,
                'judul'        => $program->judul,
                'deskripsi'    => $program->deskripsi,
                'bidang'       => $program->bidang,
                'perusahaan'   => $program->mitra->nama_usaha,
                'logo'         => $program->mitra->logo_perusahaan,
                'cover_image'  => $program->cover_image ? asset($program->cover_image) : null,
                'kuota'        => $program->kuota,
                'enrolled'     => $program->enrollments_count,
                'is_full'      => $program->isFull(),
                'registrasi_mulai'     => $program->registrasi_mulai?->format('d M Y'),
                'registrasi_selesai'   => $program->registrasi_selesai?->format('d M Y'),
                'is_registration_open' => $program->isRegistrationOpen(),
                'tanggal_mulai'   => $program->tanggal_mulai?->format('d M Y'),
                'tanggal_selesai' => $program->tanggal_selesai?->format('d M Y'),
                'tasks'        => $program->tasks->map(fn($t) => [
                    'id'      => $t->id,
                    'judul'   => $t->judul,
                    'urutan'  => $t->urutan,
                    'deadline'=> $t->deadline?->format('d M Y H:i'),
                ]),
                'mentors' => $approvedMentors->map(fn($m) => [
                    'nama'    => $m->user->nama_lengkap,
                    'profesi' => $m->profesi,
                ]),
                'already_enrolled' => $enrolled,
            ],
        ]);
    }

    /** POST /api/mitra/programs/{id}/upload-cover */
    public function uploadCover(Request $request, string $id)
    {
        $request->validate([
            'cover' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $this->getUser();
        $program = Program::where('id', $id)
            ->where('mitra_id', $user->mitra->id)
            ->firstOrFail();

        if ($request->hasFile('cover')) {
            $file = $request->file('cover');
            
            // Delete old cover if exists
            if ($program->cover_image) {
                $oldPath = public_path($program->cover_image);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $filename = 'cover_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/covers'), $filename);
            
            $path = 'uploads/covers/' . $filename;
            $program->update(['cover_image' => $path]);

            return response()->json([
                'status' => 'success',
                'message' => 'Cover program berhasil diupload!',
                'cover_url' => asset($path)
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengupload cover.'
        ], 400);
    }

    /** GET /api/mitra/programs/{id}/leaderboard */
    public function leaderboard(string $id)
    {
        $user = $this->getUser();
        $program = Program::where('id', $id)
            ->where('mitra_id', $user->mitra->id)
            ->firstOrFail();

        $enrollments = Enrollment::where('program_id', $id)
            ->with(['pelajar.pelajar'])
            ->get();

        $leaderboard = $enrollments->map(function ($enrollment) {
            $user = $enrollment->pelajar;
            $pelajarProfile = $user ? $user->pelajar : null;
            $totalPoin = $pelajarProfile ? $pelajarProfile->total_poin : 0;

            return [
                'id' => $user ? $user->id : null,
                'nama' => $user ? $user->nama_lengkap : 'Pelajar',
                'universitas' => $pelajarProfile ? $pelajarProfile->universitas : '-',
                'total_poin' => $totalPoin
            ];
        })
        ->sortByDesc('total_poin')
        ->values()
        ->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        return response()->json([
            'status' => 'success',
            'data' => $leaderboard
        ]);
    }

    /**
     * POST /api/mitra/mentor-applications/{id}/review
     * Approve or reject a mentor application.
     * {id} is the program_mentors pivot row ID.
     */
    public function reviewMentorApplication(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
        ]);

        $user = $this->getUser();

        // Find the application pivot row
        $application = DB::table('program_mentors')
            ->where('id', $id)
            ->first();

        if (! $application) {
            return response()->json(['status' => 'error', 'message' => 'Lamaran tidak ditemukan.'], 404);
        }

        // Verify the program belongs to this Mitra
        $program = Program::where('id', $application->program_id)
            ->where('mitra_id', $user->mitra->id)
            ->first();

        if (! $program) {
            return response()->json(['status' => 'error', 'message' => 'Program tidak ditemukan.'], 404);
        }

        // BL-A04/A05: Status must be 'pending' to change
        if ($application->status !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Status lamaran sudah final dan tidak dapat diubah.',
            ], 422);
        }

        // BL-D05: Verify domain affiliation
        $mentor = Mentor::find($application->mentor_id);
        if (! $mentor || $mentor->mitra_id !== $user->mitra->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mentor tidak terafiliasi dengan perusahaan Anda.',
            ], 403);
        }

        // Update application status
        DB::table('program_mentors')
            ->where('id', $id)
            ->update([
                'status'      => $request->status,
                'reviewed_at' => now(),
            ]);

        // Send notification to mentor
        $statusLabel = $request->status === 'disetujui' ? 'disetujui' : 'ditolak';
        \App\Models\Notification::create([
            'id'      => (string) Str::uuid(),
            'user_id' => $mentor->user_id,
            'type'    => 'application_' . $statusLabel,
            'data'    => [
                'title'   => 'Lamaran ' . ucfirst($statusLabel),
                'message' => 'Lamaran Anda untuk program ' . $program->judul . ' telah ' . $statusLabel . '.',
                'link'    => '/pages/mentor/dashboard',
            ],
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Lamaran mentor berhasil ' . $statusLabel . '!',
        ]);
    }

    /**
     * GET /api/mitra/mentor-applications
     * List all mentor applications for programs owned by this Mitra.
     */
    public function mentorApplications(Request $request)
    {
        $user = $this->getUser();
        $mitraId = $user->mitra->id;

        $query = DB::table('program_mentors')
            ->join('programs', 'program_mentors.program_id', '=', 'programs.id')
            ->join('mentor', 'program_mentors.mentor_id', '=', 'mentor.id')
            ->join('users', 'mentor.user_id', '=', 'users.id')
            ->where('programs.mitra_id', $mitraId)
            ->select(
                'program_mentors.id',
                'program_mentors.status',
                'program_mentors.applied_at',
                'program_mentors.reviewed_at',
                'programs.id as program_id',
                'programs.judul as program_judul',
                'users.nama_lengkap as mentor_nama',
                'users.email as mentor_email',
                'mentor.profesi as mentor_profesi',
                'mentor.tahun_pengalaman as mentor_tahun_pengalaman'
            );

        // Filter by status
        if ($request->filled('status')) {
            $query->where('program_mentors.status', $request->status);
        }

        // Filter by program
        if ($request->filled('program_id')) {
            $query->where('program_mentors.program_id', $request->program_id);
        }

        $applications = $query->orderByDesc('program_mentors.applied_at')->get();

        return response()->json(['status' => 'success', 'data' => $applications]);
    }
}
