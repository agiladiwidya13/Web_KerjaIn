-- ============================================================
--  KerjaIn Database Schema v2
--  Generated: 2026-05-23
--  Engine: MariaDB 10.4+  |  Charset: utf8mb4_unicode_ci
-- ============================================================
--  FASE 0  (sudah ada, diperbarui)
--    users, pelajar, mentor, mitra
--  FASE 1  (afiliasi Mentor–Mitra)
--    ALTER pada mentor & mitra
--  FASE 2  (Project & Task System)
--    programs, program_mentors, tasks, enrollments, submissions
--  FASE 3  (Portofolio, Sertifikat, Notifikasi)
--    portfolios, certificates, notifications
--  FASE 4  (Gamifikasi & Pesan)
--    poin_log, badges, user_badges, messages
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
--  DROP semua tabel (urutan terbalik agar FK tidak bentrok)
-- ============================================================
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `user_badges`;
DROP TABLE IF EXISTS `badges`;
DROP TABLE IF EXISTS `poin_log`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `certificates`;
DROP TABLE IF EXISTS `portfolios`;
DROP TABLE IF EXISTS `submissions`;
DROP TABLE IF EXISTS `enrollments`;
DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `program_mentors`;
DROP TABLE IF EXISTS `programs`;
DROP TABLE IF EXISTS `pelajar`;
DROP TABLE IF EXISTS `mentor`;
DROP TABLE IF EXISTS `mitra`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `migrations`;

-- ============================================================
--  FASE 0  —  Fondasi Auth & Profil (diperbarui)
-- ============================================================

-- ------------------------------------------------------------
--  Tabel: users
--  Perubahan dari v1:
--    + email_verified_at  (diperlukan untuk verifikasi Mentor)
--    ~ password_hash → password  (konvensi Laravel Auth)
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id`                char(36)                          NOT NULL,
  `nama_lengkap`      varchar(255)                      NOT NULL,
  `email`             varchar(255)                      NOT NULL,
  `password`          varchar(255)                      NOT NULL,
  `role`              enum('pelajar','mentor','mitra')  NOT NULL,
  `foto_profil`       varchar(255)                      DEFAULT NULL,
  `email_verified_at` timestamp                         NULL DEFAULT NULL,
  `remember_token`    varchar(100)                      DEFAULT NULL,
  `created_at`        timestamp                         NULL DEFAULT NULL,
  `updated_at`        timestamp                         NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: pelajar  (tidak ada perubahan dari v1)
-- ------------------------------------------------------------
CREATE TABLE `pelajar` (
  `id`          char(36)              NOT NULL,
  `user_id`     char(36)              NOT NULL,
  `universitas` varchar(255)          DEFAULT NULL,
  `jurusan`     varchar(255)          DEFAULT NULL,
  `angkatan`    smallint(5) UNSIGNED  DEFAULT NULL,
  `bio`         text                  DEFAULT NULL,
  `total_poin`  int(10) UNSIGNED      NOT NULL DEFAULT 0,
  `created_at`  timestamp             NULL DEFAULT NULL,
  `updated_at`  timestamp             NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pelajar_user_id_unique` (`user_id`),
  CONSTRAINT `pelajar_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: mitra
--  Perubahan dari v1:
--    + email_domain   (★ kunci sistem afiliasi — UNIQUE NOT NULL)
--    + logo_perusahaan (untuk sertifikat digital di Fase 3)
--    + website
-- ------------------------------------------------------------
CREATE TABLE `mitra` (
  `id`               char(36)     NOT NULL,
  `user_id`          char(36)     NOT NULL,
  `nama_usaha`       varchar(255) NOT NULL DEFAULT 'Belum diisi',
  `bidang_usaha`     varchar(255) DEFAULT NULL,
  `kota`             varchar(255) DEFAULT NULL,
  `kontak_bisnis`    varchar(255) DEFAULT NULL,
  `email_domain`     varchar(100) NOT NULL,   -- ★ misal: goto.id, tokopedia.com
  `logo_perusahaan`  varchar(255) DEFAULT NULL,
  `website`          varchar(255) DEFAULT NULL,
  `created_at`       timestamp    NULL DEFAULT NULL,
  `updated_at`       timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mitra_user_id_unique`    (`user_id`),
  UNIQUE KEY `mitra_email_domain_unique` (`email_domain`), -- ★ satu domain = satu mitra
  CONSTRAINT `mitra_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: mentor
--  Perubahan dari v1:
--    + mitra_id FK   (★ kunci sistem afiliasi)
--    ~ perusahaan    (dijaga sebagai display name, bukan basis otorisasi)
-- ------------------------------------------------------------
CREATE TABLE `mentor` (
  `id`               char(36)             NOT NULL,
  `user_id`          char(36)             NOT NULL,
  `mitra_id`         char(36)             DEFAULT NULL,  -- ★ FK ke mitra
  `profesi`          varchar(255)         DEFAULT NULL,
  `perusahaan`       varchar(255)         DEFAULT NULL,  -- display only
  `tahun_pengalaman` tinyint(3) UNSIGNED  NOT NULL DEFAULT 0,
  `bio_keahlian`     text                 DEFAULT NULL,
  `created_at`       timestamp            NULL DEFAULT NULL,
  `updated_at`       timestamp            NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mentor_user_id_unique` (`user_id`),
  KEY `mentor_mitra_id_index` (`mitra_id`),
  CONSTRAINT `mentor_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mentor_mitra_id_foreign`
    FOREIGN KEY (`mitra_id`) REFERENCES `mitra` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  FASE 2  —  Core: Project & Task System
-- ============================================================

-- ------------------------------------------------------------
--  Tabel: programs  (program/project dari mitra)
-- ------------------------------------------------------------
CREATE TABLE `programs` (
  `id`               char(36)                             NOT NULL,
  `mitra_id`         char(36)                             NOT NULL,
  `judul`            varchar(255)                         NOT NULL,
  `deskripsi`        text                                 DEFAULT NULL,
  `bidang`           varchar(100)                         DEFAULT NULL, -- misal: UI/UX, Backend, Marketing
  `status`           enum('draft','published','closed')   NOT NULL DEFAULT 'draft',
  `kuota`            smallint(5) UNSIGNED                 DEFAULT NULL, -- NULL = tidak terbatas
  `tanggal_mulai`    date                                 DEFAULT NULL,
  `tanggal_selesai`  date                                 DEFAULT NULL,
  `created_at`       timestamp                            NULL DEFAULT NULL,
  `updated_at`       timestamp                            NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `programs_mitra_id_index`  (`mitra_id`),
  KEY `programs_status_index`    (`status`),
  CONSTRAINT `programs_mitra_id_foreign`
    FOREIGN KEY (`mitra_id`) REFERENCES `mitra` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: program_mentors  (many-to-many: program ↔ mentor)
--  Mentor hanya bisa di-assign ke program dari mitranya sendiri
--  (validasi di aplikasi layer, bukan DB constraint)
-- ------------------------------------------------------------
CREATE TABLE `program_mentors` (
  `id`          char(36)   NOT NULL,
  `program_id`  char(36)   NOT NULL,
  `mentor_id`   char(36)   NOT NULL,
  `assigned_at` timestamp  NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `program_mentors_unique` (`program_id`, `mentor_id`),
  CONSTRAINT `pm_program_id_foreign`
    FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pm_mentor_id_foreign`
    FOREIGN KEY (`mentor_id`) REFERENCES `mentor` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: tasks  (tugas per program, berurutan)
-- ------------------------------------------------------------
CREATE TABLE `tasks` (
  `id`          char(36)     NOT NULL,
  `program_id`  char(36)     NOT NULL,
  `judul`       varchar(255) NOT NULL,
  `deskripsi`   text         DEFAULT NULL,
  `deadline`    datetime     DEFAULT NULL,
  `urutan`      tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `created_at`  timestamp    NULL DEFAULT NULL,
  `updated_at`  timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tasks_program_id_index` (`program_id`),
  CONSTRAINT `tasks_program_id_foreign`
    FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: enrollments  (pelajar mendaftar program)
-- ------------------------------------------------------------
CREATE TABLE `enrollments` (
  `id`          char(36)                                         NOT NULL,
  `pelajar_id`  char(36)                                         NOT NULL, -- FK ke users (role=pelajar)
  `program_id`  char(36)                                         NOT NULL,
  `status`      enum('aktif','selesai','dibatalkan','ditolak')   NOT NULL DEFAULT 'aktif',
  `enrolled_at` timestamp                                        NULL DEFAULT NULL,
  `selesai_at`  timestamp                                        NULL DEFAULT NULL,
  `created_at`  timestamp                                        NULL DEFAULT NULL,
  `updated_at`  timestamp                                        NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollments_pelajar_program_unique` (`pelajar_id`, `program_id`),
  KEY `enrollments_program_id_index` (`program_id`),
  CONSTRAINT `enrollments_pelajar_id_foreign`
    FOREIGN KEY (`pelajar_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_program_id_foreign`
    FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: submissions  (pelajar submit task, mentor review)
-- ------------------------------------------------------------
CREATE TABLE `submissions` (
  `id`             char(36)                                  NOT NULL,
  `enrollment_id`  char(36)                                  NOT NULL,
  `task_id`        char(36)                                  NOT NULL,
  `file_url`       varchar(500)                              DEFAULT NULL,
  `catatan`        text                                      DEFAULT NULL,
  `status`         enum('menunggu','disetujui','revisi')     NOT NULL DEFAULT 'menunggu',
  `feedback`       text                                      DEFAULT NULL,
  `nilai`          tinyint(3) UNSIGNED                       DEFAULT NULL, -- 0–100
  `reviewed_by`    char(36)                                  DEFAULT NULL, -- FK ke mentor.id
  `reviewed_at`    timestamp                                 NULL DEFAULT NULL,
  `created_at`     timestamp                                 NULL DEFAULT NULL,
  `updated_at`     timestamp                                 NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `submissions_enrollment_task_unique` (`enrollment_id`, `task_id`),
  KEY `submissions_task_id_index`      (`task_id`),
  KEY `submissions_reviewed_by_index`  (`reviewed_by`),
  CONSTRAINT `submissions_enrollment_id_foreign`
    FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submissions_task_id_foreign`
    FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submissions_reviewed_by_foreign`
    FOREIGN KEY (`reviewed_by`) REFERENCES `mentor` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  FASE 3  —  Portofolio, Sertifikat & Notifikasi
-- ============================================================

-- ------------------------------------------------------------
--  Tabel: portfolios  (otomatis dibuat saat enrollment = selesai)
-- ------------------------------------------------------------
CREATE TABLE `portfolios` (
  `id`            char(36)   NOT NULL,
  `enrollment_id` char(36)   NOT NULL,
  `pelajar_id`    char(36)   NOT NULL,
  `is_public`     tinyint(1) NOT NULL DEFAULT 1,
  `created_at`    timestamp  NULL DEFAULT NULL,
  `updated_at`    timestamp  NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `portfolios_enrollment_unique` (`enrollment_id`),
  KEY `portfolios_pelajar_id_index` (`pelajar_id`),
  CONSTRAINT `portfolios_enrollment_id_foreign`
    FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `portfolios_pelajar_id_foreign`
    FOREIGN KEY (`pelajar_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: certificates  (sertifikat digital per enrollment selesai)
-- ------------------------------------------------------------
CREATE TABLE `certificates` (
  `id`                char(36)     NOT NULL,
  `enrollment_id`     char(36)     NOT NULL,
  `issued_by`         char(36)     DEFAULT NULL, -- FK ke mentor.id
  `nomor_sertifikat`  varchar(100) NOT NULL,     -- misal: KI-2026-0001
  `pdf_url`           varchar(500) DEFAULT NULL,
  `issued_at`         timestamp    NULL DEFAULT NULL,
  `created_at`        timestamp    NULL DEFAULT NULL,
  `updated_at`        timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certificates_enrollment_unique`    (`enrollment_id`),
  UNIQUE KEY `certificates_nomor_unique`         (`nomor_sertifikat`),
  KEY `certificates_issued_by_index` (`issued_by`),
  CONSTRAINT `certificates_enrollment_id_foreign`
    FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `certificates_issued_by_foreign`
    FOREIGN KEY (`issued_by`) REFERENCES `mentor` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: notifications  (notifikasi in-app semua role)
-- ------------------------------------------------------------
CREATE TABLE `notifications` (
  `id`         char(36)     NOT NULL,
  `user_id`    char(36)     NOT NULL,
  `type`       varchar(100) NOT NULL, -- misal: submission_approved, new_feedback
  `data`       json         DEFAULT NULL,
  `read_at`    timestamp    NULL DEFAULT NULL,
  `created_at` timestamp    NULL DEFAULT NULL,
  `updated_at` timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_index` (`user_id`),
  KEY `notifications_read_at_index` (`read_at`),
  CONSTRAINT `notifications_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  FASE 4  —  Gamifikasi & Pesan
-- ============================================================

-- ------------------------------------------------------------
--  Tabel: poin_log  (riwayat perolehan poin pelajar)
-- ------------------------------------------------------------
CREATE TABLE `poin_log` (
  `id`          char(36)     NOT NULL,
  `pelajar_id`  char(36)     NOT NULL,
  `jumlah`      smallint(5)  NOT NULL,           -- bisa negatif (pengurangan)
  `keterangan`  varchar(255) DEFAULT NULL,        -- misal: Task disetujui, Program selesai
  `referensi_type` varchar(100) DEFAULT NULL,     -- misal: submission, enrollment
  `referensi_id`   char(36)  DEFAULT NULL,
  `created_at`  timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `poin_log_pelajar_id_index` (`pelajar_id`),
  CONSTRAINT `poin_log_pelajar_id_foreign`
    FOREIGN KEY (`pelajar_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: badges  (definisi badge pencapaian)
-- ------------------------------------------------------------
CREATE TABLE `badges` (
  `id`          char(36)     NOT NULL,
  `nama`        varchar(100) NOT NULL,
  `deskripsi`   varchar(255) DEFAULT NULL,
  `icon_url`    varchar(255) DEFAULT NULL,
  `syarat`      varchar(255) DEFAULT NULL, -- misal: selesaikan 3 program
  `created_at`  timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: user_badges  (pelajar mendapatkan badge)
-- ------------------------------------------------------------
CREATE TABLE `user_badges` (
  `id`         char(36)   NOT NULL,
  `user_id`    char(36)   NOT NULL,
  `badge_id`   char(36)   NOT NULL,
  `earned_at`  timestamp  NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_badges_unique` (`user_id`, `badge_id`),
  CONSTRAINT `user_badges_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_badges_badge_id_foreign`
    FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Tabel: messages  (pesan langsung antar user)
-- ------------------------------------------------------------
CREATE TABLE `messages` (
  `id`          char(36)    NOT NULL,
  `sender_id`   char(36)    NOT NULL,
  `receiver_id` char(36)    NOT NULL,
  `isi`         text        NOT NULL,
  `read_at`     timestamp   NULL DEFAULT NULL,
  `created_at`  timestamp   NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_index`   (`sender_id`),
  KEY `messages_receiver_id_index` (`receiver_id`),
  KEY `messages_conversation_index` (`sender_id`, `receiver_id`),
  CONSTRAINT `messages_sender_id_foreign`
    FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_receiver_id_foreign`
    FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  Tabel sistem Laravel (standar)
-- ============================================================

CREATE TABLE `migrations` (
  `id`        int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255)     NOT NULL,
  `batch`     int(11)          NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`       varchar(255)        NOT NULL,
  `connection` text                NOT NULL,
  `queue`      text                NOT NULL,
  `payload`    longtext            NOT NULL,
  `exception`  longtext            NOT NULL,
  `failed_at`  timestamp           NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email`      varchar(255) NOT NULL,
  `token`      varchar(255) NOT NULL,
  `created_at` timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id`             bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255)        NOT NULL,
  `tokenable_id`   bigint(20) UNSIGNED NOT NULL,
  `name`           varchar(255)        NOT NULL,
  `token`          varchar(64)         NOT NULL,
  `abilities`      text                DEFAULT NULL,
  `last_used_at`   timestamp           NULL DEFAULT NULL,
  `expires_at`     timestamp           NULL DEFAULT NULL,
  `created_at`     timestamp           NULL DEFAULT NULL,
  `updated_at`     timestamp           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_index` (`tokenable_type`, `tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ============================================================
--  Ringkasan tabel: 15 tabel total
-- ------------------------------------------------------------
--  Fase 0  users, pelajar, mentor, mitra
--  Fase 2  programs, program_mentors, tasks, enrollments, submissions
--  Fase 3  portfolios, certificates, notifications
--  Fase 4  poin_log, badges, user_badges, messages
--  Sistem  migrations, failed_jobs, password_reset_tokens,
--          personal_access_tokens
-- ============================================================
