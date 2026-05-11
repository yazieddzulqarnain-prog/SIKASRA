<?php
// sidebar.php - Komponen sidebar navigasi
// File ini di-include oleh semua halaman
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-cash-stack"></i></div>
        <h4>SIKASRA</h4>
        <small>Kas Asrama</small>
    </div>
    <ul class="nav-menu">
        <li class="nav-label">Menu Utama</li>
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link <?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="pemasukan.php" class="nav-link <?php echo $current_page == 'pemasukan.php' ? 'active' : ''; ?>">
                <i class="bi bi-arrow-down-circle-fill"></i> Pemasukan
            </a>
        </li>
        <li class="nav-item">
            <a href="pengeluaran.php" class="nav-link <?php echo $current_page == 'pengeluaran.php' ? 'active' : ''; ?>">
                <i class="bi bi-arrow-up-circle-fill"></i> Pengeluaran
            </a>
        </li>
        <li class="nav-item">
            <a href="laporan.php" class="nav-link <?php echo $current_page == 'laporan.php' ? 'active' : ''; ?>">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
            </a>
        </li>
        <?php if ($_SESSION['role'] == 'admin'): ?>
        <li class="nav-label">Admin</li>
        <li class="nav-item">
            <a href="users.php" class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                <i class="bi bi-people-fill"></i> Kelola User
            </a>
        </li>
        <?php endif; ?>
    </ul>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-link">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>
</div>
