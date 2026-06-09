<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Program - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .detail-header {
            background: white;
            border-bottom: 1px solid var(--dash-border);
            padding: 100px 20px 40px;
        }
        .header-content {
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            gap: 32px;
            align-items: flex-start;
        }
        .company-logo-large {
            width: 100px;
            height: 100px;
            border-radius: 16px;
            background: #f8fafc;
            border: 1px solid var(--dash-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            object-fit: cover;
        }
        .header-info {
            flex: 1;
        }
        .header-info h1 {
            font-size: 2rem;
            margin: 0 0 12px;
        }
        .meta-tags {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .meta-tag {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.9rem;
            color: var(--text-muted);
            background: #f1f5f9;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .main-container {
            max-width: 1000px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
            padding: 0 20px;
        }
        .desc-section h2 { margin-bottom: 16px; font-size: 1.3rem; }
        .desc-section p { line-height: 1.7; color: var(--text); margin-bottom: 16px; white-space: pre-line; }
        
        .task-list { margin-top: 32px; }
        .task-item {
            display: flex;
            gap: 16px;
            padding: 16px;
            border: 1px solid var(--dash-border);
            border-radius: 12px;
            margin-bottom: 12px;
            background: white;
        }
        .task-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--dash-accent-light);
            color: var(--dash-accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }
        .sidebar-card {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--dash-border);
            padding: 24px;
            position: sticky;
            top: 100px;
        }
    </style>
</head>
<body style="background: var(--dash-bg);">

<!-- TOAST -->
<div id="toast" class="toast"></div>

<!-- NAVIGATION -->
<nav>
    <div class="logo" onclick="window.location.href='/'">
        <img src="{{ asset('image/logo-kerjain.png') }}" alt="Logo KerjaIn" class="logo-img" onerror="this.style.display='none'">
        KerjaIn
    </div>
    <div class="nav-auth" id="nav-auth"></div>
</nav>

<div id="prog-cover-container" style="display:none; max-width:1000px; margin: 90px auto 0; padding: 0 20px;">
    <div style="width:100%; height:260px; border-radius:20px; overflow:hidden; box-shadow: var(--dash-shadow);">
        <img id="prog-cover-img" src="" style="width:100%; height:100%; object-fit:cover;">
    </div>
</div>

<div class="detail-header" id="detail-header-el" style="padding-top:100px;">
    <div class="header-content">
        <div class="company-logo-large" id="comp-logo"><span class="material-icons" style="font-size:3rem;">business</span></div>
        <div class="header-info">
            <h1 id="prog-judul">Memuat...</h1>
            <div class="meta-tags">
                <div class="meta-tag"><span class="material-icons icon-inline">business</span><span id="prog-perusahaan">-</span></div>
                <div class="meta-tag"><span class="material-icons icon-inline">work</span><span id="prog-bidang">-</span></div>
                <div class="meta-tag"><span class="material-icons icon-inline">group</span><span id="prog-peserta">0</span> Peserta</div>
            </div>
            <div id="enroll-action-header"></div>
        </div>
    </div>
</div>

<div class="main-container">
    <div class="main-content">
        <div class="desc-section">
            <h2>Tentang Program</h2>
            <p id="prog-deskripsi">Memuat deskripsi...</p>
        </div>

        <div class="task-list">
            <h2>Task / Tugas yang Harus Diselesaikan</h2>
            <div id="tasks-container">
                <p>Memuat task...</p>
            </div>
        </div>
    </div>

    <aside class="sidebar">
        <div class="sidebar-card">
            <h3 style="margin: 0 0 16px; font-size:1.1rem;">Informasi Pendaftaran</h3>
            
            <div style="margin-bottom: 24px;">
                <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:4px;">Batas Pendaftaran</div>
                <div style="font-weight:600;" id="prog-deadline">-</div>
            </div>
            
            <div style="margin-bottom: 24px;">
                <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:4px;">Mentors</div>
                <div id="mentors-container"></div>
            </div>

            <div id="enroll-action-sidebar"></div>
        </div>
    </aside>
</div>

<script>
const programId = "{{ $programId }}";
let currentUser = null;

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast toast-' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 3500);
}

fetch('/api/session')
    .then(r => r.json())
    .then(d => {
        if (d.loggedIn) {
            currentUser = d.user;
            const profileUrl = { pelajar: '/pages/pelajar/dashboard', mentor: '/pages/mentor/dashboard', mitra: '/pages/mitra/dashboard' };
            document.getElementById('nav-auth').innerHTML = `
                <a href="${profileUrl[d.user.role]}" class="btn-outline" style="font-weight:600;"><span class="material-icons icon-inline">bar_chart</span>Dashboard</a>
                <a href="#" onclick="fetch('/api/logout',{method:'POST'}).then(()=>window.location.reload())" class="btn-solid" style="background:#ef4444;">Keluar</a>
            `;
        } else {
            document.getElementById('nav-auth').innerHTML = `<a href="/?tab=masuk" class="btn-solid">Masuk untuk Mendaftar</a>`;
        }
        loadDetail();
    });

function loadDetail() {
    fetch('/api/programs/' + programId)
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success') {
                alert('Program tidak ditemukan');
                window.location.href = '/pages/programs';
                return;
            }
            const p = d.data;
            document.getElementById('prog-judul').textContent = p.judul;
            document.getElementById('prog-perusahaan').textContent = p.perusahaan;
            document.getElementById('prog-bidang').textContent = p.bidang || 'Umum';
            document.getElementById('prog-peserta').textContent = p.enrolled + (p.kuota ? ` / ${p.kuota}` : '');
            document.getElementById('prog-deskripsi').textContent = p.deskripsi || 'Tidak ada deskripsi.';
            document.getElementById('prog-deadline').textContent = p.tanggal_mulai || 'Kapan saja';

            if (p.logo) document.getElementById('comp-logo').innerHTML = `<img src="/${p.logo}" style="width:100%;height:100%;object-fit:cover;border-radius:16px;">`;

            if (p.cover_image) {
                document.getElementById('prog-cover-img').src = p.cover_image;
                document.getElementById('prog-cover-container').style.display = 'block';
                document.getElementById('detail-header-el').style.paddingTop = '30px';
            }

            // Tasks
            if (p.tasks.length > 0) {
                document.getElementById('tasks-container').innerHTML = p.tasks.map((t, i) => `
                    <div class="task-item">
                        <div class="task-number">${i+1}</div>
                        <div>
                            <h4 style="margin:0 0 4px;font-size:1rem;">${t.judul}</h4>
                        </div>
                    </div>
                `).join('');
            } else {
                document.getElementById('tasks-container').innerHTML = '<p class="empty-state">Belum ada task.</p>';
            }

            // Mentors
            if (p.mentors.length > 0) {
                document.getElementById('mentors-container').innerHTML = p.mentors.map(m => `
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                        <div style="width:24px;height:24px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;font-size:0.7rem;"><span class="material-icons" style="font-size:0.9rem;">person</span></div>
                        <div>
                            <div style="font-size:0.9rem;font-weight:600;">${m.nama}</div>
                            <div style="font-size:0.75rem;color:var(--text-muted);">${m.profesi || 'Mentor'}</div>
                        </div>
                    </div>
                `).join('');
            } else {
                document.getElementById('mentors-container').innerHTML = '<span style="color:var(--text-muted);font-size:0.9rem;">Belum ada mentor (Auto-review)</span>';
            }

            // Enroll Action Logic
            let btnHtml = '';
            if (!currentUser) {
                btnHtml = `<button class="btn-dash btn-dash-primary" style="width:100%;justify-content:center;" onclick="window.location.href='/?tab=masuk'">Masuk untuk Mendaftar</button>`;
            } else if (currentUser.role !== 'pelajar') {
                btnHtml = `<div class="badge badge-warning" style="display:block;text-align:center;">Hanya Pelajar yang dapat mendaftar</div>`;
            } else if (p.already_enrolled) {
                btnHtml = `<button class="btn-dash btn-dash-success" style="width:100%;justify-content:center;" onclick="window.location.href='/pages/pelajar/dashboard'">Lihat Progress (Dashboard)</button>`;
            } else if (p.is_full) {
                btnHtml = `<button class="btn-dash btn-dash-danger" style="width:100%;justify-content:center;" disabled>Kuota Penuh</button>`;
            } else {
                btnHtml = `<button class="btn-dash btn-dash-primary" style="width:100%;justify-content:center;" onclick="enroll()">Daftar Program Sekarang</button>`;
            }

            document.getElementById('enroll-action-header').innerHTML = btnHtml;
            document.getElementById('enroll-action-sidebar').innerHTML = btnHtml;
        });
}

function enroll() {
    fetch(`/api/pelajar/enroll/${programId}`, { method: 'POST' })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') {
                showToast('Berhasil mendaftar program!');
                setTimeout(() => window.location.href = '/pages/pelajar/dashboard', 1500);
            } else {
                showToast(d.message, 'error');
            }
        })
        .catch(() => showToast('Gagal terhubung ke server', 'error'));
}
</script>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
