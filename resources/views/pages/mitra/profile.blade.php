<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mitra - KerjaIn</title>
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
        <span class="nav-role-badge" style="background:#fef3c7;color:#92400e;">🏢 Mitra</span>
    </div>
    <div class="nav-auth">
        <span id="nav-nama" style="font-weight:600;color:var(--secondary);padding:8px 12px;"></span>
        <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
    </div>
</nav>

<div class="profile-container">

    <!-- SIDEBAR -->
    <aside class="profile-sidebar">
        <div class="avatar-wrap">
            <div class="avatar-circle" style="background:linear-gradient(135deg,#f59e0b,#d97706)" id="avatar-initials">?</div>
        </div>
        <h2 id="sidebar-nama">Memuat...</h2>
        <p id="sidebar-email" style="color:var(--text-muted);font-size:0.9rem;"></p>
        <div class="sidebar-badge" style="background:#fef3c7;color:#92400e;">🏢 Mitra Perusahaan</div>
        <div class="sidebar-info" id="sidebar-info"></div>
    </aside>

    <!-- MAIN PANEL -->
    <main class="profile-main">

        <div id="notif" class="notif" style="display:none;"></div>

        <!-- KARTU PROFIL -->
        <div class="profile-card">
            <div class="profile-card-header">
                <h3>📋 Data Profil Perusahaan</h3>
                <button class="btn-edit" id="btn-toggle-edit" onclick="toggleEdit()">✏️ Edit Profil</button>
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
                Tindakan ini <strong>tidak dapat dibatalkan</strong>. Semua data perusahaan dan program yang dibuat akan dihapus secara permanen.
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

<script src="{{ asset('js/profile-mitra.js') }}"></script>
</body>
</html>
