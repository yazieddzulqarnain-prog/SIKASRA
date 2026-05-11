<?php
// ============================================
// Class KasAsrama
// Fungsi: Menghitung dan mengelola saldo kas asrama
// Konsep OOP: Encapsulation pada property saldo
// ============================================

class KasAsrama {
    // Property private - encapsulation (saldo hanya bisa diakses lewat getter/setter)
    private $saldo;  // Menyimpan saldo kas asrama
    private $conn;   // Koneksi database

    // Constructor - menghitung saldo otomatis saat object dibuat
    public function __construct($conn) {
        $this->conn = $conn;
        $this->hitungSaldo(); // Langsung hitung saldo saat dibuat
    }

    // =====================
    // GETTER & SETTER untuk property private $saldo
    // Contoh encapsulation
    // =====================

    // Getter - mengambil nilai saldo
    public function getSaldo() {
        return $this->saldo;
    }

    // Setter - mengatur nilai saldo (private, hanya digunakan internal)
    private function setSaldo($saldo) {
        $this->saldo = $saldo;
    }

    // =====================
    // METHOD UTAMA
    // =====================

    // Method hitungSaldo - menghitung saldo = total pemasukan - total pengeluaran
    public function hitungSaldo() {
        // Ambil total pemasukan
        $queryMasuk = "SELECT COALESCE(SUM(jumlah), 0) as total FROM pemasukan";
        $resultMasuk = $this->conn->query($queryMasuk);
        $totalMasuk = $resultMasuk->fetch_assoc()['total'];

        // Ambil total pengeluaran
        $queryKeluar = "SELECT COALESCE(SUM(jumlah), 0) as total FROM pengeluaran";
        $resultKeluar = $this->conn->query($queryKeluar);
        $totalKeluar = $resultKeluar->fetch_assoc()['total'];

        // Hitung saldo = pemasukan - pengeluaran
        $this->setSaldo($totalMasuk - $totalKeluar);

        return $this->saldo;
    }

    // Method tambahSaldo - menambah saldo
    public function tambahSaldo($jumlah) {
        $this->saldo += $jumlah;
    }

    // Method kurangiSaldo - mengurangi saldo
    public function kurangiSaldo($jumlah) {
        $this->saldo -= $jumlah;
    }

    // Method hitungSaldoByFilter - menghitung saldo berdasarkan filter tahun & bulan
    public function hitungSaldoByFilter($tahun = null, $bulan = null) {
        // Query pemasukan dengan filter
        $queryMasuk = "SELECT COALESCE(SUM(jumlah), 0) as total FROM pemasukan WHERE 1=1";
        $queryKeluar = "SELECT COALESCE(SUM(jumlah), 0) as total FROM pengeluaran WHERE 1=1";
        $params = [];
        $types = "";

        if ($tahun) {
            $queryMasuk .= " AND YEAR(tanggal) = ?";
            $queryKeluar .= " AND YEAR(tanggal) = ?";
            $params[] = $tahun;
            $types .= "i";
        }
        if ($bulan) {
            $queryMasuk .= " AND MONTH(tanggal) = ?";
            $queryKeluar .= " AND MONTH(tanggal) = ?";
            $params[] = $bulan;
            $types .= "i";
        }

        // Hitung total pemasukan
        $stmtMasuk = $this->conn->prepare($queryMasuk);
        if (!empty($params)) {
            $stmtMasuk->bind_param($types, ...$params);
        }
        $stmtMasuk->execute();
        $totalMasuk = $stmtMasuk->get_result()->fetch_assoc()['total'];

        // Hitung total pengeluaran
        $stmtKeluar = $this->conn->prepare($queryKeluar);
        if (!empty($params)) {
            $stmtKeluar->bind_param($types, ...$params);
        }
        $stmtKeluar->execute();
        $totalKeluar = $stmtKeluar->get_result()->fetch_assoc()['total'];

        return $totalMasuk - $totalKeluar;
    }
}
?>
