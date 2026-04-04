@extends('layouts.app')

@section('content')
<div style="margin-bottom: 32px;">
    <a href="{{ route('findings.index') }}" style="color: var(--text-dim); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; transition: color 0.2s;" onmouseover="this.style.color='var(--text-main)'" onmouseout="this.style.color='var(--text-dim)'">
        <ion-icon name="arrow-back" style="font-size: 1rem;"></ion-icon> 
        Kembali ke Daftar Temuan
    </a>
</div>

<header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;">{{ $finding->finding_code }}</h1>
            <span class="badge badge-{{ strtolower(str_replace(' ', '', $finding->status)) }}" style="font-size: 0.75rem; padding: 6px 16px;">
                {{ $finding->status }}
            </span>
        </div>
        <p style="color: var(--text-dim); font-size: 1rem;">Laporan kendala aset tertanggal {{ \Carbon\Carbon::parse($finding->finding_date)->format('j F Y') }}</p>
    </div>
    
    <div style="display: flex; gap: 12px;">
        @if($finding->status == 'Pending Approval' && (auth()->id() == $finding->pic_id || auth()->user()->role == 'CPM'))
            <a href="{{ route('findings.edit', $finding->id) }}" class="btn-primary" style="background: rgba(255,255,255,0.05); color: var(--text-main); border: 1px solid var(--border);">
                <ion-icon name="create-outline" style="margin-right: 8px;"></ion-icon> Edit Laporan
            </a>
        @endif

        @if($finding->status != 'Pending Approval' && auth()->user()->role == 'CPM')
            <a href="{{ route('findings.edit', $finding->id) }}" class="btn-primary">
                <ion-icon name="sync-outline" style="margin-right: 8px;"></ion-icon> Perbarui Progres
            </a>
        @endif
    </div>
</header>

<div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 32px; align-items: start;">
    <div style="display: flex; flex-direction: column; gap: 32px;">
        <!-- Core Details -->
        <div class="glass-card" style="padding: 32px; border-radius: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
                <ion-icon name="information-circle" style="color: var(--primary);"></ion-icon>
                Informasi Umum
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px;">Area Lokasi</label>
                    <div style="font-weight: 600; font-size: 1rem; color: var(--text-main);">{{ $finding->location }}</div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px;">Kategori Aset</label>
                    <div style="font-weight: 600; font-size: 1rem; color: var(--text-main);">{{ $finding->asset_type }}</div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px;">Nama Pelapor</label>
                    <div style="font-weight: 600; font-size: 1rem; color: var(--text-main);">{{ $finding->reporter }}</div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px;">Penanggung Jawab</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 24px; height: 24px; border-radius: 50%; background: var(--border-bold); display: flex; align-items: center; justify-content: center; font-size: 0.65rem; font-weight: 800;">
                            {{ substr($finding->pic ? $finding->pic->name : 'N', 0, 1) }}
                        </div>
                        <span style="font-weight: 600;">{{ $finding->pic ? $finding->pic->name : 'Belum Ditentukan' }}</span>
                    </div>
                </div>
            </div>

            <div style="margin-top: 40px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 12px;">Ringkasan Deskriptif</label>
                <div style="padding: 24px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; line-height: 1.7; color: var(--text-secondary); font-size: 0.95rem;">
                    {{ $finding->description }}
                </div>
            </div>

            @if($finding->photo)
                <div style="margin-top: 40px;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 16px;">Bukti Visual Lampiran</label>
                    <div style="border-radius: 20px; overflow: hidden; border: 1px solid var(--border); background: black;">
                        @php
                            $photoUrl = str_starts_with($finding->photo, 'http')
                                ? $finding->photo
                                : asset('storage/' . $finding->photo);
                        @endphp
                        <img src="{{ $photoUrl }}" style="width: 100%; display: block; filter: brightness(0.9); transition: filter 0.3s;" onmouseover="this.style.filter='brightness(1)'" onmouseout="this.style.filter='brightness(0.9)'" alt="Foto Temuan">
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 32px;">
        <!-- CPM Actions -->
        @if(auth()->user()->role == 'CPM')
            @if($finding->status == 'Pending Approval')
                <div class="glass-card" style="padding: 32px; border: 1px solid rgba(52, 199, 89, 0.3); background: linear-gradient(135deg, rgba(52, 199, 89, 0.05) 0%, transparent 100%); border-radius: 24px;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--success); margin-bottom: 12px;">Menunggu Persetujuan</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.6;">Tinjau detail laporan dan berikan otorisasi untuk memulai proses perbaikan aset ini.</p>
                    <form action="{{ route('findings.approve', $finding->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary" style="background: var(--success); width: 100%; box-shadow: 0 8px 16px rgba(52, 199, 89, 0.2);">
                            Setujui Laporan
                        </button>
                    </form>
                </div>
            @elseif($finding->status == 'Open')
                <div class="glass-card" style="padding: 32px; border: 1px solid rgba(255, 59, 48, 0.2); background: linear-gradient(135deg, rgba(255, 59, 48, 0.05) 0%, transparent 100%); border-radius: 24px;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--danger); margin-bottom: 12px;">Batalkan Persetujuan</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.6;">Gunakan aksi ini jika terdapat kekeliruan informasi. Status laporan akan dikembalikan ke tahap awal.</p>
                    <form action="{{ route('findings.cancelApprove', $finding->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary" style="background: rgba(255, 59, 48, 0.1); color: var(--danger); border: 1px solid rgba(255, 59, 48, 0.3); width: 100%;">
                            Tarik Persetujuan
                        </button>
                    </form>
                </div>
            @endif
        @endif

        <!-- Timeline -->
        <div class="glass-card" style="padding: 32px; border-radius: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 24px;">Riwayat Proses</h3>
            
            <div style="position: relative; padding-left: 32px;">
                <div style="position: absolute; left: 6px; top: 8px; bottom: 8px; width: 2px; background: var(--border-bold);"></div>
                
                <div style="position: relative; margin-bottom: 32px;">
                    <div style="position: absolute; left: -32px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: var(--success); box-shadow: 0 0 0 4px rgba(52, 199, 89, 0.1);"></div>
                    <div style="font-weight: 700; font-size: 0.9rem;">Laporan Dibuat</div>
                    <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 4px;">{{ $finding->created_at->format('j M Y • H:i') }}</div>
                </div>

                @if($finding->status != 'Pending Approval')
                    <div style="position: relative; margin-bottom: 32px;">
                        <div style="position: absolute; left: -32px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: var(--primary); box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1);"></div>
                        <div style="font-weight: 700; font-size: 0.9rem;">Disetujui oleh CPM</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 4px;">Otorisasi perbaikan telah disahkan.</div>
                    </div>
                @endif

                @if(in_array($finding->status, ['On Progress', 'Done']))
                    <div style="position: relative; margin-bottom: 32px;">
                        <div style="position: absolute; left: -32px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: var(--warning); box-shadow: 0 0 0 4px rgba(255, 149, 0, 0.1);"></div>
                        <div style="font-weight: 700; font-size: 0.9rem;">Proses Perbaikan</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 4px;">Tim pelaksana sedang bekerja.</div>
                    </div>
                @endif

                @if($finding->status == 'Done')
                    <div style="position: relative;">
                        <div style="position: absolute; left: -32px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: var(--success); box-shadow: 0 0 0 4px rgba(52, 199, 89, 0.1);"></div>
                        <div style="font-weight: 700; font-size: 0.9rem;">Tugas Selesai</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 4px;">{{ $finding->actual_completion_date ?: 'Siap untuk tahapan inspeksi' }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Deadline -->
        <div class="glass-card" style="padding: 32px; border-radius: 24px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 12px;">Target Penyelesaian</label>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--accent); letter-spacing: -0.02em;">
                {{ $finding->estimated_completion_date ?: 'Belum Ditetapkan' }}
            </div>
        </div>
    </div>
</div>
@endsection
