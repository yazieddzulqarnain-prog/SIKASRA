<?php
// ============================================
// Class Transaksi (Parent Class)
// Fungsi: Class dasar untuk transaksi keuangan
// Konsep OOP: Inheritance (parent), Encapsulation, Access Modifier
// Class ini menjadi "induk" dari class Pemasukan dan Pengeluaran
// ============================================

class Transaksi {
    // Property protected - bisa diakses oleh child class (Pemasukan & Pengeluaran)
    protected $id;          // ID transaksi
    protected $tanggal;     // Tanggal transaksi
    protected $keterangan;  // Keterangan transaksi
    protected $conn;        // Koneksi database

    // Property private - hanya bisa diakses di class ini (encapsulation)
    private $jumlah;        // Jumlah uang transaksi

    // Constructor - menerima koneksi database
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // =====================
    // GETTER & SETTER untuk property private $jumlah
    // Ini adalah contoh encapsulation
    // =====================

    // Getter - mengambil nilai jumlah
    public function getJumlah() {
        return $this->jumlah;
    }

    // Setter - mengatur nilai jumlah dengan validasi
    public function setJumlah($jumlah) {
        // Validasi: jumlah harus lebih dari 0
        if ($jumlah > 0) {
            $this->jumlah = $jumlah;
            return true;
        }
        return false; // Gagal jika jumlah tidak valid
    }

    // =====================
    // METHOD DASAR (akan digunakan/di-override oleh child class)
    // =====================

    // Method tambahTransaksi - method dasar (akan di-override oleh child)
    public function tambahTransaksi($tabel, $data) {
        // Method ini bersifat umum, implementasi spesifik ada di child class
        return false;
    }

    // Method hapusTransaksi - menghapus transaksi berdasarkan id dan tabel
    public function hapusTransaksi($tabel, $id) {
        $stmt = $this->conn->prepare("DELETE FROM $tabel WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Method editTransaksi - method dasar (akan di-override oleh child)
    public function editTransaksi($tabel, $id, $data) {
        return false;
    }

    // Method getTransaksiById - mengambil satu transaksi berdasarkan id
    public function getTransaksiById($tabel, $id) {
        $stmt = $this->conn->prepare("SELECT * FROM $tabel WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}
?>
