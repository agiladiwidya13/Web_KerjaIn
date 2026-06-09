<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mitra - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

<!-- TOAST -->
<div id="toast" class="toast"></div>

<!-- DASHBOARD -->
<div class="dashboard-container">

    <!-- SIDEBAR -->
    <aside class="dash-sidebar">
        <div class="sidebar-header">
            <h3>Menu Mitra</h3>
        </div>
        <a class="nav-item active" id="nav-item-overview" onclick="showSection('overview')">
            <span class="nav-icon"><span class="material-icons">bar_chart</span></span> Dashboard
        </a>
        <a class="nav-item" id="nav-item-programs" onclick="showSection('programs')">
            <span class="nav-icon"><span class="material-icons">menu_book</span></span> Kelola Program
        </a>
        <a class="nav-item" id="nav-item-mentors" onclick="showSection('mentors')">
            <span class="nav-icon"><span class="material-icons">person</span></span> Mentor Saya
        </a>
        <a class="nav-item" href="/pages/mitra/candidates">
            <span class="nav-icon"><span class="material-icons">search</span></span> Cari Kandidat
        </a>
        <a class="nav-item" href="/pages/mitra/profile">
            <span class="nav-icon"><span class="material-icons">account_circle</span></span> Profil Perusahaan
        </a>
    </aside>

    <!-- MAIN -->
    <main class="dash-main">

        <!-- OVERVIEW -->
        <div id="section-overview">
            <div class="dash-welcome mitra">
                <h1>Halo, <span id="welcome-nama">...</span>! <span class="material-icons icon-inline">business</span></h1>
                <p>Kelola program dan temukan talenta terbaik untuk perusahaan Anda.</p>
                <div style="margin-top:8px;opacity:0.9;font-size:0.9rem;">
                    Domain: <strong>@<span id="welcome-domain">...</span></strong>
                </div>
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">school</span></div>
                    <div class="stat-value" id="stat-programs">0</div>
                    <div class="stat-label">Total Program</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">person</span></div>
                    <div class="stat-value" id="stat-mentors">0</div>
                    <div class="stat-label">Mentor Terafiliasi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><span class="material-icons">new_releases</span></div>
                    <div class="stat-value" id="stat-applicants">0</div>
                    <div class="stat-label">Pelamar Baru (7 hari)</div>
                </div>
            </div>

            <!-- CHART SECTION -->
            <div class="dash-card" id="chart-section">
                <div class="dash-card-header">
                    <h2><span class="material-icons icon-inline">insights</span>Laporan & Statistik</h2>
                </div>
                <!-- Bar Chart: Full Width -->
                <div style="margin-bottom:24px;">
                    <h3 style="font-size:0.95rem;font-weight:600;margin-bottom:12px;">Peserta per Program</h3>
                    <div style="position:relative;height:280px;">
                        <canvas id="chartPeserta"></canvas>
                    </div>
                </div>
                <!-- Line + Doughnut: Side by Side -->
                <div style="display:grid;grid-template-columns:3fr 2fr;gap:24px;">
                    <div>
                        <h3 style="font-size:0.95rem;font-weight:600;margin-bottom:12px;">Tren Pendaftaran (6 Bulan)</h3>
                        <div style="position:relative;height:250px;">
                            <canvas id="chartTren"></canvas>
                        </div>
                    </div>
                    <div>
                        <h3 style="font-size:0.95rem;font-weight:600;margin-bottom:12px;">Status Program</h3>
                        <div style="position:relative;height:250px;">
                            <canvas id="chartStatus"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Programs Quick View -->
            <div class="dash-card">
                <div class="dash-card-header">
                    <h2><span class="material-icons icon-inline">menu_book</span>Program Terbaru</h2>
                    <button class="btn-dash btn-dash-primary" onclick="showSection('programs')">Lihat Semua →</button>
                </div>
                <div class="program-list" id="overview-programs">
                    <div class="empty-state">
                        <div class="empty-icon"><span class="material-icons" style="font-size: 48px; color: var(--text-muted);">mail_outline</span></div>
                        <h3>Belum ada program</h3>
                        <p>Buat program pertama Anda untuk mulai menemukan talenta!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- PROGRAMS SECTION -->
        <div id="section-programs" style="display:none;">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h2><span class="material-icons icon-inline">school</span>Kelola Program</h2>
                    <button class="btn-dash btn-dash-primary" onclick="openCreateProgram()">+ Buat Program</button>
                </div>
                <div class="program-list" id="all-programs">
                    <div class="empty-state">
                        <div class="empty-icon"><span class="material-icons" style="font-size: 48px; color: var(--text-muted);">mail_outline</span></div>
                        <h3>Belum ada program</h3>
                        <p>Klik "Buat Program" untuk memulai.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- MENTORS SECTION -->
        <div id="section-mentors" style="display:none;">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h2><span class="material-icons icon-inline">person</span>Mentor Terafiliasi</h2>
                    <span class="badge badge-info" id="mentor-domain-badge">@domain</span>
                </div>
                <p style="color:var(--text-muted);margin-bottom:20px;font-size:0.9rem;">
                    Mentor yang mendaftar dengan email domain perusahaan Anda akan otomatis muncul di sini.
                </p>
                <div class="program-list" id="mentor-list">
                    <div class="empty-state">
                        <div class="empty-icon"><span class="material-icons" style="font-size: 48px; color: var(--text-muted);">person_outline</span></div>
                        <h3>Belum ada mentor</h3>
                        <p>Ajak mentor untuk mendaftar dengan email @<span class="mentor-domain-display"></span></p>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<!-- CREATE PROGRAM MODAL -->
<div class="dash-modal-overlay" id="create-modal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="dash-modal">
        <h3><span class="material-icons" style="margin-right: 8px; display: inline-flex; vertical-align: middle;">menu_book</span>Buat Program Baru</h3>
        <div class="form-group">
            <label>Judul Program *</label>
            <input type="text" id="prog-judul" placeholder="cth: Frontend Developer Simulation">
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea id="prog-deskripsi" rows="3" placeholder="Jelaskan program ini..."></textarea>
        </div>
        <div class="form-group">
            <label>Bidang</label>
            <input type="text" id="prog-bidang" placeholder="cth: UI/UX, Backend, Marketing">
        </div>
        <div class="form-group">
            <label>Kuota (kosongkan jika tidak terbatas)</label>
            <input type="number" id="prog-kuota" min="1" placeholder="cth: 30">
        </div>
        <div class="form-group">
            <label>Tanggal Mulai</label>
            <input type="date" id="prog-mulai">
        </div>
        <div class="form-group">
            <label>Tanggal Selesai</label>
            <input type="date" id="prog-selesai">
        </div>
        <div class="modal-actions">
            <button class="btn-dash btn-dash-outline" onclick="document.getElementById('create-modal').classList.remove('show')">Batal</button>
            <button class="btn-dash btn-dash-primary" onclick="createProgram()">Buat Program</button>
        </div>
    </div>
</div>

<script>
// ── Auth Guard ──────────────────────────────────────────────
(function() {
    fetch('/api/session')
        .then(r => r.json())
        .then(d => {
            if (!d.loggedIn || d.user.role !== 'mitra') {
                window.location.href = '/';
                return;
            }
            document.getElementById('nav-nama').textContent = d.user.nama.split(' ')[0];
            loadDashboard();
            loadMentors();
            
            // Check query param for section
            const urlParams = new URLSearchParams(window.location.search);
            const section = urlParams.get('section');
            if (section === 'programs' || section === 'mentors') {
                showSection(section);
            }
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
    document.querySelectorAll('.dash-sidebar .nav-item').forEach(n => n.classList.remove('active'));
    
    const navItem = document.getElementById('nav-item-' + name) || (window.event && event.target ? event.target.closest('.nav-item') : null);
    if (navItem) {
        navItem.classList.add('active');
    }
}

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast toast-' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 3500);
}

function loadDashboard() {
    fetch('/api/mitra/dashboard')
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success') return;

            document.getElementById('welcome-nama').textContent = d.data.nama_usaha || d.data.nama;
            document.getElementById('welcome-domain').textContent = d.data.email_domain;
            document.getElementById('stat-programs').textContent = d.data.total_programs;
            document.getElementById('stat-mentors').textContent = d.data.total_mentors;
            document.getElementById('stat-applicants').textContent = d.data.new_applicants;
            document.getElementById('mentor-domain-badge').textContent = '@' + d.data.email_domain;
            document.querySelectorAll('.mentor-domain-display').forEach(e => e.textContent = d.data.email_domain);

            renderPrograms(d.data.programs, 'overview-programs', 3);
            renderPrograms(d.data.programs, 'all-programs');
            loadCharts();
        });
}

function renderPrograms(programs, containerId, limit) {
    const data = limit ? programs.slice(0, limit) : programs;
    if (!data.length) return;

    document.getElementById(containerId).innerHTML = data.map(p => `
        <div class="program-item" onclick="window.location.href='/pages/mitra/programs/${p.id}'">
            <div class="prog-icon"><span class="material-icons">menu_book</span></div>
            <div class="prog-info">
                <h4>${p.judul}</h4>
                <p>${p.bidang || 'Umum'} · ${p.enrolled} peserta${p.kuota ? ' / ' + p.kuota + ' kuota' : ''}</p>
            </div>
            <div class="prog-meta">
                <span class="badge ${p.status === 'published' ? 'badge-success' : p.status === 'closed' ? 'badge-danger' : 'badge-warning'}">${p.status}</span>
            </div>
        </div>
    `).join('');
}

function loadMentors() {
    fetch('/api/mitra/mentors')
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success' || !d.data.length) return;

            document.getElementById('mentor-list').innerHTML = d.data.map(m => `
                <div class="program-item">
                    <div class="prog-icon"><span class="material-icons">person</span></div>
                    <div class="prog-info">
                        <h4>${m.nama}</h4>
                        <p>${m.profesi || 'Belum diisi'} · ${m.tahun_pengalaman} tahun pengalaman</p>
                        <p style="font-size:0.8rem;color:var(--text-muted);">${m.email}</p>
                    </div>
                </div>
            `).join('');
        });
}

// ── Create Program ──────────────────────────────────────────
function openCreateProgram() {
    document.getElementById('create-modal').classList.add('show');
}

function createProgram() {
    const judul = document.getElementById('prog-judul').value.trim();
    if (!judul) { showToast('Judul program wajib diisi!', 'error'); return; }

    const formData = new FormData();
    formData.append('judul', judul);
    formData.append('deskripsi', document.getElementById('prog-deskripsi').value);
    formData.append('bidang', document.getElementById('prog-bidang').value);
    const kuota = document.getElementById('prog-kuota').value;
    if (kuota) formData.append('kuota', kuota);
    const mulai = document.getElementById('prog-mulai').value;
    if (mulai) formData.append('tanggal_mulai', mulai);
    const selesai = document.getElementById('prog-selesai').value;
    if (selesai) formData.append('tanggal_selesai', selesai);

    fetch('/api/mitra/programs', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') {
                showToast('Program berhasil dibuat!');
                document.getElementById('create-modal').classList.remove('show');
                loadDashboard();
                showSection('programs');
            } else {
                showToast(d.message || 'Gagal membuat program', 'error');
            }
        })
        .catch(() => showToast('Gagal terhubung ke server', 'error'));
}
// ── Chart.js Integration ───────────────────────────────────────
let chartInstances = [];

function getChartColors() {
    const isDark = document.body.classList.contains('dark-mode');
    return {
        textColor: isDark ? '#f8fafc' : '#334155',
        gridColor: isDark ? 'rgba(148, 163, 184, 0.15)' : 'rgba(0, 0, 0, 0.06)',
        barColors: ['#6366f1','#f59e0b','#10b981','#ef4444','#3b82f6','#8b5cf6','#ec4899','#14b8a6'],
        lineColor: '#6366f1',
        lineFill: isDark ? 'rgba(99, 102, 241, 0.15)' : 'rgba(99, 102, 241, 0.1)',
        doughnutColors: {
            'Draft': '#f59e0b',
            'Published': '#10b981',
            'Closed': '#ef4444',
        },
    };
}

function loadCharts() {
    fetch('/api/mitra/dashboard-charts')
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success') return;
            renderAllCharts(d.data);
        })
        .catch(err => console.error('Chart load error:', err));
}

function renderAllCharts(data) {
    // Destroy existing chart instances
    chartInstances.forEach(c => c.destroy());
    chartInstances = [];

    const colors = getChartColors();
    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: colors.textColor, font: { family: 'Plus Jakarta Sans' } } },
        },
    };
    const scaleDefaults = {
        x: { ticks: { color: colors.textColor }, grid: { color: colors.gridColor } },
        y: { ticks: { color: colors.textColor, beginAtZero: true, precision: 0 }, grid: { color: colors.gridColor } },
    };

    // 1. Bar Chart — Peserta per Program
    const barCtx = document.getElementById('chartPeserta');
    if (barCtx && data.per_program.length) {
        chartInstances.push(new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: data.per_program.map(i => i.label),
                datasets: [{
                    label: 'Jumlah Peserta',
                    data: data.per_program.map(i => i.value),
                    backgroundColor: data.per_program.map((_, idx) => colors.barColors[idx % colors.barColors.length]),
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                ...defaultOptions,
                plugins: { ...defaultOptions.plugins, legend: { display: false } },
                scales: scaleDefaults,
            }
        }));
    } else if (barCtx) {
        barCtx.parentElement.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--text-muted);font-size:0.9rem;">Belum ada data program</div>';
    }

    // 2. Line Chart — Tren Pendaftaran
    const lineCtx = document.getElementById('chartTren');
    if (lineCtx) {
        chartInstances.push(new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: data.monthly_trend.map(i => i.label),
                datasets: [{
                    label: 'Pendaftaran',
                    data: data.monthly_trend.map(i => i.value),
                    borderColor: colors.lineColor,
                    backgroundColor: colors.lineFill,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: colors.lineColor,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                ...defaultOptions,
                plugins: { ...defaultOptions.plugins, legend: { display: false } },
                scales: scaleDefaults,
            }
        }));
    }

    // 3. Doughnut Chart — Status Program
    const doughCtx = document.getElementById('chartStatus');
    if (doughCtx && data.status_dist.length) {
        chartInstances.push(new Chart(doughCtx, {
            type: 'doughnut',
            data: {
                labels: data.status_dist.map(i => i.label),
                datasets: [{
                    data: data.status_dist.map(i => i.value),
                    backgroundColor: data.status_dist.map(i => colors.doughnutColors[i.label] || '#6366f1'),
                    borderWidth: 2,
                    borderColor: document.body.classList.contains('dark-mode') ? '#1e293b' : '#ffffff',
                }]
            },
            options: {
                ...defaultOptions,
                cutout: '60%',
                plugins: {
                    ...defaultOptions.plugins,
                    legend: {
                        position: 'bottom',
                        labels: { color: colors.textColor, font: { family: 'Plus Jakarta Sans' }, padding: 16, usePointStyle: true },
                    },
                },
            }
        }));
    } else if (doughCtx) {
        doughCtx.parentElement.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--text-muted);font-size:0.9rem;">Belum ada data</div>';
    }
}

// Re-render charts when dark mode toggles
const observer = new MutationObserver(() => {
    if (chartInstances.length) loadCharts();
});
observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
</script>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
