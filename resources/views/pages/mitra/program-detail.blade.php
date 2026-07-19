<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Program - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body style="background: var(--dash-bg);">

<nav>
    <div class="logo" onclick="window.location.href='/pages/mitra/dashboard'" style="cursor:pointer;">
        <span style="font-size:1.2rem;margin-right:8px;">←</span> Kembali ke Dashboard Mitra
    </div>
</nav>

<div id="toast" class="toast"></div>

<div class="dashboard-container" style="padding-top:64px;">
    <!-- FULL WIDTH MAIN -->
    <main class="dash-main" style="margin-left:0; max-width:1200px; margin: 0 auto; padding-top: 40px;">
        
        <div class="dash-card">
            <!-- Cover Program -->
            <div id="cover-preview-container" style="display:none; width:100%; height:220px; border-radius:16px; overflow:hidden; margin-bottom:20px; position:relative; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid var(--dash-border);">
                <img id="prog-cover-img" src="" style="width:100%; height:100%; object-fit:cover;">
            </div>
            <div style="display:flex; justify-content:flex-end; margin-bottom: 20px;">
                <button class="btn-dash btn-dash-outline" style="font-size:0.85rem; padding: 6px 12px; display: inline-flex; align-items: center; gap: 6px;" onclick="document.getElementById('cover-file-input').click()"><span class="material-icons" style="font-size:1.1rem;">image</span> Ganti Cover Program</button>
                <input type="file" id="cover-file-input" style="display:none;" accept="image/*" onchange="uploadProgramCover()">
            </div>

            <div class="dash-card-header" style="border-bottom:1px solid var(--dash-border); padding-bottom:20px; margin-bottom:24px;">
                <div>
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                        <h1 id="prog-judul" style="margin:0;font-size:1.8rem;">Memuat Program...</h1>
                        <span class="badge" id="prog-status" style="font-size:0.9rem;">-</span>
                    </div>
                    <div style="color:var(--text-muted);font-size:0.95rem;">
                        <span id="prog-bidang">-</span> · <span id="prog-peserta">0</span> Peserta
                    </div>
                    <div style="color:var(--text-muted);font-size:0.9rem;margin-top:6px;display:flex;gap:20px;flex-wrap:wrap;">
                        <span style="display:inline-flex;align-items:center;gap:4px;"><span class="material-icons" style="font-size:16px;">event_note</span> Pendaftaran: <strong id="prog-reg-period">-</strong></span>
                        <span style="display:inline-flex;align-items:center;gap:4px;"><span class="material-icons" style="font-size:16px;">calendar_today</span> Pelaksanaan: <strong id="prog-exec-period">-</strong></span>
                    </div>
                </div>
                <div style="display:flex;gap:12px;" id="action-buttons">
                    <!-- Injected -->
                </div>
            </div>

            <!-- TABS -->
            <div style="display:flex;gap:24px;border-bottom:1px solid var(--dash-border);margin-bottom:24px;">
                <div class="tab-btn active" onclick="switchTab('tasks')" style="padding:12px 16px;cursor:pointer;font-weight:600;border-bottom:3px solid var(--dash-accent);color:var(--dash-accent);" id="tab-tasks">Manajemen Task</div>
                <div class="tab-btn" onclick="switchTab('mentors')" style="padding:12px 16px;cursor:pointer;font-weight:600;border-bottom:3px solid transparent;color:var(--text-muted);" id="tab-mentors">Assign Mentor</div>
                <div class="tab-btn" onclick="switchTab('leaderboard')" style="padding:12px 16px;cursor:pointer;font-weight:600;border-bottom:3px solid transparent;color:var(--text-muted);" id="tab-leaderboard">Leaderboard Peserta</div>
            </div>

            <!-- TASKS TAB -->
            <div id="content-tasks">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <h3 style="margin:0;">Kurikulum / Task Program</h3>
                    <button class="btn-dash btn-dash-primary" onclick="document.getElementById('task-modal').classList.add('show')">+ Tambah Task</button>
                </div>
                
                <div class="program-list" id="task-list">
                    <p>Memuat...</p>
                </div>
            </div>

            <!-- MENTORS TAB -->
            <div id="content-mentors" style="display:none;">
                <h3 style="margin-top:0;margin-bottom:8px;">Mentor Program</h3>
                <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:20px;">Assign mentor terafiliasi untuk membantu mereview submission peserta di program ini.</p>
                
                <div style="display:flex;gap:12px;margin-bottom:24px;max-width:500px;">
                    <select id="mentor-select" style="flex:1;padding:10px 14px;border:1px solid var(--dash-border);border-radius:8px;">
                        <option value="">-- Pilih Mentor Terafiliasi --</option>
                    </select>
                    <button class="btn-dash btn-dash-primary" onclick="assignMentor()">Assign</button>
                </div>

                <div class="program-list" id="assigned-mentors">
                    <p>Memuat...</p>
                </div>
            </div>

            <!-- LEADERBOARD TAB -->
            <div id="content-leaderboard" style="display:none;">
                <h3 style="margin-top:0;margin-bottom:8px;">Leaderboard Peserta <span class="material-icons" style="font-size: 28px; margin-left: 8px; display: inline-flex; vertical-align: middle; color: var(--warning);">emoji_events</span></h3>
                <p style="color:var(--text-muted);font-size:0.9rem;margin-bottom:20px;">Papan peringkat poin peserta yang terdaftar pada program ini.</p>
                
                <div class="dash-card" style="box-shadow:none; padding:0; border:none; background:transparent;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--dash-border); color: var(--text); font-weight: 600;">
                                <th style="padding: 12px 16px;">Peringkat</th>
                                <th style="padding: 12px 16px;">Nama Peserta</th>
                                <th style="padding: 12px 16px;">Universitas</th>
                                <th style="padding: 12px 16px;">Total Poin Program</th>
                            </tr>
                        </thead>
                        <tbody id="leaderboard-table">
                            <tr>
                                <td colspan="4" style="padding: 20px; text-align: center; color: var(--text-muted);">Belum ada data peringkat peserta.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- TASK MODAL -->
<div class="dash-modal-overlay" id="task-modal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="dash-modal">
        <h3><span class="material-icons" style="margin-right: 8px; display: inline-flex; vertical-align: middle;">description</span>Tambah Task Baru</h3>
        <div class="form-group">
            <label>Judul Task *</label>
            <input type="text" id="task-judul">
        </div>
        <div class="form-group">
            <label>Deskripsi / Instruksi</label>
            <textarea id="task-deskripsi" rows="5"></textarea>
        </div>
        <div class="modal-actions">
            <button class="btn-dash btn-dash-outline" onclick="document.getElementById('task-modal').classList.remove('show')">Batal</button>
            <button class="btn-dash btn-dash-primary" onclick="saveTask()">Simpan Task</button>
        </div>
    </div>
</div>

<script>
const programId = "{{ $programId }}";

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast toast-' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 3500);
}

function switchTab(tab) {
    document.getElementById('content-tasks').style.display = tab === 'tasks' ? 'block' : 'none';
    document.getElementById('content-mentors').style.display = tab === 'mentors' ? 'block' : 'none';
    document.getElementById('content-leaderboard').style.display = tab === 'leaderboard' ? 'block' : 'none';
    
    document.getElementById('tab-tasks').style.color = tab === 'tasks' ? 'var(--dash-accent)' : 'var(--text-muted)';
    document.getElementById('tab-tasks').style.borderBottomColor = tab === 'tasks' ? 'var(--dash-accent)' : 'transparent';
    
    document.getElementById('tab-mentors').style.color = tab === 'mentors' ? 'var(--dash-accent)' : 'var(--text-muted)';
    document.getElementById('tab-mentors').style.borderBottomColor = tab === 'mentors' ? 'var(--dash-accent)' : 'transparent';

    document.getElementById('tab-leaderboard').style.color = tab === 'leaderboard' ? 'var(--dash-accent)' : 'var(--text-muted)';
    document.getElementById('tab-leaderboard').style.borderBottomColor = tab === 'leaderboard' ? 'var(--dash-accent)' : 'transparent';

    if (tab === 'leaderboard') {
        loadLeaderboard();
    }
}

// ── INIT ──────────────────────────────────────────────────────────
fetch('/api/session').then(r => r.json()).then(d => {
    if (!d.loggedIn || d.user.role !== 'mitra') window.location.href = '/';
    else {
        loadProgramDetail();
        loadAvailableMentors();
    }
});

function loadProgramDetail() {
    fetch('/api/mitra/programs/' + programId)
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success') {
                alert('Gagal memuat program');
                window.location.href = '/pages/mitra/dashboard';
                return;
            }
            
            const p = d.data;
            document.getElementById('prog-judul').textContent = p.judul;
            document.getElementById('prog-bidang').textContent = p.bidang || 'Umum';
            document.getElementById('prog-peserta').textContent = p.enrollments.length;

            const regMulai = p.registrasi_mulai || '-';
            const regSelesai = p.registrasi_selesai || '-';
            document.getElementById('prog-reg-period').textContent = `${regMulai} s.d. ${regSelesai}`;

            const tglMulai = p.tanggal_mulai || '-';
            const tglSelesai = p.tanggal_selesai || '-';
            document.getElementById('prog-exec-period').textContent = `${tglMulai} s.d. ${tglSelesai}`;

            if (p.cover_image) {
                const src = p.cover_image.startsWith('http') || p.cover_image.startsWith('/') 
                    ? p.cover_image 
                    : '/' + p.cover_image;
                document.getElementById('prog-cover-img').src = src;
                document.getElementById('cover-preview-container').style.display = 'block';
            } else {
                document.getElementById('cover-preview-container').style.display = 'none';
            }
            
            const statusBadge = document.getElementById('prog-status');
            statusBadge.textContent = p.status.toUpperCase();
            statusBadge.className = 'badge ' + (p.status === 'published' ? 'badge-success' : p.status === 'closed' ? 'badge-danger' : 'badge-warning');

            // Action buttons
            let actions = '';
            if (p.status === 'draft') {
                actions = `<button class="btn-dash btn-dash-success" onclick="updateStatus('publish')" style="display:inline-flex;align-items:center;gap:6px;"><span class="material-icons" style="font-size:1.1rem;">publish</span> Publish Program</button>`;
            } else if (p.status === 'published') {
                actions = `<button class="btn-dash btn-dash-danger" onclick="updateStatus('close')" style="display:inline-flex;align-items:center;gap:6px;"><span class="material-icons" style="font-size:1.1rem;">lock</span> Tutup Pendaftaran</button>`;
            }
            document.getElementById('action-buttons').innerHTML = actions;

            // Render Tasks
            const taskContainer = document.getElementById('task-list');
            if (p.tasks.length === 0) {
                taskContainer.innerHTML = '<div class="empty-state">Belum ada task. Tambahkan task agar peserta bisa mulai bekerja.</div>';
            } else {
                taskContainer.innerHTML = p.tasks.map((t, i) => `
                    <div class="program-item" style="cursor:default;">
                        <div class="prog-icon" style="background:var(--dash-bg);color:var(--text);font-weight:700;">${i+1}</div>
                        <div class="prog-info">
                            <h4 style="font-size:1.05rem;">${t.judul}</h4>
                            <p style="white-space:pre-line;margin-top:8px;">${t.deskripsi || ''}</p>
                        </div>
                        <div class="prog-meta">
                            <button class="btn-dash btn-dash-danger" style="padding:6px 12px;font-size:0.75rem;" onclick="deleteTask('${t.id}')">Hapus</button>
                        </div>
                    </div>
                `).join('');
            }

            // Render Assigned Mentors
            const mentorContainer = document.getElementById('assigned-mentors');
            if (p.mentors.length === 0) {
                mentorContainer.innerHTML = '<div class="empty-state">Belum ada mentor yang ditugaskan.</div>';
            } else {
                mentorContainer.innerHTML = p.mentors.map(m => `
                    <div class="program-item" style="cursor:default;">
                        <div class="prog-icon"><span class="material-icons">supervisor_account</span></div>
                        <div class="prog-info">
                            <h4 style="font-size:1rem;">${m.user.nama_lengkap}</h4>
                            <p>${m.profesi || 'Mentor'} · ${m.user.email}</p>
                        </div>
                    </div>
                `).join('');
            }
        });
}

function updateStatus(action) {
    fetch(`/api/mitra/programs/${programId}/${action}`, { method: 'POST' })
        .then(r => {
            if (!r.ok) {
                return r.json().then(err => { throw err; });
            }
            return r.json();
        })
        .then(d => {
            if (d.status === 'success') {
                showToast(d.message);
                loadProgramDetail();
            } else {
                showToast(d.message || 'Gagal mengubah status program.', 'error');
            }
        })
        .catch(err => {
            showToast(err.message || 'Gagal mengubah status program.', 'error');
        });
}

function uploadProgramCover() {
    const fileInput = document.getElementById('cover-file-input');
    if (!fileInput.files || fileInput.files.length === 0) return;

    const file = fileInput.files[0];
    const formData = new FormData();
    formData.append('cover', file);

    showToast('<span class="material-icons icon-inline">hourglass_empty</span> Mengupload cover...', 'warning');

    fetch(`/api/mitra/programs/${programId}/upload-cover`, {
        method: 'POST',
        body: formData
    })
    .then(r => {
        if (!r.ok) {
            return r.json().then(err => { throw err; });
        }
        return r.json();
    })
    .then(d => {
        if (d.status === 'success') {
            showToast('<span class="material-icons icon-inline">check_circle</span> Cover program berhasil diperbarui!');
            loadProgramDetail();
        } else {
            showToast(d.message || 'Gagal mengupload cover.', 'error');
        }
    })
    .catch(err => {
        const msg = err.errors && err.errors.cover ? err.errors.cover[0] : (err.message || 'Gagal mengupload cover.');
        showToast('<span class="material-icons icon-inline">error</span> ' + msg, 'error');
    });
}

// ── TASK MANAGEMENT ───────────────────────────────────────────────
function saveTask() {
    const judul = document.getElementById('task-judul').value.trim();
    const deskripsi = document.getElementById('task-deskripsi').value.trim();
    if (!judul) { showToast('Judul wajib diisi', 'error'); return; }

    fetch(`/api/mitra/programs/${programId}/tasks`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ judul, deskripsi })
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === 'success') {
            showToast('Task berhasil ditambahkan');
            document.getElementById('task-modal').classList.remove('show');
            document.getElementById('task-judul').value = '';
            document.getElementById('task-deskripsi').value = '';
            loadProgramDetail();
        }
    });
}

function deleteTask(taskId) {
    fetch(`/api/mitra/tasks/${taskId}/delete`, { method: 'POST' })
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') {
                showToast('Task dihapus');
                loadProgramDetail();
            }
        });
}

// ── MENTOR ASSIGNMENT ─────────────────────────────────────────────
function loadAvailableMentors() {
    fetch('/api/mitra/mentors')
        .then(r => r.json())
        .then(d => {
            if (d.status === 'success') {
                const select = document.getElementById('mentor-select');
                select.innerHTML = '<option value="">-- Pilih Mentor Terafiliasi --</option>' + 
                    d.data.map(m => `<option value="${m.id}">${m.nama} (${m.profesi || 'Mentor'})</option>`).join('');
            }
        });
}

function assignMentor() {
    const mentorId = document.getElementById('mentor-select').value;
    if (!mentorId) return;

    fetch(`/api/mitra/programs/${programId}/assign-mentor`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ mentor_id: mentorId })
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === 'success') {
            showToast('Mentor berhasil di-assign!');
            loadProgramDetail();
        } else {
            showToast(d.message, 'error');
        }
    });
}

function loadLeaderboard() {
    fetch(`/api/mitra/programs/${programId}/leaderboard`)
        .then(r => r.json())
        .then(d => {
            const table = document.getElementById('leaderboard-table');
            if (d.status !== 'success' || !d.data.length) {
                table.innerHTML = `<tr><td colspan="4" style="padding: 20px; text-align: center; color: var(--text-muted);">Belum ada data peringkat peserta.</td></tr>`;
                return;
            }

            table.innerHTML = d.data.map(p => {
                let rankStyle = '';
                let rankEmoji = '';
                if (p.rank === 1) { rankStyle = 'font-weight: 800; color: #fbbf24; font-size: 1.2rem;'; rankEmoji = '<span class="material-icons">emoji_events</span>'; }
                else if (p.rank === 2) { rankStyle = 'font-weight: 700; color: #9ca3af; font-size: 1.1rem;'; rankEmoji = '<span class="material-icons">emoji_events</span>'; }
                else if (p.rank === 3) { rankStyle = 'font-weight: 700; color: #b45309; font-size: 1.1rem;'; rankEmoji = '<span class="material-icons">emoji_events</span>'; }
                else { rankEmoji = `#${p.rank}`; }

                return `
                    <tr style="border-bottom: 1px solid var(--dash-border);">
                        <td style="padding: 16px; ${rankStyle}">${rankEmoji}</td>
                        <td style="padding: 16px; font-weight: 600; color: var(--text);">${p.nama}</td>
                        <td style="padding: 16px; color: var(--text-muted);">${p.universitas}</td>
                        <td style="padding: 16px; font-weight: 700; color: var(--dash-accent);"><span class="material-icons icon-inline">auto_awesome</span>${p.total_poin}</td>
                    </tr>
                `;
            }).join('');
        });
}
</script>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
