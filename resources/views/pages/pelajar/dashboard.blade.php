<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
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

<!-- TOAST -->
<div id="toast" class="toast"></div>

<!-- DASHBOARD -->
<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="dash-sidebar">
        <div class="sidebar-header">
            <h3>Menu Pelajar</h3>
        </div>
        <a class="nav-item active" id="nav-item-overview" onclick="showSection('overview')">
            <span class="material-icons nav-icon">bar_chart</span> Dashboard
        </a>
        <a class="nav-item" id="nav-item-programs" onclick="showSection('programs')">
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
        <a class="nav-item" href="/pages/pelajar/profile">
            <span class="material-icons nav-icon">account_circle</span> Profil Saya
        </a>
    </aside>

    <!-- MAIN -->
    <main class="dash-main">

        <!-- OVERVIEW -->
        <div id="section-overview">
            <div class="dash-welcome">
                <h1>Selamat datang, <span id="welcome-nama">...</span>! <span class="material-icons icon-inline">waving_hand</span></h1>
                <p>Terus kembangkan skill-mu dan selesaikan program untuk meraih sertifikat.</p>
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">school</span></div>
                    <div class="stat-value" id="stat-active">0</div>
                    <div class="stat-label">Program Aktif</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">check_circle</span></div>
                    <div class="stat-value" id="stat-completed">0</div>
                    <div class="stat-label">Selesai</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">star</span></div>
                    <div class="stat-value" id="stat-poin">0</div>
                    <div class="stat-label">Total Poin</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">emoji_events</span></div>
                    <div class="stat-value" id="stat-certs">0</div>
                    <div class="stat-label">Sertifikat</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">folder</span></div>
                    <div class="stat-value" id="stat-portfolios">0</div>
                    <div class="stat-label">Portofolio</div>
                </div>
            </div>

            <!-- Program Aktif -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <h2><span class="material-icons icon-inline">menu_book</span>Program Aktif</h2>
                    <a href="/pages/programs" class="btn-dash btn-dash-outline">Jelajahi →</a>
                </div>
                <div class="program-list" id="active-programs">
                    <div class="empty-state">
                        <div class="empty-icon"><span class="material-icons icon-large">mail_outline</span></div>
                        <h3>Belum ada program</h3>
                        <p>Mulai jelajahi dan daftar program untuk membangun portofoliomu!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- PROGRAMS SECTION -->
        <div id="section-programs" style="display:none;">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h2><span class="material-icons icon-inline">school</span>Semua Program Saya</h2>
                </div>
                <div class="program-list" id="all-programs">
                    <div class="empty-state">
                        <div class="empty-icon"><span class="material-icons icon-large">mail_outline</span></div>
                        <h3>Belum ada enrollment</h3>
                        <p>Jelajahi program dan mulai mendaftar!</p>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<script>
// ── Auth Guard ──────────────────────────────────────────────
(function() {
    fetch('/api/session')
        .then(r => r.json())
        .then(d => {
            if (!d.loggedIn || d.user.role !== 'pelajar') {
                window.location.href = '/';
                return;
            }
            document.getElementById('nav-nama').textContent = d.user.nama.split(' ')[0];
            loadDashboard();
            setInterval(loadDashboard, 30000);
            
            // Check query param for section
            const urlParams = new URLSearchParams(window.location.search);
            const section = urlParams.get('section');
            if (section === 'programs') {
                showSection('programs');
            }
        })
        .catch(() => window.location.href = '/');
})();

function handleLogout() {
    fetch('/api/logout', { method: 'POST' })
        .then(() => window.location.href = '/')
        .catch(() => window.location.href = '/');
}

// ── Section Toggle ──────────────────────────────────────────
function showSection(name) {
    document.querySelectorAll('[id^="section-"]').forEach(s => s.style.display = 'none');
    document.getElementById('section-' + name).style.display = 'block';
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    
    const navItem = document.getElementById('nav-item-' + name) || (window.event ? event.target.closest('.nav-item') : null);
    if (navItem) {
        navItem.classList.add('active');
    }
}

// ── Toast ───────────────────────────────────────────────────
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast toast-' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 3500);
}

// ── Load Dashboard Data ─────────────────────────────────────
function loadDashboard() {
    fetch('/api/pelajar/dashboard', { credentials: 'same-origin' })
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success') {
                console.error('Pelajar dashboard response error', d);
                showToast('Gagal memuat data dashboard pelajar.', 'error');
                return;
            }

            const namaLengkap = d.data.nama || d.data.nama_lengkap || 'Teman';
            document.getElementById('welcome-nama').textContent = (namaLengkap.split ? namaLengkap.split(' ')[0] : namaLengkap) || 'Teman';
            document.getElementById('stat-poin').textContent = d.data.total_poin ?? 0;
            document.getElementById('stat-completed').textContent = d.data.completed ?? 0;
            document.getElementById('stat-active').textContent = (d.data.enrollments || []).length;
            document.getElementById('stat-certs').textContent = d.data.certificate_count ?? 0;
            document.getElementById('stat-portfolios').textContent = d.data.portfolio_count ?? 0;

            if (Array.isArray(d.data.enrollments) && d.data.enrollments.length > 0) {
                document.getElementById('active-programs').innerHTML = d.data.enrollments.map(e => `
                    <div class="program-item" onclick="window.location.href='/pages/pelajar/enrollments/${e.id}'">
                        <div class="prog-icon"><span class="material-icons">menu_book</span></div>
                        <div class="prog-info">
                            <h4>${e.program || 'Program tidak diketahui'}</h4>
                            <p>${e.perusahaan || 'Perusahaan tidak diketahui'} · ${e.bidang || 'Umum'}</p>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill" style="width:${e.progress ?? 0}%"></div>
                            </div>
                            <div class="progress-label">${e.progress ?? 0}% selesai · ${e.total_tasks ?? 0} task</div>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(err => {
            console.error('Dashboard error:', err);
            showToast('Terjadi kesalahan saat memuat dashboard pelajar.', 'error');
        });

    // Load all enrollments
    fetch('/api/pelajar/enrollments', { credentials: 'same-origin' })
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success' || !d.data.length) return;

            document.getElementById('all-programs').innerHTML = d.data.map(e => `
                <div class="program-item" onclick="window.location.href='/pages/pelajar/enrollments/${e.id}'">
                    <div class="prog-icon">${e.status === 'selesai' ? '<span class="material-icons">check_circle</span>' : '<span class="material-icons">menu_book</span>'}</div>
                    <div class="prog-info">
                        <h4>${e.judul || 'Program tidak diketahui'}</h4>
                        <p>${e.perusahaan || 'Perusahaan tidak diketahui'} · ${e.bidang || 'Umum'}</p>
                        <div class="progress-bar-wrap">
                            <div class="progress-bar-fill" style="width:${e.progress ?? 0}%"></div>
                        </div>
                        <div class="progress-label">${e.progress ?? 0}% · ${e.total_tasks ?? 0} task</div>
                    </div>
                    <div class="prog-meta">
                        <span class="badge ${e.status === 'selesai' ? 'badge-success' : 'badge-primary'}">${e.status || 'unknown'}</span>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => console.error('Enrollments load error:', err));
}
</script>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
