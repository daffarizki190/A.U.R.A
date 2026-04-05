---
description: Cara melakukan Push Perubahan ke Branch Baru secara Manual
---

Ikuti langkah-langkah berikut untuk melakukan "Push" manual jika terjadi kendala sinkronisasi otomatis:

1.  **Buka Terminal** di direktori proyek `Dashboard_Outstanding`.
2.  **Pilih Branch Perbaikan**:
    ```bash
    git checkout hotfix/optimized-419-and-latency
    ```
3.  **Simpan Perubahan ke Area Kerja**:
    ```bash
    git add .
    ```
4.  **Lakukan Rekam Perubahan (Commit)**:
    ```bash
    git commit -m "fix: optimasi latensi dan perlindungan form 419"
    ```
5.  **Kirim ke Repository Pusat (Push)**:
// turbo
    ```bash
    git push origin hotfix/optimized-419-and-latency
    ```
6.  **Verifikasi Keberhasilan**:
    ```bash
    git ls-remote --heads origin hotfix/optimized-419-and-latency
    ```
    *Jika muncul deretan angka (Hash), berarti Push sudah sukses.*
