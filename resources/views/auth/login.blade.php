@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background: radial-gradient(circle at top right, rgba(0, 122, 255, 0.05), transparent), radial-gradient(circle at bottom left, rgba(255, 204, 0, 0.03), transparent);">
    <div class="glass-card" style="width: 100%; max-width: 420px; padding: 48px; border-radius: 24px;">
        <div style="text-align: center; margin-bottom: 40px;">
            <div style="width: 64px; height: 64px; background: var(--primary); border-radius: 16px; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 16px rgba(0, 122, 255, 0.3);">
                <ion-icon name="shield-checkmark" style="font-size: 32px; color: white;"></ion-icon>
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.02em; margin-bottom: 8px;">A.U.R.A</h1>
            <p style="color: var(--text-dim); font-size: 0.9rem;">Sistem Pemantauan & Pelaporan Aset Terpadu</p>
        </div>
        
        <form action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf
            
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="email">Alamat Email / Username</label>
                <input type="email" id="email" name="email" class="input" value="{{ old('email') }}" placeholder="nama@gandariacity.com" required autofocus autocomplete="off">
                @error('email')
                    <div style="color: var(--danger); font-size: 0.8rem; margin-top: 6px; font-weight: 500;">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group" style="margin-bottom: 32px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <label for="password" style="margin-bottom: 0;">Kata Sandi</label>
                </div>
                <input type="password" id="password" name="password" class="input" placeholder="••••••••" required autocomplete="new-password">
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; height: 52px; font-size: 1rem; font-weight: 700; border-radius: 12px;">
                Masuk ke Sistem
            </button>
        </form>

        <div style="margin-top: 32px; text-align: center; font-size: 0.85rem; color: var(--text-dim);">
            &copy; 2026 Gandaria City DEV CP
        </div>
    </div>
</div>
@endsection
