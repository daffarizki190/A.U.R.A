@extends('layouts.app')

@section('content')
<div style="margin-bottom: 32px;">
    <a href="{{ route('findings.index') }}" style="color: var(--text-dim); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; transition: color 0.2s;" onmouseover="this.style.color='var(--text-main)'" onmouseout="this.style.color='var(--text-dim)'">
        <ion-icon name="arrow-back" style="font-size: 1rem;"></ion-icon> 
        Back to List
    </a>
</div>

<header style="margin-bottom: 40px;">
    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em;">New Asset Finding</h1>
    <p style="color: var(--text-dim); font-size: 0.95rem;">Report a new facility issue or equipment damage</p>
</header>

<div class="glass-card" style="padding: 40px; max-width: 800px; border-radius: 24px;">
    <form action="{{ route('findings.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div class="form-group">
                <label>Tanggal Temuan</label>
                <input type="date" name="finding_date" class="input" value="{{ date('Y-m-d') }}" required>
            </div>
            
            <div class="form-group">
                <label>Lokasi (Area, Lantai, atau Gate)</label>
                <input type="text" name="location" class="input" placeholder="Contoh: Gate A1, Parkiran LG" required>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div class="form-group">
                <label>Kategori Aset (Contoh: Gate, Barrier, CCTV)</label>
                <input type="text" name="asset_type" class="input" placeholder="Aset apa yang mengalami kendala?" required>
            </div>
            
            <div class="form-group">
                <label>Foto Lampiran Laporan</label>
                <input type="file" name="photo" class="input" accept="image/*" style="padding: 9px 12px;" onchange="previewImage(event)">
                <div id="image-preview-block" style="display:none; margin-top: 12px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border);">
                    <img id="preview-image" style="width: 100%; height: 120px; object-fit: cover;" alt="Preview">
                </div>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 32px;">
            <label>Deskripsi Detail Kendala</label>
            <textarea name="description" class="input" style="height: 160px; padding: 16px; resize: vertical;" placeholder="Jelaskan kendala secara lengkap dan jelas..." required></textarea>
        </div>
        
        <div style="padding: 16px; background: rgba(0, 122, 255, 0.05); border: 1px solid rgba(0, 122, 255, 0.1); border-radius: 12px; margin-bottom: 32px; display: flex; align-items: flex-start; gap: 12px;">
            <ion-icon name="information-circle" style="color: var(--primary); font-size: 1.25rem; margin-top: 2px;"></ion-icon>
            <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">
                <strong style="color: var(--text-main);">Penugasan Penanggung Jawab Otomatis:</strong><br>
                Penanggung jawab untuk laporan ini akan otomatis dialokasikan ke akun Anda: <span style="color: var(--primary); font-weight: 600;">{{ auth()->user()->name }}</span>
            </div>
        </div>
        
        <div style="display: flex; gap: 16px;">
            <button type="submit" class="btn-primary" style="flex: 2; height: 52px; font-size: 1rem;">
                <ion-icon name="cloud-upload-outline" style="margin-right: 8px;"></ion-icon>
                Simpan Laporan
            </button>
            <a href="{{ route('findings.index') }}" class="btn-primary" style="flex: 1; height: 52px; background: rgba(0,0,0,0.03); color: var(--text-main); border: 1px solid var(--border);">
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
            previewBlock.style.display = 'block';
        }
        
        if(event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        }
    }
</script>
