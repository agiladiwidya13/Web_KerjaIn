<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Penyelesaian - {{ $certificate->nomor_sertifikat }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Icons+Extended" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }
        .cert-container {
            width: 1123px;
            height: 794px;
            background: white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            padding: 20px;
            box-sizing: border-box;
            overflow: hidden;
            background-image: radial-gradient(circle at 100% 0%, #eef2ff 0%, transparent 40%),
                              radial-gradient(circle at 0% 100%, #f0fdf4 0%, transparent 40%);
        }
        .cert-border {
            border: 12px solid #4f46e5;
            border-radius: 20px;
            height: 100%;
            box-sizing: border-box;
            padding: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            text-align: center;
            position: relative;
        }
        .cert-logo {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #4f46e5;
            margin-bottom: 12px;
        }
        .cert-logo img {
            width: 28px;
        }
        .cert-header {
            font-family: 'Playfair Display', serif;
            font-size: 4.4rem;
            font-weight: 700;
            color: #312e81;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 3px;
        }
        .cert-subheader {
            font-size: 1.3rem;
            color: #64748b;
            margin-bottom: 18px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
        }
        .cert-presented {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        .cert-name {
            font-family: 'Playfair Display', serif;
            font-size: 3.6rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 18px;
            font-style: italic;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 10px;
            display: inline-block;
            min-width: 500px;
        }
        .cert-reason {
            font-size: 1.05rem;
            line-height: 1.5;
            max-width: 760px;
            color: #475569;
            margin-bottom: 18px;
        }
        .cert-program {
            font-weight: 700;
            color: #1e293b;
            font-size: 1.2rem;
        }
        .cert-footer {
            display: flex;
            justify-content: space-around;
            width: 100%;
            padding: 0 20px;
            box-sizing: border-box;
            gap: 40px;
        }
        .signature-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 200px;
        }
        .signature-line {
            width: 100%;
            border-bottom: 2px solid #cbd5e1;
            margin-bottom: 6px;
            height: 35px;
        }
        .signature-name {
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }
        .signature-title {
            font-size: 0.75rem;
            color: #64748b;
        }
        .cert-id {
            position: absolute;
            bottom: 22px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.75rem;
            color: #94a3b8;
            letter-spacing: 1px;
        }
        .cert-stats {
            display: flex;
            gap: 6px;
            align-items: center;
            justify-content: center;
            margin: 6px auto 0;
            width: fit-content;
        }
        .stat-card {
            min-width: 110px;
            padding: 6px 10px;
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(2,6,23,0.05);
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:2px;
            font-weight:600;
        }
        .stat-score { background: #e9fff6; color:#065f46; }
        .stat-duration { background: #eef2ff; color:#3730a3; }
        .stat-value { font-size:1.15rem; font-weight:800; }
        .stat-label { font-size:0.65rem; color: #475569; font-weight:500; }
        @media print {
            .cert-stats { bottom: 64px; box-shadow: none; }
            .stat-card { box-shadow: none; }
        }
        .program-details {
            width: 86%;
            margin: 8px auto 0;
            padding-top: 12px;
            border-top: 1px solid #e6eef8;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            color: #475569;
            font-size: 0.95rem;
            align-items: center;
        }
        .program-details .left {
            display:flex;flex-direction:column;gap:6px;
        }
        .program-details .right { text-align:right; font-size:0.9rem; color:#94a3b8; }
        /* Preview scaling to show certificate as a small card */
        .preview-area { display:flex; justify-content:flex-start; padding-top:8px; }
        .cert-viewport { transform: scale(0.55); transform-origin: top left; box-shadow: 0 10px 30px rgba(2,6,23,0.12); border-radius:14px; width:618px; height:437px; }
        .cert-viewport .cert-container { width:1123px; height:794px; }
        .content-row { display:flex; gap:50px; align-items:center; max-width:1500px; margin:0 auto; }
        .preview-column { width:650px; display:flex; justify-content:center; padding-top:20px; }
        .detail-column { flex:1; }
        /* hide global floating print button on this page to avoid overlap */
        .print-btn { display:none; }
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #4f46e5;
            color: white;
            padding: 16px 32px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.4);
            transition: transform 0.2s;
        }
        .print-btn:hover {
            transform: translateY(-2px);
        }
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            display:inline-flex;
            align-items:center;
            gap:8px;
            font-weight:700;
            padding:12px 18px;
            border-radius:14px;
            text-decoration:none;
            font-size:1rem;
            z-index:1000;
        }
        
        @media print {
            * { margin: 0; padding: 0; }
            body { background: white; width: 100%; height: 100%; }
            @page { size: A4 landscape; margin: 0; }
            .print-btn { display: none; }
            .back-btn { display: none; }
            .detail-column { display: none; }
            .preview-column { width: 100%; padding: 0; }
            .content-row { justify-content: center; gap: 0; margin: 0; }
            .page-wrapper { padding: 0 !important; }
            .cert-viewport { 
                transform: scale(1) !important; 
                transform-origin: center !important; 
                box-shadow: none !important; 
                border-radius: 0 !important; 
                width: 297mm !important; 
                height: 210mm !important; 
            }
            .cert-viewport .cert-container { 
                width: 100% !important; 
                height: 100% !important; 
                box-shadow: none !important; 
                padding: 15px !important; 
            }
            .cert-border { border-width: 8px !important; padding: 18px !important; }
            .cert-header { font-size: 3.4rem !important; margin-bottom: 4px !important; }
            .cert-subheader { font-size: 1rem !important; margin-bottom: 10px !important; }
            .cert-presented { font-size: 0.95rem !important; margin-bottom: 6px !important; }
            .cert-name { font-size: 2.9rem !important; margin-bottom: 12px !important; min-width: 420px !important; }
            .cert-reason { font-size: 0.95rem !important; margin-bottom: 12px !important; max-width: 720px !important; }
            .cert-footer { padding: 0 15px !important; gap: 25px !important; }
            .signature-block { width: 180px !important; }
            .signature-line { height: 30px !important; margin-bottom: 3px !important; }
            .signature-name { font-size: 0.8rem !important; }
            .signature-title { font-size: 0.65rem !important; }
            .cert-id { font-size: 0.65rem !important; }
            .cert-stats { display: none !important; }
        }
        body.dark-mode {
            background: #0f172a;
        }
    </style>
</head>
<body>
<script>
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
    }
</script>

    <button class="print-btn" onclick="window.print()" style="display:inline-flex;align-items:center;gap:8px;"><span class="material-icons">print</span> Cetak / Simpan PDF</button>

    <div class="page-wrapper" style="position:relative;padding:40px 24px;">
        <a href="javascript:history.back()" class="back-btn btn-dash btn-dash-outline">← Kembali</a>
        <div class="content-row">
            <div class="preview-column">
                <div class="cert-viewport">
                    <div class="cert-container">
                        <div class="cert-border">
            <div class="cert-logo">
                <img src="{{ asset('image/logo-kerjain.png') }}" alt="Logo"> KerjaIn
            </div>
            
            <div class="cert-header">Sertifikat</div>
            <div class="cert-subheader">Penyelesaian Program</div>
            
            <div class="cert-presented">Diberikan dengan bangga kepada:</div>
            
            <div class="cert-name">{{ $certificate->enrollment->pelajar->user->nama_lengkap }}</div>
            
            <div class="cert-reason">
                Telah berhasil menyelesaikan seluruh tugas dan memenuhi standar kualifikasi pada program<br>
                <span class="cert-program">{{ $certificate->enrollment->program->judul }}</span><br>
                yang diselenggarakan secara resmi oleh <strong>{{ $certificate->enrollment->program->mitra->user->nama_lengkap }}</strong> melalui platform KerjaIn.
            </div>
            
            <div class="cert-footer">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $certificate->enrollment->program->mitra->user->nama_lengkap }}</div>
                    <div class="signature-title">Mitra Perusahaan</div>
                </div>
                
                <div class="signature-block">
                    <div style="height: 60px; display: flex; align-items:flex-end; padding-bottom:8px;">
                        <img src="{{ asset('image/logo-kerjain.png') }}" style="height: 40px; margin:0 auto; opacity:0.8;">
                    </div>
                    <div class="signature-line" style="height: 0;"></div>
                    <div class="signature-name">KerjaIn Team</div>
                    <div class="signature-title">Penyelenggara Platform</div>
                </div>
            </div>
                </div>
                    </div>
                </div>

            </div>

            <div class="detail-column">
                <aside class="right-card" style="width:100%;border-radius:12px;border:1px solid #e6eef8;padding:18px;background:white;box-shadow:0 8px 24px rgba(2,6,23,0.04);margin-bottom:24px;">
                        <div style="font-size:0.9rem;color:#64748b;margin-bottom:8px;">ID</div>
                        <div style="font-weight:700;margin-bottom:12px;">{{ $certificate->nomor_sertifikat }}</div>

                        @php
                            $programStart = $certificate->enrollment->program->tanggal_mulai?->format('d M Y')
                                          ?? $certificate->enrollment->enrolled_at?->format('d M Y')
                                          ?? '-';
                            $programEnd = $certificate->enrollment->program->tanggal_selesai?->format('d M Y')
                                        ?? $certificate->enrollment->selesai_at?->format('d M Y')
                                        ?? '-';

                            if ($certificate->enrollment->program->tanggal_mulai && $certificate->enrollment->program->tanggal_selesai) {
                                $projectDays = $certificate->enrollment->program->tanggal_mulai->diffInDays($certificate->enrollment->program->tanggal_selesai) + 1;
                                if ($projectDays <= 6) {
                                    $projectDuration = $projectDays . ' Hari';
                                } else {
                                    $weeks = intdiv($projectDays, 7);
                                    $remainder = $projectDays % 7;
                                    $parts = [];
                                    if ($weeks > 0) $parts[] = $weeks . ' Minggu';
                                    if ($remainder > 0) $parts[] = $remainder . ' Hari';
                                    $projectDuration = implode(' ', $parts);
                                }
                            } else {
                                $projectDuration = $certificate->enrollment->durationHuman();
                            }
                        @endphp

                        <div style="display:flex;justify-content:space-between;color:#475569;font-size:0.95rem;margin-bottom:6px;">
                            <div>Mulai</div>
                            <div>{{ $programStart }}</div>
                        </div>
                        <div style="display:flex;justify-content:space-between;color:#475569;font-size:0.95rem;margin-bottom:12px;">
                            <div>Selesai</div>
                            <div>{{ $programEnd }}</div>
                        </div>

                        <div style="border-top:1px solid #f1f5f9;padding-top:12px;margin-top:8px;">
                            <div style="font-size:0.85rem;color:#94a3b8;">Diberikan pada</div>
                            <div style="font-weight:700">{{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y') }}</div>
                        </div>

                        <div style="border-top:1px solid #f1f5f9;padding-top:12px;margin-top:12px;font-size:0.85rem;color:#94a3b8;">
                            {{ $certificate->nomor_sertifikat }} | {{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y') }}
                        </div>

                        <div style="margin-top:12px;text-align:center;">
                            <a href="{{ $certificate->pdf_url ?? '#' }}" class="btn-solid" style="background:#4f46e5;color:white;padding:10px 14px;border-radius:8px;text-decoration:none;display:inline-block;width:100%;box-sizing:border-box;">Download PDF</a>
                        </div>
                    </aside>

                    @php
                        $avgNilai = $certificate->enrollment->submissions->whereNotNull('nilai')->avg('nilai');
                        $nilaiDisplay = $avgNilai ? round($avgNilai) : '-';
                    @endphp

                    <div class="cert-stats" style="display:flex;justify-content:flex-start;gap:16px;margin-bottom:0;">
                        <div class="stat-card stat-score">
                            <div class="stat-value">{{ $nilaiDisplay }}</div>
                            <div class="stat-label">Nilai Akhir</div>
                        </div>
                        <div class="stat-card stat-duration">
                            <div class="stat-value">{{ $projectDuration }}</div>
                            <div class="stat-label">Durasi Proyek</div>
                        </div>
                    </div>
            </div>
        </div>

    </div>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
