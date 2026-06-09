<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Pelajar;
use App\Models\Mentor;
use App\Models\Mitra;
use App\Models\Program;
use App\Models\Task;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\PoinLog;
use App\Models\Message;
use App\Models\Notification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Mitra User & Mitra Profile
        $mitraUserId = (string) Str::uuid();
        $mitraId = (string) Str::uuid();
        User::create([
            'id' => $mitraUserId,
            'nama_lengkap' => 'HR GoTo',
            'email' => 'hr@goto.id',
            'password' => Hash::make('password123'),
            'role' => 'mitra',
            'foto_profil' => null,
        ]);

        Mitra::create([
            'id' => $mitraId,
            'user_id' => $mitraUserId,
            'nama_usaha' => 'GoTo Group',
            'bidang_usaha' => 'Teknologi & Informasi',
            'kota' => 'Jakarta Selatan',
            'kontak_bisnis' => '08123456789',
            'email_domain' => 'goto.id',
            'logo_perusahaan' => null,
            'website' => 'https://www.gotocompany.com',
        ]);

        // 2. Seed Mentor User & Mentor Profile
        $mentorUserId = (string) Str::uuid();
        $mentorId = (string) Str::uuid();
        User::create([
            'id' => $mentorUserId,
            'nama_lengkap' => 'Ahmad GoTo',
            'email' => 'ahmad@goto.id',
            'password' => Hash::make('password123'),
            'role' => 'mentor',
            'foto_profil' => null,
        ]);

        Mentor::create([
            'id' => $mentorId,
            'user_id' => $mentorUserId,
            'mitra_id' => $mitraId,
            'profesi' => 'Senior Software Engineer',
            'perusahaan' => 'GoTo Group',
            'tahun_pengalaman' => 6,
            'bio_keahlian' => 'Menguasai PHP, Laravel, Node.js, React, dan Cloud Infrastructure.',
        ]);

        // 3. Seed Pelajar Users & Profiles
        // Pelajar 1: Ferdian
        $pelajar1UserId = (string) Str::uuid();
        $pelajar1Id = (string) Str::uuid();
        User::create([
            'id' => $pelajar1UserId,
            'nama_lengkap' => 'Ferdian',
            'email' => 'ferdian@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'pelajar',
            'foto_profil' => null,
        ]);

        Pelajar::create([
            'id' => $pelajar1Id,
            'user_id' => $pelajar1UserId,
            'universitas' => 'UPN Veteran Jawa Timur',
            'jurusan' => 'Teknik Informatika',
            'angkatan' => 2021,
            'bio' => 'Saya adalah mahasiswa semester akhir yang aktif belajar web development khususnya menggunakan framework Laravel.',
            'total_poin' => 185,
        ]);

        // Pelajar 2: Siti Aminah
        $pelajar2UserId = (string) Str::uuid();
        $pelajar2Id = (string) Str::uuid();
        User::create([
            'id' => $pelajar2UserId,
            'nama_lengkap' => 'Siti Aminah',
            'email' => 'siti@pelajar.com',
            'password' => Hash::make('password123'),
            'role' => 'pelajar',
            'foto_profil' => null,
        ]);

        Pelajar::create([
            'id' => $pelajar2Id,
            'user_id' => $pelajar2UserId,
            'universitas' => 'Institut Teknologi Sepuluh Nopember',
            'jurusan' => 'Sistem Informasi',
            'angkatan' => 2022,
            'bio' => 'Sangat antusias dengan UI/UX design dan data analytics.',
            'total_poin' => 95,
        ]);

        // Pelajar 3: Siti Rahma
        $pelajar3UserId = (string) Str::uuid();
        $pelajar3Id = (string) Str::uuid();
        User::create([
            'id' => $pelajar3UserId,
            'nama_lengkap' => 'Siti Rahma',
            'email' => 'siti2@pelajar.com',
            'password' => Hash::make('password123'),
            'role' => 'pelajar',
            'foto_profil' => null,
        ]);

        Pelajar::create([
            'id' => $pelajar3Id,
            'user_id' => $pelajar3UserId,
            'universitas' => 'Universitas Airlangga',
            'jurusan' => 'Teknologi Sains Data',
            'angkatan' => 2022,
            'bio' => 'Ingin menjadi Data Scientist profesional.',
            'total_poin' => 50,
        ]);

        // 4. Seed Programs
        $program1Id = (string) Str::uuid();
        Program::create([
            'id' => $program1Id,
            'mitra_id' => $mitraId,
            'judul' => 'Web Development Internship',
            'deskripsi' => 'Program magang intensif untuk belajar web development menggunakan Laravel, Livewire, dan PostgreSQL. Peserta akan dibimbing oleh mentor berpengalaman untuk membangun produk nyata.',
            'bidang' => 'Teknologi',
            'status' => 'published',
            'kuota' => 15,
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addMonths(3),
        ]);

        $program2Id = (string) Str::uuid();
        Program::create([
            'id' => $program2Id,
            'mitra_id' => $mitraId,
            'judul' => 'UI/UX Design Bootcamp',
            'deskripsi' => 'Program bootcamp eksklusif untuk mempelajari UI/UX Design dari dasar hingga tingkat lanjut. Meliputi riset pengguna, wireframing, prototyping, dan usability testing.',
            'bidang' => 'Desain',
            'status' => 'published',
            'kuota' => 8,
            'tanggal_mulai' => now()->addMonth(),
            'tanggal_selesai' => now()->addMonths(4),
        ]);

        $program3Id = (string) Str::uuid();
        Program::create([
            'id' => $program3Id,
            'mitra_id' => $mitraId,
            'judul' => 'Quality Assurance & Testing Program',
            'deskripsi' => 'Belajar tentang Software Testing, Automation Testing dengan Cypress / Selenium, dan manajemen pengujian kualitas perangkat lunak.',
            'bidang' => 'Teknologi',
            'status' => 'draft',
            'kuota' => 5,
            'tanggal_mulai' => now()->addMonths(2),
            'tanggal_selesai' => now()->addMonths(5),
        ]);

        // 5. Seed Program Mentors (Assign Ahmad to Program 1)
        Program::find($program1Id)->mentors()->attach($mentorId, [
            'id' => (string) Str::uuid(),
            'assigned_at' => now(),
        ]);

        // 6. Seed Tasks
        // Tasks for Program 1
        $task1Id = (string) Str::uuid();
        Task::create([
            'id' => $task1Id,
            'program_id' => $program1Id,
            'judul' => 'Design Login Page & Register Page',
            'deskripsi' => 'Buat wireframe dan mockup desain halaman login serta registrasi untuk aplikasi e-learning KerjaIn.',
            'deadline' => now()->addWeek(),
            'urutan' => 1,
        ]);

        $task2Id = (string) Str::uuid();
        Task::create([
            'id' => $task2Id,
            'program_id' => $program1Id,
            'judul' => 'Implement Database Schema & Models',
            'deskripsi' => 'Buat migration dan Eloquent models di Laravel sesuai dengan rancangan database schema.',
            'deadline' => now()->addWeeks(2),
            'urutan' => 2,
        ]);

        $task3Id = (string) Str::uuid();
        Task::create([
            'id' => $task3Id,
            'program_id' => $program1Id,
            'judul' => 'Create Authentication Controller & API Route',
            'deskripsi' => 'Selesaikan backend API untuk registrasi, login, dan logout lengkap dengan Session-based auth.',
            'deadline' => now()->addWeeks(3),
            'urutan' => 3,
        ]);

        // Tasks for Program 2
        $program2Task1Id = (string) Str::uuid();
        Task::create([
            'id' => $program2Task1Id,
            'program_id' => $program2Id,
            'judul' => 'User Research & Persona Creation',
            'deskripsi' => 'Lakukan interview pada minimal 3 responden dan buat user persona yang merepresentasikan target pengguna aplikasi KerjaIn.',
            'deadline' => now()->addWeek(),
            'urutan' => 1,
        ]);

        // 7. Seed Enrollments
        // Ferdian enrolls in Program 1
        $enrollment1Id = (string) Str::uuid();
        Enrollment::create([
            'id' => $enrollment1Id,
            'pelajar_id' => $pelajar1UserId, // Note: pelajar_id references users.id
            'program_id' => $program1Id,
            'status' => 'aktif',
            'enrolled_at' => now()->subDays(5),
        ]);

        // Siti Aminah enrolls in Program 1
        $enrollment2Id = (string) Str::uuid();
        Enrollment::create([
            'id' => $enrollment2Id,
            'pelajar_id' => $pelajar2UserId,
            'program_id' => $program1Id,
            'status' => 'aktif',
            'enrolled_at' => now()->subDays(4),
        ]);

        // Siti Rahma enrolls in Program 2
        $enrollment3Id = (string) Str::uuid();
        Enrollment::create([
            'id' => $enrollment3Id,
            'pelajar_id' => $pelajar3UserId,
            'program_id' => $program2Id,
            'status' => 'aktif',
            'enrolled_at' => now()->subDays(2),
        ]);

        // 8. Seed Submissions
        // Ferdian submissions
        $sub1Id = (string) Str::uuid();
        Submission::create([
            'id' => $sub1Id,
            'enrollment_id' => $enrollment1Id,
            'task_id' => $task1Id,
            'file_url' => 'uploads/submissions/wireframe_login.pdf',
            'catatan' => 'Tugas sudah saya selesaikan Pak, silakan direview.',
            'status' => 'disetujui',
            'feedback' => 'Sangat baik, struktur form-nya intuitif dan desainnya rapi.',
            'nilai' => 85,
            'reviewed_by' => $mentorId,
            'reviewed_at' => now()->subDays(3),
        ]);

        $sub2Id = (string) Str::uuid();
        Submission::create([
            'id' => $sub2Id,
            'enrollment_id' => $enrollment1Id,
            'task_id' => $task2Id,
            'file_url' => 'uploads/submissions/migrations_setup.zip',
            'catatan' => 'Saya sudah membuat schema untuk tabel core dan relasinya.',
            'status' => 'disetujui',
            'feedback' => 'Bagus sekali. Skema relasinya sudah benar.',
            'nilai' => 100,
            'reviewed_by' => $mentorId,
            'reviewed_at' => now()->subDay(),
        ]);

        // Siti Aminah submissions
        $sub3Id = (string) Str::uuid();
        Submission::create([
            'id' => $sub3Id,
            'enrollment_id' => $enrollment2Id,
            'task_id' => $task1Id,
            'file_url' => 'uploads/submissions/login_siti.pdf',
            'catatan' => 'Sudah saya kumpulkan revisi desain login page.',
            'status' => 'disetujui',
            'feedback' => 'Desain yang menarik. Nilai optimal diberikan.',
            'nilai' => 95,
            'reviewed_by' => $mentorId,
            'reviewed_at' => now()->subDays(2),
        ]);

        // 9. Seed Badges
        $badge1Id = (string) Str::uuid();
        Badge::create([
            'id' => $badge1Id,
            'nama' => 'Pioneer',
            'deskripsi' => 'Peserta generasi pertama yang mendaftar ke KerjaIn.',
            'icon_url' => '/image/badges/pioneer.png',
            'syarat' => 'Mendaftar akun pelajar di platform KerjaIn.',
            'created_at' => now(),
        ]);

        $badge2Id = (string) Str::uuid();
        Badge::create([
            'id' => $badge2Id,
            'nama' => 'Code Warrior',
            'deskripsi' => 'Menyelesaikan tugas koding dengan nilai di atas 80.',
            'icon_url' => '/image/badges/code_warrior.png',
            'syarat' => 'Mendapatkan nilai >= 80 pada tugas pemrograman.',
            'created_at' => now(),
        ]);

        $badge3Id = (string) Str::uuid();
        Badge::create([
            'id' => $badge3Id,
            'nama' => 'Perfect Scorer',
            'deskripsi' => 'Mendapatkan nilai sempurna 100 pada pengerjaan tugas.',
            'icon_url' => '/image/badges/perfect_scorer.png',
            'syarat' => 'Mendapatkan nilai 100 pada salah satu tugas.',
            'created_at' => now(),
        ]);

        // 10. Seed User Badges
        // Ferdian gets all badges
        UserBadge::create([
            'id' => (string) Str::uuid(),
            'user_id' => $pelajar1UserId,
            'badge_id' => $badge1Id,
            'earned_at' => now()->subDays(5),
        ]);
        UserBadge::create([
            'id' => (string) Str::uuid(),
            'user_id' => $pelajar1UserId,
            'badge_id' => $badge2Id,
            'earned_at' => now()->subDays(3),
        ]);
        UserBadge::create([
            'id' => (string) Str::uuid(),
            'user_id' => $pelajar1UserId,
            'badge_id' => $badge3Id,
            'earned_at' => now()->subDay(),
        ]);

        // Siti Aminah gets Pioneer & Code Warrior
        UserBadge::create([
            'id' => (string) Str::uuid(),
            'user_id' => $pelajar2UserId,
            'badge_id' => $badge1Id,
            'earned_at' => now()->subDays(4),
        ]);
        UserBadge::create([
            'id' => (string) Str::uuid(),
            'user_id' => $pelajar2UserId,
            'badge_id' => $badge2Id,
            'earned_at' => now()->subDays(2),
        ]);

        // 11. Seed PoinLog
        // Ferdian's logs
        PoinLog::create([
            'id' => (string) Str::uuid(),
            'pelajar_id' => $pelajar1UserId,
            'jumlah' => 85,
            'keterangan' => 'Menyelesaikan task: Design Login Page & Register Page',
            'referensi_type' => 'submission',
            'referensi_id' => $sub1Id,
            'created_at' => now()->subDays(3),
        ]);
        PoinLog::create([
            'id' => (string) Str::uuid(),
            'pelajar_id' => $pelajar1UserId,
            'jumlah' => 100,
            'keterangan' => 'Menyelesaikan task: Implement Database Schema & Models',
            'referensi_type' => 'submission',
            'referensi_id' => $sub2Id,
            'created_at' => now()->subDay(),
        ]);

        // Siti Aminah's logs
        PoinLog::create([
            'id' => (string) Str::uuid(),
            'pelajar_id' => $pelajar2UserId,
            'jumlah' => 95,
            'keterangan' => 'Menyelesaikan task: Design Login Page & Register Page',
            'referensi_type' => 'submission',
            'referensi_id' => $sub3Id,
            'created_at' => now()->subDays(2),
        ]);

        // 12. Seed Messages
        Message::create([
            'id' => (string) Str::uuid(),
            'sender_id' => $pelajar1UserId,
            'receiver_id' => $mentorUserId,
            'isi' => 'Selamat sore Pak Ahmad. Saya ingin bertanya tentang tugas migrations, apakah field status harus Enum atau String?',
            'read_at' => now()->subDays(4)->addHours(2),
            'created_at' => now()->subDays(4),
        ]);

        Message::create([
            'id' => (string) Str::uuid(),
            'sender_id' => $mentorUserId,
            'receiver_id' => $pelajar1UserId,
            'isi' => 'Selamat sore Ferdian. Sebaiknya gunakan String saja agar lebih fleksibel ke depannya.',
            'read_at' => now()->subDays(4)->addHours(3),
            'created_at' => now()->subDays(4)->addHours(2)->addMinutes(30),
        ]);

        Message::create([
            'id' => (string) Str::uuid(),
            'sender_id' => $pelajar1UserId,
            'receiver_id' => $mentorUserId,
            'isi' => 'Baik Pak, terima kasih banyak atas arahannya.',
            'read_at' => now()->subDays(4)->addHours(4),
            'created_at' => now()->subDays(4)->addHours(3),
        ]);

        // 13. Seed Notifications
        Notification::create([
            'id' => (string) Str::uuid(),
            'user_id' => $pelajar1UserId,
            'type' => 'submission_reviewed',
            'data' => [
                'title' => 'Tugas Design Login Page & Register Page telah direview',
                'message' => 'Status: Disetujui. Nilai: 85',
                'link' => '/pages/pelajar/enrollments/' . $enrollment1Id,
            ],
            'read_at' => now()->subDays(3),
            'created_at' => now()->subDays(3),
        ]);

        Notification::create([
            'id' => (string) Str::uuid(),
            'user_id' => $pelajar1UserId,
            'type' => 'submission_reviewed',
            'data' => [
                'title' => 'Tugas Implement Database Schema & Models telah direview',
                'message' => 'Status: Disetujui. Nilai: 100',
                'link' => '/pages/pelajar/enrollments/' . $enrollment1Id,
            ],
            'read_at' => null,
            'created_at' => now()->subDay(),
        ]);
    }
}

