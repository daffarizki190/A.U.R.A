@extends('layouts.app')

@section('content')
<header style="margin-bottom: 40px;">
    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; color: var(--text-main);">Log Aktivitas Sistem</h1>
    <p style="color: var(--text-dim); font-size: 0.95rem;">Rekam jejak seluruh perubahan data pada sistem A.U.R.A</p>
</header>

<div class="glass-card" style="padding: 0; overflow: hidden; border-radius: 20px;">
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="padding-left: 32px;">Waktu</th>
                    <th>Petugas (Jabatan)</th>
                    <th>Aksi</th>
                    <th>Objek / Model</th>
                    <th>Detail Perubahan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr style="transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.01)'" onmouseout="this.style.background='transparent'">
                    <td style="padding-left: 32px;">
                        <div style="font-weight: 600; color: var(--text-main);">{{ $log->created_at->format('d/m/Y') }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem;">
                                {{ substr($log->user->name ?? 'S', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 600;">{{ $log->user->name ?? 'System' }}</div>
                                <div style="font-size: 0.7rem; color: var(--primary); font-weight: 700; text-transform: uppercase;">{{ $log->user->role ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-{{ $log->action == 'created' ? 'success' : ($log->action == 'updated' ? 'onprogres' : 'danger') }}" style="text-transform: uppercase; font-size: 0.65rem; padding: 4px 10px;">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: 600; font-size: 0.85rem;">{{ class_basename($log->model_type) }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">ID: #{{ $log->model_id }}</div>
                    </td>
                    <td style="max-width: 300px;">
                        @if($log->changes)
                            <div style="font-size: 0.75rem; padding: 12px; background: rgba(0,0,0,0.02); border-radius: 8px; border: 1px solid var(--border);">
                                @if($log->action == 'updated')
                                    @isset($log->changes['after'])
                                        <div style="color: var(--text-main); font-weight: 700; margin-bottom: 4px;">Update Kolom:</div>
                                        <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                            @foreach($log->changes['after'] as $key => $val)
                                                <span style="background: rgba(0,122,255,0.05); color: var(--primary); padding: 2px 6px; border-radius: 4px; border: 1px solid rgba(0,122,255,0.1);">{{ $key }}</span>
                                            @endforeach
                                        </div>
                                    @endisset
                                @elseif($log->action == 'created')
                                    <span style="color: var(--success); font-weight: 600;">Data baru didaftarkan ke sistem.</span>
                                @else
                                    <span style="color: var(--danger); font-weight: 600;">Data dihapus selamanya.</span>
                                @endif
                            </div>
                        @else
                            <span style="color: var(--text-dim); font-style: italic; font-size: 0.75rem;">Tidak ada detail perubahan.</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 64px 0; color: var(--text-dim);">
                        <ion-icon name="journal-outline" style="font-size: 3rem; opacity: 0.2; margin-bottom: 16px;"></ion-icon>
                        <div>Belum ada histori terekam.</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 32px;">
    {{ $logs->links() }}
</div>

<style>
    .pagination {
        display: flex;
        gap: 8px;
        list-style: none;
        padding: 0;
    }
    .page-item .page-link {
        padding: 8px 16px;
        border-radius: 8px;
        background: white;
        border: 1px solid var(--border);
        color: var(--text-main);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .page-item.active .page-link {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }
</style>
@endsection
