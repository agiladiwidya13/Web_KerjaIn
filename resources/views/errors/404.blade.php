<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan (404) - KerjaIn</title>
    <link rel="icon" type="image/png" href="{{ asset('image/logo-kerjain.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&family=Sora:wght@400;600;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root {
            --primary: #f59e0b;
            --primary-hover: #d97706;
            --secondary: #4338ca;
            --secondary-light: #6366f1;
            --bg: #fffcf2;
            --text: #1e293b;
            --text-muted: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow: hidden;
            position: relative;
        }

        /* Ambient background glow */
        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            top: -200px;
            left: -200px;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.1) 0%, transparent 70%);
            bottom: -200px;
            right: -200px;
            z-index: 0;
        }

        .container {
            max-width: 560px;
            width: 100%;
            text-align: center;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 60px 40px;
            border-radius: 32px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.04);
            position: relative;
            z-index: 1;
            animation: floatIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes floatIn {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .error-code {
            font-family: 'Sora', sans-serif;
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, var(--secondary) 0%, #7c3aed 50%, var(--primary) 100%);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 24px;
            letter-spacing: -2px;
            animation: pulse 3s infinite ease-in-out;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.02);
            }
        }

        h1 {
            font-family: 'Sora', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 16px;
            color: var(--text);
        }

        p {
            font-size: 1.05rem;
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 36px;
        }

        .actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 14px 28px;
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary), var(--secondary-light));
            color: white;
            box-shadow: 0 4px 15px rgba(67, 56, 202, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(67, 56, 202, 0.45);
        }

        .btn-secondary {
            background: white;
            color: var(--text);
            border: 2px solid rgba(0, 0, 0, 0.06);
        }

        .btn-secondary:hover {
            border-color: var(--secondary);
            color: var(--secondary);
            background: #fafbff;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="error-code">404</div>
        <h1>Ups! Halaman Tidak Ditemukan</h1>
        <p>Halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau tidak pernah ada. Mari kembali ke jalur yang benar.</p>
        <div class="actions">
            <a href="/" class="btn btn-primary"><span class="material-icons" style="font-size:20px;vertical-align:middle;">home</span> Kembali ke Beranda</a>
            <a href="javascript:history.back()" class="btn btn-secondary">← Kembali</a>
        </div>
    </div>

</body>
</html>
