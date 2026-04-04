@extends('layouts.app')

@section('content')
<header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-end;">
    <div>
        <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em; color: var(--text-main);">
            @if(auth()->user()->role == 'CPM') Dasbor Manajerial @else Ruang Kerja @endif
        </h1>
        <p style="color: var(--text-dim); font-size: 0.95rem;">
            @if(auth()->user()->role == 'CPM') Ringkasan pemantauan aset komprehensif @else Pantau progres tugas dan status laporan Anda @endif
        </p>
    </div>
    <div style="text-align: right;">
        <div style="font-size: 0.9rem; font-weight: 600;">{{ now()->translatedFormat('l, d F Y') }}</div>
        <div style="font-size: 0.75rem; color: var(--text-dim);">Sistem Aktif • Server Beroperasi Normal</div>
    </div>
</header>

@if(auth()->user()->role == 'CPM')
    <!-- ===================== CPM LAYOUT ===================== -->
    <div class="stats-grid">
        <div class="stat-card glass-card gradient-card-danger">
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
            <div style="margin-top: 8px; font-size: 0.75rem; color: var(--text-dim); font-weight: 500;">Sedang diperbaiki tim lapangan</div>
        </div>
        
        <div class="stat-card glass-card gradient-card-primary">
            <div class="stat-header">
                <div class="stat-label">Menunggu Acc</div>
                <ion-icon name="time" style="color: var(--primary); font-size: 1.5rem;"></ion-icon>
            </div>
            <div class="stat-value">{{ $stats['findings_pending'] }}</div>
            <div style="margin-top: 8px; font-size: 0.75rem; color: var(--primary); font-weight: 600;">Butuh otorisasi Anda</div>
        </div>
        
        <div class="stat-card glass-card">
            <div class="stat-header">
                <div class="stat-label">Total Temuan</div>
                <ion-icon name="analytics" style="color: var(--accent); font-size: 1.5rem;"></ion-icon>
            </div>
            <div class="stat-value">{{ $stats['findings_total'] }}</div>
            <div style="margin-top: 8px; font-size: 0.75rem; color: var(--text-dim); font-weight: 500;">Keseluruhan record sistem</div>
        </div>
    </div>

    <div class="dashboard-grid cpm-layout">
        <div class="glass-card" style="padding: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
                <div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">Persetujuan Tertunda</h3>
                    <div style="font-size: 0.8rem; color: var(--text-dim);">Tinjau dan proses laporan agar dapat dikerjakan.</div>
                </div>
                <a href="{{ route('findings.index', ['status' => 'Pending Approval']) }}" class="btn-primary" style="padding: 6px 14px; font-size: 0.75rem; background: rgba(0, 122, 255, 0.1); color: var(--primary);">Lihat Semua</a>
            </div>

            @if(count($role_data['pending_approvals']) > 0)
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID Laporan</th>
                                <th>Lokasi / Aset</th>
                                <th>Pelapor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role_data['pending_approvals'] as $finding)
                            <tr>
                                <td style="font-weight: 600; color: var(--accent);">{{ $finding->finding_code }}</td>
                                <td>
                                    <div style="font-weight: 600;">{{ $finding->asset_type }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-dim);">{{ $finding->location }}</div>
                                </td>
                                <td>{{ $finding->reporter }}</td>
                                <td>
                                    <a href="{{ route('findings.show', $finding->id) }}" class="btn-primary" style="padding: 6px 12px; font-size: 0.75rem; border-radius: 8px;">Tinjau</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <ion-icon name="checkmark-circle" style="font-size: 3rem; color: var(--success); margin-bottom: 12px;"></ion-icon>
                    <div>Tidak ada dokumen yang menumpuk. Luar biasa!</div>
                </div>
            @endif
        </div>

        <div class="glass-card" style="padding: 32px;">
            <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 24px;">Aktivitas Terkini</h3>
            
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @forelse($role_data['recent_activities'] as $activity)
                    <a href="{{ route('findings.show', $activity->id) }}" style="text-decoration: none; display: flex; align-items: start; gap: 16px; padding: 16px; border-radius: var(--radius-m); background: rgba(255,255,255,0.02); border: 1px solid var(--border); transition: all 0.2s;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center; color: var(--text-secondary); flex-shrink: 0;">
                            <ion-icon name="{{ $activity->status == 'Done' ? 'checkmark-circle' : 'bulb' }}" style="font-size: 1.2rem;"></ion-icon>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-main);">{{ $activity->finding_code }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 2px;">{{ Str::limit($activity->description, 40) }}</div>
                            <div style="font-size: 0.7rem; color: var(--primary); margin-top: 6px; font-weight: 600;">{{ $activity->status }}</div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state" style="padding: 20px;">Belum ada aktivitas terekam.</div>
                @endforelse
            </div>
        </div>
    </div>

@else
    <!-- ===================== SPV / PIC LAYOUT ===================== -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
        <div class="stat-card glass-card gradient-card-primary">
            <div class="stat-header">
                <div class="stat-label">Tugas Aktif SAYA</div>
                <ion-icon name="construct" style="color: var(--primary); font-size: 1.5rem;"></ion-icon>
            </div>
            <div class="stat-value">{{ count($role_data['my_active_tasks']) }}</div>
            <div style="margin-top: 8px; font-size: 0.75rem; color: var(--primary); font-weight: 600;">Dalam status Open/On Progress</div>
        </div>
        
        <div class="stat-card glass-card">
            <div class="stat-header">
                <div class="stat-label">Laporan Belum Di-ACC</div>
                <ion-icon name="time" style="color: var(--warning); font-size: 1.5rem;"></ion-icon>
            </div>
            <div class="stat-value">{{ count($role_data['my_submissions']) }}</div>
            <div style="margin-top: 8px; font-size: 0.75rem; color: var(--text-dim); font-weight: 500;">Menunggu persetujuan CPM</div>
        </div>
    </div>

    <div class="dashboard-grid spv-layout">
        <div class="glass-card" style="padding: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
                <div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">Pekerjaan Saya</h3>
                    <div style="font-size: 0.8rem; color: var(--text-dim);">Daftar kendala yang harus Anda eksekusi.</div>
                </div>
            </div>

            @if(count($role_data['my_active_tasks']) > 0)
                <div style="display: flex; flex-direction: column;">
                    @foreach($role_data['my_active_tasks'] as $task)
                        <a href="{{ route('findings.show', $task->id) }}" style="text-decoration: none;" class="task-card">
                            <div>
                                <div class="task-card-title">{{ $task->finding_code }} - {{ $task->asset_type }}</div>
                                <div class="task-card-subtitle"><ion-icon name="location-outline" style="vertical-align: middle;"></ion-icon> {{ $task->location }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                <span class="badge badge-{{ strtolower(str_replace(' ', '', $task->status)) }}">{{ $task->status }}</span>
                                <div style="margin-top: 8px; font-size: 0.7rem; color: var(--text-dim);">Tenggat: {{ $task->estimated_completion_date ?: '-' }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <ion-icon name="cafe-outline" style="font-size: 3rem; color: var(--text-dim); margin-bottom: 12px;"></ion-icon>
                    <div>Tidak ada tugas aktif di antrean Anda.</div>
                </div>
            @endif
        </div>

        <div class="glass-card" style="padding: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
                <div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">Status Laporan Saya</h3>
                    <div style="font-size: 0.8rem; color: var(--text-dim);">Temuan yang Anda laporkan dan menunggu ACC.</div>
                </div>
            </div>

            @if(count($role_data['my_submissions']) > 0)
                <div style="display: flex; flex-direction: column;">
                    @foreach($role_data['my_submissions'] as $submission)
                        <div class="task-card" style="cursor: default;">
                            <div>
                                <div class="task-card-title" style="font-size: 1rem;">{{ $submission->finding_code }}</div>
                                <div class="task-card-subtitle">{{ $submission->asset_type }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                <span class="badge badge-pendingapproval">Pending</span>
                                <a href="{{ route('findings.show', $submission->id) }}" style="margin-top: 8px; font-size: 0.75rem; color: var(--primary); font-weight: 600; text-decoration: none;">Lihat Detail &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <ion-icon name="checkmark-done-outline" style="font-size: 3rem; color: var(--success); margin-bottom: 12px;"></ion-icon>
                    <div>Semua laporan Anda telah diproses oleh CPM.</div>
                </div>
            @endif
        </div>
    </div>
@endif

@endsection
