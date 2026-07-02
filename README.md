# 📋 Interview HRD Dashboard

Sistem informasi berbasis **PHP Native + MySQL** untuk membantu tim **Human Resource Development (HRD)** dalam melakukan proses wawancara, penilaian, serta menentukan kandidat terbaik berdasarkan divisi pada proses rekrutmen **Youth Ranger Indonesia**.

---

## ✨ Features

- 📊 Dashboard statistik kandidat
- 🏆 TOP kandidat setiap divisi
- 📝 CRUD data kandidat
- 🌍 Filter berdasarkan regional
- 🔍 Pencarian kandidat
- 📈 Progress jumlah pendaftar setiap divisi
- 📄 Export data ke Excel
- 📑 Export data ke PDF
- 🗑️ Hapus seluruh data per regional
- 💬 SweetAlert untuk seluruh aksi CRUD
- 🎨 Modern Dashboard UI (Bootstrap 5)

---

## 🏢 Divisi yang Didukung

| Divisi | Kuota TOP |
|---------|----------:|
| Graphic Design | 4 |
| Content Creator | 3 |
| Finance | 4 |
| Project Management | 7 |
| Human Resource | 7 |
| Public Relation | 7 |
| Secretary | 2 |
| Vice Leader | 5 |
| Leader | 5 |
| Social Media Management | 1 |

---

## 🌍 Regional

- Sumatera Selatan
- Lampung
- Jambi
- Bengkulu
- Bangka Belitung

---

# 🛠️ Tech Stack

- PHP Native
- MySQL
- Bootstrap 5
- JavaScript
- SweetAlert2
- Chart.js
- DOMPDF
- XAMPP

---

# 📂 Struktur Folder

```text
HRD/
│
├── assets/
│   ├── css/
│   ├── js/
│   └── img/
│
├── database/
│   └── db_hrd.sql
│
├── index.php
├── koneksi.php
├── simpan.php
├── update.php
├── hapus.php
├── get_data.php
├── top_divisi.php
├── hapus_regional.php
├── export_excel.php
├── export_pdf.php
│
└── README.md
```

---

# 🗄️ Database

Import file berikut:

```
database/db_hrd.sql
```

Database yang digunakan:

```
db_hrd
```

---

# ⚙️ Instalasi

## 1. Clone Repository

```bash
git clone https://github.com/Darren0099/InterviewHRD.git
```

---

## 2. Masuk Folder Project

```bash
cd InterviewHRD
```

---

## 3. Pindahkan ke

```
xampp/htdocs/
```

sehingga menjadi

```
xampp/
└── htdocs/
    └── HRD/
```

---

## 4. Import Database

Buka phpMyAdmin

```
http://localhost/phpmyadmin
```

Import

```
database/db_hrd.sql
```

---

## 5. Atur Koneksi

Buka

```
koneksi.php
```

Sesuaikan

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_hrd";
```

---

## 6. Jalankan

```
http://localhost/HRD/
```

---

# 📊 Dashboard

Dashboard menampilkan

- Total Pendaftar
- Total Kandidat TOP
- Card tiap divisi
- Progress pendaftar
- Ranking kandidat
- Statistik

---

# 📝 Penilaian Kandidat

Setiap kandidat dinilai berdasarkan empat aspek.

| Aspek | Maksimal |
|--------|----------|
| Teknis | 25 |
| Komunikasi | 25 |
| Sikap | 25 |
| Motivasi | 25 |

Total maksimum

```
100
```

---

# 📄 Export

Data dapat diekspor menjadi

- Microsoft Excel (.xls)
- PDF

berdasarkan regional yang dipilih.

---

# 🚀 Roadmap

- [x] CRUD Kandidat
- [x] Dashboard HRD
- [x] Search Kandidat
- [x] Export Excel
- [x] Export PDF
- [x] Popup TOP Divisi
- [ ] Login Admin
- [ ] Login HRD
- [ ] Multi Batch Recruitment
- [ ] Grafik Statistik
- [ ] Riwayat Penilaian
- [ ] Multi User

---

# 👨‍💻 Developer

**Al-man Raffli Saputra**

GitHub

https://github.com/Darren0099

---

# 📜 License

Project ini dibuat untuk kebutuhan proses seleksi **Youth Ranger Indonesia** sebagai sistem pendukung penilaian kandidat oleh tim Human Resource.
