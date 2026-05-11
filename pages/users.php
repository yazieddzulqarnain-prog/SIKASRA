<?php
// users.php - Kelola user (hanya admin)
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
if ($_SESSION['role'] != 'admin') { header("Location: dashboard.php"); exit(); }

require_once '../config/database.php';
require_once '../classes/User.php';

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);

$msg = ''; $msgType = '';

// Proses tambah user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'tambah') {
    if ($user->tambahUser($_POST['username'], $_POST['password'], $_POST['nama_lengkap'], $_POST['role'])) {
        $msg = 'User berhasil ditambahkan!'; $msgType = 'success';
    } else {
        $msg = 'Gagal menambahkan user! Username mungkin sudah ada.'; $msgType = 'danger';
    }
}

// Proses hapus user
if (isset($_GET['hapus'])) {
    if ($_GET['hapus'] != $_SESSION['user_id']) {
        if ($user->hapusUser($_GET['hapus'])) {
            $msg = 'User berhasil dihapus!'; $msgType = 'success';
        } else {
            $msg = 'Gagal menghapus user!'; $msgType = 'danger';
        }
    } else {
        $msg = 'Tidak bisa menghapus akun sendiri!'; $msgType = 'danger';
    }
}

$allUsers = $user->getAllUsers();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - SIKASRA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title"><h5>Kelola User</h5><small>Manajemen pengguna sistem</small></div>
            <div class="user-info">
                <div class="user-detail">
                    <div class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></div>
                    <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
                </div>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['nama_lengkap'],0,1)); ?></div>
            </div>
        </div>
        <div class="content-area">
            <?php if ($msg): ?>
                <div class="alert alert-custom alert-<?php echo $msgType; ?>-custom mb-3"><?php echo $msg; ?></div>
            <?php endif; ?>

            <!-- Form Tambah User -->
            <div class="card-custom mb-4">
                <div class="card-header"><h5><i class="bi bi-person-plus me-2"></i>Tambah User Baru</h5></div>
                <div class="card-body">
                    <form method="POST" action="" class="form-custom">
                        <input type="hidden" name="action" value="tambah">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" name="nama_lengkap" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="username" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Role</label>
                                <select class="form-select" name="role" required>
                                    <option value="bendahara">Bendahara</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn-success-custom"><i class="bi bi-check-lg"></i> Tambah User</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel User -->
            <div class="card-custom">
                <div class="card-header"><h5><i class="bi bi-people me-2"></i>Daftar User</h5></div>
                <div class="card-body" style="padding:0;">
                    <table class="table-custom">
                        <thead><tr><th>No</th><th>Nama Lengkap</th><th>Username</th><th>Role</th><th>Dibuat</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php $no=1; while($row=$allUsers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo $row['nama_lengkap']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><span class="badge-role badge-<?php echo $row['role']; ?>"><?php echo ucfirst($row['role']); ?></span></td>
                                <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php if($row['id'] != $_SESSION['user_id']): ?>
                                        <a href="users.php?hapus=<?php echo $row['id']; ?>" class="btn-danger-custom" onclick="return confirm('Yakin hapus user ini?')"><i class="bi bi-trash"></i></a>
                                    <?php else: ?>
                                        <span style="color:#9ca3af;font-size:12px;">Akun aktif</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
