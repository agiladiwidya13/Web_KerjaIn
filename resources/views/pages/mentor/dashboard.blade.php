<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mentor - KerjaIn</title>
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
        <span class="nav-role-badge" style="background:#d1fae5;color:#065f46;"><span class="material-icons icon-inline">person</span>Mentor</span>
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
            <h3>Menu Mentor</h3>
        </div>
        <a class="nav-item active" href="/pages/mentor/dashboard">
            <span class="nav-icon"><span class="material-icons">bar_chart</span></span> Dashboard
        </a>
        <a class="nav-item" href="/pages/mentor/submissions">
            <span class="nav-icon"><span class="material-icons">description</span></span> Review Tugas
        </a>
        <a class="nav-item" href="/pages/mentor/profile">
            <span class="nav-icon"><span class="material-icons">account_circle</span></span> Profil Saya
        </a>
    </aside>

    <!-- MAIN -->
    <main class="dash-main">

        <div id="section-overview">
            <div class="dash-welcome mentor">
                <h1>Halo, <span id="welcome-nama">...</span>! <span class="material-icons icon-inline">waving_hand</span></h1>
                <p>Kelola review tugas dan bantu peserta berkembang.</p>
                <div id="afiliasi-badge" style="margin-top:12px;"></div>
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">school</span></div>
                    <div class="stat-value" id="stat-programs">0</div>
                    <div class="stat-label">Program Di-handle</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">hourglass_empty</span></div>
                    <div class="stat-value" id="stat-pending">0</div>
                    <div class="stat-label">Menunggu Review</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">check_circle</span></div>
                    <div class="stat-value" id="stat-reviewed">0</div>
                    <div class="stat-label">Total Direview</div>
                </div>
            </div>

            <!-- Program yang di-handle -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <h2><span class="material-icons icon-inline">menu_book</span>Program yang Saya Handle</h2>
                </div>
                <div class="program-list" id="mentor-programs">
                    <div class="empty-state">
                        <div class="empty-icon"><span class="material-icons icon-large">mail_outline</span></div>
                        <h3>Belum ada program</h3>
                        <p>Anda belum di-assign ke program manapun.</p>
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
            if (!d.loggedIn || d.user.role !== 'mentor') {
                window.location.href = '/';
                return;
            }
            document.getElementById('nav-nama').textContent = d.user.nama.split(' ')[0];
            loadDashboard();
        })
        .catch(() => window.location.href = '/');
})();

function handleLogout() {
    fetch('/api/logout', { method: 'POST' })
        .then(() => window.location.href = '/')
        .catch(() => window.location.href = '/');
}

function showSection(name) {
    document.querySelectorAll('[id^="section-"]').forEach(s => s.style.display = 'none');
    document.getElementById('section-' + name).style.display = 'block';
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    event.target.closest('.nav-item').classList.add('active');
}

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast toast-' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 3500);
}

function loadDashboard() {
    fetch('/api/mentor/dashboard')
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success') return;

            document.getElementById('welcome-nama').textContent = d.data.nama.split(' ')[0];
            document.getElementById('stat-programs').textContent = d.data.programs.length;
            document.getElementById('stat-pending').textContent = d.data.pending_review;
            document.getElementById('stat-reviewed').textContent = d.data.total_reviewed;

            // [*] Afiliasi badge
            if (d.data.afiliasi) {
                const logo = d.data.afiliasi.logo_perusahaan
                    ? `<img src="/${d.data.afiliasi.logo_perusahaan}" alt="Logo">`
                    : '<span class="material-icons" style="font-size: 32px; color: var(--text-muted);">business</span>';
                document.getElementById('afiliasi-badge').innerHTML = `
                    <div class="affiliation-badge">
                        ${logo} Terafiliasi dengan ${d.data.afiliasi.nama_usaha}
                    </div>
                `;
            }

            // Program list
            if (d.data.programs.length > 0) {
                document.getElementById('mentor-programs').innerHTML = d.data.programs.map(p => `
                    <div class="program-item">
                        <div class="prog-icon"><span class="material-icons">menu_book</span></div>
                        <div class="prog-info">
                            <h4>${p.judul}</h4>
                            <p>${p.perusahaan} · ${p.bidang || 'Umum'} · ${p.enrollments} peserta</p>
                        </div>
                        <div class="prog-meta">
                            <span class="badge ${p.status === 'published' ? 'badge-success' : 'badge-warning'}">${p.status}</span>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(err => console.error('Dashboard error:', err));
}
</script>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
