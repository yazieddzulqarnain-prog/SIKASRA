<?php
// ============================================
// Class User
// Fungsi: Mengelola data pengguna (login, logout, dll)
// Konsep OOP: Encapsulation, Getter/Setter, Constructor
// ============================================

class User {
    // Property dengan access modifier
    protected $id;           // protected - bisa diakses oleh child class
    protected $username;     // protected
    private $password;       // private - hanya bisa diakses di class ini (encapsulation)
    protected $role;         // protected
    protected $nama_lengkap; // protected
    protected $conn;         // koneksi database

    // Constructor - menerima koneksi database
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // =====================
    // GETTER METHODS
    // Untuk mengambil nilai property
    // =====================

    // Getter untuk id
    public function getId() {
        return $this->id;
    }

    // Getter untuk username
    public function getUsername() {
        return $this->username;
    }

    // Getter untuk role
    public function getRole() {
        return $this->role;
    }

    // Getter untuk nama lengkap
    public function getNamaLengkap() {
        return $this->nama_lengkap;
    }

    // =====================
    // SETTER METHODS
    // Untuk mengubah nilai property
    // =====================

    // Setter untuk username
    public function setUsername($username) {
        $this->username = $username;
    }

    // Setter untuk password (dengan hash untuk keamanan)
    public function setPassword($password) {
        // Password di-hash agar aman
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    // Setter untuk role
    public function setRole($role) {
        $this->role = $role;
    }

    // Setter untuk nama lengkap
    public function setNamaLengkap($nama_lengkap) {
        $this->nama_lengkap = $nama_lengkap;
    }

    // =====================
    // METHOD UTAMA
    // =====================

    // Method login - memeriksa username dan password
    public function login($username, $password) {
        // Menggunakan prepared statement untuk keamanan
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Jika user ditemukan
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password yang di-hash
            if (password_verify($password, $user['password'])) {
                // Simpan data user ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

                // Set property object
                $this->id = $user['id'];
                $this->username = $user['username'];
                $this->role = $user['role'];
                $this->nama_lengkap = $user['nama_lengkap'];

                return true; // Login berhasil
            }
        }
        return false; // Login gagal
    }

    // Method logout - menghapus session
    public function logout() {
        session_unset();   // Hapus semua data session
        session_destroy(); // Hancurkan session
    }

    // Method getUser - mengambil data user berdasarkan id
    public function getUser($id) {
        $stmt = $this->conn->prepare("SELECT id, username, nama_lengkap, role, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Method getAllUsers - mengambil semua data user (untuk admin)
    public function getAllUsers() {
        $query = "SELECT id, username, nama_lengkap, role, created_at FROM users ORDER BY id ASC";
        $result = $this->conn->query($query);
        return $result;
    }

    // Method tambahUser - menambah user baru
    public function tambahUser($username, $password, $nama_lengkap, $role) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed, $nama_lengkap, $role);
        return $stmt->execute();
    }

    // Method hapusUser - menghapus user berdasarkan id
    public function hapusUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
