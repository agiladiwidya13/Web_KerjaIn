<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mahasiswa - KerjaIn</title>
    <link rel="icon" type="image/png" href="../../image/logo-kerjain.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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
        <span class="nav-role-badge"><span class="material-icons" style="font-size: 18px; margin-right: 6px; display: inline-flex; vertical-align: middle;">school</span>Mahasiswa</span>
    </div>
    <div class="nav-auth">
        <span id="nav-nama" style="font-weight:600;color:var(--secondary);padding:8px 12px;"></span>
        <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="dashboard-container">
    <!-- DASHBOARD SIDEBAR -->
    <aside class="dash-sidebar">
        <div class="sidebar-header">
            <h3>Menu Pelajar</h3>
        </div>
        <a class="nav-item" href="/pages/pelajar/dashboard">
            <span class="material-icons nav-icon">bar_chart</span> Dashboard
        </a>
        <a class="nav-item" href="/pages/pelajar/dashboard?section=programs">
            <span class="material-icons nav-icon">menu_book</span> Program Saya
        </a>
        <a class="nav-item" href="/pages/pelajar/certificates">
            <span class="material-icons nav-icon">emoji_events</span> Sertifikat
        </a>
        <a class="nav-item" href="/pages/pelajar/portfolios">
            <span class="material-icons nav-icon">badge</span> Portofolio
        </a>
        <a class="nav-item" href="/pages/programs">
            <span class="material-icons nav-icon">search</span> Cari Program
        </a>
        <a class="nav-item active" href="/pages/pelajar/profile">
            <span class="material-icons nav-icon">account_circle</span> Profil Saya
        </a>
    </aside>

    <div class="dash-main profile-wrapper" style="display:flex; gap:24px;">

    <!-- SIDEBAR -->
    <aside class="profile-sidebar">
        <div class="avatar-wrap" onclick="document.getElementById('foto-input').click()">
            <div class="avatar-circle" id="avatar-initials">?</div>
            <img id="avatar-img" class="avatar-img-el" src="" alt="Foto Profil" style="display:none;">
            <div class="avatar-overlay">Ganti</div>
        </div>
        <input type="file" id="foto-input" accept="image/*" style="display:none;" onchange="uploadFotoProfil(this)">
        <h2 id="sidebar-nama">Memuat...</h2>
        <p id="sidebar-email" style="color:var(--text-muted);font-size:0.9rem;"></p>
        <div class="sidebar-badge"><span class="material-icons" style="font-size: 18px; margin-right: 6px; display: inline-flex; vertical-align: middle;">school</span>Mahasiswa</div>
        <div class="sidebar-info" id="sidebar-info"></div>
    </aside>

    <!-- MAIN PANEL -->
    <main class="profile-main">

        <!-- NOTIFIKASI -->
        <div id="notif" class="notif" style="display:none;"></div>

        <!-- KARTU PROFIL -->
        <div class="profile-card">
            <div class="profile-card-header">
                <h3 style="display: flex; align-items: center; gap: 8px;"><span class="material-icons">assignment</span> Data Profil Saya</h3>
                <button class="btn-edit" id="btn-toggle-edit" onclick="toggleEdit()"><span class="material-icons icon-inline" style="font-size: 1.1rem; margin-right: 4px; vertical-align: middle;">edit</span> Edit Profil</button>
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
                    <button class="btn-save" id="btn-save" onclick="simpanProfil()"><span class="material-icons icon-inline" style="font-size:1.1rem; margin-right:4px;">save</span> Simpan Perubahan</button>
                </div>
            </div>
        </div>

        <!-- KARTU GANTI PASSWORD -->
        <div class="profile-card">
            <div class="profile-card-header">
                <h3 style="display: flex; align-items: center; gap: 8px;"><span class="material-icons">lock</span> Ganti Password</h3>
            </div>
            <div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-top: 16px;">
                <div class="form-group">
                    <label>Password Saat Ini</label>
                    <input type="password" id="p-current" placeholder="Masukkan password saat ini">
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" id="p-new" placeholder="Minimal 8 karakter">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <input type="password" id="p-confirm" placeholder="Ulangi password baru">
                </div>
            </div>
            <div class="form-actions" style="margin-top: 20px; border-top: 1px solid var(--dash-border); padding-top: 16px; display: flex; justify-content: flex-end;">
                <button class="btn-save" id="btn-change-pass" onclick="gantiPassword()" style="background:var(--primary);color:#fff;display:inline-flex;align-items:center;gap:6px;"><span class="material-icons" style="font-size:1.1rem;">vpn_key</span> Ganti Password</button>
            </div>
        </div>

        <!-- KARTU HAPUS AKUN -->
        <div class="profile-card danger-card">
            <div class="profile-card-header">
                <h3 style="display: flex; align-items: center; gap: 8px;"><span class="material-icons">delete_forever</span> Hapus Akun</h3>
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
                    <button class="btn-danger" id="btn-del" onclick="hapusAkun()" style="display:inline-flex;align-items:center;gap:6px;"><span class="material-icons" style="font-size:1.1rem;">delete</span> Hapus Akun Saya</button>
                </div>
            </div>
            <button class="btn-danger-outline" id="btn-show-delete" onclick="toggleDeleteForm(true)">Hapus Akun Saya</button>
        </div>

    </main>
    </div>
</div>

<script src="{{ asset('js/profile-pelajar.js') }}"></script>
<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
