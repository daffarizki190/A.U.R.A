@extends('layouts.app')

@section('content')
<div style="margin-bottom: 32px;">
    <a href="{{ route('ba.index') }}" style="color: var(--text-dim); text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; transition: color 0.2s;" onmouseover="this.style.color='var(--text-main)'" onmouseout="this.style.color='var(--text-dim)'">
        <ion-icon name="arrow-back" style="font-size: 1rem;"></ion-icon> 
        Kembali ke Daftar
    </a>
</div>

<header style="margin-bottom: 40px;">
    <h1 style="font-size: 2.25rem; font-weight: 800; letter-spacing: -0.03em;">Buat Berita Acara</h1>
    <p style="color: var(--text-dim); font-size: 0.95rem;">Buat laporan resmi untuk insiden keamanan atau kendala fasilitas</p>
</header>

<div class="glass-card" style="padding: 40px; max-width: 900px; border-radius: 24px;">
    <form action="{{ route('ba.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div class="form-group">
                <label>Jenis Insiden</label>
                <select name="ba_type" class="input">
                    <option value="Kehilangan">Kehilangan</option>
                    <option value="Kerusakan">Kerusakan</option>
                    <option value="Insiden Keamanan">Insiden Keamanan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Tanggal Kejadian</label>
                <input type="date" name="incident_date" class="input" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
            <div class="form-group">
                <label>Nama Pelanggan atau Pihak Ketiga</label>
                <input type="text" name="customer_name" class="input" placeholder="Nama lengkap pihak yang terlibat" required>
            </div>
            
            <div class="form-group">
                <label>Plat Nomor Kendaraan (Opsional)</label>
                <input type="text" name="license_plate" class="input" placeholder="Contoh: B 1234 ABC">
            </div>
        </div>
        
        <div class="form-group" style="margin-bottom: 24px;">
            <label>Unggah Dokumen Lampiran (Opsional)</label>
            <input type="file" name="attachment" class="input" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg" style="padding: 9px 12px;" onchange="previewBAFile(event)">
            <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 6px;">Format yang didukung: PDF, Word, Excel, Gambar (Maks 10 MB).</div>
            
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
        
        <div class="form-group" style="margin-bottom: 32px;">
            <label>Kronologi Kejadian</label>
            <textarea name="chronology" class="input" style="height: 200px; padding: 20px; line-height: 1.6; resize: vertical;" placeholder="Berikan penjelasan secara rinci mengenai berjalannya kejadian dari awal hingga akhir..." required></textarea>
        </div>
        
        <div style="padding: 16px; background: rgba(0, 122, 255, 0.05); border: 1px solid rgba(0, 122, 255, 0.1); border-radius: 12px; margin-bottom: 32px; display: flex; align-items: flex-start; gap: 12px;">
            <ion-icon name="shield-checkmark" style="color: var(--primary); font-size: 1.25rem; margin-top: 2px;"></ion-icon>
            <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">
                <strong style="color: var(--text-main);">Pencatatan Penanggung Jawab:</strong><br>
                Penanggung jawab utama dari dokumen ini akan dicatat dalam nama: <span style="color: var(--primary); font-weight: 600;">{{ auth()->user()->name }}</span>
            </div>
        </div>
        
        <div style="display: flex; gap: 16px;">
            <button type="submit" class="btn-primary" style="flex: 2; height: 52px; font-size: 1rem;">
                <ion-icon name="document-text-outline" style="margin-right: 8px;"></ion-icon>
                Ajukan Dokumen
            </button>
            <a href="{{ route('ba.index') }}" class="btn-primary" style="flex: 1; height: 52px; background: rgba(0,0,0,0.03); color: var(--text-main); border: 1px solid var(--border);">
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
        
        previewBlock.style.display = 'block';
        imgPreview.style.display = 'none';
        pdfPreview.style.display = 'none';
        genericPreview.style.display = 'none';
        
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function() {
                imgPreview.src = reader.result;
                imgPreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            const fileURL = URL.createObjectURL(file);
            pdfPreview.src = fileURL;
            pdfPreview.style.display = 'block';
        } else {
            genericFilename.innerText = file.name;
            genericPreview.style.display = 'block';
        }
    }
</script>

@endsection
