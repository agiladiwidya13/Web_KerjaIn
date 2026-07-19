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
    @include('partials.mitra-sidebar')

    <main class="dash-main">
        <div class="dash-welcome">
            <h1>Cari Kandidat Talenta</h1>
            <p style="opacity: 0.85; font-size: 0.95rem;">Temukan talenta terbaik yang telah menyelesaikan program KerjaIn.</p>
        </div>

        <div class="dash-card" style="margin-bottom: 24px;">
            <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; align-items: end;">
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label for="search-universitas" style="font-weight:600;">Universitas</label>
                    <select id="search-universitas" style="width:100%; padding: 12px; border-radius: 8px; border: 1px solid var(--dash-border);">
                        <option value="">Semua universitas</option>
                        <option value="UPN Veteran Jawa Timur">UPN Veteran Jawa Timur</option>
                        <option value="Universitas Indonesia">Universitas Indonesia</option>
                        <option value="Institut Teknologi Bandung">Institut Teknologi Bandung</option>
                        <option value="Universitas Gadjah Mada">Universitas Gadjah Mada</option>
                        <option value="Institut Teknologi Sepuluh Nopember">Institut Teknologi Sepuluh Nopember</option>
                        <option value="Universitas Padjadjaran">Universitas Padjadjaran</option>
                        <option value="Universitas Brawijaya">Universitas Brawijaya</option>
                        <option value="Universitas Airlangga">Universitas Airlangga</option>
                        <option value="Universitas Diponegoro">Universitas Diponegoro</option>
                        <option value="Universitas Negeri Yogyakarta">Universitas Negeri Yogyakarta</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <div id="universitas-custom-wrap" style="display:none; gap:8px;">
                        <input type="text" id="search-universitas-custom" placeholder="Ketikan universitas lain..." style="width:100%; padding: 12px; border-radius: 8px; border: 1px solid var(--dash-border);">
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; gap:8px;">
                    <label for="search-jurusan" style="font-weight:600;">Jurusan</label>
                    <select id="search-jurusan" style="width:100%; padding: 12px; border-radius: 8px; border: 1px solid var(--dash-border);">
                        <option value="">Semua jurusan</option>
                        <option value="Teknik Informatika">Teknik Informatika</option>
                        <option value="Sistem Informasi">Sistem Informasi</option>
                        <option value="Manajemen">Manajemen</option>
                        <option value="Akuntansi">Akuntansi</option>
                        <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                        <option value="Teknik Elektro">Teknik Elektro</option>
                        <option value="Teknik Sipil">Teknik Sipil</option>
                        <option value="Psikologi">Psikologi</option>
                        <option value="Ilmu Komunikasi">Ilmu Komunikasi</option>
                        <option value="Pendidikan Bahasa Inggris">Pendidikan Bahasa Inggris</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    <div id="jurusan-custom-wrap" style="display:none; gap:8px;">
                        <input type="text" id="search-jurusan-custom" placeholder="Ketikan jurusan lain..." style="width:100%; padding: 12px; border-radius: 8px; border: 1px solid var(--dash-border);">
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <button class="btn-dash btn-dash-primary" onclick="loadCandidates()">Cari Talenta</button>
                </div>
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
        let univ = document.getElementById('search-universitas').value;
        const univCustom = document.getElementById('search-universitas-custom').value.trim();
        let jurusan = document.getElementById('search-jurusan').value;
        const jurusanCustom = document.getElementById('search-jurusan-custom').value.trim();

        if (univ === 'Lainnya') {
            univ = univCustom;
        }
        if (jurusan === 'Lainnya') {
            jurusan = jurusanCustom;
        }

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

    function updateCustomFields() {
        const univSelect = document.getElementById('search-universitas');
        const jurusanSelect = document.getElementById('search-jurusan');
        const univCustomWrap = document.getElementById('universitas-custom-wrap');
        const jurusanCustomWrap = document.getElementById('jurusan-custom-wrap');

        if (univSelect.value === 'Lainnya') {
            univSelect.style.display = 'none';
            univCustomWrap.style.display = 'flex';
        } else {
            univSelect.style.display = 'block';
            univCustomWrap.style.display = 'none';
            document.getElementById('search-universitas-custom').value = '';
        }

        if (jurusanSelect.value === 'Lainnya') {
            jurusanSelect.style.display = 'none';
            jurusanCustomWrap.style.display = 'flex';
        } else {
            jurusanSelect.style.display = 'block';
            jurusanCustomWrap.style.display = 'none';
            document.getElementById('search-jurusan-custom').value = '';
        }
    }

    document.getElementById('search-universitas').addEventListener('change', updateCustomFields);
    document.getElementById('search-jurusan').addEventListener('change', updateCustomFields);
    document.getElementById('search-universitas-reset').addEventListener('click', function() {
        document.getElementById('search-universitas').value = '';
        updateCustomFields();
    });
    document.getElementById('search-jurusan-reset').addEventListener('click', function() {
        document.getElementById('search-jurusan').value = '';
        updateCustomFields();
    });

    updateCustomFields();

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
                    <a href="/profil/${c.id}" class="btn-dash btn-dash-outline" style="padding: 6px 12px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 6px; color:#2563eb; border-color:#2563eb;">Lihat Profil</a>
                </div>
            </div>
        `).join('');
    }
</script>
<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
