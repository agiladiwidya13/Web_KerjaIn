<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papan Peringkat - KerjaIn</title>
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
        <span class="nav-role-badge" style="background:#dbeafe;color:#1e3a8a;"><span class="material-icons" style="font-size: 18px; margin-right: 6px; display: inline-flex; vertical-align: middle;">school</span>Pelajar</span>
    </div>
    <div class="nav-auth">
        <a href="#" onclick="handleLogout()" class="btn-solid" style="background:#ef4444;">Keluar</a>
    </div>
</nav>

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
        <a class="nav-item" href="/pages/programs">
            <span class="material-icons nav-icon">search</span> Cari Program
        </a>
        <a class="nav-item active" href="/pages/pelajar/leaderboard">
            <span class="material-icons nav-icon">emoji_events</span> Leaderboard
        </a>
        <a class="nav-item" href="/pages/pelajar/profile">
            <span class="material-icons nav-icon">account_circle</span> Profil Saya
        </a>
        <a class="nav-item" href="/pages/messages">
            <span class="material-icons nav-icon">email</span> Pesan
        </a>
    </aside>

    <main class="dash-main">
        <div class="dash-welcome" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
            <h2 style="display: flex; align-items: center; gap: 8px; margin: 0 0 8px;">Papan Peringkat Talenta <span class="material-icons" style="font-size: 1.8rem;">emoji_events</span></h2>
            <p style="opacity: 0.9; font-size: 0.95rem;">Lihat posisimu dan bersaing menjadi talenta terbaik di KerjaIn!</p>
        </div>

        <div class="dash-card">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--dash-border);">
                        <th style="padding: 16px;">Peringkat</th>
                        <th style="padding: 16px;">Nama</th>
                        <th style="padding: 16px;">Asal Institusi</th>
                        <th style="padding: 16px;">Total Poin</th>
                        <th style="padding: 16px;">Badges</th>
                    </tr>
                </thead>
                <tbody id="leaderboard-table">
                    <tr>
                        <td colspan="5" style="padding: 20px; text-align: center; color: var(--text-muted);">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', loadLeaderboard);

    function handleLogout() {
        fetch('/api/logout', { method: 'POST' })
            .then(() => window.location.href = '/')
            .catch(() => window.location.href = '/');
    }

    function loadLeaderboard() {
        fetch('/api/pelajar/leaderboard')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    renderLeaderboard(data.data);
                } else {
                    document.getElementById('leaderboard-table').innerHTML = `<tr><td colspan="5" style="padding: 20px; text-align: center;">Gagal memuat papan peringkat.</td></tr>`;
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('leaderboard-table').innerHTML = `<tr><td colspan="5" style="padding: 20px; text-align: center;">Terjadi kesalahan.</td></tr>`;
            });
    }

    function renderLeaderboard(data) {
        const table = document.getElementById('leaderboard-table');
        if (data.length === 0) {
            table.innerHTML = `<tr><td colspan="5" style="padding: 20px; text-align: center;">Belum ada data.</td></tr>`;
            return;
        }

        table.innerHTML = data.map(p => {
            let rankStyle = '';
            let rankEmoji = '';
            if (p.rank === 1) { rankStyle = 'font-weight: 800; color: #fbbf24; font-size: 1.2rem;'; rankEmoji = '<span class="material-icons">emoji_events</span>'; }
            else if (p.rank === 2) { rankStyle = 'font-weight: 700; color: #9ca3af; font-size: 1.1rem;'; rankEmoji = '<span class="material-icons">emoji_events</span>'; }
            else if (p.rank === 3) { rankStyle = 'font-weight: 700; color: #b45309; font-size: 1.1rem;'; rankEmoji = '<span class="material-icons">emoji_events</span>'; }
            
            const badgesHtml = p.badges.map(b => `<span title="${b.nama}" style="font-size: 1.2rem; margin-right: 4px;">${b.icon}</span>`).join('');

            return `
                <tr style="border-bottom: 1px solid var(--dash-border);">
                    <td style="padding: 16px; ${rankStyle}">${rankEmoji} #${p.rank}</td>
                    <td style="padding: 16px; font-weight: 600;">
                        <a href="/profil/${p.id}" style="color: var(--primary); text-decoration: none;">${p.nama}</a>
                    </td>
                    <td style="padding: 16px; color: var(--text-muted);">${p.universitas || '-'}</td>
                    <td style="padding: 16px; font-weight: 700; color: var(--warning);"><span class="material-icons icon-inline">auto_awesome</span>${p.total_poin}</td>
                    <td style="padding: 16px;">${badgesHtml || '<span style="color:var(--text-muted);font-size:0.85rem;">-</span>'}</td>
                </tr>
            `;
        }).join('');
    }
</script>
<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
