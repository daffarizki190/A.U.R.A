@extends('layouts.app')

@section('content')
<header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em;">Berita Acara</h1>
        <p style="color: var(--text-dim); font-size: 0.95rem;">Laporan resmi untuk insiden dan kendala</p>
    </div>
    @if(auth()->user()->role == 'SPV' || auth()->user()->role == 'IT')
    <a href="{{ route('ba.create') }}" class="btn-primary" style="gap: 8px;">
        <ion-icon name="document-attach" style="font-size: 1.2rem;"></ion-icon>
        Buat BA Baru
    </a>
    @endif
</header>

<div class="glass-card" style="padding: 24px; margin-bottom: 32px; border-radius: 20px;">
    <form action="{{ route('ba.index') }}" method="GET" style="display: flex; gap: 16px; align-items: flex-end;">
        <div style="flex: 1; max-width: 200px;">
            <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px; display: block;">Filter Status</label>
            <select name="status" class="input" style="height: 48px;">
                <option value="">Semua Dokumen</option>
                <option value="Submitted" {{ request('status') == 'Submitted' ? 'selected' : '' }}>Waiting Approved</option>
                <option value="Processed" {{ request('status') == 'Processed' ? 'selected' : '' }}>ON PROGRES</option>
                <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>DONE</option>
                <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <div style="flex: 2;">
            <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px; display: block;">Kategori Insiden</label>
            <div style="position: relative;">
                <ion-icon name="funnel-outline" style="position: absolute; left: 16px; top: 14px; color: var(--text-dim);"></ion-icon>
                <input type="text" name="ba_type" class="input" style="padding-left: 44px; height: 48px;" value="{{ request('ba_type') }}" placeholder="Cari tipe (Kerusakan, Kehilangan)...">
            </div>
        </div>
        <button type="submit" class="btn-primary" style="height: 48px; min-width: 120px;">Cari</button>
        @if(request()->anyFilled(['status', 'ba_type']))
            <a href="{{ route('ba.index') }}" class="btn-primary" style="height: 48px; background: rgba(0,0,0,0.03); color: var(--text-main);">Reset</a>
        @endif
    </form>
</div>

<div class="glass-card table-container" style="border-radius: 20px;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nomor Dokumen</th>
                <th>Jenis Insiden</th>
                <th>Tanggal Kejadian</th>
                <th>Pelanggan atau Pihak</th>
                <th>Pemilik atau PIC</th>
                <th>Status</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bas as $ba)
                <tr style="cursor: pointer;" onclick="window.location='{{ route('ba.show', $ba->id) }}'">
                    <td style="font-weight: 700; font-family: 'JetBrains Mono', monospace; color: var(--accent);">{{ $ba->ba_number }}</td>
                    <td style="font-weight: 600;">{{ $ba->ba_type }}</td>
                    <td style="color: var(--text-secondary);">{{ \Carbon\Carbon::parse($ba->incident_date)->format('M d, Y') }}</td>
                    <td>{{ $ba->customer_name }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--border-bold); display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800;">
                                {{ substr($ba->pic ? $ba->pic->name : 'N', 0, 1) }}
                            </div>
                            <span>{{ $ba->pic ? $ba->pic->name : 'Belum Ditentukan' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-{{ strtolower(str_replace(' ', '', $ba->status)) }}">
                            {{ str_replace('Submitted', 'Waiting Approved', str_replace('Processed', 'ON PROGRES', str_replace('Done', 'DONE', $ba->status))) }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('ba.show', $ba->id) }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                            Lihat Berkas <ion-icon name="chevron-forward"></ion-icon>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 80px 20px;">
                        <div style="display: inline-flex; width: 64px; height: 64px; border-radius: 20px; background: rgba(0,0,0,0.02); align-items: center; justify-content: center; margin-bottom: 16px; color: var(--text-dim);">
                            <ion-icon name="document-text-outline" style="font-size: 2rem;"></ion-icon>
                        </div>
                        <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 4px;">Tidak ada dokumen tercatat</div>
                        <p style="color: var(--text-dim); font-size: 0.9rem;">Mulai dengan membuat Berita Acara baru untuk suatu insiden.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($bas->hasPages())
    <div style="margin-top: 24px;">
        {{ $bas->links('pagination::bootstrap-5') }}
    </div>
@endif

@endsection
