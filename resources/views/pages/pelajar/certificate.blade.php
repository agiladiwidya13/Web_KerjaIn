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
            padding: 40px;
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
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }
        .cert-logo {
            position: absolute;
            top: 40px;
            left: 40px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.5rem;
            color: #4f46e5;
        }
        .cert-logo img {
            width: 40px;
        }
        .cert-header {
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            font-weight: 700;
            color: #312e81;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        .cert-subheader {
            font-size: 1.2rem;
            color: #64748b;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
        }
        .cert-presented {
            font-size: 1.2rem;
            margin-bottom: 16px;
        }
        .cert-name {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 30px;
            font-style: italic;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 10px;
            display: inline-block;
            min-width: 600px;
        }
        .cert-reason {
            font-size: 1.1rem;
            line-height: 1.6;
            max-width: 800px;
            color: #475569;
            margin-bottom: 40px;
        }
        .cert-program {
            font-weight: 700;
            color: #1e293b;
            font-size: 1.4rem;
        }
        .cert-footer {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-top: auto;
            padding: 0 60px;
            box-sizing: border-box;
        }
        .signature-block {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 250px;
        }
        .signature-line {
            width: 100%;
            border-bottom: 2px solid #cbd5e1;
            margin-bottom: 12px;
            height: 60px;
        }
        .signature-name {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 4px;
        }
        .signature-title {
            font-size: 0.9rem;
            color: #64748b;
        }
        .cert-id {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 0.85rem;
            color: #94a3b8;
            letter-spacing: 1px;
        }
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
        
        @media print {
            body { background: white; }
            .print-btn { display: none; }
            .cert-container { box-shadow: none; }
        }
    </style>
</head>
<body>

    <button class="print-btn" onclick="window.print()" style="display:inline-flex;align-items:center;gap:8px;"><span class="material-icons">print</span> Cetak / Simpan PDF</button>

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
            
            <div class="cert-id">
                ID Sertifikat: {{ $certificate->nomor_sertifikat }} | Diterbitkan: {{ \Carbon\Carbon::parse($certificate->issued_at)->format('d F Y') }}
            </div>
        </div>
    </div>

<script src="{{ asset('js/dashboard-global.js') }}"></script>
</body>
</html>
