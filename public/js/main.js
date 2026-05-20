// js/main.js
// Script utama untuk index.html
// Menangani: login, daftar, cek session, navigasi modal

const programs = [
    { company:"Bank Mandiri", title:"Investment Banking Virtual Experience", hours:"4-5 Jam", cat:"finance", img:"https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=400&h=200&q=75&auto=format&fit=crop", emoji:"🏦" },
    { company:"GoTo Group", title:"Software Engineering Simulation", hours:"5-6 Jam", cat:"tech", img:"https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=200&q=75&auto=format&fit=crop", emoji:"💻" },
    { company:"BCG Indonesia", title:"Strategy Consulting Job Simulation", hours:"3-4 Jam", cat:"consulting", img:"https://images.unsplash.com/photo-1552664730-d307ca884978?w=400&h=200&q=75&auto=format&fit=crop", emoji:"📊" },
    { company:"Unilever", title:"Digital Marketing Simulation", hours:"2-3 Jam", cat:"marketing", img:"https://images.unsplash.com/photo-1432888622747-4eb9a8efeb07?w=400&h=200&q=75&auto=format&fit=crop", emoji:"📱" },
    { company:"Tokopedia", title:"Product Management Experience", hours:"4-5 Jam", cat:"tech", img:"https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=400&h=200&q=75&auto=format&fit=crop", emoji:"🛍️" },
    { company:"BCA", title:"Retail Banking Simulation", hours:"3-4 Jam", cat:"finance", img:"https://images.unsplash.com/photo-1601597111158-2fceff292cdc?w=400&h=200&q=75&auto=format&fit=crop", emoji:"💳" },
    { company:"Deloitte Indonesia", title:"Tax & Advisory Consulting", hours:"4-6 Jam", cat:"consulting", img:"https://images.unsplash.com/photo-1556740738-b6a63e27c4df?w=400&h=200&q=75&auto=format&fit=crop", emoji:"⚖️" },
    { company:"Shopee", title:"Growth Marketing Simulation", hours:"2-3 Jam", cat:"marketing", img:"https://images.unsplash.com/photo-1533750349088-cd871a92f312?w=400&h=200&q=75&auto=format&fit=crop", emoji:"🎯" },
];

let currentFilter = 'semua';
let currentRole   = 'pelajar';
let pendingTab    = 'masuk';

// ============================================================
// CEK SESSION SAAT HALAMAN DIBUKA
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    checkSession();
    renderHomeGrid();
});

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
        pelajar: '/pages/pelajar/profile',
        mentor:  '/pages/mentor/profile',
        mitra:   '/pages/mitra/profile'
    };
    document.getElementById('nav-auth').innerHTML = `
        <a href="${profileUrl[user.role]}" class="btn-outline" style="font-weight:600;">
            👤 ${user.nama.split(' ')[0]}
        </a>
        <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
    `;
}

// ============================================================
// RENDER KARTU PROGRAM
// ============================================================
function createCard(p) {
    return `<div class="card">
        <div class="card-image">
            <img src="${p.img}" alt="${p.title}" onerror="this.parentElement.innerHTML='<div style=\'background:#eef2ff;height:100%;display:flex;align-items:center;justify-content:center;font-size:3rem;\'>${p.emoji}</div>'" loading="lazy">
            <div class="card-image-overlay"><div class="card-emoji-badge">${p.emoji}</div></div>
        </div>
        <div class="card-content">
            <p class="company-name">${p.company}</p>
            <h3 class="job-title">${p.title}</h3>
            <div class="tags">
                <span class="tag-pill">⏱ ${p.hours}</span>
                <span class="tag-pill">🆓 Gratis</span>
                <span class="tag-pill">🎓 Sertifikat</span>
            </div>
            <button class="btn-start" onclick="openRoleModal('daftar')">Ambil Peluang →</button>
        </div>
    </div>`;
}

function renderHomeGrid() {
    document.getElementById('home-grid').innerHTML = programs.slice(0, 4).map(createCard).join('');
}

function renderExploreGrid() {
    const q = (document.getElementById('search-input').value || '').toLowerCase();
    const filtered = programs.filter(p => {
        const matchCat = currentFilter === 'semua' || p.cat === currentFilter;
        const matchQ   = !q || p.company.toLowerCase().includes(q) || p.title.toLowerCase().includes(q);
        return matchCat && matchQ;
    });
    document.getElementById('explore-grid').innerHTML = filtered.map(createCard).join('');
    document.getElementById('empty-state').style.display = filtered.length ? 'none' : 'block';
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
        badge.textContent = '🎓 Mahasiswa';
        badge.className = 'modal-role-badge student';
        mitraExtra.style.display = 'none';
        document.getElementById('reg-name').placeholder = 'Nama lengkap kamu';
        document.getElementById('login-email').placeholder = 'mahasiswa@email.com';
        document.getElementById('reg-email').placeholder = 'mahasiswa@email.com';
    } else if (role === 'mitra') {
        badge.textContent = '🏢 Perusahaan';
        badge.className = 'modal-role-badge company';
        mitraExtra.style.display = 'block';
        document.getElementById('reg-name').placeholder = 'Nama PIC / HR';
        document.getElementById('login-email').placeholder = 'hr@perusahaan.com';
        document.getElementById('reg-email').placeholder = 'hr@perusahaan.com';
    } else if (role === 'mentor') {
        badge.textContent = '👨‍🏫 Mentor';
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

    showAlert('⏳ Memproses login...', 'success');
    document.getElementById('btn-masuk').disabled = true;

    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', pass);
    formData.append('role', currentRole);

    fetch('/api/login', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('✅ ' + data.message, 'success');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1000);
            } else {
                showAlert('❌ ' + data.message, 'error');
                document.getElementById('btn-masuk').disabled = false;
            }
        })
        .catch(() => {
            showAlert('❌ Gagal terhubung ke server. Pastikan XAMPP aktif.', 'error');
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

    showAlert('⏳ Membuat akun...', 'success');
    document.getElementById('btn-daftar').disabled = true;

    const formData = new FormData();
    formData.append('nama_lengkap', name);
    formData.append('email', email);
    formData.append('password', pass);
    formData.append('role', currentRole);
    if (currentRole === 'mitra') {
        formData.append('nama_usaha', document.getElementById('reg-usaha').value.trim());
    }

    fetch('/api/register', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                showAlert('🎉 ' + data.message, 'success');
                setTimeout(() => switchTab('masuk'), 1800);
            } else {
                showAlert('❌ ' + data.message, 'error');
            }
            document.getElementById('btn-daftar').disabled = false;
        })
        .catch(() => {
            showAlert('❌ Gagal terhubung ke server. Pastikan XAMPP aktif.', 'error');
            document.getElementById('btn-daftar').disabled = false;
        });
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAllModals(); });

// ============================================================
// LOGOUT
// ============================================================
function handleLogout() {
    const confirmed = confirm('Apakah Anda yakin ingin keluar?');
    if (!confirmed) return;
    
    fetch('/api/logout', { method: 'POST' })
        .then(() => { window.location.href = '/'; })
        .catch(() => { window.location.href = '/'; });
}
