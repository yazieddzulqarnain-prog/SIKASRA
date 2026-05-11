<?php
// ============================================
// Class Database
// Fungsi: Mengelola koneksi ke database MySQL
// Konsep OOP: Class, Object, Property, Method, Constructor, Encapsulation
// ============================================

class Database {
    // Property private - encapsulation (tidak bisa diakses dari luar class)
    private $host = "localhost";       // alamat server database
    private $username = "root";        // username MySQL (default XAMPP)
    private $password = "";            // password MySQL (default kosong di XAMPP)
    private $database = "db_kas_asrama"; // nama database
    private $conn;                     // menyimpan koneksi database

    // Constructor - otomatis dipanggil saat object dibuat
    // Langsung menghubungkan ke database
    public function __construct() {
        $this->connect();
    }

    // Method private untuk koneksi ke database
    private function connect() {
        // Membuat koneksi menggunakan mysqli
        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        // Cek apakah koneksi berhasil
        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }

        // Set charset ke utf8 agar karakter Indonesia tampil dengan benar
        $this->conn->set_charset("utf8");
    }

    // Getter - mengambil object koneksi
    public function getConnection() {
        return $this->conn;
    }
}
?>
