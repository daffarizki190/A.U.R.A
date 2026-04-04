@extends('layouts.app')

@section('content')
<header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em;">Temuan Aset</h1>
        <p style="color: var(--text-dim); font-size: 0.95rem;">Manajemen dan pelacakan isu aset yang berjalan</p>
    </div>
    @if(auth()->user()->role == 'SPV' || auth()->user()->role == 'IT')
    <a href="{{ route('findings.create') }}" class="btn-primary" style="gap: 8px;">
        <ion-icon name="add-circle" style="font-size: 1.2rem;"></ion-icon>
        Buat Laporan
    </a>
    @endif
</header>

<div class="glass-card" style="padding: 24px; margin-bottom: 32px; border-radius: 20px;">
    <form action="{{ route('findings.index') }}" method="GET" style="display: flex; gap: 16px; align-items: flex-end;">
        <div style="flex: 1; max-width: 200px;">
            <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px; display: block;">Filter Status</label>
            <select name="status" class="input" style="height: 48px;">
                <option value="">Semua Status</option>
                <option value="Pending Approval" {{ request('status') == 'Pending Approval' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                <option value="Open" {{ request('status') == 'Open' ? 'selected' : '' }}>Open</option>
                <option value="On Progress" {{ request('status') == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>Done</option>
            </select>
        </div>
        <div style="flex: 2;">
            <label style="font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px; display: block;">Cari Lokasi Aset</label>
            <div style="position: relative;">
                <ion-icon name="search-outline" style="position: absolute; left: 16px; top: 14px; color: var(--text-dim);"></ion-icon>
                <input type="text" name="location" class="input" style="padding-left: 44px; height: 48px;" value="{{ request('location') }}" placeholder="Cari berdasarkan area, gate, atau lantai...">
            </div>
        </div>
        <button type="submit" class="btn-primary" style="height: 48px; min-width: 120px;">Cari</button>
        @if(request()->anyFilled(['status', 'location']))
            <a href="{{ route('findings.index') }}" class="btn-primary" style="height: 48px; background: rgba(0,0,0,0.03); color: var(--text-main);">Reset</a>
        @endif
    </form>
</div>

<div class="glass-card table-container" style="border-radius: 20px;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Tanggal Pendaftaran</th>
                <th>Lokasi</th>
                <th>Kategori Aset</th>
                <th>Penanggung Jawab</th>
                <th>Status</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($findings as $finding)
                <tr style="cursor: pointer;" onclick="window.location='{{ route('findings.show', $finding->id) }}'">
                    <td style="font-weight: 700; font-family: 'JetBrains Mono', monospace; color: var(--primary);">{{ $finding->finding_code }}</td>
                    <td style="color: var(--text-secondary);">{{ \Carbon\Carbon::parse($finding->finding_date)->format('d/m/Y') }}</td>
                    <td style="font-weight: 600;">{{ $finding->location }}</td>
                    <td>{{ $finding->asset_type }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--border-bold); display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800;">
                                {{ substr($finding->pic ? $finding->pic->name : 'N', 0, 1) }}
                            </div>
                            <span>{{ $finding->pic ? $finding->pic->name : 'Belum Ditentukan' }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-{{ strtolower(str_replace(' ', '', $finding->status)) }}">
                            {{ $finding->status }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('findings.show', $finding->id) }}" style="color: var(--primary); text-decoration: none; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 4px;">
                            Lihat Detail <ion-icon name="chevron-forward"></ion-icon>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 80px 20px;">
                        <div style="display: inline-flex; width: 64px; height: 64px; border-radius: 20px; background: rgba(0,0,0,0.02); align-items: center; justify-content: center; margin-bottom: 16px; color: var(--text-dim);">
                            <ion-icon name="search-outline" style="font-size: 2rem;"></ion-icon>
                        </div>
                        <div style="font-weight: 700; font-size: 1.1rem; margin-bottom: 4px;">Tidak ada data ditemukan</div>
                        <p style="color: var(--text-dim); font-size: 0.9rem;">Coba sesuaikan filter pencarian atau buat laporan temuan baru.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($findings->hasPages())
    <div style="margin-top: 24px;">
        {{ $findings->links('pagination::bootstrap-5') }}
    </div>
@endif

@endsection
