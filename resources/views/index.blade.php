<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KerjaIn - Masa Depan Cerah</title>
    <link rel="icon" type="image/png" href="image/logo-kerjain.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <!-- Cara manggil file style.css -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Cara manggil file profile.css -->
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
</head>
<body>

<!-- NAVIGATION -->
<nav>
    <div class="logo" onclick="showPage('home')">
        <img src="image/logo-kerjain.png" alt="Logo KerjaIn" class="logo-img" onerror="this.style.display='none'">
        KerjaIn
    </div>
    <div class="nav-links">
        <a onclick="showPage('jelajahi')" id="nav-jelajahi">Jelajahi</a>
        <a onclick="showPage('tentang')" id="nav-tentang">Tentang</a>
    </div>
    <div class="nav-auth" id="nav-auth">
        <!-- Diisi dinamis oleh JS berdasarkan status login -->
        <a class="btn-outline" onclick="openRoleModal('masuk')">Masuk</a>
        <a class="btn-solid" onclick="openRoleModal('daftar')">Daftar</a>
    </div>
</nav>

<!-- ===== HOME PAGE ===== -->
<div id="page-home" class="page active">
    <section class="hero">
        <div class="hero-left">
            <div class="hero-badge"><span class="material-icons icon-inline">rocket</span>50+ Program Aktif dari Perusahaan Top Indonesia</div>
            <h1>Terangi Karirmu<br>dengan Pengalaman<br>Nyata</h1>
            <p>Jembatan antara bangku kuliah dan dunia profesional. Selesaikan simulasi dari industri terbaik dan raih masa depan cerahmu.</p>
            <div class="hero-cta">
                <button class="cta-primary" onclick="showPage('jelajahi')">Jelajahi Program</button>
                <button class="cta-secondary" onclick="showPage('tentang')">Tentang KerjaIn</button>
            </div>
        </div>
        <div class="hero-right">
            <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=900&q=80&auto=format&fit=crop" alt="Mahasiswa bekerja sama di laptop" onerror="this.src='https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=900&q=80'">
        </div>
    </section>

    <div class="stats-bar">
        <div class="stat-item"><div class="stat-number">12.000+</div><div class="stat-label">Mahasiswa Aktif</div></div>
        <div class="stat-item"><div class="stat-number">50+</div><div class="stat-label">Program Tersedia</div></div>
        <div class="stat-item"><div class="stat-number">30+</div><div class="stat-label">Perusahaan Mitra</div></div>
        <div class="stat-item"><div class="stat-number">100%</div><div class="stat-label">Gratis & Bersertifikat</div></div>
    </div>

    <section class="how-section">
        <h2>Bagaimana Cara Kerjanya?</h2>
        <div class="how-grid">
            <div class="how-step"><div class="step-num">1</div><h4>Pilih Program</h4><p>Temukan simulasi yang sesuai minat dan bidangmu dari 30+ perusahaan mitra.</p></div>
            <div class="how-step"><div class="step-num">2</div><h4>Kerjakan Tugas</h4><p>Selesaikan tugas nyata yang dirancang langsung oleh tim HR perusahaan.</p></div>
            <div class="how-step"><div class="step-num">3</div><h4>Dapatkan Feedback</h4><p>Terima feedback dan penilaian dari model jawaban yang disusun para expert.</p></div>
            <div class="how-step"><div class="step-num">4</div><h4>Raih Sertifikat</h4><p>Unduh sertifikat untuk LinkedIn dan CV — diakui oleh ratusan perusahaan.</p></div>
        </div>
    </section>

    <div class="section-wrap">
        <h2 class="section-title">Program Unggulan</h2>
        <div class="program-grid" id="home-grid"></div>
    </div>

    <section class="testimonial-section">
        <h2>Kata Mereka yang Sudah Bergabung</h2>
        <div class="testimonial-grid">
            <div class="testimonial-card">
                <img class="testi-avatar" src="https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?w=100&h=100&q=80&auto=format&fit=crop&crop=face" alt="Putri" onerror="this.style.display='none'">
                <div class="testi-stars"><span class="material-icons">star</span><span class="material-icons">star</span><span class="material-icons">star</span><span class="material-icons">star</span><span class="material-icons">star</span></div>
                <p class="testi-text">"Setelah menyelesaikan simulasi BCG di KerjaIn, saya percaya diri melamar magang konsultan. Alhamdulillah langsung diterima!"</p>
                <div class="testi-name">Putri Ramadhani</div>
                <div class="testi-role">Mahasiswa Manajemen, UI · Intern di BCG</div>
            </div>
            <div class="testimonial-card">
                <img class="testi-avatar" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&q=80&auto=format&fit=crop&crop=face" alt="Dimas" onerror="this.style.display='none'">
                <div class="testi-stars"><span class="material-icons">star</span><span class="material-icons">star</span><span class="material-icons">star</span><span class="material-icons">star</span><span class="material-icons">star</span></div>
                <p class="testi-text">"Simulasi GoTo sangat mirip dengan pekerjaan nyata. HR bilang sertifikat KerjaIn-ku jadi pembeda di antara ratusan pelamar."</p>
                <div class="testi-name">Dimas Pratama</div>
                <div class="testi-role">Mahasiswa Teknik Informatika, ITS · Intern di GoTo</div>
            </div>
            <div class="testimonial-card">
                <img class="testi-avatar" src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&h=100&q=80&auto=format&fit=crop&crop=face" alt="Anisa" onerror="this.style.display='none'">
                <div class="testi-stars"><span class="material-icons">star</span><span class="material-icons">star</span><span class="material-icons">star</span><span class="material-icons">star</span><span class="material-icons">star</span></div>
                <p class="testi-text">"Platform gratis terbaik yang pernah ada! Saya dari daerah dan tidak bisa magang offline, KerjaIn jadi jembatan saya ke dunia kerja."</p>
                <div class="testi-name">Anisa Fitri</div>
                <div class="testi-role">Mahasiswa Akuntansi, Unhas · Intern di BCA</div>
            </div>
        </div>
    </section>
</div>

<!-- ===== JELAJAHI PAGE ===== -->
<div id="page-jelajahi" class="page">
    <div class="explore-header">
        <h2>Jelajahi Semua Program</h2>
        <p>Temukan simulasi kerja dari perusahaan terbaik sesuai minat dan bidangmu</p>
    </div>
    <div class="filter-bar">
        <div class="search-box">
            <svg width="18" height="18" fill="none" stroke="#94a3b8" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="search-input" placeholder="Cari program, perusahaan, atau bidang..." oninput="filterCards()">
        </div>
        <button class="filter-chip active" onclick="setFilter(this,'semua')">Semua</button>
        <button class="filter-chip" onclick="setFilter(this,'tech')"><span class="material-icons icon-inline">code</span>Teknologi</button>
        <button class="filter-chip" onclick="setFilter(this,'desain')"><span class="material-icons icon-inline">palette</span>Desain</button>
        <button class="filter-chip" onclick="setFilter(this,'finance')"><span class="material-icons icon-inline">account_balance</span>Keuangan</button>
        <button class="filter-chip" onclick="setFilter(this,'marketing')"><span class="material-icons icon-inline">campaign</span>Marketing</button>
        <button class="filter-chip" onclick="setFilter(this,'consulting')"><span class="material-icons icon-inline">bar_chart</span>Konsultansi</button>
    </div>
    <div class="container">
        <div class="program-grid" id="explore-grid"></div>
        <div id="empty-state" style="display:none;text-align:center;padding:60px 20px;color:var(--text-muted);">
            <div style="font-size:3rem;margin-bottom:16px;"><span class="material-icons icon-large">search</span></div>
            <h3 style="margin-bottom:8px;color:var(--text);">Tidak ada hasil</h3>
            <p>Coba kata kunci lain atau pilih kategori yang berbeda.</p>
        </div>
    </div>
</div>

<!-- ===== TENTANG PAGE ===== -->
<div id="page-tentang" class="page">
    <div class="about-hero">
        <img class="about-hero-img" src="https://images.unsplash.com/photo-1531482615713-2afd69097998?w=1400&q=80&auto=format&fit=crop" alt="Tim bekerja bersama" onerror="this.src='https://images.unsplash.com/photo-1497366216548-37526070297c?w=1400&q=80'">
        <div class="about-hero-overlay"></div>
        <div class="about-hero-content">
            <h2>Tentang KerjaIn</h2>
            <p>Kami percaya setiap mahasiswa Indonesia berhak mendapatkan akses ke pengalaman profesional berkelas dunia — tanpa biaya.</p>
        </div>
    </div>
    <div class="about-content">
        <div class="about-grid">
            <div class="about-text">
                <h3>Mengapa KerjaIn Hadir?</h3>
                <p>Banyak mahasiswa Indonesia menghadapi hambatan besar: persaingan magang yang ketat, kurangnya pengalaman di CV, dan kesenjangan antara ilmu kampus dengan kebutuhan industri nyata.</p>
                <p>KerjaIn hadir untuk menjembatani kesenjangan ini. Kami bekerja sama langsung dengan perusahaan-perusahaan terkemuka untuk menghadirkan simulasi kerja yang autentik dan terstruktur.</p>
                <p>Selesaikan program, dapatkan sertifikat yang diakui industri, dan bangun portofolio yang membuat HR terkesan sejak hari pertama melamar.</p>
            </div>
            <div class="about-image-wrap">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=700&q=80&auto=format&fit=crop" alt="Tim KerjaIn bekerja" onerror="this.src='https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=700&q=80'">
                <div class="about-img-badge"><div><div class="badge-num">30+</div><div class="badge-lbl">Perusahaan<br>Mitra</div></div></div>
            </div>
        </div>
        <div class="team-section">
            <h3>Tim di Balik KerjaIn</h3>
            <p>Sekelompok anak muda yang percaya teknologi bisa mengubah karir jutaan mahasiswa Indonesia.</p>
            <div class="team-grid">
                <div class="team-card"><img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=300&h=200&q=80&auto=format&fit=crop&crop=top" alt="Agil"><div class="team-card-body"><h4>Agil Adiwidya</h4><p>Co-Founder & CEO</p></div></div>
                <div class="team-card"><img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=300&h=200&q=80&auto=format&fit=crop&crop=top" alt="Asti"><div class="team-card-body"><h4>Asti Sofiana</h4><p>Co-Founder & CTO</p></div></div>
                <div class="team-card"><img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=300&h=200&q=80&auto=format&fit=crop&crop=top" alt="Ferdian"><div class="team-card-body"><h4>Ferdian</h4><p>Co-Founder & TI</p></div></div>
            </div>
        </div>
    </div>
</div>

<!-- ===== ROLE PICKER MODAL ===== -->
<div class="modal-overlay" id="role-modal-overlay" onclick="handleRoleOverlayClick(event)">
    <div class="role-modal">
        <button class="role-modal-close" onclick="closeAllModals()"><span class="material-icons">close</span></button>
        <div class="role-modal-logo">KerjaIn</div>
        <h3 id="role-modal-title">Masuk ke KerjaIn</h3>
        <p id="role-modal-sub">Pilih peran Anda untuk melanjutkan</p>
        <div class="role-cards">
            <div class="role-card student" onclick="openAuthModal('pelajar')">
                <span class="material-icons role-icon">school</span>
                <h4>Untuk Mahasiswa</h4>
                <p>Ikuti program simulasi kerja, kembangkan skill, dan raih sertifikat industri</p>
            </div>
            <div class="role-card company" onclick="openAuthModal('mitra')">
                <span class="material-icons role-icon">business</span>
                <h4>Untuk Perusahaan</h4>
                <p>Buat program simulasi, temukan bakat terbaik, dan perkuat employer branding</p>
            </div>
            <div class="role-card mentor" onclick="openAuthModal('mentor')">
                <span class="material-icons role-icon">person</span>
                <h4>Untuk Mentor</h4>
                <p>Berbagi keahlian, bantu mahasiswa berkembang, dan bentuk generasi profesional</p>
            </div>
        </div>
    </div>
</div>

<!-- ===== AUTH FORM MODAL ===== -->
<div class="modal-overlay" id="auth-modal" onclick="handleAuthOverlayClick(event)">
    <div class="modal">
        <button class="modal-close" onclick="closeAllModals()"><span class="material-icons">close</span></button>
        <div class="modal-header-top">
            <button class="modal-back" onclick="backToRolePicker()" title="Kembali">←</button>
            <span class="modal-role-badge" id="role-badge"><span class="material-icons md-18" style="margin-right: 6px;">school</span>Mahasiswa</span>
        </div>
        <div class="modal-logo">KerjaIn</div>
        <h3 id="modal-title">Selamat Datang Kembali</h3>
        <p class="modal-sub" id="modal-sub">Masuk untuk melanjutkan perjalanan karirmu</p>

        <div class="tab-switch">
            <button class="tab-btn active" id="tab-masuk" onclick="switchTab('masuk')">Masuk</button>
            <button class="tab-btn" id="tab-daftar" onclick="switchTab('daftar')">Daftar</button>
        </div>

        <div id="alert-box" class="alert"></div>

        <!-- Panel Masuk -->
        <div class="form-panel active" id="panel-masuk">
            <form id="form-masuk" action="backend/api/login.php" method="POST" onsubmit="handleMasuk(event)" novalidate>
                <div class="form-group">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" placeholder="email@contoh.com" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="login-pass">Password</label>
                    <input type="password" id="login-pass" name="password" placeholder="Masukkan password" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn-auth" id="btn-masuk">Masuk ke Akun →</button>
            </form>
        </div>

        <!-- Panel Daftar -->
        <div class="form-panel" id="panel-daftar">
            <form id="form-daftar" action="backend/api/register.php" method="POST" onsubmit="handleDaftar(event)" novalidate>
                <!-- Field nama usaha khusus mitra -->
                <div id="mitra-extra" style="display:none;">
                    <div class="form-group">
                        <label for="reg-usaha">Nama Usaha / Perusahaan</label>
                        <input type="text" id="reg-usaha" name="nama_usaha" placeholder="PT. Nama Perusahaan Anda" autocomplete="organization">
                    </div>
                </div>
                <div class="form-group">
                    <label for="reg-name" id="name-label">Nama Lengkap</label>
                    <input type="text" id="reg-name" name="nama_lengkap" placeholder="Nama lengkap kamu" required autocomplete="name">
                </div>
                <div class="form-group">
                    <label for="reg-email">Email</label>
                    <input type="email" id="reg-email" name="email" placeholder="email@contoh.com" required autocomplete="email">
                </div>
                <div class="form-group">
                    <label for="reg-pass">Password</label>
                    <input type="password" id="reg-pass" name="password" placeholder="Minimal 8 karakter" required minlength="8" autocomplete="new-password">
                </div>
                <input type="hidden" id="reg-role" name="role" value="pelajar">
                <button type="submit" class="btn-auth" id="btn-daftar">Buat Akun Gratis →</button>
            </form>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <div class="logo-footer">KerjaIn</div>
            <p>Platform simulasi kerja terpercaya untuk mahasiswa Indonesia. Bangun pengalaman nyata, raih karir impian tanpa biaya.</p>
        </div>
        <div class="footer-col">
            <h4>Platform</h4>
            <a onclick="showPage('jelajahi')">Jelajahi Program</a>
            <a onclick="showPage('tentang')">Tentang Kami</a>
            <a onclick="openRoleModal('daftar')">Daftar Gratis</a>
        </div>
        <div class="footer-col">
            <h4>Kontak</h4>
            <a>ayokerjain@gmail.com</a>
            <a>@ayokerjain</a>
            <a>KerjaIn</a>
        </div>
    </div>
    <div class="footer-bottom">© 2026 KerjaIn · Menyinari Jalan Karir Mahasiswa Indonesia</div>
</footer>

<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
