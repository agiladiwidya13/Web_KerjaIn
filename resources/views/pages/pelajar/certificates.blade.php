<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Saya - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 24px;
        }
        .cert-card {
            background: var(--dash-card);
            border: 1px solid var(--dash-border);
            border-radius: 20px;
            padding: 24px;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 18px;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }
        .cert-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 24px 48px rgba(0,0,0,.08);
            border-color: rgba(79,70,229,0.25);
        }
        .cert-card-top {
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }
        .cert-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            background: rgba(79,70,229,.1);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .cert-card-icon img {
            width: 28px;
            height: 28px;
            object-fit: contain;
        }
        .cert-card-title {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }
        .cert-card-title h3 {
            margin: 0;
            font-size: 1.18rem;
            line-height: 1.3;
        }
        .cert-card-title p {
            margin: 0;
            color: var(--text-muted);
            line-height: 1.4;
            font-size: 0.96rem;
        }
        .cert-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .cert-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .cert-link-row {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .cert-link {
            color: #4f46e5;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            background: rgba(79, 70, 229, 0.08);
            border: 1px solid rgba(79, 70, 229, 0.16);
            padding: 10px 16px;
            border-radius: 999px;
            transition: background 0.2s ease, transform 0.2s ease;
        }
        .cert-link:hover {
            background: rgba(79, 70, 229, 0.18);
            transform: translateY(-1px);
            text-decoration: none;
        }
        .cert-card {
            background: var(--dash-card);
            border: 1px solid var(--dash-border);
            border-radius: 20px;
            padding: 24px;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 18px;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
            color: inherit;
            text-decoration: none;
        }
        .cert-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 24px 48px rgba(0,0,0,.08);
            border-color: rgba(79,70,229,0.25);
        }
    </style>
</head>
<body>

<nav>
    <div class="logo" onclick="window.location.href='/pages/pelajar/dashboard'">
        <img src="{{ asset('image/logo-kerjain.png') }}" alt="Logo KerjaIn" class="logo-img" onerror="this.style.display='none'">
        KerjaIn
    </div>
    <div class="nav-links">
        <span class="nav-role-badge"><span class="material-icons" style="font-size: 18px; margin-right: 6px; display: inline-flex; vertical-align: middle;">school</span>Mahasiswa</span>
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
            <h3>Menu Pelajar</h3>
        </div>
        <a class="nav-item" href="/pages/pelajar/dashboard">
            <span class="material-icons nav-icon">bar_chart</span> Dashboard
        </a>
        <a class="nav-item" href="/pages/pelajar/dashboard?section=programs">
            <span class="material-icons nav-icon">menu_book</span> Program Saya
        </a>
        <a class="nav-item" href="/pages/pelajar/certificates">
            <span class="material-icons nav-icon">emoji_events</span> Sertifikat
        </a>
        <a class="nav-item" href="/pages/pelajar/portfolios">
            <span class="material-icons nav-icon">badge</span> Portofolio
        </a>
        <a class="nav-item" href="/pages/programs">
            <span class="material-icons nav-icon">search</span> Cari Program
        </a>
        <a class="nav-item" href="/pages/pelajar/profile">
            <span class="material-icons nav-icon">account_circle</span> Profil Saya
        </a>
    </aside>

    <main class="dash-main">
        <div style="margin-bottom:32px;">
            <h1 style="margin:0 0 8px;">Sertifikat Saya</h1>
            <p style="color:var(--text-muted);margin:0;">Daftar sertifikat yang kamu peroleh dari penyelesaian program.</p>
        </div>

        <div class="cards-grid" id="certificates-grid">
            <div class="empty-state">
                <div class="empty-icon"><span class="material-icons icon-large">mail_outline</span></div>
                <h3>Belum ada sertifikat</h3>
                <p>Selesaikan program pertama kamu untuk mulai mendapatkan sertifikat.</p>
            </div>
        </div>
    </main>
</div>

<script>
(function() {
    fetch('/api/session')
        .then(r => r.json())
        .then(d => {
            if (!d.loggedIn || d.user.role !== 'pelajar') {
                window.location.href = '/';
                return;
            }
            document.getElementById('nav-nama').textContent = d.user.nama.split(' ')[0];
            document.querySelector('.nav-item[href="/pages/pelajar/certificates"]').classList.add('active');
            loadCertificates();
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

function loadCertificates() {
    fetch('/api/pelajar/certificates')
        .then(r => r.json())
        .then(d => {
            if (d.status !== 'success') return;
            const container = document.getElementById('certificates-grid');
            if (!d.data.length) return;

            container.innerHTML = d.data.map(cert => `
                <a href="/sertifikat/${cert.id}" class="cert-card">
                    <div class="cert-card-top">
                        <div class="cert-card-icon">
                            <img src="${cert.logo || '/image/logo-kerjain.png'}" alt="Logo">
                        </div>
                        <div class="cert-card-title">
                            <h3>${cert.program}</h3>
                            <p>${cert.perusahaan}</p>
                        </div>
                    </div>
                    <div class="cert-meta">
                        <span>No. ${cert.nomor_sertifikat}</span>
                        <span>Diterbitkan: ${cert.issued_at}</span>
                    </div>
                    <div class="cert-link-row">
                        <span class="cert-link">Lihat Sertifikat <span class="material-icons">arrow_forward</span></span>
                    </div>
                </a>
            `).join('');
        })
        .catch(err => {
            console.error('Load certificates failed', err);
        });
}
</script>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
