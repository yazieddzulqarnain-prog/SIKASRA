<?php
// ============================================
// index.php - Halaman utama / redirect
// Jika sudah login -> ke dashboard
// Jika belum login -> ke halaman login
// ============================================

session_start();

// Cek apakah user sudah login
if (isset($_SESSION['user_id'])) {
    // Sudah login, arahkan ke dashboard
    header("Location: pages/dashboard.php");
} else {
    // Belum login, arahkan ke halaman login
    header("Location: pages/login.php");
}
exit();
?>
