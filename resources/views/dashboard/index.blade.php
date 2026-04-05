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
                <div class="stat-label">ON PROGRES</div>
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
                <div class="stat-label">Waiting Approved</div>
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
                                <th>Tgl Temuan</th>
                                <th>Lokasi atau Aset</th>
                                <th>Pelapor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role_data['pending_approvals'] as $finding)
                            <tr>
                                <td style="font-weight: 600; color: var(--accent);">{{ $finding->finding_code }}</td>
                                <td>{{ \Carbon\Carbon::parse($finding->finding_date)->format('d/m/Y') }}</td>
                                <td>
                                    <div style="font-weight: 600;">{{ $finding->asset_type }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-dim);">{{ $finding->location }}</div>
                                </td>
                                <td>{{ $finding->pic?->name ?? $finding->reporter ?? '-' }}</td>
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
                    <a href="{{ route('findings.show', $activity->id) }}" style="text-decoration: none; display: flex; align-items: start; gap: 16px; padding: 16px; border-radius: var(--radius-m); background: rgba(0,0,0,0.02); border: 1px solid var(--border); transition: all 0.2s;">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(0, 0, 0, 0.05); display: flex; align-items: center; justify-content: center; color: var(--text-secondary); flex-shrink: 0;">
                            <ion-icon name="{{ $activity->status == 'Done' ? 'checkmark-circle' : 'bulb' }}" style="font-size: 1.2rem;"></ion-icon>
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-main);">{{ $activity->finding_code }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 2px;">{{ Str::limit($activity->description, 40) }}</div>
                            <div style="font-size: 0.7rem; color: var(--primary); margin-top: 6px; font-weight: 600;">{{ $activity->status == 'Pending Approval' ? 'WAITING APPROVED' : ($activity->status == 'Done' ? 'DONE' : 'ON PROGRES') }}</div>
                        </div>
                    </a>
                @empty
                    <div class="empty-state" style="padding: 20px;">Belum ada aktivitas terekam.</div>
                @endforelse
            </div>
        </div>
    </div>

@else
    <!-- ===================== PEMILIK ATAU PIC LAYOUT ===================== -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));">
        <div class="stat-card glass-card gradient-card-primary">
            <div class="stat-header">
                <div class="stat-label">Laporan Aktif</div>
                <ion-icon name="construct" style="color: var(--primary); font-size: 1.5rem;"></ion-icon>
            </div>
            <div class="stat-value">{{ count($role_data['active_findings']) + count($role_data['active_ba']) }}</div>
            <div style="margin-top: 8px; font-size: 0.75rem; color: var(--primary); font-weight: 600;">Selesaikan tugas ini segera</div>
        </div>
        
        <div class="stat-card glass-card">
            <div class="stat-header">
                <div class="stat-label">Menunggu Persetujuan</div>
                <ion-icon name="time" style="color: var(--warning); font-size: 1.5rem;"></ion-icon>
            </div>
            <div class="stat-value">{{ count($role_data['waiting_findings']) }}</div>
            <div style="margin-top: 8px; font-size: 0.75rem; color: var(--text-dim); font-weight: 500;">Menunggu validasi manajemen CPM</div>
        </div>

        <div class="stat-card glass-card">
            <div class="stat-header">
                <div class="stat-label">Laporan Selesai</div>
                <ion-icon name="checkmark-done" style="color: var(--success); font-size: 1.5rem;"></ion-icon>
            </div>
            <div class="stat-value">{{ count($role_data['completed_findings']) + count($role_data['completed_ba']) }}</div>
            <div style="margin-top: 8px; font-size: 0.75rem; color: var(--success); font-weight: 600;">Record 5 laporan terakhir</div>
        </div>
    </div>

    <div class="dashboard-grid spv-layout">
        <!-- LEFT: ACTIVE REPORTS -->
        <div class="glass-card" style="padding: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
                <div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">Pekerjaan Aktif SAYA</h3>
                    <div style="font-size: 0.8rem; color: var(--text-dim);">Daftar laporan Temuan & Berita Acara yang sedang berjalan.</div>
                </div>
            </div>

            @php 
                $has_active = count($role_data['active_findings']) > 0 || count($role_data['active_ba']) > 0 || count($role_data['waiting_findings']) > 0;
            @endphp

            @if($has_active)
                <div style="display: flex; flex-direction: column; gap: 24px;">

                    {{-- LAYER 1: Active Findings (Temuan) --}}
                    @if(count($role_data['active_findings']) > 0)
                        <div style="background: rgba(0, 122, 255, 0.04); border: 1px solid rgba(0, 122, 255, 0.15); border-radius: 16px; overflow: hidden;">
                            <div style="padding: 12px 18px; border-bottom: 1px solid rgba(0, 122, 255, 0.12); display: flex; align-items: center; gap: 8px;">
                                <ion-icon name="build" style="color: var(--primary); font-size: 0.9rem;"></ion-icon>
                                <span style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--primary);">Temuan Aset</span>
                                <span style="margin-left: auto; background: rgba(0,122,255,0.15); color: var(--primary); font-size: 0.68rem; font-weight: 700; padding: 2px 8px; border-radius: 20px;">{{ count($role_data['active_findings']) }}</span>
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                @foreach($role_data['active_findings'] as $task)
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid rgba(0,0,0,0.04);">
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-main);">{{ $task->finding_code }}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $task->asset_type }} • {{ $task->location }}</div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0; margin-left: 12px;">
                                            <span class="badge badge-onprogres">ON PROGRES</span>
                                            <a href="{{ route('findings.show', $task->id) }}" class="btn-primary" style="padding: 5px 12px; font-size: 0.73rem; border-radius: 8px;">Buka</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- LAYER 2: Active BA (Berita Acara) --}}
                    @if(count($role_data['active_ba']) > 0)
                        <div style="background: rgba(255, 149, 0, 0.04); border: 1px solid rgba(255, 149, 0, 0.2); border-radius: 16px; overflow: hidden;">
                            <div style="padding: 12px 18px; border-bottom: 1px solid rgba(255, 149, 0, 0.12); display: flex; align-items: center; gap: 8px;">
                                <ion-icon name="document-text" style="color: var(--warning); font-size: 0.9rem;"></ion-icon>
                                <span style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--warning);">Berita Acara</span>
                                <span style="margin-left: auto; background: rgba(255,149,0,0.15); color: var(--warning); font-size: 0.68rem; font-weight: 700; padding: 2px 8px; border-radius: 20px;">{{ count($role_data['active_ba']) }}</span>
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                @foreach($role_data['active_ba'] as $ba)
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid rgba(0,0,0,0.04);">
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-main);">{{ $ba->ba_number }}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $ba->ba_type }} • {{ $ba->customer_name }}</div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0; margin-left: 12px;">
                                            <span class="badge badge-onprogres">ON PROGRES</span>
                                            <a href="{{ route('ba.show', $ba->id) }}" class="btn-primary" style="padding: 5px 12px; font-size: 0.73rem; border-radius: 8px;">Buka</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- LAYER 3: Waiting Approval --}}
                    @if(count($role_data['waiting_findings']) > 0)
                        <div style="background: rgba(255, 214, 10, 0.03); border: 1px dashed rgba(255, 214, 10, 0.35); border-radius: 16px; overflow: hidden;">
                            <div style="padding: 12px 18px; border-bottom: 1px dashed rgba(255, 214, 10, 0.2); display: flex; align-items: center; gap: 8px;">
                                <ion-icon name="time" style="color: var(--accent); font-size: 0.9rem;"></ion-icon>
                                <span style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--accent);">Menunggu Persetujuan CPM</span>
                                <span style="margin-left: auto; background: rgba(255,214,10,0.15); color: var(--accent); font-size: 0.68rem; font-weight: 700; padding: 2px 8px; border-radius: 20px;">{{ count($role_data['waiting_findings']) }}</span>
                            </div>
                            <div style="display: flex; flex-direction: column;">
                                @foreach($role_data['waiting_findings'] as $waiting)
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-bottom: 1px solid rgba(0,0,0,0.04);">
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-weight: 700; font-size: 0.88rem; color: var(--text-main);">{{ $waiting->finding_code }}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 2px;">{{ $waiting->asset_type }} • {{ $waiting->location }}</div>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0; margin-left: 12px;">
                                            <span class="badge badge-pendingapproval">WAITING APPROVED</span>
                                            <a href="{{ route('findings.show', $waiting->id) }}" class="btn-primary" style="padding: 5px 12px; font-size: 0.73rem; border-radius: 8px; background: rgba(255,255,255,0.06); color: var(--text-main);">Detail</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            @else
                <div class="empty-state">
                    <ion-icon name="checkmark-circle-outline" style="font-size: 3rem; color: var(--success); margin-bottom: 12px;"></ion-icon>
                    <div>Semua tugas Anda telah bersih.</div>
                </div>
            @endif
        </div>

        <!-- RIGHT: COMPLETED HISTORY -->
        <div class="glass-card" style="padding: 32px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px;">
                <div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">Riwayat Laporan Selesai</h3>
                    <div style="font-size: 0.8rem; color: var(--text-dim);">Laporan terakhir yang telah dituntaskan.</div>
                </div>
            </div>

            @php 
                $has_completed = count($role_data['completed_findings']) > 0 || count($role_data['completed_ba']) > 0;
            @endphp

            @if($has_completed)
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    {{-- Completed Findings --}}
                    @foreach($role_data['completed_findings'] as $done)
                        <div class="task-card" style="padding: 12px 16px; opacity: 0.8;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 0.85rem;">{{ $done->finding_code }}</div>
                                <div style="font-size: 0.7rem; color: var(--text-dim);">Selesai pada {{ \Carbon\Carbon::parse($done->updated_at)->format('d/m/Y') }}</div>
                            </div>
                            <a href="{{ route('findings.show', $done->id) }}" style="color: var(--primary); font-size: 1.1rem;"><ion-icon name="arrow-forward-circle"></ion-icon></a>
                        </div>
                    @endforeach

                    {{-- Completed BA --}}
                    @foreach($role_data['completed_ba'] as $ba_done)
                        <div class="task-card" style="padding: 12px 16px; opacity: 0.8;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; font-size: 0.85rem;">{{ $ba_done->ba_number }}</div>
                                <div style="font-size: 0.7rem; color: var(--text-dim);">Berita Acara {{ $ba_done->status }}</div>
                            </div>
                            <a href="{{ route('ba.show', $ba_done->id) }}" style="color: var(--primary); font-size: 1.1rem;"><ion-icon name="arrow-forward-circle"></ion-icon></a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="padding: 24px;">
                    <ion-icon name="document-text-outline" style="font-size: 2rem; color: var(--border); margin-bottom: 8px;"></ion-icon>
                    <div style="font-size: 0.75rem;">Belum ada riwayat laporan selesai.</div>
                </div>
            @endif
        </div>
    </div>
@endif

@endsection
