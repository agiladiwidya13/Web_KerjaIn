<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lamaran Mentor - KerjaIn</title>
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
        <span class="nav-role-badge" style="background:#fef3c7;color:#92400e;"><span class="material-icons icon-inline">business</span>Mitra</span>
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

    @include('partials.mitra-sidebar')

    <!-- MAIN -->
    <main class="dash-main">
        <div style="margin-bottom:32px; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h1 style="margin:0 0 8px; font-family:'Sora',sans-serif;">Lamaran Mentor</h1>
                <p style="color:var(--text-muted);margin:0;">Tinjau dan seleksi mentor yang ingin bergabung pada program magang/belajar Anda.</p>
            </div>
            
            <div style="display:flex; gap:8px;">
                <select id="filter-status" onchange="loadApplications()" class="form-control" style="background:white; border:1px solid var(--dash-border); border-radius:8px; padding:8px 16px; font-size:0.9rem;">
                    <option value="">Semua Status</option>
                    <option value="pending" selected>Menunggu Persetujuan</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>

        <div id="loading-state" class="empty-state">
            <div class="empty-icon"><span class="material-icons rotate icon-large">sync</span></div>
            <h3>Memuat lamaran...</h3>
        </div>

        <div id="empty-state" class="empty-state" style="display:none;">
            <div class="empty-icon"><span class="material-icons icon-large">mail_outline</span></div>
            <h3>Tidak Ada Lamaran</h3>
            <p>Tidak ada lamaran mentor yang sesuai dengan filter saat ini.</p>
        </div>

        <div class="app-table-card" id="table-container" style="display:none;">
            <table class="app-table">
                <thead>
                    <tr>
                        <th>Mentor</th>
                        <th>Program Tujuan</th>
                        <th>Tanggal Melamar</th>
                        <th>Status</th>
                        <th style="width: 220px;">Tindakan</th>
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
            if (!d.loggedIn || d.user.role !== 'mitra') {
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
    document.getElementById('loading-state').style.display = 'flex';
    document.getElementById('table-container').style.display = 'none';
    document.getElementById('empty-state').style.display = 'none';

    const statusFilter = document.getElementById('filter-status').value;
    let url = '/api/mitra/mentor-applications';
    if (statusFilter) {
        url += `?status=${statusFilter}`;
    }

    fetch(url)
        .then(r => r.json())
        .then(res => {
            document.getElementById('loading-state').style.display = 'none';

            if (res.status !== 'success') {
                showToast(res.message || 'Gagal memuat lamaran.', 'danger');
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

                const dateStr = app.applied_at 
                    ? new Date(app.applied_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric'})
                    : '-';

                let actionCol = '';
                if (app.status === 'pending') {
                    actionCol = `
                        <div style="display:flex; gap:8px;">
                            <button class="btn-dash btn-dash-primary" onclick="reviewApplication('${app.id}', 'disetujui')" style="padding:6px 12px; font-size:0.8rem; background:#10b981;">Setujui</button>
                            <button class="btn-dash btn-dash-outline" onclick="reviewApplication('${app.id}', 'ditolak')" style="border-color:#ef4444; color:#ef4444; padding:6px 12px; font-size:0.8rem;">Tolak</button>
                        </div>`;
                } else {
                    actionCol = `<span style="color:var(--text-muted); font-size:0.85rem;">Selesai Direview</span>`;
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <div style="font-weight:700; color:var(--secondary);">${app.mentor_nama}</div>
                        <div style="font-size:0.8rem; color:var(--text-muted); margin-top:2px;">${app.mentor_profesi || 'Profesional'} (${app.mentor_tahun_pengalaman || 0} thn exp)</div>
                        <div style="font-size:0.8rem; color:var(--text-muted);">${app.mentor_email}</div>
                    </td>
                    <td>
                        <div style="font-weight:600; color:var(--secondary);">${app.program_judul}</div>
                    </td>
                    <td>${dateStr}</td>
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

function reviewApplication(applicationId, status) {
    const actionLabel = status === 'disetujui' ? 'menyetujui' : 'menolak';
    if (!confirm(`Apakah Anda yakin ingin ${actionLabel} lamaran mentor ini?`)) return;

    fetch(`/api/mitra/mentor-applications/${applicationId}/review`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status: status })
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
        .catch(() => showToast('Gagal memproses lamaran.', 'danger'));
}
</script>
<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
