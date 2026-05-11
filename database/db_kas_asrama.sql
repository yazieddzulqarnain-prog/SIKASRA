-- ============================================
-- SIKASRA - Sistem Pengelolaan Keuangan Kas Asrama
-- Database: db_kas_asrama
-- ============================================

-- Buat database
CREATE DATABASE IF NOT EXISTS db_kas_asrama;
USE db_kas_asrama;

-- ============================================
-- Tabel users
-- Menyimpan data pengguna (admin & bendahara)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'bendahara') NOT NULL DEFAULT 'bendahara',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Tabel pemasukan
-- Menyimpan data pemasukan kas asrama
-- ============================================
CREATE TABLE IF NOT EXISTS pemasukan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    keterangan TEXT,
    sumber_pemasukan VARCHAR(100) NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- Tabel pengeluaran
-- Menyimpan data pengeluaran kas asrama
-- ============================================
CREATE TABLE IF NOT EXISTS pengeluaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    keterangan TEXT,
    kategori_pengeluaran VARCHAR(100) NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- Data awal user (password di-hash dengan password_hash)
-- Admin     -> username: admin,     password: admin123
-- Bendahara -> username: bendahara, password: bendahara123
-- ============================================
INSERT INTO users (username, password, nama_lengkap, role) VALUES
('admin', '$2y$10$EILjooD8pGKmAguqqG4X5.bQiSvcD4bGF5PYlK651eNN6LUYfVP.q', 'Administrator', 'admin'),
('bendahara', '$2y$10$ipqMVfuaQMbPRqveHeZyZexMpFWrSH8YcmI7Crej1WKfY8sUu21km', 'Bendahara Asrama', 'bendahara');

-- ============================================
-- Data contoh pemasukan (2024 - 2026)
-- ============================================
INSERT INTO pemasukan (tanggal, jumlah, keterangan, sumber_pemasukan, created_by) VALUES
-- Tahun 2024
('2024-01-10', 600000, 'Iuran kas bulan Januari 2024', 'Iuran Bulanan', 1),
('2024-03-05', 350000, 'Donasi dari wali penghuni', 'Donasi', 2),
('2024-05-15', 750000, 'Iuran kas bulan Mei 2024', 'Iuran Bulanan', 1),
('2024-07-20', 400000, 'Hasil penjualan barang bekas', 'Kegiatan', 2),
('2024-09-08', 550000, 'Iuran kas bulan September 2024', 'Iuran Bulanan', 1),
('2024-09-25', 200000, 'Denda keterlambatan bayar kas', 'Denda', 1),
('2024-11-12', 800000, 'Iuran kas bulan November 2024', 'Iuran Bulanan', 2),
('2024-11-28', 300000, 'Donasi acara akhir tahun', 'Donasi', 1),
-- Tahun 2025
('2025-02-05', 650000, 'Iuran kas bulan Februari 2025', 'Iuran Bulanan', 1),
('2025-04-10', 500000, 'Iuran kas bulan April 2025', 'Iuran Bulanan', 2),
('2025-04-22', 250000, 'Sumbangan dari alumni angkatan 2020', 'Donasi', 1),
('2025-06-15', 700000, 'Iuran kas bulan Juni 2025', 'Iuran Bulanan', 1),
('2025-06-28', 450000, 'Hasil kegiatan bazaar asrama', 'Kegiatan', 2),
('2025-08-10', 550000, 'Iuran kas bulan Agustus 2025', 'Iuran Bulanan', 2),
('2025-10-05', 600000, 'Iuran kas bulan Oktober 2025', 'Iuran Bulanan', 1),
('2025-10-20', 150000, 'Denda pelanggaran aturan asrama', 'Denda', 1),
('2025-12-08', 750000, 'Iuran kas bulan Desember 2025', 'Iuran Bulanan', 2),
('2025-12-25', 500000, 'Donasi Natal dan akhir tahun', 'Donasi', 1),
-- Tahun 2026
('2026-01-10', 700000, 'Iuran kas bulan Januari 2026', 'Iuran Bulanan', 1),
('2026-02-08', 650000, 'Iuran kas bulan Februari 2026', 'Iuran Bulanan', 2),
('2026-03-12', 500000, 'Iuran kas bulan Maret 2026', 'Iuran Bulanan', 1),
('2026-03-25', 300000, 'Donasi dari alumni', 'Donasi', 2),
('2026-04-15', 550000, 'Iuran kas bulan April 2026', 'Iuran Bulanan', 1),
('2026-04-28', 800000, 'Hasil acara pentas seni asrama', 'Kegiatan', 2),
('2026-05-01', 500000, 'Iuran kas bulan Mei 2026', 'Iuran Bulanan', 1),
('2026-05-03', 200000, 'Sumbangan dari alumni', 'Donasi', 1),
('2026-05-05', 1000000, 'Kas dari kegiatan bazar', 'Kegiatan', 2);

-- ============================================
-- Data contoh pengeluaran (2024 - 2026)
-- ============================================
INSERT INTO pengeluaran (tanggal, jumlah, keterangan, kategori_pengeluaran, created_by) VALUES
-- Tahun 2024
('2024-01-20', 200000, 'Beli alat kebersihan', 'Kebersihan', 1),
('2024-03-15', 350000, 'Bayar listrik bulan Maret', 'Utilitas', 2),
('2024-05-20', 180000, 'Beli perlengkapan dapur', 'Perlengkapan', 1),
('2024-07-10', 250000, 'Bayar air bulan Juli', 'Utilitas', 2),
('2024-09-18', 400000, 'Perbaikan atap bocor', 'Perbaikan', 1),
('2024-11-05', 300000, 'Konsumsi acara akhir tahun', 'Konsumsi', 2),
('2024-11-20', 150000, 'Beli lampu pengganti', 'Perlengkapan', 1),
-- Tahun 2025
('2025-02-15', 280000, 'Bayar listrik bulan Februari', 'Utilitas', 1),
('2025-02-28', 120000, 'Beli sabun dan deterjen', 'Kebersihan', 2),
('2025-04-18', 350000, 'Bayar listrik bulan April', 'Utilitas', 1),
('2025-06-10', 200000, 'Beli alat tulis dan papan info', 'Perlengkapan', 2),
('2025-06-25', 450000, 'Perbaikan pipa air bocor', 'Perbaikan', 1),
('2025-08-20', 300000, 'Bayar listrik bulan Agustus', 'Utilitas', 2),
('2025-10-15', 250000, 'Konsumsi rapat pengurus', 'Konsumsi', 1),
('2025-12-10', 380000, 'Bayar listrik bulan Desember', 'Utilitas', 2),
('2025-12-28', 500000, 'Dekorasi dan konsumsi acara akhir tahun', 'Kegiatan', 1),
-- Tahun 2026
('2026-01-15', 250000, 'Bayar listrik bulan Januari', 'Utilitas', 1),
('2026-01-28', 100000, 'Beli sapu dan pel', 'Kebersihan', 2),
('2026-02-12', 300000, 'Bayar air bulan Februari', 'Utilitas', 1),
('2026-03-10', 180000, 'Beli ember dan gayung', 'Perlengkapan', 2),
('2026-03-28', 350000, 'Bayar listrik bulan Maret', 'Utilitas', 1),
('2026-04-10', 200000, 'Konsumsi kegiatan kerja bakti', 'Konsumsi', 2),
('2026-04-25', 150000, 'Beli tisu dan pembersih lantai', 'Kebersihan', 1),
('2026-05-02', 150000, 'Beli sabun dan pembersih', 'Kebersihan', 1),
('2026-05-04', 300000, 'Bayar listrik bulan Mei', 'Utilitas', 2),
('2026-05-06', 100000, 'Beli alat tulis untuk asrama', 'Perlengkapan', 1);
