@extends('layouts.app')

@section('content')
<div style="margin-bottom: 32px;">
    <a href="{{ route('ba.index') }}" style="color: var(--text-dim); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; transition: color 0.2s;" onmouseover="this.style.color='var(--text-main)'" onmouseout="this.style.color='var(--text-dim)'">
        <ion-icon name="arrow-back" style="font-size: 1rem;"></ion-icon> 
        Kembali ke Daftar Berita Acara
    </a>
</div>

<header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
            <h1 style="font-size: 2.5rem; font-weight: 800; letter-spacing: -0.04em;">{{ $ba->ba_number }}</h1>
            <span class="badge badge-{{ strtolower(str_replace(' ', '', $ba->status)) }}" style="font-size: 0.75rem; padding: 6px 16px;">
                {{ $ba->status }}
            </span>
        </div>
        <p style="color: var(--text-dim); font-size: 1rem;">Laporan resmi diajukan pada {{ \Carbon\Carbon::parse($ba->created_at)->format('j F Y') }}</p>
    </div>
    
    <div style="display: flex; gap: 12px;">
        @if($ba->status == 'Submitted' && (auth()->id() == $ba->pic_id || auth()->user()->role == 'CPM'))
            <a href="{{ route('ba.edit', $ba->id) }}" class="btn-primary" style="background: rgba(255,255,255,0.05); color: var(--text-main); border: 1px solid var(--border);">
                <ion-icon name="create-outline" style="margin-right: 8px;"></ion-icon> Edit Dokumen
            </a>
        @endif

        @if($ba->status == 'Processed' && auth()->user()->role == 'CPM')
            <a href="{{ route('ba.edit', $ba->id) }}" class="btn-primary">
                <ion-icon name="sync-outline" style="margin-right: 8px;"></ion-icon> Perbarui Progres
            </a>
        @endif
        
        <a href="{{ route('ba.print', $ba->id) }}" target="_blank" class="btn-primary" style="background: rgba(255,255,255,0.05); color: var(--text-main); border: 1px solid var(--border);">
            <ion-icon name="print-outline" style="margin-right: 8px;"></ion-icon> Cetak PDF/Print
        </a>
    </div>
</header>

<div style="display: grid; grid-template-columns: 1.6fr 1fr; gap: 32px; align-items: start;">
    <div style="display: flex; flex-direction: column; gap: 32px;">
        <!-- Core Details -->
        <div class="glass-card" style="padding: 32px; border-radius: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 24px; display: flex; align-items: center; gap: 8px;">
                <ion-icon name="document-text" style="color: var(--accent);"></ion-icon>
                Informasi Insiden
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px;">Kategori Insiden</label>
                    <div style="font-weight: 600; font-size: 1rem; color: var(--text-main);">{{ $ba->ba_type }}</div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px;">Tanggal Kejadian</label>
                    <div style="font-weight: 600; font-size: 1rem; color: var(--text-main);">{{ \Carbon\Carbon::parse($ba->incident_date)->format('d F Y') }}</div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px;">Nama Pelanggan atau Pihak Terkait</label>
                    <div style="font-weight: 600; font-size: 1rem; color: var(--text-main);">{{ $ba->customer_name }}</div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 8px;">Plat Nomor Kendaraan</label>
                    <div style="font-weight: 600; font-size: 1rem; color: var(--text-main);">{{ $ba->license_plate ?: '-' }}</div>
                </div>
                @if($ba->attachment)
                <div style="grid-column: span 2; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.05);">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 12px;">Dokumen Lampiran</label>
                    @php
                        $attachmentUrl = str_starts_with($ba->attachment, 'http') 
                            ? $ba->attachment 
                            : Storage::disk('s3')->url($ba->attachment);
                    @endphp
                    <a href="{{ $attachmentUrl }}" target="_blank" class="btn-primary" style="background: rgba(255,255,255,0.05); color: var(--text-main); border: 1px solid var(--border); width: fit-content;">
                        <ion-icon name="document-attach-outline" style="margin-right: 8px; font-size: 1.2rem;"></ion-icon>
                        Unduh atau Lihat Lampiran
                     </a>
                </div>
                @endif
            </div>

            <div style="margin-top: 40px;">
                <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 12px;">Kronologi Rinci</label>
                <div style="padding: 32px; background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 16px; line-height: 1.8; color: var(--text-secondary); font-size: 1rem;">
                    {!! nl2br(e($ba->chronology)) !!}
                </div>
            </div>
        </div>
    </div>

    <div style="display: flex; flex-direction: column; gap: 32px;">
        <!-- CPM Actions -->
        @if(auth()->user()->role == 'CPM')
            @if($ba->status == 'Submitted')
                <div class="glass-card" style="padding: 32px; border: 1px solid rgba(52, 199, 89, 0.3); background: linear-gradient(135deg, rgba(52, 199, 89, 0.05) 0%, transparent 100%); border-radius: 24px;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--success); margin-bottom: 12px;">Butuh Tinjauan</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.6;">Sebagai CPM, Anda harus melakukan verifikasi kronologi sebelum menyetujui Berita Acara Resmi ini.</p>
                    <form action="{{ route('ba.approve', $ba->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary" style="background: var(--success); width: 100%; box-shadow: 0 8px 16px rgba(52, 199, 89, 0.2);">
                            Otorisasi Dokumen
                        </button>
                    </form>
                </div>
            @elseif($ba->status == 'Processed')
                <div class="glass-card" style="padding: 32px; border: 1px solid rgba(255, 59, 48, 0.2); background: linear-gradient(135deg, rgba(255, 59, 48, 0.05) 0%, transparent 100%); border-radius: 24px;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--danger); margin-bottom: 12px;">Batalkan Otorisasi</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.6;">Gunakan fitur ini untuk menarik persetujuan jika ditemukan ketidaksesuaian pada kronologi.</p>
                    <form action="{{ route('ba.cancelApprove', $ba->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-primary" style="background: rgba(255, 59, 48, 0.1); color: var(--danger); border: 1px solid rgba(255, 59, 48, 0.3); width: 100%;">
                            Tarik Persetujuan
                        </button>
                    </form>
                </div>
            @endif
        @endif

        <!-- Workflow Timeline -->
        <div class="glass-card" style="padding: 32px; border-radius: 24px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 24px;">Siklus Hidup Dokumen</h3>
            
            <div style="position: relative; padding-left: 32px;">
                <div style="position: absolute; left: 6px; top: 8px; bottom: 8px; width: 2px; background: var(--border-bold);"></div>
                
                <div style="position: relative; margin-bottom: 32px;">
                    <div style="position: absolute; left: -32px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: var(--success); box-shadow: 0 0 0 4px rgba(52, 199, 89, 0.1);"></div>
                    <div style="font-weight: 700; font-size: 0.9rem;">Draf Dokumen Dibuat</div>
                    <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 4px;">{{ $ba->created_at->format('j M Y • H:i') }}</div>
                </div>

                <div style="position: relative; margin-bottom: 32px;">
                    @php $isSubmitted = in_array($ba->status, ['Submitted', 'Processed', 'Done']); @endphp
                    <div style="position: absolute; left: -32px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: {{ $isSubmitted ? 'var(--primary)' : 'var(--border-bold)' }};"></div>
                    <div style="font-weight: 700; font-size: 0.9rem; opacity: {{ $isSubmitted ? '1' : '0.4' }};">Dikirim ke Manajemen</div>
                    <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 4px;">Pelacakan fase otorisasi.</div>
                </div>

                <div style="position: relative; margin-bottom: 32px;">
                    @php $isProcessed = in_array($ba->status, ['Processed', 'Done']); @endphp
                    <div style="position: absolute; left: -32px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: {{ $isProcessed ? 'var(--warning)' : 'var(--border-bold)' }};"></div>
                    <div style="font-weight: 700; font-size: 0.9rem; opacity: {{ $isProcessed ? '1' : '0.4' }};">Memproses Insiden</div>
                </div>

                <div style="position: relative;">
                    @php $isDone = ($ba->status == 'Done'); @endphp
                    <div style="position: absolute; left: -32px; top: 4px; width: 14px; height: 14px; border-radius: 50%; background: {{ $isDone ? 'var(--success)' : 'var(--border-bold)' }};"></div>
                    <div style="font-weight: 700; font-size: 0.9rem; opacity: {{ $isDone ? '1' : '0.4' }};">Arsip atau Selesai</div>
                    <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 4px;">Pendaftaran dokumen final.</div>
                </div>
            </div>
        </div>

        <!-- PIC -->
        <div class="glass-card" style="padding: 32px; border-radius: 24px;">
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; margin-bottom: 12px;">Pelapor atau Penanggung Jawab</label>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: var(--border-bold); display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 800; color: var(--accent);">
                    {{ substr($ba->pic ? $ba->pic->name : 'N', 0, 1) }}
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 1rem;">{{ $ba->pic ? $ba->pic->name : 'Belum Ditentukan' }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-dim);">Peran: {{ $ba->pic ? $ba->pic->role : 'Tidak Diketahui' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
