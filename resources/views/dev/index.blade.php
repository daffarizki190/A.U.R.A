@extends('layouts.app')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     HEADER
══════════════════════════════════════════════════════════════ --}}
<header style="margin-bottom: 36px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
            <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--success); box-shadow: 0 0 0 3px rgba(52,199,89,0.2); animation: pulse 2s infinite;"></div>
            <span style="font-size: 0.75rem; font-weight: 700; color: var(--success); text-transform: uppercase; letter-spacing: 0.1em;">SISTEM AKTIF</span>
        </div>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em;">Dev Monitor</h1>
        <p style="color: var(--text-dim); font-size: 0.9rem; margin-top: 4px;">A.U.R.A System Health Dashboard — Real-time monitoring</p>
    </div>
    <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
        <button onclick="manualRefresh()" id="refresh-btn" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border); color: var(--text-dim); padding: 7px 14px; border-radius: 10px; font-size: 0.75rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.2s;" onmouseover="this.style.color='var(--text-main)'" onmouseout="this.style.color='var(--text-dim)'">
            <ion-icon name="refresh-outline"></ion-icon> Refresh
        </button>
    </div>
</header>

{{-- ══════════════════════════════════════════════════════════════
     ALERTS (jika ada)
══════════════════════════════════════════════════════════════ --}}
@if(count($alerts) > 0)
    {{-- ⚠ ALERT BANNER: lampu merah darurat --}}
    @php
        $hasCritical = collect($alerts)->contains('level', 'critical');
        $hasWarn     = collect($alerts)->contains('level', 'warn');
        $bannerColor = $hasCritical ? '#ff3b30' : ($hasWarn ? '#ff9500' : '#007aff');
        $bannerBg    = $hasCritical ? 'rgba(255,59,48,0.06)' : ($hasWarn ? 'rgba(255,149,0,0.06)' : 'rgba(0,122,255,0.06)');
        $bangIcon    = $hasCritical ? 'warning' : ($hasWarn ? 'alert-circle' : 'information-circle');
    @endphp

    <div id="alerts-container" style="
        margin-bottom: 28px;
        border: 1.5px solid {{ $bannerColor }};
        border-radius: 20px;
        overflow: hidden;
        background: {{ $bannerBg }};
        animation: {{ $hasCritical ? 'borderFlash 1.2s ease-in-out infinite' : 'none' }};
    ">
        {{-- Header banner --}}
        <div style="
            background: {{ $bannerColor }};
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        ">
            {{-- Lampu berkedip --}}
            <div style="
                width: 12px; height: 12px;
                border-radius: 50%;
                background: white;
                animation: {{ $hasCritical ? 'lampFlash 0.8s ease-in-out infinite' : 'pulse 2s infinite' }};
                flex-shrink: 0;
                box-shadow: 0 0 8px white;
            "></div>
            <ion-icon name="{{ $bangIcon }}" style="color: white; font-size: 1.1rem; flex-shrink: 0;"></ion-icon>
            <span style="font-size: 0.78rem; font-weight: 800; color: white; text-transform: uppercase; letter-spacing: 0.1em;">
                {{ $hasCritical ? '⚠ PERHATIAN — ADA MASALAH KRITIS' : ($hasWarn ? '⚠ PERINGATAN SISTEM' : 'ℹ INFORMASI SISTEM') }}
            </span>
            <span style="margin-left: auto; font-size: 0.7rem; font-weight: 700; color: rgba(255,255,255,0.8);">
                {{ count($alerts) }} item
            </span>
        </div>

        {{-- List alert --}}
        <div style="display: flex; flex-direction: column;">
            @foreach($alerts as $i => $alert)
                @php
                    $isLast = $i === count($alerts) - 1;
                    $styles = [
                        'critical' => ['dot' => '#ff3b30', 'label' => 'KRITIS',    'icon' => 'warning'],
                        'warn'     => ['dot' => '#ff9500', 'label' => 'PERINGATAN','icon' => 'alert-circle'],
                        'info'     => ['dot' => '#007aff', 'label' => 'INFO',      'icon' => 'information-circle'],
                    ];
                    $s = $styles[$alert['level']] ?? $styles['info'];
                @endphp
                <div style="
                    display: flex;
                    align-items: center;
                    gap: 14px;
                    padding: 14px 20px;
                    {{ !$isLast ? 'border-bottom: 1px solid rgba(255,255,255,0.06);' : '' }}
                    animation: {{ $alert['level'] === 'critical' ? 'rowFlash 1.2s ease-in-out infinite' : 'none' }};
                ">
                    {{-- Dot indikator --}}
                    <div style="
                        width: 8px; height: 8px;
                        border-radius: 50%;
                        background: {{ $s['dot'] }};
                        flex-shrink: 0;
                        animation: {{ $alert['level'] === 'critical' ? 'dotPulse 1s ease-in-out infinite' : 'pulse 3s infinite' }};
                        box-shadow: 0 0 6px {{ $s['dot'] }};
                    "></div>

                    <ion-icon name="{{ $s['icon'] }}" style="color: {{ $s['dot'] }}; font-size: 1.15rem; flex-shrink: 0;"></ion-icon>

                    <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-main); flex: 1;">
                        {{ $alert['msg'] }}
                    </span>

                    <span style="
                        font-size: 0.65rem;
                        font-weight: 900;
                        text-transform: uppercase;
                        letter-spacing: 0.08em;
                        color: {{ $s['dot'] }};
                        background: rgba({{ $alert['level'] === 'critical' ? '255,59,48' : ($alert['level'] === 'warn' ? '255,149,0' : '0,122,255') }}, 0.12);
                        padding: 3px 10px;
                        border-radius: 6px;
                        border: 1px solid {{ $s['dot'] }}40;
                        flex-shrink: 0;
                    ">{{ $s['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- ══════════════════════════════════════════════════════════════
     ROW 1 — HEALTH STATUS CARDS
══════════════════════════════════════════════════════════════ --}}
<div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 16px; margin-bottom: 28px;">

    {{-- Database --}}
    @php $db = $health['database']; @endphp
    <div class="glass-card" style="padding: 20px 22px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: {{ $db['status'] === 'online' ? 'var(--success)' : 'var(--danger)' }};"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
            <ion-icon name="server" style="font-size: 1.3rem; color: var(--text-dim);"></ion-icon>
            <span style="font-size: 0.65rem; font-weight: 800; padding: 3px 10px; border-radius: 20px; background: {{ $db['status'] === 'online' ? 'rgba(52,199,89,0.15)' : 'rgba(255,59,48,0.15)' }}; color: {{ $db['status'] === 'online' ? 'var(--success)' : 'var(--danger)' }};">● {{ strtoupper($db['status']) }}</span>
        </div>
        <div style="font-weight: 800; font-size: 0.82rem; color: var(--text-main); margin-bottom: 4px;">Database</div>
        <div style="font-size: 0.7rem; color: var(--text-dim); line-height: 1.7;">
            {{ $db['driver'] ?? 'pgsql' }} • Port {{ $db['port'] ?? '6543' }}<br>
            Latensi: <strong style="color: {{ ($db['latency'] ?? 0) < 200 ? 'var(--success)' : 'var(--warning)' }}">{{ $db['latency'] ?? '-' }} ms</strong><br>
            Tabel: {{ $db['tables'] ?? '-' }}
        </div>
    </div>

    {{-- Vercel --}}
    @php $vc = $health['vercel']; @endphp
    <div class="glass-card" style="padding: 20px 22px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: {{ $vc['status'] === 'online' ? 'var(--success)' : ($vc['status'] === 'warn' ? 'var(--warning)' : 'var(--danger)') }};"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
            <ion-icon name="cloud" style="font-size: 1.3rem; color: var(--text-dim);"></ion-icon>
            <span style="font-size: 0.65rem; font-weight: 800; padding: 3px 10px; border-radius: 20px; background: {{ $vc['status'] === 'online' ? 'rgba(52,199,89,0.15)' : ($vc['status'] === 'warn' ? 'rgba(255,149,0,0.15)' : 'rgba(255,59,48,0.15)') }}; color: {{ $vc['status'] === 'online' ? 'var(--success)' : ($vc['status'] === 'warn' ? 'var(--warning)' : 'var(--danger)') }};">● {{ strtoupper($vc['status']) }}</span>
        </div>
        <div style="font-weight: 800; font-size: 0.82rem; color: var(--text-main); margin-bottom: 4px;">Vercel Server</div>
        <div style="font-size: 0.7rem; color: var(--text-dim); line-height: 1.7;">
            HTTP {{ $vc['http_code'] ?? '-' }}<br>
            Latensi: <strong style="color: {{ ($vc['latency'] ?? 9999) < 1500 ? 'var(--success)' : 'var(--warning)' }}">{{ $vc['latency'] ?? '-' }} ms</strong><br>
            <span style="font-size: 0.62rem; color: var(--text-dim); word-break: break-all;">a-u-r-a.vercel.app</span>
        </div>
    </div>

    {{-- PHP / Laravel --}}
    @php $ph = $health['php']; @endphp
    <div class="glass-card" style="padding: 20px 22px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--primary);"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
            <ion-icon name="code-slash" style="font-size: 1.3rem; color: var(--text-dim);"></ion-icon>
            <span style="font-size: 0.65rem; font-weight: 800; padding: 3px 10px; border-radius: 20px; background: rgba(0,122,255,0.12); color: var(--primary);">● RUNNING</span>
        </div>
        <div style="font-weight: 800; font-size: 0.82rem; color: var(--text-main); margin-bottom: 4px;">PHP / Laravel</div>
        <div style="font-size: 0.7rem; color: var(--text-dim); line-height: 1.7;">
            PHP {{ $ph['php'] }}<br>
            Laravel {{ $ph['laravel'] }}<br>
            Debug: <strong style="color: {{ config('app.debug') ? 'var(--warning)' : 'var(--success)' }}">{{ $ph['debug'] }}</strong>
        </div>
    </div>

    {{-- Cache --}}
    @php $ca = $health['cache']; @endphp
    <div class="glass-card" style="padding: 20px 22px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: {{ $ca['status'] === 'online' ? 'var(--success)' : 'var(--warning)' }};"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
            <ion-icon name="flash" style="font-size: 1.3rem; color: var(--text-dim);"></ion-icon>
            <span style="font-size: 0.65rem; font-weight: 800; padding: 3px 10px; border-radius: 20px; background: {{ $ca['status'] === 'online' ? 'rgba(52,199,89,0.15)' : 'rgba(255,149,0,0.15)' }}; color: {{ $ca['status'] === 'online' ? 'var(--success)' : 'var(--warning)' }};">● {{ strtoupper($ca['status']) }}</span>
        </div>
        <div style="font-weight: 800; font-size: 0.82rem; color: var(--text-main); margin-bottom: 4px;">Cache</div>
        <div style="font-size: 0.7rem; color: var(--text-dim); line-height: 1.7;">
            Driver: {{ $ca['driver'] }}<br>
            Read/Write: <strong style="color: var(--success)">OK</strong>
        </div>
    </div>

    {{-- Queue --}}
    @php $qu = $health['queue']; @endphp
    <div class="glass-card" style="padding: 20px 22px; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: {{ $qu['status'] === 'online' ? 'var(--success)' : 'var(--warning)' }};"></div>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
            <ion-icon name="layers" style="font-size: 1.3rem; color: var(--text-dim);"></ion-icon>
            <span style="font-size: 0.65rem; font-weight: 800; padding: 3px 10px; border-radius: 20px; background: {{ $qu['status'] === 'online' ? 'rgba(52,199,89,0.15)' : 'rgba(255,149,0,0.15)' }}; color: {{ $qu['status'] === 'online' ? 'var(--success)' : 'var(--warning)' }};">● {{ strtoupper($qu['status']) }}</span>
        </div>
        <div style="font-weight: 800; font-size: 0.82rem; color: var(--text-main); margin-bottom: 4px;">Queue</div>
        <div style="font-size: 0.7rem; color: var(--text-dim); line-height: 1.7;">
            Driver: {{ $qu['driver'] }}<br>
            Pending: <strong>{{ $qu['pending'] }}</strong> | Failed: <strong style="color: {{ $qu['failed'] > 0 ? 'var(--warning)' : 'var(--text-dim)' }}">{{ $qu['failed'] }}</strong>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     ROW 2 — DATA STATISTICS
══════════════════════════════════════════════════════════════ --}}
<div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 28px;">

    {{-- Temuan Breakdown (Horizontal Strip) --}}
    <div class="glass-card" style="padding: 18px 26px;">
        <div style="display: flex; align-items: center; gap: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; border-right: 1px solid var(--border); padding-right: 24px; min-width: 200px;">
                <ion-icon name="analytics" style="color: var(--primary); font-size: 1.4rem;"></ion-icon>
                <div style="display: flex; flex-direction: column;">
                    <h3 style="font-size: 0.9rem; font-weight: 700;">Temuan Aset</h3>
                    <span style="font-size: 0.7rem; color: var(--text-dim); font-weight: 500;">Total: {{ $stats['findings_total'] }}</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                <div style="display: flex; align-items: center; gap: 14px; padding: 10px 18px; background: rgba(255,149,0,0.06); border: 1px solid rgba(255,149,0,0.18); border-radius: 12px; flex: 1;">
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--warning);">{{ $stats['findings_pending'] }}</div>
                    <div style="font-size: 0.65rem; color: var(--text-dim); line-height: 1.2; font-weight: 700; text-transform: uppercase;">WAITING APPROVED</div>
                </div>
                <div style="display: flex; align-items: center; gap: 14px; padding: 10px 18px; background: rgba(0,122,255,0.06); border: 1px solid rgba(0,122,255,0.18); border-radius: 12px; flex: 1;">
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--primary);">{{ $stats['findings_open'] + $stats['findings_progress'] }}</div>
                    <div style="font-size: 0.65rem; color: var(--text-dim); line-height: 1.2; font-weight: 700; text-transform: uppercase;">ON PROGRES</div>
                </div>
                <div style="display: flex; align-items: center; gap: 14px; padding: 10px 18px; background: rgba(52,199,89,0.06); border: 1px solid rgba(52,199,89,0.18); border-radius: 12px; flex: 1;">
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--success);">{{ $stats['findings_done'] }}</div>
                    <div style="font-size: 0.65rem; color: var(--text-dim); line-height: 1.2; font-weight: 700; text-transform: uppercase;">DONE</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Berita Acara Breakdown (Horizontal Strip) --}}
    <div class="glass-card" style="padding: 18px 26px;">
        <div style="display: flex; align-items: center; gap: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; border-right: 1px solid var(--border); padding-right: 24px; min-width: 200px;">
                <ion-icon name="document-text" style="color: var(--warning); font-size: 1.4rem;"></ion-icon>
                <div style="display: flex; flex-direction: column;">
                    <h3 style="font-size: 0.9rem; font-weight: 700;">Berita Acara</h3>
                    <span style="font-size: 0.7rem; color: var(--text-dim); font-weight: 500;">Total: {{ $stats['ba_total'] }}</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                <div style="display: flex; align-items: center; gap: 14px; padding: 10px 18px; background: rgba(255,149,0,0.06); border: 1px solid rgba(255,149,0,0.15); border-radius: 12px; flex: 1;">
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--warning);">{{ $stats['ba_active'] }}</div>
                    <div style="font-size: 0.68rem; color: var(--text-dim); font-weight: 700; text-transform: uppercase;">Sedang Diproses</div>
                </div>
                <div style="display: flex; align-items: center; gap: 14px; padding: 10px 18px; background: rgba(52,199,89,0.06); border: 1px solid rgba(52,199,89,0.15); border-radius: 12px; flex: 1;">
                    <div style="font-size: 1.6rem; font-weight: 800; color: var(--success);">{{ $stats['ba_done'] }}</div>
                    <div style="font-size: 0.68rem; color: var(--text-dim); font-weight: 700; text-transform: uppercase;">Selesai</div>
                </div>
            </div>
        </div>
    </div>

    {{-- System Info (Horizontal Bar) --}}
    <div class="glass-card" style="padding: 16px 26px;">
        <div style="display: flex; align-items: center; gap: 24px;">
            <div style="display: flex; align-items: center; gap: 12px; border-right: 1px solid var(--border); padding-right: 24px; min-width: 200px;">
                <ion-icon name="settings" style="color: var(--text-dim); font-size: 1.4rem;"></ion-icon>
                <div style="display: flex; flex-direction: column;">
                    <h3 style="font-size: 0.9rem; font-weight: 700;">Info Sistem</h3>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 32px; flex: 1; overflow-x: auto; white-space: nowrap;">
                @foreach([
                    ['k' => 'Memory', 'v' => $health['php']['memory']],
                    ['k' => 'Users',  'v' => $stats['users']],
                    ['k' => 'Logs',   'v' => number_format($stats['activity_logs'])],
                ] as $row)
                    <div style="display: flex; flex-direction: column; gap: 2px;">
                        <span style="font-size: 0.62rem; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">{{ $row['k'] }}</span>
                        <span style="font-size: 0.9rem; font-weight: 800; color: var(--text-main);">{{ $row['v'] }}</span>
                    </div>
                @endforeach
                
                <div style="height: 24px; width: 1px; background: var(--border);"></div>

                <div style="display: flex; gap: 8px;">
                    @foreach($stats['roles'] as $r)
                        <span style="font-size: 0.65rem; font-weight: 700; padding: 3px 10px; border-radius: 6px; background: rgba(0,122,255,0.08); border: 1px solid rgba(0,122,255,0.15); color: var(--primary);">{{ $r->role }}: {{ $r->total }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     ROW 3 — ACTIVITY LOG
══════════════════════════════════════════════════════════════ --}}
<div class="glass-card" style="padding: 28px;">
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
        <ion-icon name="terminal" style="color: var(--accent); font-size: 1.1rem;"></ion-icon>
        <h3 style="font-size: 0.95rem; font-weight: 700;">Live Activity Log</h3>
        <span style="font-size: 0.68rem; color: var(--text-dim); margin-left: 4px;">15 aktivitas terakhir dari seluruh sistem</span>
        <a href="{{ route('admin.logs') }}" style="margin-left: auto; font-size: 0.73rem; font-weight: 700; color: var(--primary); text-decoration: none;">Lihat Semua →</a>
    </div>

    {{-- Terminal-style log --}}
    <div style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 18px; font-family: 'SF Mono', 'Courier New', monospace; font-size: 0.72rem; line-height: 1.9; max-height: 340px; overflow-y: auto;">
        @forelse($logs as $log)
            @php
                $action = strtolower($log->action ?? '');
                $color = str_contains($action, 'delete') ? '#ff453a'
                       : (str_contains($action, 'create') ? '#32d74b'
                       : (str_contains($action, 'update') || str_contains($action, 'approve') ? '#ffd60a'
                       : '#636366'));
            @endphp
            <div style="display: flex; gap: 12px; padding: 2px 0;">
                <span style="color: #636366; flex-shrink: 0;">{{ $log->created_at->format('H:i:s') }}</span>
                <span style="color: #8e8e93; flex-shrink: 0;">{{ $log->user?->name ?? 'System' }}</span>
                <span style="color: {{ $color }}; flex-shrink: 0; text-transform: uppercase; font-size: 0.65rem; padding-top: 1px;">{{ $log->action ?? '-' }}</span>
                <span style="color: #c7c7cc; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $log->description ?? $log->model_type ?? '' }}</span>
            </div>
        @empty
            <div style="color: #636366; text-align: center; padding: 20px;">// Belum ada aktivitas terekam</div>
        @endforelse
    </div>
</div>

{{-- Silent auto-refresh setiap 60 detik (1 menit) --}}
<script>
setTimeout(() => { window.location.reload(); }, 60000);

function manualRefresh() {
    window.location.reload();
}
</script>

<style>
@keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 3px rgba(52,199,89,0.2); }
    50%       { box-shadow: 0 0 0 7px rgba(52,199,89,0.05); }
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to   { transform: rotate(360deg); }
}
/* Lampu merah blinking */
@keyframes lampFlash {
    0%, 100% { opacity: 1;   box-shadow: 0 0 12px white, 0 0 24px rgba(255,255,255,0.6); }
    50%       { opacity: 0.2; box-shadow: none; }
}
@keyframes dotPulse {
    0%, 100% { transform: scale(1);   box-shadow: 0 0 6px currentColor; }
    50%       { transform: scale(1.5); box-shadow: 0 0 14px currentColor; }
}
@keyframes borderFlash {
    0%, 100% { box-shadow: 0 0 0 0   rgba(255,59,48,0);   border-color: #ff3b30; }
    50%       { box-shadow: 0 0 16px 4px rgba(255,59,48,0.35); border-color: rgba(255,59,48,0.5); }
}
@keyframes rowFlash {
    0%, 100% { background: transparent; }
    50%       { background: rgba(255,59,48,0.06); }
}
</style>

@endsection
