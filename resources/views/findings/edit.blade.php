@extends('layouts.app')

@section('content')
<div style="margin-bottom: 32px;">
    <a href="{{ route('findings.show', $finding->id) }}" style="color: var(--text-dim); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; transition: color 0.2s;" onmouseover="this.style.color='var(--text-main)'" onmouseout="this.style.color='var(--text-dim)'">
        <ion-icon name="arrow-back" style="font-size: 1rem;"></ion-icon> 
        Kembali ke Detail
    </a>
</div>

<header style="margin-bottom: 40px;">
    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em;">Perbarui Status Temuan</h1>
    <p style="color: var(--text-dim); font-size: 0.95rem;">Manajemen progres temuan untuk {{ $finding->finding_code }}</p>
</header>

<div class="glass-card" style="padding: 40px; max-width: 700px; border-radius: 24px;">
    <form action="{{ route('findings.update', $finding->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div class="form-group">
                <label>Status Operasional</label>
                <select name="status" class="input">
                    <option value="Pending Approval" {{ $finding->status == 'Pending Approval' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="Open" {{ $finding->status == 'Open' ? 'selected' : '' }}>Open (Disetujui)</option>
                    <option value="On Progress" {{ $finding->status == 'On Progress' ? 'selected' : '' }}>On Progress</option>
                    <option value="Done" {{ $finding->status == 'Done' ? 'selected' : '' }}>Done (Selesai)</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Alokasi Penanggung Jawab (PIC)</label>
                <select name="pic_id" class="input">
                    <option value="">-- Pilih Penanggung Jawab --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $finding->pic_id == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->role }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div style="margin-bottom: 24px;">
            <div class="form-group">
                <label>Perbarui Foto Lampiran (Opsional)</label>
                <input type="file" name="photo" class="input" accept="image/*" style="padding: 9px 12px;" onchange="previewImage(event)">
                <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 6px;">Biarkan kosong jika tidak ingin mengubah foto lampran saat ini.</div>
                <div id="image-preview-block" style="display:none; margin-top: 16px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border);">
                    <img id="preview-image" style="width: 100%; height: 160px; object-fit: cover;" alt="Preview image">
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
            <div class="form-group">
                <label>Estimasi Penyelesaian</label>
                <input type="date" name="estimated_completion_date" class="input" value="{{ $finding->estimated_completion_date }}">
                <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 6px;">Target waktu penyelesaian perbaikan.</div>
            </div>

            <div class="form-group">
                <label>Aktual Penyelesaian</label>
                <input type="date" name="actual_completion_date" class="input" value="{{ $finding->actual_completion_date }}">
                <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 6px;">Hanya diisi jika status laporan adalah 'Done'.</div>
            </div>
        </div>
        
        <div style="display: flex; gap: 16px; margin-top: 40px; padding-top: 32px; border-top: 1px solid var(--border);">
            <button type="submit" class="btn-primary" style="flex: 2; height: 52px; font-size: 1rem;">
                <ion-icon name="save-outline" style="margin-right: 8px;"></ion-icon>
                Simpan Perubahan
            </button>
            <a href="{{ route('findings.show', $finding->id) }}" class="btn-primary" style="flex: 1; height: 52px; background: rgba(0,0,0,0.03); color: var(--text-main); border: 1px solid var(--border);">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        const previewBlock = document.getElementById('image-preview-block');
        const previewImg = document.getElementById('preview-image');
        
        reader.onload = function() {
            previewImg.src = reader.result;
            if(previewBlock) previewBlock.style.display = 'block';
        }
        
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
