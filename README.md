# KerjaIn

[![Laravel Version](https://img.shields.io/badge/Laravel-v10.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%5E8.1-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**KerjaIn** adalah platform manajemen program magang, pelatihan, dan pengerjaan tugas proyek terintegrasi yang dirancang untuk menjembatani **Pelajar**, **Mitra (Perusahaan/Organisasi)**, dan **Mentor**. Platform ini dilengkapi dengan fitur gamifikasi (poin dan lencana) untuk memotivasi pelajar, fitur pengiriman tugas dengan sistem penilaian, serta modul penerbitan sertifikat digital otomatis saat menyelesaikan program.

---

## 📌 Daftar Isi
- [Peran Pengguna & Fitur Utama](#-peran-pengguna--fitur-utama)
- [Teknologi yang Digunakan](#%EF%B8%8F-teknologi-yang-digunakan)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Panduan Instalasi Lokal](#-panduan-instalasi-lokal)
- [Akses Login Uji Coba](#-akses-login-uji-coba)
- [Struktur Database](#-struktur-database)
- [Struktur Folder Utama](#-struktur-folder-utama)

---

## 👥 Peran Pengguna & Fitur Utama

Platform KerjaIn memfasilitasi interaksi antara tiga peran pengguna utama:

### 1. 🎓 Pelajar (Student)
* **Browse & Daftar Program**: Menjelajahi program magang/pelatihan yang dipublikasikan oleh berbagai Mitra dan mendaftar secara langsung.
* **Manajemen Tugas**: Mengakses tugas-tugas terstruktur di setiap program dan mengunggah hasil pekerjaan (submission).
* **Portofolio & Sertifikat**: Mengakses sertifikat kelulusan digital yang diterbitkan secara otomatis dan menampilkannya di halaman profil publik / portofolio.
* **Gamifikasi (Leaderboard & Lencana)**: Memperoleh poin dari tugas yang dikerjakan untuk menaikkan peringkat pada papan peringkat (leaderboard) serta mendapatkan lencana (badge) pencapaian.

### 2. 🏢 Mitra (Partner / Company)
* **Manajemen Program**: Membuat, menyimpan draf, mempublikasikan, dan mengelola kuota program pelatihan/magang.
* **Seleksi Calon Peserta**: Menyeleksi dan menyetujui pendaftaran dari Pelajar.
* **Manajemen Mentor**: Memilih dan menyetujui aplikasi dari Mentor yang ingin mendampingi program mereka.

### 3. 👨‍🏫 Mentor
* **Pengajuan Mentor**: Mengajukan diri untuk memandu dan mendampingi program magang yang dipublikasikan oleh Mitra.
* **Penilaian Tugas**: Memeriksa, memberikan feedback, dan menilai tugas/submission yang dikirimkan oleh Pelajar.
* **Komunikasi Langsung**: Berinteraksi dengan pelajar melalui pesan langsung untuk bimbingan terarah.

---

## 🛠️ Teknologi yang Digunakan

* **Backend Framework**: [Laravel 10](https://laravel.com) (PHP >= 8.1)
* **Frontend Assets Manager**: [Vite](https://vitejs.dev/)
* **Database**: MySQL / MariaDB
* **Keamanan & API Auth**: Laravel Sanctum
* **Real-time & Broadcast**: Log / Pusher Channel integration

---

## 📋 Persyaratan Sistem

Pastikan perangkat Anda telah terinstal:
* **PHP >= 8.1** (beserta ekstensi umum seperti BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
* **Composer** (Dependency manager PHP)
* **Node.js** (LTS) & **NPM**
* **MySQL** atau **MariaDB** (menggunakan XAMPP, Laragon, atau native service)

---

## ⚙️ Panduan Instalasi Lokal

Ikuti langkah-langkah berikut untuk menjalankan project KerjaIn di komputer lokal Anda:

### 1. Kloning Project dan Masuk ke Direktori
Jika Anda baru mengunduh project dari repositori Git:
```bash
git clone <repository-url>
cd KerjaIn
```

### 2. Install Dependensi PHP
Instal library Laravel yang dibutuhkan melalui Composer:
```bash
composer install
```

### 3. Install Dependensi JavaScript
Instal paket-paket frontend melalui NPM:
```bash
npm install
```

### 4. Konfigurasi Environment File
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` yang baru dibuat dan sesuaikan konfigurasi basis data Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kerjain_db
DB_USERNAME=username_mysql_anda # biasanya 'root'
DB_PASSWORD=password_mysql_anda # biasanya kosong atau sesuai setelan lokal Anda
```

### 5. Generate Application Key
Generate kunci enkripsi aplikasi Laravel Anda:
```bash
php artisan key:generate
```

### 6. Impor/Konfigurasi Database
Project ini dilengkapi dengan skema basis data awal yang tersimpan pada berkas:
* `kerjain_db_v2.sql`

Anda dapat mengimpor file tersebut langsung melalui phpMyAdmin, DBeaver, TablePlus, atau cli:
```bash
mysql -u username_anda -p nama_database_anda < kerjain_db_v2.sql
```
*Atau, jika ingin menjalankan migrasi Laravel segar (apabila migrasi sudah lengkap di direktori `database/migrations`):*
```bash
php artisan migrate --seed
```

### 7. Jalankan Aplikasi
Jalankan server lokal PHP:
```bash
php artisan serve
```
Aplikasi Laravel akan berjalan secara default di `http://127.0.0.1:8000`.

Di terminal terpisah, jalankan server kompilasi aset Vite untuk frontend:
```bash
npm run dev
```

---

## 🔑 Akses Login Uji Coba

Setelah menjalankan seeder database (`php artisan db:seed` atau import database SQL), Anda dapat menggunakan akun-akun demo berikut untuk menguji coba berbagai dashboard peran di platform KerjaIn. Semua akun demo menggunakan password yang sama: **`password123`**.

| Peran (Role) | Nama Lengkap | Email / Username | Password | Deskripsi / Kegunaan |
| :--- | :--- | :--- | :--- | :--- |
| **🎓 Pelajar** | Ferdian | `ferdian@gmail.com` | `password123` | Pelajar magang utama, memiliki poin & lencana, pendaftar di program GoTo. |
| **🎓 Pelajar** | Siti Aminah | `siti@pelajar.com` | `password123` | Pelajar magang aktif di program GoTo. |
| **🎓 Pelajar** | Siti Rahma | `siti2@pelajar.com` | `password123` | Pelajar magang aktif di UI/UX Design bootcamp. |
| **👨‍🏫 Mentor** | Ahmad GoTo | `ahmad@goto.id` | `password123` | Mentor pembimbing program GoTo, berhak menilai tugas pelajar. |
| **🏢 Mitra** | HR GoTo | `hr@goto.id` | `password123` | Akun mitra GoTo Group untuk membuat & mengelola program magang. |

### Akun Tambahan (Dari Dashboard Seeder Telkom):
| Peran (Role) | Nama Lengkap | Email / Username | Password | Deskripsi / Kegunaan |
| :--- | :--- | :--- | :--- | :--- |
| **🏢 Mitra** | HR Telkom Indonesia | `hr@telkom.co.id` | `password123` | Akun mitra PT Telkom Indonesia. |
| **👨‍🏫 Mentor** | Budi Mentor Telkom | `budi.mentor@telkom.co.id` | `password123` | Mentor pembimbing program PT Telkom Indonesia. |

---

## 🗄️ Struktur Database

Secara garis besar, database KerjaIn (`kerjain_db_v2.sql`) berisi relasi utama:
- `users`: Menyimpan data autentikasi utama serta peran (`role`: pelajar, mitra, mentor).
- `pelajars`, `mitras`, `mentors`: Tabel profil yang terikat one-to-one dengan `users`.
- `programs`: Program magang/pelatihan yang dibuat oleh `mitras`.
- `program_mentors`: Relasi many-to-many antara program dan mentor pendamping.
- `enrollments`: Pendaftaran pelajar ke suatu program.
- `tasks`: Tugas-tugas terstruktur di dalam program.
- `submissions`: Hasil pengiriman tugas oleh pelajar yang dinilai oleh mentor.
- `certificates`: Sertifikat yang diterbitkan setelah kelulusan program.
- `messages` & `notifications`: Sistem chat dan pemberitahuan antar-pengguna.

---

## 📂 Struktur Folder Utama

* [app/Models/](file:///C:/Users/sam47/OneDrive/Dokumen/Project%20History/Web%20Develop/KerjaIn/app/Models) - Definisi relasi ORM dan struktur data entitas (User, Program, Pelajar, dll).
* [app/Http/Controllers/](file:///C:/Users/sam47/OneDrive/Dokumen/Project%20History/Web%20Develop/KerjaIn/app/Http/Controllers) - Logika alur proses, handling API request, dan pemrosesan data.
* [routes/web.php](file:///C:/Users/sam47/OneDrive/Dokumen/Project%20History/Web%20Develop/KerjaIn/routes/web.php) - Alur navigasi halaman dan routing tampilan Blade.
* [routes/api.php](file:///C:/Users/sam47/OneDrive/Dokumen/Project%20History/Web%20Develop/KerjaIn/routes/api.php) - Endpoint API untuk operasi CRUD interaktif.
* [resources/views/](file:///C:/Users/sam47/OneDrive/Dokumen/Project%20History/Web%20Develop/KerjaIn/resources/views) - Halaman-halaman antarmuka pengguna (Blade templates).

