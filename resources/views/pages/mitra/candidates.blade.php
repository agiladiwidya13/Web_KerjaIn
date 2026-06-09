<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Kandidat - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>

<nav>
    <div class="logo" onclick="window.location.href='/'">
        <img src="{{ asset('image/logo-kerjain.png') }}" alt="Logo KerjaIn" class="logo-img" onerror="this.style.display='none'">
        KerjaIn
    </div>
    <div class="nav-links">
        <span class="nav-role-badge" style="background:#fef3c7;color:#92400e;"><span class="material-icons" style="font-size: 18px; margin-right: 6px; display: inline-flex; vertical-align: middle;">business</span>Mitra</span>
    </div>
    <div class="nav-auth">
        <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
    </div>
</nav>

<div class="dashboard-container">
    <aside class="dash-sidebar">
        <div class="sidebar-header">
            <h3>Menu Mitra</h3>
        </div>
        <a class="nav-item" href="/pages/mitra/dashboard">
            <span class="material-icons nav-icon">bar_chart</span> Dashboard
        </a>
        <a class="nav-item" href="/pages/mitra/dashboard?section=programs">
            <span class="material-icons nav-icon">menu_book</span> Kelola Program
        </a>
        <a class="nav-item" href="/pages/mitra/dashboard?section=mentors">
            <span class="material-icons nav-icon">person</span> Mentor Saya
        </a>
        <a class="nav-item active" href="/pages/mitra/candidates">
            <span class="material-icons nav-icon">search</span> Cari Kandidat
        </a>
        <a class="nav-item" href="/pages/mitra/profile">
            <span class="material-icons nav-icon">account_circle</span> Profil Perusahaan
        </a>
    </aside>

    <main class="dash-main">
        <div class="dash-welcome">
            <h1>Cari Kandidat Talenta</h1>
            <p style="opacity: 0.85; font-size: 0.95rem;">Temukan talenta terbaik yang telah menyelesaikan program KerjaIn.</p>
        </div>

        <div class="dash-card" style="margin-bottom: 24px;">
            <div style="display: flex; gap: 16px;">
                <input type="text" id="search-universitas" placeholder="Filter Universitas..." style="flex: 1; padding: 12px; border-radius: 8px; border: 1px solid var(--dash-border);">
                <input type="text" id="search-jurusan" placeholder="Filter Jurusan..." style="flex: 1; padding: 12px; border-radius: 8px; border: 1px solid var(--dash-border);">
                <button class="btn-dash btn-dash-primary" onclick="loadCandidates()">Cari Talenta</button>
            </div>
        </div>

        <div id="candidates-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            <div class="dash-card" style="grid-column: 1 / -1; text-align: center; color: var(--text-muted);">
                Memuat kandidat...
            </div>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadCandidates);

    function handleLogout() {
        fetch('/api/logout', { method: 'POST' })
            .then(() => window.location.href = '/')
            .catch(() => window.location.href = '/');
    }

    function loadCandidates() {
        const univ = document.getElementById('search-universitas').value;
        const jurusan = document.getElementById('search-jurusan').value;
        
        let url = `/api/mitra/candidates?`;
        if (univ) url += `universitas=${encodeURIComponent(univ)}&`;
        if (jurusan) url += `jurusan=${encodeURIComponent(jurusan)}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    renderCandidates(data.data);
                } else {
                    document.getElementById('candidates-container').innerHTML = `<div class="dash-card" style="grid-column: 1 / -1;">Gagal memuat kandidat.</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('candidates-container').innerHTML = `<div class="dash-card" style="grid-column: 1 / -1;">Terjadi kesalahan.</div>`;
            });
    }

    function renderCandidates(candidates) {
        const container = document.getElementById('candidates-container');
        if (candidates.length === 0) {
            container.innerHTML = `<div class="dash-card" style="grid-column: 1 / -1; text-align: center;">Tidak ada kandidat yang ditemukan.</div>`;
            return;
        }

        container.innerHTML = candidates.map(c => `
            <div class="dash-card" style="display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                    <div>
                        <h4 style="margin: 0 0 4px; font-size: 1.1rem;">${c.nama}</h4>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">${c.universitas || 'Universitas tidak diketahui'}</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">${c.jurusan || 'Jurusan tidak diketahui'}</div>
                    </div>
                    <div style="background: var(--warning); color: #fff; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;">
                        <span class="material-icons" style="font-size: 14px; display: inline-flex; align-items: center; justify-content: center;">auto_awesome</span>${c.total_poin || 0} Poin
                    </div>
                </div>
                <p style="font-size: 0.9rem; color: var(--text-muted); flex: 1;">${c.bio || 'Belum ada bio.'}</p>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--dash-border);">
                    <div style="font-size: 0.85rem; color: var(--dash-accent); font-weight: 600;">
                        <span class="material-icons icon-inline">work</span>${c.total_portofolio} Portofolio
                    </div>
                    <a href="/profil/${c.id}" target="_blank" class="btn-dash btn-dash-outline" style="padding: 6px 12px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px;">Lihat Profil <span class="material-icons" style="font-size: 14px;">open_in_new</span></a>
                </div>
            </div>
        `).join('');
    }
</script>
<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
