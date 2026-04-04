# A.U.R.A (Asset Update & Report Application) 🏢

**A.U.R.A** (sebelumnya *Dashboard Outstanding Gandaria City*) adalah sistem manajemen pelaporan insiden, pemantauan aset, dan pembuatan Berita Acara (BA) berstandar korporat yang dibangun khusus untuk lingkungan properti Gandaria City. Aplikasi ini memastikan dokumentasi alur kerja berjalan secara transparan, aman, dan tanpa kertas (*paperless*).

---

## ✨ Fitur Utama (Core Features)

- **Dasbor Pemantauan Real-Time**: Panel analitik langsung yang me-*refresh* diri setiap 60 detik untuk memonitor status insiden ("Open", "On Progress", "Pending Approval").
- **Manajemen Temuan Aset**: Sistem tiket untuk mendokumentasikan aset/kerusakan fasilitas.
- **Berkas Berita Acara (BA)**: Sistem terpusat untuk membukukan kejadian (Kehilangan, Kerusakan, Serah Terima) lengkap dengan form tanda tangan digital/cetak.
- **Live File Preview**: Fitur pratinjau lampiran langsung di dalam browser (men-dukung Gambar, PDF, dan ikon Dokumen Word/Excel).
- **Cetak Dokumen Resmi (A4)**: Sistem konversi *Berita Acara* menjadi *Layout* Cetak Fisik / PDF berstandar KOP Surat Gandaria City tanpa merusak format.
- **Paging & Optimasi**: Pengolahan data berlapis (Pagination) untuk meminimalisasi kebocoran memori saat data laporan melebihi kapasitas standar.
- **Role-Based Access Control (RBAC)**: Sistem otoritas ketat. Tidak sembarang orang dapat memberikan status *Approved* (Disetujui). Administrator Utama (CPM) memegang kontrol mutlak atas validasi akhir.

---

## 👥 Aktor & Hak Akses (Roles)

Sistem ini memfasilitasi 3 jenis profil pengguna utama:
1. **CPM (Pusat Manajemen / ex: Rizal Maulana)** - Pemegang otoritas tertinggi tingkat Manajemen. Sehari-hari hanya peran ini yang memiliki instrumen rahasia untuk melakukan persetujuan akhir (*Approve*) terhadap Berita Acara maupun penyelesaian Temuan Aset.
2. **SPV (Supervisor / ex: Yamin, Akmal)** - Pengawas operasional harian yang memiliki hak dasar untuk membuat, memantau, dan melaporkan Berita Acara kejadian dari lapangan.
3. **IT (Tim Teknis / ex: Irvandi)** - Penanggungjawab jaringan/teknologi yang dapat memantau dan memperbarui progres tiket perbaikan dari segi piranti keras (Hardware/Aset IT).

---

## 🛠️ Tumpukan Teknologi (Tech Stack)

Aplikasi ini menolak kerangka kerja kaku (*bloated framework*) dan dirancang di atas pondasi fleksibel berkinerja tinggi:
- **Backend Framework**: Laravel 12.x
- **Bahasa Pemrograman**: PHP 8.2+
- **Database Utama**: PostgreSQL tingkat-Enterprise (via Supabase Cloud)
- **Arsitektur Antarmuka (UI)**: Custom Blade Templating dengan *Glassmorphism CSS Native* (Desain Antarmuka Transparan Premium).
- **Manajemen Ikon**: File Pustaka SVG murni `Ionicons 7`.

---

## 📂 Struktur Direktori Inti (Project Structure)

Berikut adalah atlas panduan untuk membaca kerangkan proyek AURA:

```text
A.U.R.A/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Jantung Logika Sistem (AssetFindingController, BeritaAcaraController)
│   │   └── Middleware/        # Satpam Keamanan Anti-Hack URL (EnsureUserRole.php)
│   └── Models/                # Pemetaan/Penghubung Database (BeritaAcara.php, AssetFinding.php, User.php)
├── database/
│   ├── migrations/            # Histori Arsitektur Tabel (Supabase PostgreSQL)
│   └── seeders/               # Komando Pengisi Rekayasa Tabel (Akun Pak Rizal Maulana CPM, Pak Akhmad Nuryamin SPV)
├── public/
│   ├── css/
│   │   └── app.css            # 🎨 (PENTING) Pusat desain sistem (Warna Gelap, Radius Tombol, CSS Grid)
│   └── storage/               # Brankas Publik Penyimpanan File Upload (Foto Insiden, Dokumen Bukti)
├── resources/
│   └── views/                 # Wajah Front-End Aplikasi
│       ├── auth/              # Halaman Tampilan Security Gerbang Login AURA
│       ├── ba/                # Tampilan HTML Berita Acara (Tabel, Print-A4, Detail Preview)
│       ├── dashboard/         # Tampilan Dasbor Induk (Statistik Kotak Angka Berkedip)
│       ├── findings/          # Tampilan Laporan Temuan Kendala Aset
│       └── layouts/
│           └── app.blade.php  # 💀 Tulang Rusuk Desain Aplikasi (Sidebar Kiri & Background Theme)
└── routes/
    └── web.php                # 🚦 Peta Jalan Pintas Terminal URL (Akses Lalu Lintas Endpoints)
```

---

## 🚀 Panduan Basis Pengembangan (Development Local Setup)

Bagi pengembang penerus yang diutus untuk mengambil alih lingkungan uji coba lokal:

1. Pastikan Anda memiliki PHP 8.2+ dan Composer terinstal secara *Global*.
2. Buka terminal & jalankan perintah `composer install` pada halaman *root*.
3. Salin/Atur file `.env` Anda dan isi jalur kredensial koneksi Database `PostgreSQL` Supabase Gandaria City.
4. (KRUSIAL): Wajib mejalankan `php artisan storage:link` agar sistem sinkronisasi jalur gambar foto pelaporan berfungsi.
5. Jalankan `php artisan serve` untuk menyalakan reaktor lokal Anda.
6. Login menggunakan `cpm@gandariacity.com` (Sandi: `password123`).

---
*(Didokumentasikan oleh Tim Pengembangan Infrastruktur CentralPark Gandaria City - © 2026)*
