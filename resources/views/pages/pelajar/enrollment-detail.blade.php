<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workspace Program - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .ws-header {
            background: white;
            border-bottom: 1px solid var(--dash-border);
            padding: 80px 20px 24px;
        }
        .ws-header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .ws-main {
            max-width: 1200px;
            margin: 32px auto;
            display: grid;
            grid-template-columns: 3fr 2fr;
            gap: 24px;
            padding: 0 20px;
        }
        .task-nav-item {
            padding: 16px;
            border-radius: 12px;
            border: 1px solid var(--dash-border);
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
            background: white;
        }
        .task-nav-item:hover { background: #f8fafc; }
        .task-nav-item.active {
            border-color: var(--dash-accent);
            background: var(--dash-accent-light);
        }
        .task-status-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            color: white;
        }
        .task-status-icon .material-icons {
            font-size: 14px;
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .status-none { background: #cbd5e1; }
        .status-menunggu { background: var(--dash-warning); }
        .status-disetujui { background: var(--dash-success); }
        .status-revisi { background: var(--dash-danger); }
        
        .task-viewer {
            background: white;
            border-radius: 16px;
            border: 1px solid var(--dash-border);
            padding: 32px;
        }
        .task-viewer h2 { margin-top: 0; font-size: 1.5rem; margin-bottom: 24px; }
        .submit-area {
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px dashed var(--dash-border);
        }
        .feedback-box {
            padding: 16px;
            border-radius: 8px;
            margin-top: 16px;
            background: #f8fafc;
            border-left: 4px solid var(--dash-accent);
        }

        /* Dark Mode Compatibility */
        body.dark-mode .ws-header {
            background: var(--dash-card);
            border-bottom-color: var(--dash-border);
        }
        body.dark-mode .task-nav-item {
            background: var(--dash-card);
            border-color: var(--dash-border);
            color: var(--text);
        }
        body.dark-mode .task-nav-item:hover {
            background: #0f172a;
        }
        body.dark-mode .task-nav-item.active {
            background: rgba(99, 102, 241, 0.15);
            border-color: var(--dash-accent);
        }
        body.dark-mode .task-viewer {
            background: var(--dash-card);
            border-color: var(--dash-border);
            color: var(--text);
        }
        body.dark-mode .feedback-box {
            background: #1e293b !important;
            color: var(--text) !important;
        }
        body.dark-mode .feedback-box[style*="background:#fef2f2"],
        body.dark-mode .feedback-box[style*="background: #fef2f2"] {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #fca5a5 !important;
        }
        body.dark-mode .status-none {
            background: #475569;
        }
    </style>
</head>
<body style="background: var(--dash-bg);">

<nav>
    <div class="logo" onclick="window.location.href='/pages/pelajar/dashboard'" style="cursor:pointer;">
        <span style="font-size:1.2rem;margin-right:8px;">←</span> Kembali ke Dashboard
    </div>
</nav>

<div id="toast" class="toast"></div>

<div class="ws-header">
    <div class="ws-header-content">
        <div>
            <div style="font-size:0.9rem;color:var(--text-muted);font-weight:600;margin-bottom:4px;" id="ws-perusahaan">Memuat...</div>
            <h1 id="ws-judul" style="margin:0;font-size:1.75rem;">Workspace Program</h1>
        </div>
        <div style="text-align:right;">
            <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:4px;">Progress Penyelesaian</div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div class="progress-bar-wrap" style="width:200px;margin:0;">
                    <div class="progress-bar-fill" id="ws-progress" style="width:0%"></div>
                </div>
                <span style="font-weight:700;color:var(--dash-accent);" id="ws-progress-text">0%</span>
            </div>
        </div>
    </div>
</div>

<div class="ws-main">
    <!-- TASK VIEWER -->
    <div class="task-viewer">
        <div id="task-content">
            <div class="empty-state">
                <div class="empty-icon"><span class="material-icons" style="font-size:3rem; color:var(--text-muted);">arrow_back</span></div>
                <h3>Pilih tugas di menu samping</h3>
                <p>Klik salah satu tugas untuk melihat instruksi dan mengirimkan hasil kerja.</p>
            </div>
        </div>
    </div>

    <!-- TASK LIST SIDEBAR -->
    <div>
        <h3 style="margin-top:0;margin-bottom:16px;">Daftar Tugas</h3>
        <div id="task-list">
            <p>Memuat...</p>
        </div>
    </div>
</div>

<script>
const enrollmentId = "{{ $enrollmentId }}";
let globalTasks = [];
let activeTaskId = null;

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast toast-' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 3500);
}

// ── INIT ──────────────────────────────────────────────────────────
fetch('/api/session').then(r => r.json()).then(d => {
    if (!d.loggedIn || d.user.role !== 'pelajar') window.location.href = '/';
    else loadEnrollment();
});

function loadEnrollment() {
    fetch('/api/pelajar/enrollments/' + enrollmentId)
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success') {
                alert('Data tidak ditemukan');
                window.location.href = '/pages/pelajar/dashboard';
                return;
            }
            
            const e = d.data;
            document.getElementById('ws-perusahaan').textContent = e.perusahaan;
            document.getElementById('ws-judul').textContent = e.judul;
            document.getElementById('ws-progress').style.width = e.progress + '%';
            document.getElementById('ws-progress-text').textContent = e.progress + '%';

            globalTasks = e.tasks;
            renderTaskList();
            
            if (activeTaskId) {
                viewTask(activeTaskId);
            } else if (globalTasks.length > 0) {
                viewTask(globalTasks[0].id);
            }
        });
}

function renderTaskList() {
    const container = document.getElementById('task-list');
    if (!globalTasks.length) {
        container.innerHTML = '<p>Belum ada task di program ini.</p>';
        return;
    }

    container.innerHTML = globalTasks.map(t => {
        const sub = t.submission;
        let statusClass = 'status-none';
        let statusIcon = '<span class="material-icons icon-inline">hourglass_empty</span>';
        let statusText = 'Belum dikerjakan';

        if (sub) {
            statusClass = 'status-' + sub.status;
            if (sub.status === 'menunggu') { statusIcon = '<span class="material-icons icon-inline">hourglass_empty</span>'; statusText = 'Menunggu Review'; }
            if (sub.status === 'disetujui') { statusIcon = '<span class="material-icons icon-inline">check_circle</span>'; statusText = 'Disetujui'; }
            if (sub.status === 'revisi') { statusIcon = '<span class="material-icons icon-inline">warning</span>'; statusText = 'Perlu Revisi'; }
        }

        return `
            <div class="task-nav-item ${t.id === activeTaskId ? 'active' : ''}" onclick="viewTask('${t.id}')">
                <div class="task-status-icon ${statusClass}">${statusIcon}</div>
                <div>
                    <div style="font-weight:600;font-size:0.95rem;margin-bottom:4px;">${t.judul}</div>
                    <div style="font-size:0.8rem;color:var(--text-muted);">${statusText}</div>
                </div>
            </div>
        `;
    }).join('');
}

function viewTask(taskId) {
    activeTaskId = taskId;
    renderTaskList(); // update active state

    const t = globalTasks.find(x => x.id === taskId);
    const sub = t.submission;
    const isApproved = sub && sub.status === 'disetujui';
    const isPending = sub && sub.status === 'menunggu';

    let submissionHtml = '';

    if (isApproved) {
        submissionHtml = `
            <div class="submit-area">
                <h3 style="color:var(--dash-success);display:flex;align-items:center;gap:8px;"><span class="material-icons">check_circle</span>Tugas Disetujui</h3>
                <p>Kerja bagus! Tugas ini telah memenuhi kriteria.</p>
                <div class="feedback-box" style="border-left-color:var(--dash-success);">
                    <strong>Nilai:</strong> ${sub.nilai || '-'}/100<br>
                    <strong>Feedback Mentor:</strong><br>
                    ${sub.feedback || 'Tidak ada catatan tambahan.'}
                </div>
                ${sub.file_url ? `<p><a href="/${sub.file_url}" target="_blank" class="btn-dash btn-dash-outline" style="display:inline-flex;align-items:center;gap:6px;"><span class="material-icons" style="font-size:1.1rem;">description</span> Lihat File Submission</a></p>` : ''}
            </div>
        `;
    } else if (isPending) {
        submissionHtml = `
            <div class="submit-area">
                <h3 style="color:var(--dash-warning);display:flex;align-items:center;gap:8px;"><span class="material-icons">hourglass_empty</span> Menunggu Review</h3>
                <p>Tugas Anda sedang direview oleh mentor. Mohon tunggu notifikasi selanjutnya.</p>
                ${sub.file_url ? `<p><a href="/${sub.file_url}" target="_blank" class="btn-dash btn-dash-outline" style="display:inline-flex;align-items:center;gap:6px;"><span class="material-icons" style="font-size:1.1rem;">description</span> Lihat File Submission</a></p>` : ''}
            </div>
        `;
    } else {
        // Form submit (bisa baru atau revisi)
        const revisiMsg = sub && sub.status === 'revisi' ? `
            <div class="feedback-box" style="border-left-color:var(--dash-danger);background:#fef2f2;margin-bottom:24px;">
                <strong style="color:var(--dash-danger);"><span class="material-icons" style="margin-right: 6px; display: inline-flex; vertical-align: middle;">warning</span>Perlu Revisi</strong><br>
                <strong>Feedback Mentor:</strong><br>
                ${sub.feedback}
            </div>
        ` : '';

        submissionHtml = `
            <div class="submit-area">
                <h3>Kirim Hasil Pekerjaan</h3>
                ${revisiMsg}
                <div class="form-group" style="margin-bottom:16px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;font-size:0.9rem;">Catatan Pengerjaan (Opsional)</label>
                    <textarea id="sub-catatan" rows="4" style="width:100%;padding:12px;border:1px solid var(--dash-border);border-radius:8px;" placeholder="Jelaskan hasil pekerjaanmu atau sertakan link Google Drive/Github di sini..."></textarea>
                </div>
                <div class="form-group" style="margin-bottom:24px;">
                    <label style="display:block;margin-bottom:8px;font-weight:600;font-size:0.9rem;">Upload File (Opsional)</label>
                    <input type="file" id="sub-file" style="width:100%;padding:12px;border:1px solid var(--dash-border);border-radius:8px;">
                    <small style="color:var(--text-muted);">Format bebas, maksimal 10MB.</small>
                </div>
                <button class="btn-dash btn-dash-primary" onclick="submitTask('${t.id}')">Kirim Tugas</button>
            </div>
        `;
    }

    document.getElementById('task-content').innerHTML = `
        <h2>${t.judul}</h2>
        <div style="white-space:pre-line;line-height:1.7;color:var(--text);">${t.deskripsi || 'Tidak ada deskripsi spesifik.'}</div>
        ${submissionHtml}
    `;
}

function submitTask(taskId) {
    const catatan = document.getElementById('sub-catatan').value;
    const file = document.getElementById('sub-file').files[0];

    if (!catatan && !file) {
        showToast('Mohon isi catatan atau upload file.', 'error');
        return;
    }

    const formData = new FormData();
    formData.append('enrollment_id', enrollmentId);
    formData.append('task_id', taskId);
    if (catatan) formData.append('catatan', catatan);
    if (file) formData.append('file', file);

    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'Mengirim...';

    fetch('/api/pelajar/submit', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') {
                showToast(d.message);
                loadEnrollment(); // reload data
            } else {
                showToast(d.message, 'error');
                btn.disabled = false;
                btn.textContent = 'Kirim Tugas';
            }
        })
        .catch(() => {
            showToast('Gagal mengirim tugas', 'error');
            btn.disabled = false;
            btn.textContent = 'Kirim Tugas';
        });
}
</script>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
