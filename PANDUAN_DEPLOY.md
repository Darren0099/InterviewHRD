# Panduan Deploy: GitHub + Wasmer (gratis)

Catatan: panduan ini disusun dari dokumentasi Wasmer, belum diuji langsung dengan akun Anda.
Kalau ada langkah yang tampilannya beda, ikuti istilah yang paling mirip di dashboard Wasmer.

## A. Unggah ke GitHub (repo https://github.com/Darren0099/InterviewHRD)

1. Pastikan Git terpasang: buka Terminal Laragon, ketik `git --version`.
   Kalau tidak dikenali, pasang dari https://git-scm.com lalu buka terminal baru.
2. Salin repo lama ke folder kerja, amankan versi lama sebagai cabang cadangan:
   ```
   cd C:\laragon\www
   git clone https://github.com/Darren0099/InterviewHRD.git InterviewHRD-git
   cd InterviewHRD-git
   git branch backup-php-native
   git push origin backup-php-native
   ```
3. Kosongkan isi repo lama, lalu salin proyek Laravel (tanpa vendor dan .env):
   ```
   git rm -r -q .
   robocopy C:\laragon\www\InterviewHRD . /E /XD vendor node_modules .git /XF .env
   ```
   (robocopy menampilkan banyak teks, itu normal.)
4. Periksa lalu kirim:
   ```
   git add -A
   git status
   git commit -m "Migrasi ke Laravel, dashboard baru, login HR"
   git push origin main
   ```
   Jika diminta login, ikuti jendela browser dari GitHub. Kalau diminta password, gunakan Personal Access Token.
5. Buka halaman repo di browser. Harus ada `artisan`, `app`, `config`, `public`.
   Tidak boleh ada `.env` dan `vendor`.

## B. Deploy di Wasmer

1. Daftar di https://wasmer.io (bisa dengan akun GitHub).
2. Buat app baru dari Git ("Import from Git" / "New app"). Izinkan akses ke repo,
   pilih `Darren0099/InterviewHRD`, cabang `main`. Wasmer mendeteksi Laravel otomatis.
3. Aktifkan "Enable Database" dan pilih MySQL. Database hanya tersedia di region
   us-losa1, fr-roub1, atau ca-beau1. Pilih salah satu (ketiganya jauh dari Indonesia, jadi
   halaman bisa terasa sedikit lebih lambat).
4. Isi variabel lingkungan (Secrets) sesuai `.env.production.example`:
   - `APP_KEY`: jalankan `php artisan key:generate --show` di laptop, salin hasilnya (diawali `base64:`).
   - `HRD_PASSWORD`: password login HR yang kuat.
   - `APP_URL`: alamat app Anda (didapat setelah deploy pertama; isi lalu deploy ulang).
   - Variabel `DB_*` TIDAK perlu diisi, Wasmer mengisinya otomatis.
5. Jalankan deploy dan tunggu selesai. Catat alamatnya (`https://NAMA-APP.wasmer.app`).
6. Buat tabel: di dashboard app buka tab Databases, klik "Go to DB Explorer" (Adminer),
   pilih "SQL command", tempel isi file `database/penilaian.sql`, klik Execute.
7. (Opsional) Pindahkan data lama: di HeidiSQL ekspor tabel `penilaian` (hanya data, format INSERT),
   lalu jalankan SQL-nya di Adminer.
8. Buka alamat app, login, lalu coba: tambah kandidat, unduh PDF, unduh Excel.

## C. Memperbarui setelah live
Ubah file di laptop, lalu:
```
git add -A
git commit -m "pesan perubahan"
git push origin main
```
Wasmer akan deploy ulang dari cabang `main`.

## D. Jika ada masalah
| Gejala | Cara |
|---|---|
| Build gagal | Buka log build di dashboard, kirim pesannya |
| Halaman error 500 | Set `APP_DEBUG=true` sementara untuk melihat pesan, lalu kembalikan ke false |
| "Please provide a valid cache path" | Tambah variabel `VIEW_COMPILED_PATH=/tmp` |
| Tampilan tanpa CSS | Pastikan `FORCE_HTTPS=true` dan `APP_URL` benar |
| 503 "HRD_PASSWORD belum diatur" | Isi variabel `HRD_PASSWORD` |
| "Base table not found" | Langkah B6 belum dijalankan |
| Excel terunduh sebagai .xls | Normal bila server tidak punya ekstensi zip; klik Yes pada peringatan format di Excel |
