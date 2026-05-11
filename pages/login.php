<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
require_once '../config/database.php';
require_once '../classes/User.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $db = new Database();
    $user = new User($db->getConnection());
    if ($user->login($username, $password)) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIKASRA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="logo-section">
            <div class="logo-icon"><i class="bi bi-cash-stack"></i></div>
            <h2>SIKASRA</h2>
            <p>Sistem Pengelolaan Keuangan Kas Asrama</p>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-custom alert-danger-custom mb-3">
                <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>
        <div class="mt-4 text-center" style="font-size:12px;color:#9ca3af;">
            <p class="mb-1"><strong>Demo Login:</strong></p>
            <p class="mb-0">Admin: admin / admin123</p>
            <p>Bendahara: bendahara / bendahara123</p>
        </div>
        <!-- Link untuk anggota/viewer -->
        <div class="text-center" style="margin-top:16px;padding-top:16px;border-top:1px solid #e5e7eb;">
            <a href="../laporan_publik.php" style="color:#1a73e8;font-size:14px;font-weight:500;text-decoration:none;">
                <i class="bi bi-eye me-1"></i>Lihat Laporan sebagai Anggota
            </a>
            <p style="font-size:11px;color:#9ca3af;margin-top:4px;">Akses tanpa login</p>
        </div>
    </div>
</div>
</body>
</html>
