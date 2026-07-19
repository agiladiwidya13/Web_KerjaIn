<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lamaran Mentor Saya - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .app-table-card {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--dash-border);
            overflow: hidden;
            margin-top: 24px;
        }
        .app-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        .app-table th, .app-table td {
            padding: 16px 24px;
            border-bottom: 1px solid var(--dash-border);
            font-size: 0.9rem;
        }
        .app-table th {
            background: var(--dash-bg);
            font-weight: 700;
            color: var(--secondary);
            font-family: 'Sora', sans-serif;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        .app-table tr:last-child td {
            border-bottom: none;
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
        .status-disetujui { background: #d1fae5; color: #059669; }
        .status-ditolak { background: #fee2e2; color: #dc2626; }

        .app-program-title {
            font-weight: 600;
            color: #0f172a;
        }
        .app-company-name {
            color: #475569;
        }

        body.dark-mode .app-table-card {
            background: #111827;
            border-color: #334155;
        }
        body.dark-mode .app-table th,
        body.dark-mode .app-table td {
            border-color: #334155;
            color: #e2e8f0;
        }
        body.dark-mode .app-program-title,
        body.dark-mode .app-company-name {
            color: #f8fafc;
        }
        body.dark-mode .app-table th {
            background: #0f172a;
            color: #a5b4fc;
        }
        body.dark-mode .app-table td {
            background: transparent;
        }
        body.dark-mode .app-table tr:hover td {
            background: rgba(255,255,255,0.04);
        }
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
        <a class="nav-item" href="/pages/mentor/explore">
            <span class="material-icons nav-icon">explore</span> Jelajahi Program
        </a>
        <a class="nav-item active" href="/pages/mentor/applications">
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
            <h1 style="margin:0 0 8px; font-family:'Sora',sans-serif;">Riwayat Lamaran Mentor</h1>
            <p style="color:var(--text-muted);margin:0;">Lacak status keanggotaan/mentor Anda pada program-program KerjaIn.</p>
        </div>

        <div id="loading-state" class="empty-state">
            <div class="empty-icon"><span class="material-icons rotate icon-large">sync</span></div>
            <h3>Memuat riwayat lamaran...</h3>
        </div>

        <div id="empty-state" class="empty-state" style="display:none;">
            <div class="empty-icon"><span class="material-icons icon-large">assignment</span></div>
            <h3>Belum Ada Lamaran</h3>
            <p>Anda belum melamar sebagai mentor pada program manapun saat ini.</p>
            <a href="/pages/mentor/explore" class="btn-dash btn-dash-primary" style="margin-top:16px;">Jelajahi Program</a>
        </div>

        <div class="app-table-card" id="table-container" style="display:none;">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Program</th>
                        <th>Perusahaan</th>
                        <th>Tanggal Melamar</th>
                        <th>Tanggal Direview</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="app-list"></tbody>
            </table>
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
            loadApplications();
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

function loadApplications() {
    fetch('/api/mentor/my-applications')
        .then(r => r.json())
        .then(res => {
            document.getElementById('loading-state').style.display = 'none';

            if (res.status !== 'success') {
                showToast(res.message || 'Gagal memuat riwayat.', 'danger');
                return;
            }

            const list = res.data;
            if (list.length === 0) {
                document.getElementById('empty-state').style.display = 'flex';
                return;
            }

            const tbody = document.getElementById('app-list');
            tbody.innerHTML = '';
            document.getElementById('table-container').style.display = 'block';

            list.forEach(app => {
                let statusLabel = '';
                let statusIcon = '';
                if (app.status === 'pending') {
                    statusLabel = 'Menunggu';
                    statusIcon = 'hourglass_empty';
                } else if (app.status === 'disetujui') {
                    statusLabel = 'Disetujui';
                    statusIcon = 'check_circle';
                } else if (app.status === 'ditolak') {
                    statusLabel = 'Ditolak';
                    statusIcon = 'cancel';
                }

                let actionCol = '';
                if (app.status === 'pending') {
                    actionCol = `<button class="btn-dash btn-dash-outline" onclick="cancelApplication('${app.id}')" style="border-color:#ef4444; color:#ef4444; padding:6px 12px; font-size:0.8rem;">Batalkan</button>`;
                } else {
                    actionCol = `<span style="color:var(--text-muted); font-size:0.85rem;">Selesai Diproses</span>`;
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <div class="app-program-title">${app.judul}</div>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">Bidang: ${app.bidang || '-'}</div>
                    </td>
                    <td class="app-company-name" style="font-weight:600;">${app.perusahaan}</td>
                    <td>${app.applied_at || '-'}</td>
                    <td>${app.reviewed_at || '-'}</td>
                    <td>
                        <span class="status-badge status-${app.status}">
                            <span class="material-icons" style="font-size:14px;">${statusIcon}</span>
                            ${statusLabel}
                        </span>
                    </td>
                    <td>${actionCol}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(err => {
            document.getElementById('loading-state').style.display = 'none';
            showToast('Gagal memuat data lamaran.', 'danger');
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
                loadApplications();
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
