<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajahi Program - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .program-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            margin-top: 24px;
        }
        .program-card {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--dash-border);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .program-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.05);
            border-color: var(--primary);
        }
        .card-body {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .card-bidang {
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .card-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--secondary);
            line-height: 1.4;
        }
        .card-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 20px;
            flex-grow: 1;
        }
        .meta-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 16px 0;
            border-top: 1px dashed var(--dash-border);
            border-bottom: 1px dashed var(--dash-border);
            margin-bottom: 20px;
        }
        .meta-item {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            color: var(--secondary);
            gap: 8px;
        }
        .meta-item .material-icons {
            font-size: 18px;
            color: var(--text-muted);
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 700;
            gap: 6px;
        }
        .status-pending { background: #fef3c7; color: #d97706; }
        .status-approved { background: #d1fae5; color: #059669; }
        .status-rejected { background: #fee2e2; color: #dc2626; }
        .status-closed { background: #f3f4f6; color: #6b7280; }
    </style>
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
        <a class="nav-item" href="/pages/mentor/dashboard">
            <span class="material-icons nav-icon">bar_chart</span> Dashboard
        </a>
        <a class="nav-item active" href="/pages/mentor/explore">
            <span class="material-icons nav-icon">explore</span> Jelajahi Program
        </a>
        <a class="nav-item" href="/pages/mentor/applications">
            <span class="material-icons nav-icon">assignment_turned_in</span> Lamaran Saya
        </a>
        <a class="nav-item" href="/pages/mentor/submissions">
            <span class="material-icons nav-icon">description</span> Review Tugas
        </a>
        <a class="nav-item" href="/pages/mentor/profile">
            <span class="material-icons nav-icon">account_circle</span> Profil Saya
        </a>
    </aside>

    <!-- MAIN -->
    <main class="dash-main">
        <div style="margin-bottom:32px;">
            <h1 style="margin:0 0 8px; font-family:'Sora',sans-serif;">Jelajahi Program Kemitraan</h1>
            <p style="color:var(--text-muted);margin:0;">Daftar sebagai mentor pada program-program dari perusahaan terafiliasi Anda.</p>
        </div>

        <div id="loading-state" class="empty-state">
            <div class="empty-icon"><span class="material-icons rotate icon-large">sync</span></div>
            <h3>Memuat program...</h3>
        </div>

        <div id="no-company-state" class="empty-state" style="display:none;">
            <div class="empty-icon"><span class="material-icons icon-large">business_off</span></div>
            <h3>Belum Terafiliasi</h3>
            <p>Anda belum terafiliasi dengan mitra manapun. Silakan lengkapi profil Anda dengan email domain yang valid.</p>
            <a href="/pages/mentor/profile" class="btn-dash btn-dash-primary" style="margin-top:16px;">Lengkapi Profil</a>
        </div>

        <div id="empty-programs-state" class="empty-state" style="display:none;">
            <div class="empty-icon"><span class="material-icons icon-large">school</span></div>
            <h3>Tidak Ada Program</h3>
            <p>Perusahaan afiliasi Anda belum mempublikasikan program magang/belajar saat ini.</p>
        </div>

        <div class="program-grid" id="program-list" style="display:none;"></div>
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
            loadPrograms();
        })
        .catch(() => window.location.href = '/');
})();

function handleLogout() {
    fetch('/api/logout', { method: 'POST' })
        .then(() => window.location.href = '/')
        .catch(() => window.location.href = '/');
}

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast toast-' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 3500);
}

function loadPrograms() {
    fetch('/api/mentor/explore-programs')
        .then(r => r.json())
        .then(res => {
            document.getElementById('loading-state').style.display = 'none';

            if (res.status === 'error') {
                if (res.message.includes('terafiliasi')) {
                    document.getElementById('no-company-state').style.display = 'flex';
                } else {
                    showToast(res.message, 'danger');
                }
                return;
            }

            const programs = res.data;
            if (programs.length === 0) {
                document.getElementById('empty-programs-state').style.display = 'flex';
                return;
            }

            const container = document.getElementById('program-list');
            container.innerHTML = '';
            container.style.display = 'grid';

            programs.forEach(p => {
                let actionBtn = '';
                
                if (p.application_status) {
                    if (p.application_status === 'pending') {
                        actionBtn = `
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <span class="status-badge status-pending"><span class="material-icons" style="font-size:16px;">hourglass_empty</span>Menunggu</span>
                                <button class="btn-dash btn-dash-outline" onclick="cancelApplication('${p.id}')" style="border-color:#ef4444; color:#ef4444; padding:6px 12px; font-size:0.85rem;">Batal</button>
                            </div>`;
                    } else if (p.application_status === 'disetujui') {
                        actionBtn = `<span class="status-badge status-approved" style="width:100%; justify-content:center;"><span class="material-icons" style="font-size:16px;">check_circle</span>Terdaftar</span>`;
                    } else if (p.application_status === 'ditolak') {
                        actionBtn = `<span class="status-badge status-rejected" style="width:100%; justify-content:center;"><span class="material-icons" style="font-size:16px;">cancel</span>Ditolak Mitra</span>`;
                    }
                } else {
                    if (p.is_registration_open) {
                        actionBtn = `<button class="btn-dash btn-dash-primary" onclick="applyToProgram('${p.id}', this)" style="width:100%;">Daftar sebagai Mentor</button>`;
                    } else {
                        actionBtn = `<button class="btn-dash btn-dash-outline" disabled style="width:100%; opacity:0.6; cursor:not-allowed;">Pendaftaran Ditutup</button>`;
                    }
                }

                const card = document.createElement('div');
                card.className = 'program-card';
                card.innerHTML = `
                    <div class="card-body">
                        <div class="card-bidang">${p.bidang || 'Lainnya'}</div>
                        <h3 class="card-title">${p.judul}</h3>
                        <p class="card-desc">${p.deskripsi || 'Tidak ada deskripsi.'}</p>
                        
                        <div class="meta-info">
                            <div class="meta-item">
                                <span class="material-icons">business</span>
                                <span>${p.perusahaan}</span>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons">event_note</span>
                                <span>Pendaftaran: ${p.registrasi_mulai} - ${p.registrasi_selesai}</span>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons">calendar_today</span>
                                <span>Program: ${p.tanggal_mulai} - ${p.tanggal_selesai}</span>
                            </div>
                            <div class="meta-item">
                                <span class="material-icons">people</span>
                                <span>${p.enrolled} / ${p.kuota || '∞'} Peserta</span>
                            </div>
                        </div>

                        <div class="card-action" style="margin-top:auto;">
                            ${actionBtn}
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });
        })
        .catch(err => {
            document.getElementById('loading-state').style.display = 'none';
            showToast('Gagal memuat data program.', 'danger');
        });
}

function applyToProgram(programId, button) {
    const originalText = button.textContent;
    button.disabled = true;
    button.innerHTML = '<span class="material-icons rotate icon-inline" style="font-size:16px;">sync</span> Mengirim...';

    fetch(`/api/mentor/apply/${programId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                loadPrograms();
            } else {
                showToast(res.message, 'danger');
                button.disabled = false;
                button.textContent = originalText;
            }
        })
        .catch(() => {
            showToast('Gagal mengirim pendaftaran.', 'danger');
            button.disabled = false;
            button.textContent = originalText;
        });
}

function cancelApplication(programId) {
    if (!confirm('Apakah Anda yakin ingin membatalkan lamaran ini?')) return;

    fetch(`/api/mentor/cancel-application/${programId}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast(res.message, 'success');
                loadPrograms();
            } else {
                showToast(res.message, 'danger');
            }
        })
        .catch(() => showToast('Gagal membatalkan lamaran.', 'danger'));
}
</script>
<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
