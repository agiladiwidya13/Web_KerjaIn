<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portofolio Saya - KerjaIn</title>
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
        .portfolio-card {
            background: var(--dash-card);
            border: 1px solid var(--dash-border);
            border-radius: 20px;
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .portfolio-card h3 {
            margin: 0;
            font-size: 1.2rem;
        }
        .portfolio-card p {
            margin: 0;
            color: var(--text-muted);
        }
        .portfolio-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }
        .portfolio-tag {
            background: rgba(59, 130, 246, 0.08);
            color: #2563eb;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.9rem;
        }
        .task-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 10px;
        }
        .task-item {
            background: var(--dash-bg);
            border: 1px solid var(--dash-border);
            border-radius: 14px;
            padding: 12px 14px;
        }
        .task-item span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.95rem;
            color: var(--text-muted);
        }
        .detail-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .detail-modal.show {
            display: flex;
        }
        .detail-modal-content {
            background: var(--dash-card);
            border-radius: 20px;
            padding: 28px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }
        .detail-modal-header {
            margin-bottom: 20px;
            border-bottom: 1px solid var(--dash-border);
            padding-bottom: 16px;
        }
        .detail-modal-header h2 {
            margin: 0 0 4px;
        }
        .detail-modal-header p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        .cert-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }

        .modal-close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: transparent;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 6px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .modal-close:hover { background: rgba(0,0,0,0.04); }
        .modal-close .material-icons { font-size: 20px; }
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
            <h1 style="margin:0 0 8px;">Portofolio Saya</h1>
            <p style="color:var(--text-muted);margin:0;">Lihat ringkasan portofolio hasil penyelesaian program dan task.</p>
        </div>

        <div class="cards-grid" id="portfolios-grid">
            <div class="empty-state">
                <div class="empty-icon"><span class="material-icons icon-large">mail_outline</span></div>
                <h3>Belum ada portofolio</h3>
                <p>Selesaikan program untuk mulai membuat portofolio.</p>
            </div>
        </div>
    </main>
</div>

<!-- Portfolio Detail Modal -->
<div class="detail-modal" id="portfolio-modal" onclick="if(event.target===this)this.classList.remove('show')">
    <div class="detail-modal-content">
        <button class="modal-close" aria-label="Tutup" title="Tutup" onclick="document.getElementById('portfolio-modal').classList.remove('show')">
            <span class="material-icons">close</span>
        </button>
        <div class="detail-modal-header">
            <h2 id="modal-program">-</h2>
            <p id="modal-perusahaan">-</p>
        </div>
        <ul class="task-list" id="modal-tasks"></ul>
    </div>
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
            document.querySelector('.nav-item[href="/pages/pelajar/portfolios"]').classList.add('active');
            loadPortfolios();
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

function escapeHtml(value) {
    return String(value || '').replace(/[&<>'"]/g, function (char) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[char];
    });
}

function renderEmptyPortfolioState(container) {
    container.innerHTML = `
        <div class="empty-state">
            <div class="empty-icon"><span class="material-icons icon-large">mail_outline</span></div>
            <h3>Belum ada portofolio</h3>
            <p>Selesaikan program untuk mulai membuat portofolio.</p>
        </div>
    `;
}

function loadPortfolios() {
    fetch('/api/pelajar/portfolios')
        .then(r => r.json())
        .then(d => {
            const container = document.getElementById('portfolios-grid');
            if (d.status !== 'success') {
                renderEmptyPortfolioState(container);
                return;
            }

            if (!d.data.length) {
                renderEmptyPortfolioState(container);
                return;
            }

            container.innerHTML = '';

            d.data.forEach(p => {
                const card = document.createElement('div');
                card.className = 'portfolio-card';
                card.innerHTML = `
                    <div class="portfolio-meta">
                        <div>
                            <h3>${escapeHtml(p.program)}</h3>
                            <p>${escapeHtml(p.perusahaan)} · ${escapeHtml(p.bidang || 'Umum')}</p>
                        </div>
                        <div class="portfolio-tag">${escapeHtml(p.created_at)}</div>
                    </div>
                    <p>${p.tasks.length} tugas selesai</p>
                `;

                const footer = document.createElement('div');
                footer.style.cssText = 'display:flex;justify-content:flex-end;';
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'cert-link';
                button.style.cssText = 'border:none;background:none;cursor:pointer;text-decoration:none;';
                button.textContent = 'Lihat Selengkapnya →';
                button.addEventListener('click', () => showPortfolioDetail(p));
                footer.appendChild(button);
                card.appendChild(footer);
                container.appendChild(card);
            });
        })
        .catch(err => {
            console.error('Load portfolios failed', err);
            const container = document.getElementById('portfolios-grid');
            renderEmptyPortfolioState(container);
        });
    
    setTimeout(loadPortfolios, 30000);
}

function showPortfolioDetail(portfolio) {
    document.getElementById('modal-program').textContent = portfolio.program;
    document.getElementById('modal-perusahaan').textContent = `${portfolio.perusahaan} · ${portfolio.bidang || 'Umum'}`;
    document.getElementById('modal-tasks').innerHTML = portfolio.tasks.map((t, i) => `
        <li class="task-item">
            <strong>${i+1}. ${escapeHtml(t.judul)}</strong>
            <div style="margin-top:8px;font-size:0.9rem;display:flex;align-items:center;gap:6px;">
                <span class="material-icons" style="font-size:16px;">check_circle</span>
                <span>${escapeHtml(t.status || 'Belum dinilai')}</span>
                ${t.nilai ? `<span style="margin-left:auto;font-weight:600;">${escapeHtml(t.nilai)} pts</span>` : ''}
            </div>
            ${t.file_url ? `<a href="/${escapeHtml(t.file_url)}" target="_blank" style="color:#3b82f6;font-size:0.9rem;margin-top:8px;display:inline-flex;align-items:center;gap:6px;text-decoration:none;"><span class="material-icons" style="font-size:16px;">attach_file</span>Lihat File</a>` : ''}
        </li>
    `).join('');
    document.getElementById('portfolio-modal').classList.add('show');
}
</script>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
