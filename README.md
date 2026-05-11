# SIKASRA — Sistem Pengelolaan Keuangan Kas Asrama

![PHP](https://img.shields.io/badge/PHP-Native_OOP-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=flat-square&logo=bootstrap&logoColor=white)
![Chart.js](https://img.shields.io/badge/Chart.js-4.4-FF6384?style=flat-square&logo=chartdotjs&logoColor=white)

Aplikasi web untuk mengelola keuangan kas asrama, dibangun dengan **PHP Native (OOP)** dan **MySQL**. Dibuat sebagai tugas UAS mata kuliah **Konsep Bahasa Pemrograman**.

---

## 📋 Fitur Utama

| Fitur | Deskripsi |
|---|---|
| 🔐 **Login & RBAC** | Sistem autentikasi dengan 3 role (Admin, Bendahara, Anggota) |
| 📊 **Dashboard** | Statistik kas, grafik bulanan (Bar & Doughnut Chart) |
| 💰 **CRUD Pemasukan** | Tambah, edit, hapus data pemasukan kas |
| 💸 **CRUD Pengeluaran** | Tambah, edit, hapus data pengeluaran kas |
| 📈 **Laporan + Filter** | Laporan keuangan dengan filter tahun & bulan + grafik |
| 👥 **Kelola User** | Manajemen akun pengguna (khusus Admin) |
| 👁️ **Halaman Publik** | Anggota bisa melihat laporan tanpa login (read-only) |
| 📉 **Grafik Statistik** | Chart.js — Bar Chart (bulanan/tahunan) & Doughnut Chart |

---

## 🏗️ Konsep OOP yang Diterapkan

- **Inheritance** — `Pemasukan` dan `Pengeluaran` extends `Transaksi`
- **Encapsulation** — Property `private`/`protected` + getter/setter
- **Polymorphism** — Override method `tambahTransaksi()` dan `editTransaksi()`
- **Constructor** — Inisialisasi koneksi dan logika awal di setiap class

---

## 📁 Struktur Folder

```
sikasra/
├── index.php                    # Redirect ke halaman login
├── laporan_publik.php           # Halaman publik untuk anggota (tanpa login)
├── README.md                    # Dokumentasi project
├── config/
│   └── database.php             # Class Database (koneksi MySQL)
├── classes/
│   ├── User.php                 # Class User (autentikasi & kelola user)
│   ├── Transaksi.php            # Class Transaksi (parent/abstract)
│   ├── Pemasukan.php            # Class Pemasukan (child - inheritance)
│   ├── Pengeluaran.php          # Class Pengeluaran (child - inheritance)
│   └── KasAsrama.php            # Class KasAsrama (encapsulation saldo)
├── pages/
│   ├── login.php                # Halaman login
│   ├── logout.php               # Proses logout (destroy session)
│   ├── dashboard.php            # Dashboard + grafik statistik
│   ├── pemasukan.php            # CRUD pemasukan
│   ├── pengeluaran.php          # CRUD pengeluaran
│   ├── laporan.php              # Laporan keuangan + filter + grafik
│   ├── users.php                # Kelola user (admin only)
│   └── sidebar.php              # Komponen navigasi sidebar
├── assets/
│   └── css/
│       └── style.css            # Custom CSS (desain modern)
└── database/
    └── db_kas_asrama.sql        # File SQL (struktur tabel + seed data)
```

---

## 🚀 Cara Menjalankan

### Prasyarat
- **XAMPP** (Apache + MySQL) terinstall
- PHP versi 7.4 atau lebih baru
- Extension `mysqli` diaktifkan di `php.ini`

### Langkah Instalasi

1. **Copy folder `sikasra/`** ke dalam `C:\xampp\htdocs\`

2. **Jalankan XAMPP** — start Apache dan MySQL

3. **Buat database via phpMyAdmin:**
   - Buka `http://localhost/phpmyadmin`
   - Klik **"New"** → buat database bernama `db_kas_asrama`
   - Klik tab **"Import"** → pilih file `database/db_kas_asrama.sql` → klik **"Go"**

4. **Akses aplikasi:**
   ```
   http://localhost/sikasra/
   ```

---

## 👤 Akun Default

| Role | Username | Password | Hak Akses |
|---|---|---|---|
| **Admin** | `admin` | `admin123` | Semua fitur + kelola user |
| **Bendahara** | `bendahara` | `bendahara123` | Input data + laporan |
| **Anggota/Viewer** | — | — (tanpa login) | Lihat laporan (read-only) |

### URL Akses per Role:
- **Admin / Bendahara:** `http://localhost/sikasra/` → login
- **Anggota (publik):** `http://localhost/sikasra/laporan_publik.php` → langsung lihat

---

## 📊 Fitur Grafik

Grafik tersedia di **3 halaman** (Dashboard, Laporan, Laporan Publik):

| Mode Filter | Grafik Bar Chart | Label |
|---|---|---|
| **Semua Tahun** (tanpa filter) | Total pemasukan vs pengeluaran **per tahun** | 2024, 2025, 2026 |
| **Tahun dipilih** | Total per bulan di tahun tersebut | Jan — Des |
| **Tahun + Bulan** | Per bulan, bulan yang dipilih di-**highlight** | Jan — Des |

Grafik menggunakan library **Chart.js 4.4** (CDN).

---

## 🗄️ Seed Data

Database sudah dilengkapi data contoh untuk **3 tahun** (2024–2026):

| Tahun | Pemasukan | Pengeluaran |
|---|---|---|
| 2024 | 8 transaksi | 7 transaksi |
| 2025 | 10 transaksi | 9 transaksi |
| 2026 | 9 transaksi | 10 transaksi |

---

## 🛠️ Teknologi

| Komponen | Teknologi |
|---|---|
| Backend | PHP 7.4+ (Native OOP) |
| Database | MySQL (mysqli, prepared statements) |
| Frontend | HTML5, CSS3, Bootstrap 5.3 |
| Grafik | Chart.js 4.4 |
| Font | Inter (Google Fonts) |
| Icons | Bootstrap Icons |

---

## 📝 Lisensi

Project ini dibuat untuk keperluan tugas UAS mata kuliah **Konsep Bahasa Pemrograman**.

---

*SIKASRA © 2026 — Sistem Pengelolaan Keuangan Kas Asrama*
