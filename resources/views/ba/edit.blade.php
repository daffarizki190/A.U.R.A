@extends('layouts.app')

@section('content')
<div style="margin-bottom: 32px;">
    <a href="{{ route('ba.show', $ba->id) }}" style="color: var(--text-dim); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; transition: color 0.2s;" onmouseover="this.style.color='var(--text-main)'" onmouseout="this.style.color='var(--text-dim)'">
        <ion-icon name="arrow-back" style="font-size: 1rem;"></ion-icon> 
        Kembali ke Detail
    </a>
</div>

<header style="margin-bottom: 40px;">
    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em;">Perbarui Progres Dokumen</h1>
    <p style="color: var(--text-dim); font-size: 0.95rem;">Manajemen alur kerja untuk {{ $ba->ba_number }}</p>
</header>

<div class="glass-card" style="padding: 40px; max-width: 700px; border-radius: 24px;">
    <form action="{{ route('ba.update', $ba->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div class="form-group">
                <label>Status Alur Kerja</label>
                <select name="status" class="input">
                    <option value="Submitted" {{ $ba->status == 'Submitted' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                    <option value="Processed" {{ $ba->status == 'Processed' ? 'selected' : '' }}>Diproses (Terotorisasi)</option>
                    <option value="Done" {{ $ba->status == 'Done' ? 'selected' : '' }}>Selesai (Diarsipkan)</option>
                    <option value="Rejected" {{ $ba->status == 'Rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Penanggung Jawab (PIC)</label>
                <select name="pic_id" class="input">
                    <option value="">-- Pilih Penanggung Jawab --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $ba->pic_id == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->role }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 24px;">
            <label>Perbarui Dokumen Lampiran (Opsional)</label>
            <input type="file" name="attachment" class="input" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" style="padding: 9px 12px;" onchange="previewBAFile(event)">
            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 6px;">Biarkan kosong jika Anda tidak ingin mengubah dokumen yang sudah diunggah sebelumnya.</div>
            
            <div id="file-preview-block" style="display:none; margin-top: 16px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border); background: var(--bg-secondary); padding: 8px;">
                <img id="preview-image" style="display:none; width: 100%; max-height: 300px; object-fit: contain; border-radius: 8px;" alt="Preview Thumbnail">
                <iframe id="preview-pdf" style="display:none; width: 100%; height: 400px; border: none; border-radius: 8px;"></iframe>
                <div id="preview-generic" style="display:none; padding: 16px; text-align: center; color: var(--text-main);">
                    <ion-icon name="document" style="font-size: 3rem; color: var(--accent); margin-bottom: 8px;"></ion-icon>
                    <div id="generic-filename" style="font-weight: 600;"></div>
                    <div style="font-size: 0.8rem; color: var(--text-dim); margin-top: 4px;">File siap diunggah</div>
                </div>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
            <div class="form-group">
                <label>Tanggal Pengajuan ke Pusat (HO)</label>
                <input type="datetime-local" name="submitted_at" class="input" value="{{ $ba->submitted_at ? date('Y-m-d\TH:i', strtotime($ba->submitted_at)) : '' }}">
            </div>
            
            <div class="form-group">
                <label>Tanggal Persetujuan Manajemen</label>
                <input type="datetime-local" name="approved_at" class="input" value="{{ $ba->approved_at ? date('Y-m-d\TH:i', strtotime($ba->approved_at)) : '' }}">
            </div>
        </div>
        
        <div style="display: flex; gap: 16px; margin-top: 40px; padding-top: 32px; border-top: 1px solid var(--border);">
            <button type="submit" class="btn-primary" style="flex: 2; height: 52px; font-size: 1rem;">
                <ion-icon name="sync-outline" style="margin-right: 8px;"></ion-icon>
                Perbarui Dokumen
            </button>
            <a href="{{ route('ba.show', $ba->id) }}" class="btn-primary" style="flex: 1; height: 52px; background: rgba(0,0,0,0.03); color: var(--text-main); border: 1px solid var(--border);">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
    function previewBAFile(event) {
        const file = event.target.files[0];
        if(!file) return;
        
        const previewBlock = document.getElementById('file-preview-block');
        const imgPreview = document.getElementById('preview-image');
        const pdfPreview = document.getElementById('preview-pdf');
        const genericPreview = document.getElementById('preview-generic');
        const genericFilename = document.getElementById('generic-filename');
        
        if (previewBlock) previewBlock.style.display = 'block';
        if (imgPreview) imgPreview.style.display = 'none';
        if (pdfPreview) pdfPreview.style.display = 'none';
        if (genericPreview) genericPreview.style.display = 'none';
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function() {
                if (imgPreview) {
                    imgPreview.src = reader.result;
                    imgPreview.style.display = 'block';
                }
            }
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            const fileURL = URL.createObjectURL(file);
            if (pdfPreview) {
                pdfPreview.src = fileURL;
                pdfPreview.style.display = 'block';
            }
        } else {
            if (genericFilename) genericFilename.innerText = file.name;
            if (genericPreview) genericPreview.style.display = 'block';
        }
    }
</script>
