<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>A.U.R.A - Gandaria City</title>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
    <style>
        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }
        .hero-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 80px 24px;
            text-align: center;
            position: relative;
        }
        .hero-bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            z-index: -1;
            opacity: 0.5;
        }
        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
        }
        .shape-1 {
            width: 400px;
            height: 400px;
            background: rgba(0, 122, 255, 0.1);
            top: -100px;
            right: -100px;
        }
        .shape-2 {
            width: 300px;
            height: 300px;
            background: rgba(52, 199, 89, 0.05);
            bottom: -50px;
            left: -50px;
        }
        .hero-logo {
            width: 80px;
            height: 80px;
            background: var(--primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 32px;
            box-shadow: 0 12px 24px rgba(0, 122, 255, 0.2);
            color: white;
            font-size: 40px;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            letter-spacing: -0.04em;
            margin-bottom: 16px;
            line-height: 1.1;
        }
        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--text-dim);
            max-width: 600px;
            margin-bottom: 48px;
            line-height: 1.6;
        }
        .nav-buttons {
            display: flex;
            gap: 16px;
        }
        .footer {
            padding: 24px;
            text-align: center;
            color: var(--text-dim);
            font-size: 0.85rem;
            border-top: 1px solid var(--border);
        }
    </style>
</head>
<body>
    <div class="hero-bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
    </div>

    <main class="hero-section">
        <div class="hero-logo">
            <ion-icon name="shield-checkmark"></ion-icon>
        </div>
        <h1 class="hero-title">
            <span style="color: var(--primary);">A.U.R.A</span> System
        </h1>
        <p class="hero-subtitle">
            Sistem terintegrasi untuk pemantauan, pelaporan, dan manajemen aset Gandaria City secara real-time dan profesional.
        </p>

        <div class="nav-buttons">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary" style="height: 56px; padding: 0 32px; font-size: 1rem; border-radius: 16px;">
                        Buka Dashboard Utama
                        <ion-icon name="arrow-forward-outline" style="margin-left: 8px;"></ion-icon>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary" style="height: 56px; padding: 0 48px; font-size: 1rem; border-radius: 16px;">
                        Masuk Petugas
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-primary" style="height: 56px; padding: 0 32px; background: white; color: var(--text-main); border: 1px solid var(--border); border-radius: 16px;">
                            Registrasi
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </main>

    <footer class="footer">
        &copy; 2026 Gandaria City DEV CP - Enterprise Asset Management Solution
    </footer>
</body>
</html>
