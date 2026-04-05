<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'A.U.R.A Dashboard' }} - Gandaria City</title>
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <!-- Hotwire Turbo for SPA-like experience -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-esm.js"></script>
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
                    @if(auth()->user()->role === 'DEV')
                        {{-- DEV: hanya menu monitoring --}}
                        <a href="{{ route('dev.status') }}" class="nav-link {{ request()->is('dev*') ? 'active' : '' }}">
                            <ion-icon name="pulse"></ion-icon>
                            Status Sistem
                        </a>
                        <a href="{{ route('admin.logs') }}" class="nav-link {{ request()->is('admin/logs*') ? 'active' : '' }}">
                            <ion-icon name="journal"></ion-icon>
                            Log Aktivitas
                        </a>
                    @else
                        {{-- Menu operasional untuk CPM, SPV, IT --}}
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

                        @if(auth()->user()->role === 'CPM')
                            <a href="{{ route('admin.logs') }}" class="nav-link {{ request()->is('admin/logs*') ? 'active' : '' }}">
                                <ion-icon name="journal"></ion-icon>
                                Log Aktivitas
                            </a>
                            <div style="margin-top: 24px; padding: 0 20px; font-size: 0.65rem; font-weight: 800; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.1em;">Dev Tools</div>
                            <a href="{{ route('dev.status') }}" class="nav-link {{ request()->is('dev*') ? 'active' : '' }}">
                                <ion-icon name="pulse"></ion-icon>
                                Status Sistem
                            </a>
                        @endif
                    @endif
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
                            <div style="font-size: 0.7rem; color: var(--text-dim);">Jabatan: {{ auth()->user()->role === 'DEV' ? 'Dev Monitor' : auth()->user()->role }}</div>
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

    {{-- Script Pencegahan Double-Submit & CSRF Global --}}
    <script>
        // Gunakan Event Delegation agar bekerja pada semua form (statis maupun dinamis)
        document.addEventListener('submit', function (e) {
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            
            if (submitBtn && !submitBtn.disabled) {
                // Simpan teks asli jika belum ada
                if (!submitBtn.hasAttribute('data-original-text')) {
                    submitBtn.setAttribute('data-original-text', submitBtn.innerHTML);
                }
                
                // Set state loading segera setelah submit
                // Delay 0ms dengan setTimeout agar tidak menghentikan event loop browser
                setTimeout(() => {
                    submitBtn.disabled = true;
                    submitBtn.style.opacity = '0.7';
                    submitBtn.style.cursor = 'not-allowed';
                    submitBtn.innerHTML = '<ion-icon name="sync-outline" style="animation: spin 2s linear infinite; margin-right: 8px;"></ion-icon> Mohon Tunggu...';
                }, 0);
                
                // Fallback: Aktifkan kembali setelah 10 detik jika tidak ada respon dari server
                setTimeout(() => { if(submitBtn.disabled) { reEnableButton(submitBtn); } }, 10000);
            }
        });

        // Re-enable jika Turbo mendeteksi form selesai (misal: validasi gagal di server)
        document.addEventListener('turbo:submit-end', (event) => {
            const submitBtn = event.target.querySelector('button[type="submit"]');
            if (submitBtn) reEnableButton(submitBtn);
        });

        // Pastikan semua tombol aktif kembali saat halaman dimuat (termasuk navigasi 'Back')
        document.addEventListener('turbo:load', () => {
            document.querySelectorAll('button[type="submit"]').forEach(reEnableButton);
        });

        function reEnableButton(btn) {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
            if (btn.hasAttribute('data-original-text')) {
                btn.innerHTML = btn.getAttribute('data-original-text');
            }
        }
    </script>
</body>

</html>
