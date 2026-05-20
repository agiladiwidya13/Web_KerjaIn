<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mahasiswa - KerjaIn</title>
    <link rel="icon" type="image/png" href="../../image/logo-kerjain.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
<body>

<!-- NAVIGATION -->
<nav>
    <div class="logo" onclick="window.location.href='/'">
        <img src="{{ asset('image/logo-kerjain.png') }}" alt="Logo KerjaIn" class="logo-img" onerror="this.style.display='none'">
        KerjaIn
    </div>
    <div class="nav-links">
        <span class="nav-role-badge">🎓 Mahasiswa</span>
    </div>
    <div class="nav-auth">
        <span id="nav-nama" style="font-weight:600;color:var(--secondary);padding:8px 12px;"></span>
        <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="profile-container">

    <!-- SIDEBAR -->
    <aside class="profile-sidebar">
        <div class="avatar-wrap">
            <div class="avatar-circle" id="avatar-initials">?</div>
        </div>
        <h2 id="sidebar-nama">Memuat...</h2>
        <p id="sidebar-email" style="color:var(--text-muted);font-size:0.9rem;"></p>
        <div class="sidebar-badge">🎓 Mahasiswa</div>
        <div class="sidebar-info" id="sidebar-info"></div>
    </aside>

    <!-- MAIN PANEL -->
    <main class="profile-main">

        <!-- NOTIFIKASI -->
        <div id="notif" class="notif" style="display:none;"></div>

        <!-- KARTU PROFIL -->
        <div class="profile-card">
            <div class="profile-card-header">
                <h3>📋 Data Profil Saya</h3>
                <button class="btn-edit" id="btn-toggle-edit" onclick="toggleEdit()">✏️ Edit Profil</button>
            </div>

            <!-- MODE TAMPIL -->
            <div id="view-mode">
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Nama Lengkap</span><span class="info-val" id="v-nama">—</span></div>
                    <div class="info-item"><span class="info-label">Email</span><span class="info-val" id="v-email">—</span></div>
                    <div class="info-item"><span class="info-label">Universitas</span><span class="info-val" id="v-universitas">—</span></div>
                    <div class="info-item"><span class="info-label">Jurusan</span><span class="info-val" id="v-jurusan">—</span></div>
                    <div class="info-item"><span class="info-label">Angkatan</span><span class="info-val" id="v-angkatan">—</span></div>
                    <div class="info-item info-item-full"><span class="info-label">Bio</span><span class="info-val" id="v-bio">—</span></div>
                </div>
            </div>

            <!-- MODE EDIT -->
            <div id="edit-mode" style="display:none;">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" id="e-nama" placeholder="Nama lengkap kamu">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="e-email" disabled style="background:#f1f5f9;cursor:not-allowed;">
                    </div>
                    <div class="form-group">
                        <label>Universitas <span style="color:red">*</span></label>
                        <input type="text" id="e-universitas" placeholder="cth: Universitas Pembangunan Nasional">
                    </div>
                    <div class="form-group">
                        <label>Jurusan <span style="color:red">*</span></label>
                        <input type="text" id="e-jurusan" placeholder="cth: Teknik Informatika">
                    </div>
                    <div class="form-group">
                        <label>Angkatan <span style="color:red">*</span></label>
                        <input type="number" id="e-angkatan" placeholder="cth: 2022" min="2000" max="2099">
                    </div>
                    <div class="form-group form-group-full">
                        <label>Bio Singkat</label>
                        <textarea id="e-bio" rows="3" placeholder="Ceritakan sedikit tentang dirimu..."></textarea>
                    </div>
                </div>
                <div class="form-actions">
                    <button class="btn-cancel" onclick="cancelEdit()">Batal</button>
                    <button class="btn-save" id="btn-save" onclick="simpanProfil()">💾 Simpan Perubahan</button>
                </div>
            </div>
        </div>

        <!-- KARTU HAPUS AKUN -->
        <div class="profile-card danger-card">
            <div class="profile-card-header">
                <h3>🗑️ Hapus Akun</h3>
            </div>
            <p style="color:var(--text-muted);margin-bottom:16px;">
                Tindakan ini <strong>tidak dapat dibatalkan</strong>. Semua data profil, skill, pendaftaran, dan portofolio kamu akan dihapus secara permanen.
            </p>
            <div id="delete-form" style="display:none;">
                <div class="form-group" style="max-width:360px;">
                    <label>Konfirmasi Password</label>
                    <input type="password" id="del-pass" placeholder="Masukkan password kamu">
                </div>
                <div class="form-actions">
                    <button class="btn-cancel" onclick="toggleDeleteForm(false)">Batal</button>
                    <button class="btn-danger" id="btn-del" onclick="hapusAkun()">🗑️ Hapus Akun Saya</button>
                </div>
            </div>
            <button class="btn-danger-outline" id="btn-show-delete" onclick="toggleDeleteForm(true)">Hapus Akun Saya</button>
        </div>

    </main>
</div>

<script src="{{ asset('js/profile-pelajar.js') }}"></script>
</body>
</html>
