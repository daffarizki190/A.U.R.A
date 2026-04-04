<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'A.U.R.A Dashboard' }} - Gandaria City</title>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
</head>

<body>
    @auth
        <div class="dashboard-layout">
            <aside class="sidebar">
                <div class="sidebar-brand">
                    GANDARIA CITY<br>
                    <span
                        style="font-size: 0.75rem; font-weight: 400; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.1em;">A.U.R.A
                        System</span>
                </div>

                <nav style="flex: 1;">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                        <ion-icon name="grid"></ion-icon>
                        Dasbor Utama
                    </a>
                    <a href="{{ route('findings.index') }}"
                        class="nav-link {{ request()->is('findings*') ? 'active' : '' }}">
                        <ion-icon name="build"></ion-icon>
                        Temuan Aset
                    </a>
                    <a href="{{ route('ba.index') }}" class="nav-link {{ request()->is('ba*') ? 'active' : '' }}">
                        <ion-icon name="document-text"></ion-icon>
                        Berita Acara
                    </a>
                </nav>

                <div class="user-info">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div
                            style="width: 32px; height: 32px; border-radius: 50%; background: var(--border-bold); display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--primary); font-size: 0.8rem;">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <div
                                style="font-weight: 600; font-size: 0.85rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ auth()->user()->name }}</div>
                            <div style="font-size: 0.7rem; color: var(--text-dim);">{{ auth()->user()->role }}</div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            style="background: none; border: none; color: var(--danger); cursor: pointer; font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 6px; padding: 4px 0; opacity: 0.8; transition: opacity 0.2s;">
                            <ion-icon name="log-out-outline"></ion-icon> Keluar Akun
                        </button>
                    </form>
                </div>
            </aside>

            <main class="main-content">
                <div style="min-height: calc(100vh - 60px); display: flex; flex-direction: column;">
                    <div style="flex: 1;">
                        @yield('content')
                    </div>

                    <footer
                        style="margin-top: auto; padding-top: 2rem; padding-bottom: 0.5rem; text-align: center; color: var(--text-dim); font-size: 0.75rem; font-weight: 500;">
                        &copy; 2026 Gandaria City DEV CP </footer>
                </div>
            </main>
        </div>
    @else
        @yield('content')
    @endauth
</body>

</html>