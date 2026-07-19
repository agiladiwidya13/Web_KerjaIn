<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Tugas - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body style="background: var(--dash-bg);">

<nav>
    <div class="logo" onclick="window.location.href='/'">
        <img src="{{ asset('image/logo-kerjain.png') }}" alt="Logo KerjaIn" class="logo-img" onerror="this.style.display='none'">
        KerjaIn
    </div>
    <div class="nav-links">
        <span class="nav-role-badge" style="background:#d1fae5;color:#065f46;"><span class="material-icons" style="font-size: 18px; margin-right: 6px; display: inline-flex; vertical-align: middle;">person</span>Mentor</span>
    </div>
    <div class="nav-auth">
        <span id="nav-nama" style="font-weight:600;color:var(--secondary);padding:8px 12px;"></span>
        <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
    </div>
</nav>

<div id="toast" class="toast"></div>

<div class="dashboard-container">
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
        <a class="nav-item" href="/pages/mentor/applications">
            <span class="material-icons nav-icon">assignment_turned_in</span> Lamaran Saya
        </a>
        <a class="nav-item active" href="/pages/mentor/submissions">
            <span class="material-icons nav-icon">description</span> Review Tugas
        </a>
        <a class="nav-item" href="/pages/mentor/profile">
            <span class="material-icons nav-icon">account_circle</span> Profil Saya
        </a>
    </aside>

    <main class="dash-main">
        
        <div style="margin-bottom:32px;">
            <h1 style="margin:0 0 8px;">Review Tugas Peserta</h1>
            <p style="color:var(--text-muted);margin:0;">Bantu peserta berkembang dengan memberikan feedback konstruktif.</p>
        </div>

        <!-- Filter -->
        <div style="display:flex;gap:12px;margin-bottom:24px;">
            <button class="btn-dash btn-dash-primary filter-btn" onclick="loadSubmissions('')" id="filter-all">Semua</button>
            <button class="btn-dash btn-dash-outline filter-btn" onclick="loadSubmissions('menunggu')" id="filter-menunggu">Menunggu Review <span class="material-icons icon-inline">hourglass_empty</span></button>
            <button class="btn-dash btn-dash-outline filter-btn" onclick="loadSubmissions('disetujui')" id="filter-disetujui">Disetujui <span class="material-icons icon-inline">check_circle</span></button>
            <button class="btn-dash btn-dash-outline filter-btn" onclick="loadSubmissions('revisi')" id="filter-revisi">Revisi <span class="material-icons icon-inline">warning</span></button>
        </div>

        <!-- Submission List -->
        <div class="program-list" id="sub-list">
            <p>Memuat submission...</p>
        </div>

    </main>
</div>

<!-- REVIEW MODAL -->
<div class="dash-modal-overlay" id="review-modal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="dash-modal">
        <h3><span class="material-icons icon-inline">description</span>Review Tugas</h3>
        
        <div style="background:var(--dash-bg);padding:16px;border-radius:12px;margin-bottom:20px;border:1px solid var(--dash-border);">
            <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:4px;">Peserta</div>
            <div style="font-weight:600;margin-bottom:12px;" id="rev-pelajar">-</div>
            
            <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:4px;">Task</div>
            <div style="font-weight:600;margin-bottom:12px;" id="rev-task">-</div>

            <div style="font-size:0.85rem;color:var(--text-muted);margin-bottom:4px;">Catatan Pengerjaan</div>
            <div style="white-space:pre-line;background:var(--dash-card);padding:12px;border-radius:8px;border:1px solid var(--dash-border);" id="rev-catatan">-</div>

            <div id="rev-file-link" style="margin-top:12px;"></div>
        </div>

        <div id="rev-status-note" style="margin-bottom:16px;color:var(--text-muted);font-size:0.92rem;display:none;"></div>
        <div class="form-group">
            <label>Keputusan Review *</label>
            <select id="rev-status" onchange="toggleNilaiInput(this.value)">
                <option value="disetujui">Disetujui (Lulus)</option>
                <option value="revisi">Perlu Revisi</option>
            </select>
        </div>
        
        <div class="form-group" id="group-nilai">
            <label>Nilai (0-100) *</label>
            <input type="number" id="rev-nilai" min="0" max="100" placeholder="cth: 85">
        </div>

        <div class="form-group">
            <label>Feedback / Pesan untuk Peserta</label>
            <textarea id="rev-feedback" rows="4" placeholder="Berikan feedback membangun..."></textarea>
        </div>

        <div class="modal-actions">
            <button class="btn-dash btn-dash-outline" onclick="document.getElementById('review-modal').classList.remove('show')">Batal</button>
            <button id="rev-submit-btn" class="btn-dash btn-dash-primary" onclick="submitReview()">Kirim Review</button>
        </div>
    </div>
</div>

<script>
let currentSubId = null;

function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast toast-' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 3500);
}

// ── INIT ──────────────────────────────────────────────────────────
fetch('/api/session').then(r => r.json()).then(d => {
    if (!d.loggedIn || d.user.role !== 'mentor') window.location.href = '/';
    else {
        loadSubmissions('menunggu');
        // Auto refresh every 30 seconds
        setInterval(() => loadSubmissions(document.querySelector('.filter-btn.btn-dash-primary')?.id?.replace('filter-', '') || ''), 30000);
    }
});

function loadSubmissions(statusFilter) {
    // Update active filter button
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('btn-dash-primary');
        b.classList.add('btn-dash-outline');
    });
    const activeBtn = document.getElementById('filter-' + (statusFilter || 'all'));
    if(activeBtn) {
        activeBtn.classList.remove('btn-dash-outline');
        activeBtn.classList.add('btn-dash-primary');
    }

    let url = '/api/mentor/submissions';
    if (statusFilter) url += '?status=' + statusFilter;

    fetch(url)
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success') return;
            const container = document.getElementById('sub-list');
            
            if (d.data.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon"><span class="material-icons" style="font-size: 48px; color: var(--text-muted);">mail_outline</span></div>
                        <h3>Tidak ada data</h3>
                        <p>Belum ada submission dengan status tersebut dari program perusahaan Anda.</p>
                    </div>`;
                return;
            }

            container.innerHTML = d.data.map(s => {
                let statusBadge = '';
                if (s.status === 'menunggu') statusBadge = '<span class="badge badge-warning">Menunggu</span>';
                if (s.status === 'disetujui') statusBadge = '<span class="badge badge-success">Disetujui</span>';
                if (s.status === 'revisi') statusBadge = '<span class="badge badge-danger">Revisi</span>';

                return `
                    <div class="submission-item" style="cursor:pointer;background:var(--dash-card);" onclick="openReviewModal('${s.id}')">
                        <div class="sub-status ${s.status}"></div>
                        <div class="sub-info">
                            <h4>${s.task_judul}</h4>
                            <div class="sub-meta">Peserta: <strong>${s.pelajar}</strong> · Program: ${s.program}</div>
                            <div style="font-size:0.8rem;color:var(--text-muted);margin-top:4px;">Dikirim: ${s.created_at}</div>
                        </div>
                        <div class="prog-meta">
                            ${statusBadge}
                        </div>
                    </div>
                `;
            }).join('');
            
            // Simpan data ke memori untuk referensi modal
            window.submissionsData = d.data;
        });
}

function openReviewModal(subId) {
    currentSubId = subId;
    const s = window.submissionsData.find(x => x.id === subId);
    if (!s) return;

    document.getElementById('rev-pelajar').textContent = s.pelajar;
    document.getElementById('rev-task').textContent = s.task_judul;
    document.getElementById('rev-catatan').textContent = s.catatan || 'Tidak ada catatan.';
    
    const fileLink = document.getElementById('rev-file-link');
    if (s.file_url) {
        fileLink.innerHTML = `<a href="${s.file_url}" target="_blank" class="btn-dash btn-dash-outline" style="font-size:0.8rem;padding:6px 12px;display:inline-flex;align-items:center;gap:6px;"><span class="material-icons" style="font-size:1rem;">description</span> Buka File Attachment</a>`;
    } else {
        fileLink.innerHTML = '';
    }

    const note = document.getElementById('rev-status-note');
    const submitBtn = document.getElementById('rev-submit-btn');
    const statusInput = document.getElementById('rev-status');
    const nilaiInput = document.getElementById('rev-nilai');
    const feedbackInput = document.getElementById('rev-feedback');

    statusInput.value = s.status === 'revisi' ? 'revisi' : 'disetujui';
    nilaiInput.value = s.nilai || '';
    feedbackInput.value = s.feedback || '';
    toggleNilaiInput(statusInput.value);

    if (s.status !== 'menunggu') {
        note.style.display = 'block';
        note.textContent = 'Submission ini sudah dinilai. Anda masih bisa melihat detail dan file, tetapi tidak dapat mengirim review lagi.';
        submitBtn.disabled = true;
        statusInput.disabled = true;
        nilaiInput.disabled = true;
        feedbackInput.disabled = true;
    } else {
        note.style.display = 'none';
        submitBtn.disabled = false;
        statusInput.disabled = false;
        nilaiInput.disabled = false;
        feedbackInput.disabled = false;
    }

    document.getElementById('review-modal').classList.add('show');
}

function toggleNilaiInput(status) {
    const group = document.getElementById('group-nilai');
    if (status === 'disetujui') {
        group.style.display = 'block';
    } else {
        group.style.display = 'none';
        document.getElementById('rev-nilai').value = '';
    }
}

function submitReview() {
    const submitBtn = document.getElementById('rev-submit-btn');
    if (submitBtn.disabled) {
        showToast('Review sudah tidak dapat diedit untuk submission ini.', 'info');
        return;
    }

    const status = document.getElementById('rev-status').value;
    const nilai = document.getElementById('rev-nilai').value;
    const feedback = document.getElementById('rev-feedback').value;

    if (status === 'disetujui' && (!nilai || nilai < 0 || nilai > 100)) {
        showToast('Nilai wajib diisi antara 0-100 untuk status disetujui', 'error');
        return;
    }

    fetch(`/api/mentor/submissions/${currentSubId}/review`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ status, nilai, feedback })
    })
    .then(r => r.json())
    .then(d => {
        if (d.status === 'success') {
            showToast('Review berhasil dikirim!');
            document.getElementById('review-modal').classList.remove('show');
            // Refresh list
            const currentFilter = document.querySelector('.filter-btn.btn-dash-primary').id.replace('filter-', '');
            loadSubmissions(currentFilter === 'all' ? '' : currentFilter);
        } else {
            showToast(d.message, 'error');
        }
    });
}
</script>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
