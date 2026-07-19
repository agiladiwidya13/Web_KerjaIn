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
        .browse-header {
            background: linear-gradient(135deg, var(--primary) 0%, #312e81 100%);
            padding: 80px 20px 40px;
            color: white;
            text-align: center;
        }
        .browse-header h1 {
            font-size: 2.5rem;
            margin-bottom: 16px;
        }
        .search-bar {
            max-width: 600px;
            margin: 0 auto;
            display: flex;
            gap: 12px;
        }
        .search-bar input {
            flex: 1;
            padding: 14px 24px;
            border-radius: 50px;
            border: 1px solid var(--dash-border);
            background: var(--dash-card);
            color: var(--text);
            font-size: 1rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.06);
            outline: none;
            transition: all 0.2s;
        }
        .search-bar input:focus {
            border-color: var(--dash-accent);
        }
        .program-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .prog-card {
            background: var(--dash-card);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--dash-border);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }
        .prog-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--dash-shadow);
        }
        .prog-banner {
            height: 120px;
            background: var(--dash-accent-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        .prog-content {
            padding: 24px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .prog-content h3 {
            font-size: 1.2rem;
            margin: 0 0 8px;
            color: var(--text);
        }
        .company-info {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .company-logo {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            object-fit: cover;
            background: var(--dash-bg);
        }
        .prog-desc {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.5;
            flex: 1;
        }
        .prog-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 16px;
            border-top: 1px solid var(--dash-border);
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>
<body style="background: var(--dash-bg);">
<script>
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
    }
</script>

<!-- NAVIGATION -->
<nav>
    <div class="logo" onclick="window.location.href='/'">
        <img src="{{ asset('image/logo-kerjain.png') }}" alt="Logo KerjaIn" class="logo-img" onerror="this.style.display='none'">
        KerjaIn
    </div>
    <div class="nav-links">
        <a href="/">Beranda</a>
        <a href="/pages/programs" class="active">Program</a>
    </div>
    <div class="nav-auth" id="nav-auth">
        <!-- Injected via JS -->
    </div>
</nav>

<div class="browse-header">
    <h1>Eksplorasi Program Unggulan</h1>
    <p style="opacity:0.8; margin-bottom: 32px;">Tingkatkan portofoliomu dengan mengerjakan real-project dari perusahaan ternama.</p>
    <div class="search-bar">
        <input type="text" id="search-input" placeholder="Cari posisi atau perusahaan..." onkeyup="if(event.key==='Enter') loadPrograms()">
        <button class="btn-dash btn-dash-primary" style="border-radius:50px; padding: 0 24px;" onclick="loadPrograms()">Cari</button>
    </div>
</div>

<div class="program-grid" id="program-container">
    <!-- Programs injected here -->
    <div class="empty-state" style="grid-column: 1/-1;">
        <p>Memuat program...</p>
    </div>
</div>

<script>
// Check login state
let currentUser = null;
fetch('/api/session')
    .then(r => r.json())
    .then(d => {
        if (d.loggedIn) {
            currentUser = d.user;
            const profileUrl = {
                pelajar: '/pages/pelajar/dashboard',
                mentor:  '/pages/mentor/dashboard',
                mitra:   '/pages/mitra/dashboard'
            };
            document.getElementById('nav-auth').innerHTML = `
                <a href="${profileUrl[d.user.role]}" class="btn-outline" style="font-weight:600;"><span class="material-icons icon-inline">bar_chart</span>Dashboard</a>
                <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
            `;
        } else {
            document.getElementById('nav-auth').innerHTML = `
                <a href="#" class="btn-outline" onclick="window.location.href='/?tab=masuk'">Masuk</a>
                <a href="#" class="btn-solid" onclick="window.location.href='/?tab=daftar'">Daftar Gratis</a>
            `;
        }
    });

function handleLogout() {
    fetch('/api/logout', { method: 'POST' }).then(() => window.location.reload());
}

function loadPrograms() {
    const q = document.getElementById('search-input').value;
    let url = '/api/programs';
    if (q) url += '?q=' + encodeURIComponent(q);

    fetch(url)
        .then(r => r.json())
        .then(d => {
            const container = document.getElementById('program-container');
            if (d.status !== 'success' || !d.data.length) {
                container.innerHTML = `
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <div class="empty-icon"><span class="material-icons icon-large">search</span></div>
                        <h3>Tidak ada program ditemukan</h3>
                        <p>Coba gunakan kata kunci lain.</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = d.data.map(p => {
                const logo = p.logo ? `<img src="/${p.logo}" class="company-logo">` : `<div class="company-logo"><span class="material-icons" style="font-size:2rem;">business</span></div>`;
                const kuota = p.kuota ? `<span style="color:var(--text-muted)">${p.enrolled}/${p.kuota} Peserta</span>` : `<span style="color:var(--text-muted)">${p.enrolled} Peserta</span>`;
                const bannerHtml = p.cover_image 
                    ? `<img src="${p.cover_image}" style="width:100%; height:100%; object-fit:cover;">`
                    : `<span class="material-icons" style="font-size:3rem;">work</span>`;
                
                return `
                <div class="prog-card" onclick="window.location.href='/pages/programs/${p.id}'">
                    <div class="prog-banner" style="overflow:hidden; display:flex; align-items:center; justify-content:center;">${bannerHtml}</div>
                    <div class="prog-content">
                        <h3>${p.judul}</h3>
                        <div class="company-info">
                            ${logo} ${p.perusahaan}
                        </div>
                        ${p.registrasi_mulai && p.registrasi_selesai ? `
                            <div style="font-size:0.8rem; color:var(--text-muted); margin-bottom:12px; display:inline-flex; align-items:center; gap:4px;">
                                <span class="material-icons" style="font-size:14px;">event_note</span>
                                Pendaftaran: ${p.registrasi_mulai} - ${p.registrasi_selesai}
                            </div>
                        ` : ''}
                        <p class="prog-desc">${p.deskripsi || 'Tidak ada deskripsi.'}</p>
                        <div class="prog-footer">
                            <span class="badge badge-primary">${p.bidang || 'Umum'}</span>
                            ${kuota}
                        </div>
                    </div>
                </div>
                `;
            }).join('');
        });
}

loadPrograms();
</script>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
