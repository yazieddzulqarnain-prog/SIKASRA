<?php
// dashboard.php - Halaman utama setelah login
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

require_once '../config/database.php';
require_once '../classes/Pemasukan.php';
require_once '../classes/Pengeluaran.php';
require_once '../classes/KasAsrama.php';

// Buat object
$db = new Database();
$conn = $db->getConnection();
$pemasukan = new Pemasukan($conn);
$pengeluaran = new Pengeluaran($conn);
$kas = new KasAsrama($conn);

// Ambil data statistik
$totalMasuk = $pemasukan->getTotalPemasukan();
$totalKeluar = $pengeluaran->getTotalPengeluaran();
$saldo = $kas->getSaldo();
$jmlMasuk = $pemasukan->getJumlahTransaksiPemasukan();
$jmlKeluar = $pengeluaran->getJumlahTransaksiPengeluaran();
$totalTransaksi = $jmlMasuk + $jmlKeluar;

// Ambil data statistik bulanan untuk grafik
$statMasuk = $pemasukan->getStatistikBulanan();    // Array 12 bulan pemasukan
$statKeluar = $pengeluaran->getStatistikBulanan();  // Array 12 bulan pengeluaran

// Data tabel terbaru
$dataMasuk = $pemasukan->tampilPemasukan();
$dataKeluar = $pengeluaran->tampilPengeluaran();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIKASRA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <!-- Chart.js CDN untuk grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="app-wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title">
                <h5>Dashboard</h5>
                <small>Selamat datang, <?php echo $_SESSION['nama_lengkap']; ?>!</small>
            </div>
            <div class="user-info">
                <div class="user-detail">
                    <div class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></div>
                    <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
                </div>
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['nama_lengkap'],0,1)); ?></div>
            </div>
        </div>
        <div class="content-area">
            <!-- Stat Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card card-income">
                        <div class="stat-icon"><i class="bi bi-arrow-down-circle"></i></div>
                        <div class="stat-label">Total Pemasukan</div>
                        <div class="stat-value money-green">Rp <?php echo number_format($totalMasuk,0,',','.'); ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card card-expense">
                        <div class="stat-icon"><i class="bi bi-arrow-up-circle"></i></div>
                        <div class="stat-label">Total Pengeluaran</div>
                        <div class="stat-value money-red">Rp <?php echo number_format($totalKeluar,0,',','.'); ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card card-balance">
                        <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
                        <div class="stat-label">Saldo Kas</div>
                        <div class="stat-value money-blue">Rp <?php echo number_format($saldo,0,',','.'); ?></div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card card-transaction">
                        <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                        <div class="stat-label">Jumlah Transaksi</div>
                        <div class="stat-value"><?php echo $totalTransaksi; ?></div>
                    </div>
                </div>
            </div>

            <!-- Grafik Statistik Bulanan -->
            <div class="row g-4 mb-4">
                <div class="col-md-8">
                    <div class="card-custom">
                        <div class="card-header">
                            <h5><i class="bi bi-bar-chart-line me-2" style="color:#1a73e8;"></i>Statistik Keuangan Bulanan <?php echo date('Y'); ?></h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartBulanan" height="280"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-custom">
                        <div class="card-header">
                            <h5><i class="bi bi-pie-chart me-2" style="color:#f59e0b;"></i>Proporsi Keuangan</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="chartProporsi" height="280"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tables -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card-custom">
                        <div class="card-header"><h5><i class="bi bi-arrow-down-circle me-2" style="color:#10b981;"></i>Pemasukan Terbaru</h5></div>
                        <div class="card-body" style="padding:0;">
                            <table class="table-custom">
                                <thead><tr><th>Tanggal</th><th>Sumber</th><th>Jumlah</th></tr></thead>
                                <tbody>
                                <?php $c=0; while($row=$dataMasuk->fetch_assoc()): if($c>=5) break; $c++; ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                        <td><?php echo $row['sumber_pemasukan']; ?></td>
                                        <td class="money-green">Rp <?php echo number_format($row['jumlah'],0,',','.'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php if($c==0): ?><tr><td colspan="3" class="text-center" style="padding:24px;color:#9ca3af;">Belum ada data</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-custom">
                        <div class="card-header"><h5><i class="bi bi-arrow-up-circle me-2" style="color:#ef4444;"></i>Pengeluaran Terbaru</h5></div>
                        <div class="card-body" style="padding:0;">
                            <table class="table-custom">
                                <thead><tr><th>Tanggal</th><th>Kategori</th><th>Jumlah</th></tr></thead>
                                <tbody>
                                <?php $c=0; while($row=$dataKeluar->fetch_assoc()): if($c>=5) break; $c++; ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                        <td><?php echo $row['kategori_pengeluaran']; ?></td>
                                        <td class="money-red">Rp <?php echo number_format($row['jumlah'],0,',','.'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php if($c==0): ?><tr><td colspan="3" class="text-center" style="padding:24px;color:#9ca3af;">Belum ada data</td></tr><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script untuk Chart.js -->
<script>
// Data bulanan dari PHP (diubah ke JavaScript)
const dataPemasukan = [<?php echo implode(',', array_values($statMasuk)); ?>];
const dataPengeluaran = [<?php echo implode(',', array_values($statKeluar)); ?>];
const namaBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

// ===== Grafik Bar Chart - Statistik Bulanan =====
const ctxBar = document.getElementById('chartBulanan').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: namaBulan,
        datasets: [
            {
                label: 'Pemasukan',
                data: dataPemasukan,
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1,
                borderRadius: 6,
                barPercentage: 0.6
            },
            {
                label: 'Pengeluaran',
                data: dataPengeluaran,
                backgroundColor: 'rgba(239, 68, 68, 0.7)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1,
                borderRadius: 6,
                barPercentage: 0.6
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: { family: 'Inter', size: 12, weight: '500' },
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 20
                }
            },
            tooltip: {
                backgroundColor: 'rgba(31, 41, 55, 0.95)',
                titleFont: { family: 'Inter', size: 13 },
                bodyFont: { family: 'Inter', size: 12 },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': Rp ' + 
                               context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.05)' },
                ticks: {
                    font: { family: 'Inter', size: 11 },
                    callback: function(value) {
                        if (value >= 1000000) return 'Rp ' + (value/1000000) + ' jt';
                        if (value >= 1000) return 'Rp ' + (value/1000) + ' rb';
                        return 'Rp ' + value;
                    }
                }
            },
            x: {
                grid: { display: false },
                ticks: { font: { family: 'Inter', size: 11 } }
            }
        }
    }
});

// ===== Grafik Doughnut - Proporsi Pemasukan vs Pengeluaran =====
const totalMasuk = <?php echo $totalMasuk; ?>;
const totalKeluar = <?php echo $totalKeluar; ?>;
const ctxPie = document.getElementById('chartProporsi').getContext('2d');
new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: ['Pemasukan', 'Pengeluaran'],
        datasets: [{
            data: [totalMasuk, totalKeluar],
            backgroundColor: [
                'rgba(16, 185, 129, 0.8)',
                'rgba(239, 68, 68, 0.8)'
            ],
            borderColor: ['#ffffff', '#ffffff'],
            borderWidth: 3,
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '65%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    font: { family: 'Inter', size: 12, weight: '500' },
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 16
                }
            },
            tooltip: {
                backgroundColor: 'rgba(31, 41, 55, 0.95)',
                titleFont: { family: 'Inter', size: 13 },
                bodyFont: { family: 'Inter', size: 12 },
                padding: 12,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        return context.label + ': Rp ' + 
                               context.parsed.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>
</body>
</html>
