<?php
// ============================================
// Class Pemasukan (Child Class dari Transaksi)
// Fungsi: Mengelola data pemasukan kas asrama
// Konsep OOP: Inheritance (extends Transaksi)
// ============================================

// Memuat class parent (Transaksi)
require_once __DIR__ . '/Transaksi.php';

class Pemasukan extends Transaksi {
    // Property tambahan khusus pemasukan
    protected $sumber_pemasukan; // Sumber pemasukan (Iuran, Donasi, dll)

    // Constructor - memanggil constructor parent
    public function __construct($conn) {
        parent::__construct($conn); // Memanggil constructor Transaksi
    }

    // =====================
    // METHOD KHUSUS PEMASUKAN
    // =====================

    // Method tampilPemasukan - mengambil semua data pemasukan
    public function tampilPemasukan() {
        $query = "SELECT p.*, u.nama_lengkap 
                  FROM pemasukan p 
                  LEFT JOIN users u ON p.created_by = u.id 
                  ORDER BY p.tanggal DESC";
        $result = $this->conn->query($query);
        return $result;
    }

    // Override method tambahTransaksi dari parent class
    public function tambahTransaksi($tabel = 'pemasukan', $data = []) {
        $stmt = $this->conn->prepare(
            "INSERT INTO pemasukan (tanggal, jumlah, keterangan, sumber_pemasukan, created_by) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sdssi",
            $data['tanggal'],
            $data['jumlah'],
            $data['keterangan'],
            $data['sumber_pemasukan'],
            $data['created_by']
        );

        // Set property menggunakan setter dari parent
        $this->setJumlah($data['jumlah']);
        $this->sumber_pemasukan = $data['sumber_pemasukan'];

        return $stmt->execute();
    }

    // Override method editTransaksi dari parent class
    public function editTransaksi($tabel = 'pemasukan', $id = 0, $data = []) {
        $stmt = $this->conn->prepare(
            "UPDATE pemasukan SET tanggal = ?, jumlah = ?, keterangan = ?, sumber_pemasukan = ? WHERE id = ?"
        );
        $stmt->bind_param(
            "sdssi",
            $data['tanggal'],
            $data['jumlah'],
            $data['keterangan'],
            $data['sumber_pemasukan'],
            $id
        );
        return $stmt->execute();
    }

    // Method getTotalPemasukan - menghitung total semua pemasukan
    public function getTotalPemasukan() {
        $query = "SELECT COALESCE(SUM(jumlah), 0) as total FROM pemasukan";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Method getJumlahTransaksiPemasukan - menghitung jumlah transaksi pemasukan
    public function getJumlahTransaksiPemasukan() {
        $query = "SELECT COUNT(*) as jumlah FROM pemasukan";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['jumlah'];
    }

    // Method getStatistikBulanan - mengambil total pemasukan per bulan (tahun ini)
    public function getStatistikBulanan($tahun = null) {
        if ($tahun == null) {
            $tahun = date('Y'); // Tahun sekarang
        }
        // Query: total pemasukan dikelompokkan per bulan
        $query = "SELECT MONTH(tanggal) as bulan, COALESCE(SUM(jumlah), 0) as total 
                  FROM pemasukan 
                  WHERE YEAR(tanggal) = ? 
                  GROUP BY MONTH(tanggal) 
                  ORDER BY bulan ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $tahun);
        $stmt->execute();
        $result = $stmt->get_result();

        // Isi array 12 bulan dengan 0, lalu update yang ada datanya
        $data = array_fill(1, 12, 0);
        while ($row = $result->fetch_assoc()) {
            $data[(int)$row['bulan']] = (float)$row['total'];
        }
        return $data;
    }

    // Method tampilPemasukanByFilter - mengambil data pemasukan berdasarkan filter tahun & bulan
    public function tampilPemasukanByFilter($tahun = null, $bulan = null) {
        $query = "SELECT p.*, u.nama_lengkap 
                  FROM pemasukan p 
                  LEFT JOIN users u ON p.created_by = u.id 
                  WHERE 1=1";
        $params = [];
        $types = "";

        // Filter berdasarkan tahun
        if ($tahun) {
            $query .= " AND YEAR(p.tanggal) = ?";
            $params[] = $tahun;
            $types .= "i";
        }
        // Filter berdasarkan bulan
        if ($bulan) {
            $query .= " AND MONTH(p.tanggal) = ?";
            $params[] = $bulan;
            $types .= "i";
        }

        $query .= " ORDER BY p.tanggal DESC";
        $stmt = $this->conn->prepare($query);

        // Bind parameter jika ada filter
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    // Method getTotalPemasukanByFilter - total pemasukan berdasarkan filter
    public function getTotalPemasukanByFilter($tahun = null, $bulan = null) {
        $query = "SELECT COALESCE(SUM(jumlah), 0) as total FROM pemasukan WHERE 1=1";
        $params = [];
        $types = "";

        if ($tahun) {
            $query .= " AND YEAR(tanggal) = ?";
            $params[] = $tahun;
            $types .= "i";
        }
        if ($bulan) {
            $query .= " AND MONTH(tanggal) = ?";
            $params[] = $bulan;
            $types .= "i";
        }

        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Method getTahunTersedia - mengambil daftar tahun yang ada datanya (untuk dropdown)
    public function getTahunTersedia() {
        // Gabungkan tahun dari pemasukan dan pengeluaran
        $query = "SELECT DISTINCT YEAR(tanggal) as tahun FROM pemasukan 
                  UNION 
                  SELECT DISTINCT YEAR(tanggal) as tahun FROM pengeluaran 
                  ORDER BY tahun DESC";
        $result = $this->conn->query($query);
        $tahun = [];
        while ($row = $result->fetch_assoc()) {
            $tahun[] = $row['tahun'];
        }
        return $tahun;
    }

    // Method getStatistikTahunan - mengambil total pemasukan per tahun (untuk grafik semua tahun)
    public function getStatistikTahunan() {
        $query = "SELECT YEAR(tanggal) as tahun, COALESCE(SUM(jumlah), 0) as total 
                  FROM pemasukan 
                  GROUP BY YEAR(tanggal) 
                  ORDER BY tahun ASC";
        $result = $this->conn->query($query);
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[$row['tahun']] = (float)$row['total'];
        }
        return $data;
    }
}
?>
