<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pelajar - {{ $pelajar->user->nama_lengkap }}</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--dash-bg);
            overflow: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        nav {
            flex-shrink: 0;
            height: 70px;
            background: white;
            border-bottom: 1px solid var(--dash-border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            box-sizing: border-box;
        }
        .main-wrapper {
            flex: 1;
            display: flex;
            gap: 24px;
            padding: 24px;
            box-sizing: border-box;
            min-height: 0;
        }
        .profile-sidebar {
            width: 360px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex-shrink: 0;
            min-height: 0;
        }
        .profile-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-height: 0;
        }
        .profile-card {
            background: linear-gradient(135deg, var(--primary) 0%, #312e81 100%);
            border-radius: 16px;
            padding: 24px;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.15);
            flex-shrink: 0;
        }
        .profile-avatar-small {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 16px;
        }
        .profile-name {
            font-size: 1.35rem;
            font-weight: 700;
            margin: 0 0 6px;
            letter-spacing: -0.5px;
        }
        .profile-bio {
            font-size: 0.85rem;
            opacity: 0.9;
            margin: 0 0 16px;
            line-height: 1.4;
        }
        .badge-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }
        .profile-badge {
            background: rgba(255, 255, 255, 0.12);
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            padding: 8px 12px;
            border-radius: 10px;
            text-align: left;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .card-stat {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--dash-border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex: 1;
            min-height: 0;
        }
        .section-title {
            font-size: 1.15rem;
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
        }
        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--dash-border);
        }
        .stat-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }
        .content-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--dash-border);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
        }
        .scrollable-list {
            flex: 1;
            overflow-y: auto;
            padding-right: 8px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .scrollable-list::-webkit-scrollbar {
            width: 6px;
        }
        .scrollable-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .scrollable-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .scrollable-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .portfolio-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 16px;
            border: 1px solid var(--dash-border);
            transition: all 0.2s;
        }
        .portfolio-item:hover {
            border-color: var(--primary);
            background: #f1f5f9;
        }
        .cert-item {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #f8fafc;
            padding: 14px 18px;
            border-radius: 12px;
            border: 1px solid var(--dash-border);
            text-decoration: none;
            color: inherit;
            transition: all 0.2s;
        }
        .cert-item:hover {
            border-color: var(--primary);
            background: #f1f5f9;
        }
        .cert-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #eef2ff;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 0.9rem;
            height: 100%;
            padding: 32px;
            text-align: center;
            gap: 8px;
        }

        /* ===== DARK MODE OVERRIDES ===== */
        body.dark-mode {
            --dash-bg: #0f172a;
            --dash-card: #1e293b;
            --dash-border: #334155;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            color: var(--text);
        }
        body.dark-mode nav {
            background: #1e293b !important;
            border-color: #334155;
        }
        body.dark-mode .profile-sidebar,
        body.dark-mode .profile-card,
        body.dark-mode .card-stat,
        body.dark-mode .content-card {
            background: #1e293b;
            border-color: #334155;
        }
        body.dark-mode .profile-badge {
            background: #334155;
            color: #f8fafc;
        }
        body.dark-mode .stat-item {
            border-color: #334155;
        }
        body.dark-mode .portfolio-item,
        body.dark-mode .cert-item {
            background: #0f172a;
            border-color: #334155;
        }
        body.dark-mode .portfolio-item:hover,
        body.dark-mode .cert-item:hover {
            background: #1e293b;
            border-color: var(--primary);
        }
        body.dark-mode .cert-icon {
            background: #334155;
            color: var(--primary);
        }
        body.dark-mode .scrollable-list::-webkit-scrollbar-thumb {
            background: #475569;
        }
        body.dark-mode .scrollable-list::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
</head>
<body>

<nav>
    <div class="logo" onclick="window.location.href='/'" style="cursor: pointer; display: flex; align-items: center; gap: 8px;">
        <img src="{{ asset('image/logo-kerjain.png') }}" alt="Logo KerjaIn" class="logo-img" style="height: 32px;">
        <span style="font-weight: 800; font-size: 1.25rem; color: var(--primary);">KerjaIn</span>
    </div>
</nav>

<div class="main-wrapper">
    <!-- LEFT SIDEBAR -->
    <div class="profile-sidebar">
        <!-- PROFILE CARD -->
        <div class="profile-card">
            <div class="profile-avatar-small">
                @if($pelajar->user->foto_profil)
                    <img src="{{ asset($pelajar->user->foto_profil) }}" style="width:100%; height:100%; object-fit:cover;">
                @else
                    <span class="material-icons" style="font-size: 2.5rem; color: white;">school</span>
                @endif
            </div>
            <h2 class="profile-name">{{ $pelajar->user->nama_lengkap }}</h2>
            <p class="profile-bio">{{ $pelajar->bio ?? 'Mahasiswa yang bersemangat mencari pengalaman kerja nyata.' }}</p>
            
            <div class="badge-list">
                <div class="profile-badge">
                    <span class="material-icons" style="font-size: 1.1rem;">school</span>
                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $pelajar->universitas ?? 'Universitas' }}</span>
                </div>
                <div class="profile-badge">
                    <span class="material-icons" style="font-size: 1.1rem;">menu_book</span>
                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $pelajar->jurusan ?? 'Jurusan' }} ({{ $pelajar->angkatan ?? '2023' }})</span>
                </div>
            </div>
        </div>

        <!-- STATS CARD -->
        <div class="card-stat">
            <h3 class="section-title">
                <span class="material-icons" style="color:var(--primary); font-size: 1.3rem;">bar_chart</span>
                Statistik Prestasi
            </h3>
            
            <div class="stat-item">
                <div style="color:var(--text-muted); font-size: 0.9rem;">Proyek Selesai</div>
                <div style="font-weight:700; font-size:1.1rem; color: var(--text);">{{ $pelajar->portfolios->count() }}</div>
            </div>
            <div class="stat-item">
                <div style="color:var(--text-muted); font-size: 0.9rem;">Sertifikat Edukasi</div>
                <div style="font-weight:700; font-size:1.1rem; color: var(--text);">{{ $pelajar->certificates->count() }}</div>
            </div>
            <div class="stat-item" style="border-bottom: none; padding-bottom: 0;">
                <div style="color:var(--dash-accent); font-weight:600; font-size: 0.9rem;">Total Poin</div>
                <div style="font-weight:800; font-size:1.2rem; color:var(--dash-accent); display:flex; align-items:center; gap:4px;">
                    <span class="material-icons" style="font-size: 1.2rem;">auto_awesome</span>
                    {{ $pelajar->total_poin ?? 0 }}
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT CONTENT AREA -->
    <div class="profile-content">
        <!-- PORTFOLIO CARD -->
        <div class="content-card">
            <h3 class="section-title">
                <span class="material-icons" style="color: var(--primary); font-size: 1.3rem;">work</span>
                Portofolio Proyek
            </h3>
            <div class="scrollable-list">
                @if($pelajar->portfolios->count() > 0)
                    @foreach($pelajar->portfolios as $porto)
                    <div class="portfolio-item">
                        <div style="font-size:0.75rem; color:var(--dash-accent); font-weight:700; margin-bottom:6px;">
                            {{ $porto->enrollment->program->mitra->user->nama_lengkap ?? 'Mitra Perusahaan' }}
                        </div>
                        <h4 style="margin:0 0 8px; font-size:1rem; color: var(--text);">{{ $porto->enrollment->program->judul }}</h4>
                        <p style="color:var(--text-muted); font-size:0.85rem; line-height:1.4; margin: 0;">
                            {{ Str::limit($porto->enrollment->program->deskripsi, 140) }}
                        </p>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <span class="material-icons" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 8px;">folder_open</span>
                        <div>Belum ada portofolio yang diselesaikan.</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- CERTIFICATE CARD -->
        <div class="content-card">
            <h3 class="section-title">
                <span class="material-icons" style="color: var(--primary); font-size: 1.3rem;">military_tech</span>
                Sertifikat Terverifikasi
            </h3>
            <div class="scrollable-list">
                @if($pelajar->certificates->count() > 0)
                    @foreach($pelajar->certificates as $cert)
                    <a href="/sertifikat/{{ $cert->id }}" target="_blank" class="cert-item">
                        <div class="cert-icon">
                            <span class="material-icons" style="font-size: 1.25rem;">military_tech</span>
                        </div>
                        <div style="flex:1; min-width: 0;">
                            <div style="font-weight:600; font-size:0.9rem; margin-bottom:2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--text);">
                                {{ $cert->enrollment->program->judul ?? 'Penyelesaian Program' }}
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">
                                ID: {{ $cert->nomor_sertifikat }} · Diterbitkan: {{ \Carbon\Carbon::parse($cert->issued_at)->format('M Y') }}
                            </div>
                        </div>
                        <span class="material-icons" style="font-size: 1.2rem; color: var(--text-muted);">open_in_new</span>
                    </a>
                    @endforeach
                @else
                    <div class="empty-state">
                        <span class="material-icons" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 8px;">workspace_premium</span>
                        <div>Belum ada sertifikat yang diterbitkan.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
