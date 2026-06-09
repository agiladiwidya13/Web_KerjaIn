<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mitra - KerjaIn</title>
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
        <span class="nav-role-badge" style="background:#fef3c7;color:#92400e;"><span class="material-icons" style="font-size: 18px; margin-right: 6px; display: inline-flex; vertical-align: middle;">business</span>Mitra</span>
    </div>
    <div class="nav-auth">
        <span id="nav-nama" style="font-weight:600;color:var(--secondary);padding:8px 12px;"></span>
        <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
    </div>
</nav>

<div class="dashboard-container" style="padding-top: 72px;">
    <!-- DASHBOARD SIDEBAR -->
    <aside class="dash-sidebar">
        <div class="sidebar-header">
            <h3>Menu Mitra</h3>
        </div>
        <a class="nav-item" href="/pages/mitra/dashboard">
            <span class="material-icons nav-icon">bar_chart</span> Dashboard
        </a>
        <a class="nav-item" href="/pages/mitra/dashboard?section=programs">
            <span class="material-icons nav-icon">menu_book</span> Kelola Program
        </a>
        <a class="nav-item" href="/pages/mitra/dashboard?section=mentors">
            <span class="material-icons nav-icon">person</span> Mentor Saya
        </a>
        <a class="nav-item" href="/pages/mitra/candidates">
            <span class="material-icons nav-icon">search</span> Cari Kandidat
        </a>
        <a class="nav-item active" href="/pages/mitra/profile">
            <span class="material-icons nav-icon">account_circle</span> Profil Perusahaan
        </a>
    </aside>

    <div class="dash-main profile-wrapper" style="display:flex; gap:24px;">

    <!-- SIDEBAR -->
    <aside class="profile-sidebar">
        <div class="avatar-wrap" onclick="document.getElementById('foto-input').click()">
            <div class="avatar-circle" style="background:linear-gradient(135deg,#f59e0b,#d97706)" id="avatar-initials">?</div>
            <img id="avatar-img" class="avatar-img-el" src="" alt="Foto Profil" style="display:none;">
            <div class="avatar-overlay">Ganti</div>
        </div>
        <input type="file" id="foto-input" accept="image/*" style="display:none;" onchange="uploadFotoProfil(this)">
        <h2 id="sidebar-nama">Memuat...</h2>
        <p id="sidebar-email" style="color:var(--text-muted);font-size:0.9rem;"></p>
        <div class="sidebar-badge" style="background:#fef3c7;color:#92400e;"><span class="material-icons" style="font-size: 18px; margin-right: 6px; display: inline-flex; vertical-align: middle;">business</span>Mitra Perusahaan</div>
        <div class="sidebar-info" id="sidebar-info"></div>
    </aside>

    <!-- MAIN PANEL -->
    <main class="profile-main">

        <div id="notif" class="notif" style="display:none;"></div>

        <!-- KARTU PROFIL -->
        <div class="profile-card">
            <div class="profile-card-header">
                <h3 style="display: flex; align-items: center; gap: 8px;"><span class="material-icons">assignment</span> Data Profil Perusahaan</h3>
                <button class="btn-edit" id="btn-toggle-edit" onclick="toggleEdit()"><span class="material-icons icon-inline" style="font-size: 1.1rem; margin-right: 4px; vertical-align: middle;">edit</span> Edit Profil</button>
            </div>

            <!-- MODE TAMPIL -->
            <div id="view-mode">
                <div class="info-grid">
                    <div class="info-item"><span class="info-label">Nama PIC / HR</span><span class="info-val" id="v-nama">—</span></div>
                    <div class="info-item"><span class="info-label">Email</span><span class="info-val" id="v-email">—</span></div>
                    <div class="info-item"><span class="info-label">Nama Usaha</span><span class="info-val" id="v-usaha">—</span></div>
                    <div class="info-item"><span class="info-label">Bidang Usaha</span><span class="info-val" id="v-bidang">—</span></div>
                    <div class="info-item"><span class="info-label">Kota</span><span class="info-val" id="v-kota">—</span></div>
                    <div class="info-item"><span class="info-label">Kontak Bisnis</span><span class="info-val" id="v-kontak">—</span></div>
                    <div class="info-item info-item-full" style="display:flex; align-items:center; gap:16px;">
                        <span class="info-label" style="min-width:120px;">Logo Perusahaan</span>
                        <div class="logo-wrap" style="width:64px; height:64px; border-radius:8px; border:2px dashed #cbd5e1; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8fafc;">
                            <img id="v-logo" src="" alt="Logo" style="width:100%; height:100%; object-fit:cover; display:none;">
                            <span id="v-logo-empty" style="font-size:1.5rem; color:#cbd5e1;"><span class="material-icons" style="font-size: 48px;">business</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MODE EDIT -->
            <div id="edit-mode" style="display:none;">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama PIC / HR</label>
                        <input type="text" id="e-nama" placeholder="Nama penanggung jawab">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="e-email" disabled style="background:#f1f5f9;cursor:not-allowed;">
                    </div>
                    <div class="form-group">
                        <label>Nama Usaha <span style="color:red">*</span></label>
                        <input type="text" id="e-usaha" placeholder="cth: PT. Contoh Jaya">
                    </div>
                    <div class="form-group">
                        <label>Bidang Usaha</label>
                        <input type="text" id="e-bidang" placeholder="cth: Teknologi & Informasi">
                    </div>
                    <div class="form-group">
                        <label>Kota</label>
                        <input type="text" id="e-kota" placeholder="cth: Surabaya">
                    </div>
                    <div class="form-group">
                        <label>Kontak Bisnis</label>
                        <input type="text" id="e-kontak" placeholder="cth: 08xx-xxxx-xxxx">
                    </div>
                    <div class="form-group form-group-full">
                        <label>Logo Perusahaan</label>
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div class="logo-wrap" style="width:64px; height:64px; border-radius:8px; border:2px dashed #cbd5e1; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#f8fafc;">
                                <img id="e-logo-preview" src="" alt="Logo Preview" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <span id="e-logo-empty" style="font-size:1.5rem; color:#cbd5e1;"><span class="material-icons" style="font-size: 48px;">business</span></span>
                            </div>
                            <button type="button" class="btn-edit" onclick="document.getElementById('logo-input').click()"><span class="material-icons icon-inline" style="font-size:1.1rem; margin-right:4px;">folder_open</span> Pilih File Logo</button>
                            <input type="file" id="logo-input" accept="image/*" style="display:none;" onchange="uploadLogoPerusahaan(this)">
                        </div>
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
                Tindakan ini <strong>tidak dapat dibatalkan</strong>. Semua data perusahaan dan program yang dibuat akan dihapus secara permanen.
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

<script src="{{ asset('js/profile-mitra.js') }}"></script>
<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
