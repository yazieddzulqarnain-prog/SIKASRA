<?php
// ============================================
// Class Pengeluaran (Child Class dari Transaksi)
// Fungsi: Mengelola data pengeluaran kas asrama
// Konsep OOP: Inheritance (extends Transaksi)
// ============================================

// Memuat class parent (Transaksi)
require_once __DIR__ . '/Transaksi.php';

class Pengeluaran extends Transaksi {
    // Property tambahan khusus pengeluaran
    protected $kategori_pengeluaran; // Kategori pengeluaran (Kebersihan, Utilitas, dll)

    // Constructor - memanggil constructor parent
    public function __construct($conn) {
        parent::__construct($conn); // Memanggil constructor Transaksi
    }

    // =====================
    // METHOD KHUSUS PENGELUARAN
    // =====================

    // Method tampilPengeluaran - mengambil semua data pengeluaran
    public function tampilPengeluaran() {
        $query = "SELECT p.*, u.nama_lengkap 
                  FROM pengeluaran p 
                  LEFT JOIN users u ON p.created_by = u.id 
                  ORDER BY p.tanggal DESC";
        $result = $this->conn->query($query);
        return $result;
    }

    // Override method tambahTransaksi dari parent class
    public function tambahTransaksi($tabel = 'pengeluaran', $data = []) {
        $stmt = $this->conn->prepare(
            "INSERT INTO pengeluaran (tanggal, jumlah, keterangan, kategori_pengeluaran, created_by) 
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sdssi",
            $data['tanggal'],
            $data['jumlah'],
            $data['keterangan'],
            $data['kategori_pengeluaran'],
            $data['created_by']
        );

        // Set property menggunakan setter dari parent
        $this->setJumlah($data['jumlah']);
        $this->kategori_pengeluaran = $data['kategori_pengeluaran'];

        return $stmt->execute();
    }

    // Override method editTransaksi dari parent class
    public function editTransaksi($tabel = 'pengeluaran', $id = 0, $data = []) {
        $stmt = $this->conn->prepare(
            "UPDATE pengeluaran SET tanggal = ?, jumlah = ?, keterangan = ?, kategori_pengeluaran = ? WHERE id = ?"
        );
        $stmt->bind_param(
            "sdssi",
            $data['tanggal'],
            $data['jumlah'],
            $data['keterangan'],
            $data['kategori_pengeluaran'],
            $id
        );
        return $stmt->execute();
    }

    // Method getTotalPengeluaran - menghitung total semua pengeluaran
    public function getTotalPengeluaran() {
        $query = "SELECT COALESCE(SUM(jumlah), 0) as total FROM pengeluaran";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Method getJumlahTransaksiPengeluaran - menghitung jumlah transaksi pengeluaran
    public function getJumlahTransaksiPengeluaran() {
        $query = "SELECT COUNT(*) as jumlah FROM pengeluaran";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['jumlah'];
    }

    // Method getStatistikBulanan - mengambil total pengeluaran per bulan (tahun ini)
    public function getStatistikBulanan($tahun = null) {
        if ($tahun == null) {
            $tahun = date('Y'); // Tahun sekarang
        }
        // Query: total pengeluaran dikelompokkan per bulan
        $query = "SELECT MONTH(tanggal) as bulan, COALESCE(SUM(jumlah), 0) as total 
                  FROM pengeluaran 
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

    // Method tampilPengeluaranByFilter - mengambil data pengeluaran berdasarkan filter tahun & bulan
    public function tampilPengeluaranByFilter($tahun = null, $bulan = null) {
        $query = "SELECT p.*, u.nama_lengkap 
                  FROM pengeluaran p 
                  LEFT JOIN users u ON p.created_by = u.id 
                  WHERE 1=1";
        $params = [];
        $types = "";

        if ($tahun) {
            $query .= " AND YEAR(p.tanggal) = ?";
            $params[] = $tahun;
            $types .= "i";
        }
        if ($bulan) {
            $query .= " AND MONTH(p.tanggal) = ?";
            $params[] = $bulan;
            $types .= "i";
        }

        $query .= " ORDER BY p.tanggal DESC";
        $stmt = $this->conn->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    // Method getTotalPengeluaranByFilter - total pengeluaran berdasarkan filter
    public function getTotalPengeluaranByFilter($tahun = null, $bulan = null) {
        $query = "SELECT COALESCE(SUM(jumlah), 0) as total FROM pengeluaran WHERE 1=1";
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

    // Method getStatistikTahunan - mengambil total pengeluaran per tahun (untuk grafik semua tahun)
    public function getStatistikTahunan() {
        $query = "SELECT YEAR(tanggal) as tahun, COALESCE(SUM(jumlah), 0) as total 
                  FROM pengeluaran 
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
