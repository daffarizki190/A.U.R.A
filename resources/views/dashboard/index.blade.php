@extends('layouts.app')

@section('content')
<header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; color: var(--text-main);">Dasbor Utama</h1>
        <p style="color: var(--text-dim); font-size: 0.95rem;">Ringkasan real-time pemantauan aset dan berita acara</p>
    </div>
    <div style="text-align: right;">
        <div style="font-size: 0.9rem; font-weight: 600;">{{ now()->translatedFormat('l, d F Y') }}</div>
        <div style="font-size: 0.75rem; color: var(--text-dim);">Sistem Aktif • Server Beroperasi Normal</div>
    </div>
</header>

<div class="stats-grid">
    <div class="stat-card glass-card">
        <div class="stat-header">
            <div class="stat-label">Temuan Open</div>
            <ion-icon name="alert-circle" style="color: var(--danger); font-size: 1.5rem;"></ion-icon>
        </div>
        <div class="stat-value">{{ $stats['findings_open'] }}</div>
        <div style="margin-top: 8px; font-size: 0.75rem; color: var(--danger); font-weight: 600; display: flex; align-items: center; gap: 4px;">
            <ion-icon name="trending-up-outline"></ion-icon> Perlu Perhatian Khusus
        </div>
    </div>
    
    <div class="stat-card glass-card">
        <div class="stat-header">
            <div class="stat-label">On Progress</div>
            <ion-icon name="hammer" style="color: var(--warning); font-size: 1.5rem;"></ion-icon>
        </div>
        <div class="stat-value">{{ $stats['findings_progress'] }}</div>
        <div style="margin-top: 8px; font-size: 0.75rem; color: var(--text-dim); font-weight: 500;">
            Sedang dalam perbaikan
        </div>
    </div>
    
    <div class="stat-card glass-card">
        <div class="stat-header">
            <div class="stat-label">Menunggu Persetujuan</div>
            <ion-icon name="time" style="color: var(--primary); font-size: 1.5rem;"></ion-icon>
        </div>
        <div class="stat-value">{{ $stats['findings_pending'] }}</div>
        <div style="margin-top: 8px; font-size: 0.75rem; color: var(--text-dim); font-weight: 500;">
            Menunggu otorisasi CPM
        </div>
    </div>
    
    <div class="stat-card glass-card">
        <div class="stat-header">
            <div class="stat-label">BA Diproses</div>
            <ion-icon name="document-text" style="color: var(--accent); font-size: 1.5rem;"></ion-icon>
        </div>
        <div class="stat-value">{{ $stats['ba_submitted'] + $stats['ba_processed'] }}</div>
        <div style="margin-top: 8px; font-size: 0.75rem; color: var(--text-dim); font-weight: 500;">
            Total dokumen aktif berjalan
        </div>
    </div>
</div>

<div class="glass-card" style="padding: 40px; border-radius: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h3 style="font-size: 1.25rem; font-weight: 700;">Aktivitas Terbaru</h3>
        <button class="btn-primary" style="padding: 6px 16px; font-size: 0.75rem;">Lihat Semua</button>
    </div>
    
    <div style="display: flex; flex-direction: column; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 16px; padding: 16px; border-radius: var(--radius-m); background: rgba(255,255,255,0.02); border: 1px solid var(--border);">
            <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(52, 199, 89, 0.1); display: flex; align-items: center; justify-content: center; color: var(--success);">
                <ion-icon name="notifications" style="font-size: 1.25rem;"></ion-icon>
            </div>
            <div>
                <div style="font-weight: 600; font-size: 0.9rem;">Sistem Diinisialisasi</div>
                <div style="font-size: 0.75rem; color: var(--text-dim);">Dasbor pemantauan telah siap untuk beroperasi penuh.</div>
            </div>
            <div style="margin-left: auto; font-size: 0.75rem; color: var(--text-dim);">Baru saja</div>
        </div>
    </div>
</div>

<script>
    // Auto-refresh Dashboard setiap 60 detik (Real-time monitoring)
    setTimeout(function() {
        window.location.reload();
    }, 60000);
</script>
@endsection
