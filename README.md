# Interview HRD — Laravel Edition

Versi Laravel dari **InterviewHRD** (Youth Ranger Indonesia), dengan tampilan dashboard modern bergaya *gamification* (tema ungu, ikon SVG), ramah HP, dan export **Excel + PDF** yang memuat waktu unduh.

## Persyaratan
- PHP **8.2+** (aktifkan ekstensi `gd`, `zip`, `mbstring`, `xml`, `fileinfo`, `pdo_mysql` di `php.ini` XAMPP)
- Composer 2
- MySQL / MariaDB (XAMPP)

## Cara menjalankan
```bash
cd InterviewHRD-Laravel
composer install
copy .env.example .env          # Linux/Mac: cp .env.example .env
php artisan key:generate
```
1. Buat database kosong `db_hrd` di phpMyAdmin (atau pakai database lama yang sudah ada).
2. Cek `.env` bagian `DB_*` (default XAMPP: user `root`, password kosong).
3. Jalankan:
```bash
php artisan migrate
php artisan serve --host=0.0.0.0 --port=8000
```
Buka http://localhost:8000

### Membuka dari HP (satu Wi-Fi)
Cek IP laptop (`ipconfig` / `ifconfig`), lalu buka `http://IP-LAPTOP:8000` di browser HP.

### Data lama
- Sudah punya tabel `penilaian` dari `db_hrd.sql`? Import dulu SQL-nya, lalu `php artisan migrate`.
  Data **tidak dihapus**; migrasi hanya menambah kolom `created_at` / `updated_at` bila belum ada.
  (Tabel lama diasumsikan punya kolom `id` sebagai primary key.)
- Ingin coba dengan data contoh: `php artisan db:seed --class=DemoSeeder`

## Pemetaan file lama → Laravel
| PHP lama | Laravel |
|---|---|
| `index.php` | `DashboardController` + `resources/views/dashboard.blade.php`, `kandidat.blade.php` |
| `koneksi.php` | `.env` (`DB_*`) |
| `simpan.php` | `PenilaianController@store` (`POST /penilaian`) |
| `update.php` | `PenilaianController@update` (`PUT /penilaian/{id}`) |
| `hapus.php` | `PenilaianController@destroy` (`DELETE /penilaian/{id}`) |
| `hapus_regional.php` | `PenilaianController@destroyRegional` (`DELETE /regional`) |
| `top_divisi.php` | `PenilaianController@top` (`GET /top`) |
| `get_data.php` | `KandidatController` (pencarian & filter di halaman Kandidat) |
| `export_excel.php` | `ExportController@excel` (`.xlsx`, PhpSpreadsheet) |
| `export_pdf.php` | `ExportController@pdf` (DomPDF) |

## Fitur baru
- Level & XP, misi rekrutmen, badge pencapaian, leaderboard HRD, konfetti saat kandidat masuk TOP
- Navigasi bawah di HP, teks besar, kartu kandidat (bukan tabel kecil), form dengan slider
- Export Excel/PDF per regional atau **semua regional**, berisi *waktu unduh*, *waktu input*, peringkat, dan status TOP
- Zona waktu `Asia/Jakarta` (ubah `APP_TIMEZONE` di `.env`)

## Kuota TOP
Diatur di `config/hrd.php` (mengikuti kode `top_divisi.php`). Catatan: README lama menulis Secretary = 2 dan
Social Media Management = 1, tetapi kode lama memakai 5 dan 4 — versi ini mengikuti **kode**. Silakan ubah bila perlu.

## Catatan keamanan
Seperti versi lama, belum ada login. Jika aplikasi dipasang di internet, tambahkan autentikasi (mis. Laravel Breeze) lebih dulu.

## Login HR
Isi `HRD_USERNAME` dan `HRD_PASSWORD` di `.env`. Jika `HRD_PASSWORD` dikosongkan, login nonaktif (hanya untuk lokal).
Di server production, aplikasi menolak jalan bila `HRD_PASSWORD` kosong.

## Deploy gratis
Lihat `PANDUAN_DEPLOY.md` (GitHub + Wasmer).
