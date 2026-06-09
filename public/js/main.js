// js/main.js
// Script utama untuk index.html
// Menangani: login, daftar, cek session, navigasi modal

let apiPrograms = [];
let currentFilter = 'semua';
let currentRole   = 'pelajar';
let pendingTab    = 'masuk';

// ============================================================
// CEK SESSION SAAT HALAMAN DIBUKA
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    checkSession();
    fetchPrograms();
    
    // Check if session has expired
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('expired') === '1') {
        showGlobalToast('Sesi Anda telah berakhir. Silakan login kembali.', 'error');
        // Clean URL parameter without page reload
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});

function showGlobalToast(msg, type = 'success') {
    let t = document.getElementById('global-toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'global-toast';
        document.body.appendChild(t);
    }
    t.textContent = msg;
    t.className = 'toast toast-' + type + ' show';
    setTimeout(() => t.classList.remove('show'), 3500);
}

function checkSession() {
    fetch('/api/session')
        .then(res => res.json())
        .then(data => {
            if (data.loggedIn) {
                // Update navbar jika sudah login
                updateNavLoggedIn(data.user);
            }
        })
        .catch(() => {}); // Abaikan error jika PHP belum jalan
}

function updateNavLoggedIn(user) {
    const roleLabel = { pelajar: 'Mahasiswa', mentor: 'Mentor', mitra: 'Mitra' };
    const profileUrl = {
        pelajar: '/pages/pelajar/dashboard',
        mentor:  '/pages/mentor/dashboard',
        mitra:   '/pages/mitra/dashboard'
    };
    document.getElementById('nav-auth').innerHTML = `
        <a href="${profileUrl[user.role]}" class="btn-outline" style="font-weight:600;">
            <span class="material-icons icon-inline">bar_chart</span>Dashboard
        </a>
        <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
    `;
}

// ============================================================
// FETCH & RENDER PROGRAM DARI API
// ============================================================
const bidangIcons = {
    'teknologi': 'code', 'tech': 'code', 'desain': 'palette', 'design': 'palette',
    'keuangan': 'account_balance', 'finance': 'account_balance', 'marketing': 'campaign',
    'konsultansi': 'bar_chart', 'consulting': 'bar_chart', 'data': 'trending_up',
    'bisnis': 'business', 'business': 'business',
};

function getIcon(bidang) {
    if (!bidang) return 'list_alt';
    const key = bidang.toLowerCase();
    for (const [k, v] of Object.entries(bidangIcons)) {
        if (key.includes(k)) return v;
    }
    return 'list_alt';
}

function getCatFromBidang(bidang) {
    if (!bidang) return 'lainnya';
    const b = bidang.toLowerCase();
    if (b.includes('teknologi') || b.includes('tech') || b.includes('software') || b.includes('engineering')) return 'tech';
    if (b.includes('keuangan') || b.includes('finance') || b.includes('banking')) return 'finance';
    if (b.includes('marketing') || b.includes('pemasaran')) return 'marketing';
    if (b.includes('konsultansi') || b.includes('consulting')) return 'consulting';
    if (b.includes('desain') || b.includes('design')) return 'desain';
    return 'lainnya';
}

function fetchPrograms() {
    fetch('/api/programs', {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            apiPrograms = data.data || [];
        } else {
            apiPrograms = [];
        }
        renderHomeGrid();
        renderExploreGrid();
    })
    .catch(() => {
        apiPrograms = [];
        renderHomeGrid();
        renderExploreGrid();
    });
}

function createCard(p) {
    const iconName = getIcon(p.bidang);
    const iconHtml = `<span class="material-icons" style="font-size:3rem;">${iconName}</span>`;
    const enrolled = p.enrolled || 0;
    const kuotaText = p.kuota ? `${enrolled}/${p.kuota} peserta` : `${enrolled} peserta`;
    const periode = p.tanggal_mulai ? `${p.tanggal_mulai}` : 'Fleksibel';
    
    const coverHtml = p.cover_image 
        ? `<img src="${p.cover_image}" alt="${p.judul}" style="width:100%; height:100%; object-fit:cover;">`
        : `<div style="background:linear-gradient(135deg, var(--primary-light), var(--primary)); height:100%; display:flex; align-items:center; justify-content:center; color:white;">
                ${iconHtml}
            </div>`;
    
    return `<div class="card">
        <div class="card-image">
            ${coverHtml}
            <div class="card-image-overlay"><div class="card-emoji-badge"><span class="material-icons" style="font-size:1.5rem;">${iconName}</span></div></div>
        </div>
        <div class="card-content">
            <p class="company-name">${p.perusahaan}</p>
            <h3 class="job-title">${p.judul}</h3>
            <div class="tags">
                <span class="tag-pill"><span class="material-icons icon-inline">${iconName}</span>${p.bidang || 'Umum'}</span>
                <span class="tag-pill"><span class="material-icons icon-inline">group</span>${kuotaText}</span>
                <span class="tag-pill"><span class="material-icons icon-inline">calendar_today</span>${periode}</span>
            </div>
            <button class="btn-start" onclick="openRoleModal('daftar')">Ambil Peluang →</button>
        </div>
    </div>`;
}

function renderHomeGrid() {
    const grid = document.getElementById('home-grid');
    if (!grid) return;
    if (apiPrograms.length === 0) {
        grid.innerHTML = '<p style="text-align:center;color:var(--text-muted);padding:40px;grid-column:1/-1;">Belum ada program yang tersedia saat ini.</p>';
        return;
    }
    grid.innerHTML = apiPrograms.slice(0, 4).map(createCard).join('');
}

function renderExploreGrid() {
    const searchEl = document.getElementById('search-input');
    const q = (searchEl ? searchEl.value : '').toLowerCase();
    const filtered = apiPrograms.filter(p => {
        const cat = getCatFromBidang(p.bidang);
        const matchCat = currentFilter === 'semua' || cat === currentFilter;
        const matchQ = !q || 
            (p.perusahaan || '').toLowerCase().includes(q) || 
            (p.judul || '').toLowerCase().includes(q) ||
            (p.bidang || '').toLowerCase().includes(q);
        return matchCat && matchQ;
    });
    const grid = document.getElementById('explore-grid');
    if (grid) {
        grid.innerHTML = filtered.map(createCard).join('');
    }
    const emptyState = document.getElementById('empty-state');
    if (emptyState) {
        emptyState.style.display = filtered.length ? 'none' : 'block';
    }
}

function filterCards() { renderExploreGrid(); }

function setFilter(el, cat) {
    document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    currentFilter = cat;
    renderExploreGrid();
}

// ============================================================
// NAVIGASI HALAMAN
// ============================================================
function showPage(name) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.getElementById('page-' + name).classList.add('active');
    document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
    const navEl = document.getElementById('nav-' + name);
    if (navEl) navEl.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });
    if (name === 'jelajahi') renderExploreGrid();
}

// ============================================================
// MODAL
// ============================================================
function openRoleModal(tab) {
    pendingTab = tab || 'masuk';
    document.getElementById('role-modal-title').textContent =
        tab === 'masuk' ? 'Masuk ke KerjaIn' : 'Daftar di KerjaIn';
    document.getElementById('role-modal-sub').textContent = 'Pilih peran Anda untuk melanjutkan';
    document.getElementById('role-modal-overlay').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function openAuthModal(role) {
    currentRole = role;
    document.getElementById('role-modal-overlay').classList.remove('show');

    const badge = document.getElementById('role-badge');
    const mitraExtra = document.getElementById('mitra-extra');

    if (role === 'pelajar') {
        badge.textContent = 'Mahasiswa';
        badge.innerHTML = '<span class="material-icons icon-inline">school</span>Mahasiswa';
        badge.className = 'modal-role-badge student';
        mitraExtra.style.display = 'none';
        document.getElementById('reg-name').placeholder = 'Nama lengkap kamu';
        document.getElementById('login-email').placeholder = 'mahasiswa@email.com';
        document.getElementById('reg-email').placeholder = 'mahasiswa@email.com';
    } else if (role === 'mitra') {
        badge.innerHTML = '<span class="material-icons icon-inline">business</span>Perusahaan';
        badge.className = 'modal-role-badge company';
        mitraExtra.style.display = 'block';
        document.getElementById('reg-name').placeholder = 'Nama PIC / HR';
        document.getElementById('login-email').placeholder = 'hr@perusahaan.com';
        document.getElementById('reg-email').placeholder = 'hr@perusahaan.com';
    } else if (role === 'mentor') {
        badge.innerHTML = '<span class="material-icons icon-inline">person</span>Mentor';
        badge.className = 'modal-role-badge mentor';
        mitraExtra.style.display = 'none';
        document.getElementById('reg-name').placeholder = 'Nama lengkap Anda';
        document.getElementById('login-email').placeholder = 'mentor@email.com';
        document.getElementById('reg-email').placeholder = 'mentor@email.com';
    }

    document.getElementById('auth-modal').classList.add('show');
    document.getElementById('alert-box').className = 'alert';
    document.getElementById('reg-role').value = role;
    switchTab(pendingTab);
}

function backToRolePicker() {
    document.getElementById('auth-modal').classList.remove('show');
    openRoleModal(pendingTab);
}

function closeAllModals() {
    document.getElementById('role-modal-overlay').classList.remove('show');
    document.getElementById('auth-modal').classList.remove('show');
    document.body.style.overflow = '';
}

function handleRoleOverlayClick(e) {
    if (e.target === document.getElementById('role-modal-overlay')) closeAllModals();
}

function handleAuthOverlayClick(e) {
    if (e.target === document.getElementById('auth-modal')) closeAllModals();
}

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    document.getElementById('panel-' + tab).classList.add('active');

    const roleName = { pelajar: 'Mahasiswa', mentor: 'Mentor', mitra: 'Perusahaan' }[currentRole];
    if (tab === 'masuk') {
        document.getElementById('modal-title').textContent = 'Selamat Datang Kembali';
        document.getElementById('modal-sub').textContent = `Masuk sebagai ${roleName}`;
    } else {
        document.getElementById('modal-title').textContent = 'Buat Akun Gratis';
        document.getElementById('modal-sub').textContent = `Daftar sebagai ${roleName}`;
    }
    document.getElementById('alert-box').className = 'alert';
}

function showAlert(msg, type) {
    const el = document.getElementById('alert-box');
    el.textContent = msg;
    el.className = 'alert ' + type;
}

// ============================================================
// LOGIN → kirim ke /api/login
// ============================================================
function handleMasuk(event) {
    if (event) event.preventDefault();
    const email = document.getElementById('login-email').value.trim();
    const pass  = document.getElementById('login-pass').value.trim();

    if (!email || !pass) { showAlert('Mohon isi email dan password.', 'error'); return; }
    if (!email.includes('@')) { showAlert('Format email tidak valid.', 'error'); return; }

    showAlert('Processing login...', 'success');
    document.getElementById('btn-masuk').disabled = true;

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', pass);
    formData.append('role', currentRole);

    fetch('/api/login', { 
        method: 'POST', 
        headers: {
            'Accept': 'application/json'
        },
        body: formData 
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert(data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                showAlert(data.message, 'error');
                document.getElementById('btn-masuk').disabled = false;
            }
        })
        .catch(() => {
            showAlert('Gagal terhubung ke server. Pastikan XAMPP aktif.', 'error');
            document.getElementById('btn-masuk').disabled = false;
        });
}

// ============================================================
// DAFTAR → kirim ke /api/register
// ============================================================
function handleDaftar(event) {
    if (event) event.preventDefault();
    const name  = document.getElementById('reg-name').value.trim();
    const email = document.getElementById('reg-email').value.trim();
    const pass  = document.getElementById('reg-pass').value.trim();

    if (currentRole === 'mitra') {
        const usaha = document.getElementById('reg-usaha').value.trim();
        if (!usaha) { showAlert('Mohon isi nama usaha / perusahaan.', 'error'); return; }
    }
    if (!name || !email || !pass) { showAlert('Mohon lengkapi semua field.', 'error'); return; }
    if (!email.includes('@')) { showAlert('Format email tidak valid.', 'error'); return; }
    if (pass.length < 8) { showAlert('Password minimal 8 karakter.', 'error'); return; }

    showAlert('Creating account...', 'success');
    document.getElementById('btn-daftar').disabled = true;

    const formData = new FormData();
    formData.append('nama_lengkap', name);
    formData.append('email', email);
    formData.append('password', pass);
    formData.append('role', currentRole);
    if (currentRole === 'mitra') {
        formData.append('nama_usaha', document.getElementById('reg-usaha').value.trim());
    }

    fetch('/api/register', { 
        method: 'POST', 
        headers: {
            'Accept': 'application/json'
        },
        body: formData 
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert(data.message, 'success');
                setTimeout(() => switchTab('masuk'), 1800);
            } else {
                showAlert(data.message, 'error');
            }
            document.getElementById('btn-daftar').disabled = false;
        })
        .catch(() => {
            showAlert('Gagal terhubung ke server. Pastikan XAMPP aktif.', 'error');
            document.getElementById('btn-daftar').disabled = false;
        });
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAllModals(); });

// ============================================================
// LOGOUT
// ============================================================
function handleLogout() {
    fetch('/api/logout', { method: 'POST' })
        .then(() => { window.location.href = '/'; })
        .catch(() => { window.location.href = '/'; });
}
