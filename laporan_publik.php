<?php
// ============================================
// laporan_publik.php - Halaman Publik untuk Anggota/Viewer
// Bisa diakses TANPA LOGIN
// Hanya bisa MELIHAT data, tidak bisa mengubah
// ============================================

require_once 'config/database.php';
require_once 'classes/Pemasukan.php';
require_once 'classes/Pengeluaran.php';
require_once 'classes/KasAsrama.php';

$db = new Database();
$conn = $db->getConnection();
$pemasukan = new Pemasukan($conn);
$pengeluaran = new Pengeluaran($conn);
$kas = new KasAsrama($conn);

// Ambil filter dari GET parameter
$filterTahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';
$filterBulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';

// Daftar tahun yang tersedia
$tahunList = $pemasukan->getTahunTersedia();

// Nama bulan Indonesia
$namaBulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

// Ambil data sesuai filter
if ($filterTahun || $filterBulan) {
    $totalMasuk = $pemasukan->getTotalPemasukanByFilter($filterTahun ?: null, $filterBulan ?: null);
    $totalKeluar = $pengeluaran->getTotalPengeluaranByFilter($filterTahun ?: null, $filterBulan ?: null);
    $saldo = $kas->hitungSaldoByFilter($filterTahun ?: null, $filterBulan ?: null);
    $dataMasuk = $pemasukan->tampilPemasukanByFilter($filterTahun ?: null, $filterBulan ?: null);
    $dataKeluar = $pengeluaran->tampilPengeluaranByFilter($filterTahun ?: null, $filterBulan ?: null);
} else {
    $totalMasuk = $pemasukan->getTotalPemasukan();
    $totalKeluar = $pengeluaran->getTotalPengeluaran();
    $saldo = $kas->getSaldo();
    $dataMasuk = $pemasukan->tampilPemasukan();
    $dataKeluar = $pengeluaran->tampilPengeluaran();
}

// Data grafik — logika: jika tahun dipilih → tampilkan bulanan, jika semua tahun → tampilkan per tahun
$chartMode = $filterTahun ? 'bulanan' : 'tahunan';
if ($chartMode === 'bulanan') {
    $statMasuk = $pemasukan->getStatistikBulanan($filterTahun);
    $statKeluar = $pengeluaran->getStatistikBulanan($filterTahun);
    $chartLabels = json_encode(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des']);
    $chartTitle = 'Statistik Bulanan — Tahun ' . $filterTahun;
} else {
    $statMasukTahunan = $pemasukan->getStatistikTahunan();
    $statKeluarTahunan = $pengeluaran->getStatistikTahunan();
    $semuaTahun = array_unique(array_merge(array_keys($statMasukTahunan), array_keys($statKeluarTahunan)));
    sort($semuaTahun);
    $statMasuk = []; $statKeluar = [];
    foreach ($semuaTahun as $t) {
        $statMasuk[] = $statMasukTahunan[$t] ?? 0;
        $statKeluar[] = $statKeluarTahunan[$t] ?? 0;
    }
    $chartLabels = json_encode($semuaTahun);
    $chartTitle = 'Statistik Keuangan Per Tahun';
}

// Label filter aktif
$filterLabel = "Semua Data";
if ($filterTahun && $filterBulan) {
    $filterLabel = $namaBulan[(int)$filterBulan] . " " . $filterTahun;
} elseif ($filterTahun) {
    $filterLabel = "Tahun " . $filterTahun;
} elseif ($filterBulan) {
    $filterLabel = "Bulan " . $namaBulan[(int)$filterBulan];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Publik - SIKASRA</title>
    <meta name="description" content="Lihat laporan keuangan kas asrama secara publik - SIKASRA">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        /* Layout khusus halaman publik (tanpa sidebar) */
        .public-navbar {
            background: linear-gradient(135deg, #0d47a1 0%, #1a73e8 100%);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.15);
        }
        .public-navbar .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .public-navbar .brand-icon {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .public-navbar .brand h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }
        .public-navbar .brand small {
            font-size: 11px;
            opacity: 0.8;
        }
        .public-navbar .login-link a {
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.15);
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .public-navbar .login-link a:hover {
            background: rgba(255,255,255,0.25);
        }
        .public-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px 32px;
        }
        .viewer-badge {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .public-navbar { padding: 12px 16px; }
            .public-content { padding: 16px; }
        }
    </style>
</head>
<body style="background:#f0f2f5;">
    <!-- Navbar Publik -->
    <div class="public-navbar">
        <div class="brand">
            <div class="brand-icon"><i class="bi bi-cash-stack"></i></div>
            <div>
                <h4>SIKASRA</h4>
                <small>Sistem Pengelolaan Keuangan Kas Asrama</small>
            </div>
        </div>
        <div class="login-link">
            <a href="pages/login.php"><i class="bi bi-box-arrow-in-right"></i> Login Admin</a>
        </div>
    </div>

    <div class="public-content">
        <!-- Badge Viewer -->
        <div class="viewer-badge">
            <i class="bi bi-eye"></i> Mode Anggota — Anda hanya bisa melihat data (read-only)
        </div>

        <!-- Filter Tahun & Bulan -->
        <div class="card-custom mb-4">
            <div class="card-header">
                <h5><i class="bi bi-funnel me-2" style="color:#1a73e8;"></i>Filter Laporan</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="form-custom">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Tahun</label>
                            <select class="form-select" name="tahun">
                                <option value="">-- Semua Tahun --</option>
                                <?php foreach ($tahunList as $t): ?>
                                    <option value="<?php echo $t; ?>" <?php echo $filterTahun == $t ? 'selected' : ''; ?>><?php echo $t; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Bulan</label>
                            <select class="form-select" name="bulan">
                                <option value="">-- Semua Bulan --</option>
                                <?php foreach ($namaBulan as $num => $nama): ?>
                                    <option value="<?php echo $num; ?>" <?php echo $filterBulan == $num ? 'selected' : ''; ?>><?php echo $nama; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn-primary-custom"><i class="bi bi-search"></i> Filter</button>
                            <a href="laporan_publik.php" class="btn-warning-custom ms-2"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
                        </div>
                        <div class="col-md-3 text-end">
                            <span style="font-size:13px;color:#6b7280;">Menampilkan: <strong style="color:#1a73e8;"><?php echo $filterLabel; ?></strong></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Ringkasan -->
        <div class="report-summary">
            <div class="summary-item income">
                <h6 style="color:#059669;">Total Pemasukan</h6>
                <div class="amount money-green">Rp <?php echo number_format($totalMasuk,0,',','.'); ?></div>
            </div>
            <div class="summary-item expense">
                <h6 style="color:#dc2626;">Total Pengeluaran</h6>
                <div class="amount money-red">Rp <?php echo number_format($totalKeluar,0,',','.'); ?></div>
            </div>
            <div class="summary-item balance">
                <h6 style="color:#1a73e8;">Saldo Akhir</h6>
                <div class="amount money-blue">Rp <?php echo number_format($saldo,0,',','.'); ?></div>
            </div>
        </div>

        <!-- Grafik Statistik -->
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card-custom">
                    <div class="card-header">
                        <h5><i class="bi bi-bar-chart-line me-2" style="color:#1a73e8;"></i><?php echo $chartTitle; ?></h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartBulanan" height="280"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-custom">
                    <div class="card-header">
                        <h5><i class="bi bi-pie-chart me-2" style="color:#f59e0b;"></i>Proporsi — <?php echo $filterLabel; ?></h5>
                    </div>
                    <div class="card-body">
                        <canvas id="chartProporsi" height="280"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Pemasukan (Read-Only, tanpa tombol aksi) -->
        <div class="card-custom mb-4">
            <div class="card-header"><h5><i class="bi bi-arrow-down-circle me-2" style="color:#10b981;"></i>Data Pemasukan</h5></div>
            <div class="card-body" style="padding:0;">
                <table class="table-custom">
                    <thead><tr><th>No</th><th>Tanggal</th><th>Sumber</th><th>Keterangan</th><th>Jumlah</th></tr></thead>
                    <tbody>
                    <?php $no=1; while($row=$dataMasuk->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td><?php echo $row['sumber_pemasukan']; ?></td>
                            <td><?php echo $row['keterangan']; ?></td>
                            <td class="money-green">Rp <?php echo number_format($row['jumlah'],0,',','.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if($no==1): ?><tr><td colspan="5" class="text-center" style="padding:24px;color:#9ca3af;">Tidak ada data</td></tr><?php endif; ?>
                    </tbody>
                    <tfoot><tr><td colspan="4" style="text-align:right;font-weight:700;padding:14px 16px;">Total Pemasukan</td>
                    <td class="money-green" style="font-weight:700;padding:14px 16px;">Rp <?php echo number_format($totalMasuk,0,',','.'); ?></td></tr></tfoot>
                </table>
            </div>
        </div>

        <!-- Tabel Pengeluaran (Read-Only, tanpa tombol aksi) -->
        <div class="card-custom mb-4">
            <div class="card-header"><h5><i class="bi bi-arrow-up-circle me-2" style="color:#ef4444;"></i>Data Pengeluaran</h5></div>
            <div class="card-body" style="padding:0;">
                <table class="table-custom">
                    <thead><tr><th>No</th><th>Tanggal</th><th>Kategori</th><th>Keterangan</th><th>Jumlah</th></tr></thead>
                    <tbody>
                    <?php $no=1; while($row=$dataKeluar->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                            <td><?php echo $row['kategori_pengeluaran']; ?></td>
                            <td><?php echo $row['keterangan']; ?></td>
                            <td class="money-red">Rp <?php echo number_format($row['jumlah'],0,',','.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if($no==1): ?><tr><td colspan="5" class="text-center" style="padding:24px;color:#9ca3af;">Tidak ada data</td></tr><?php endif; ?>
                    </tbody>
                    <tfoot><tr><td colspan="4" style="text-align:right;font-weight:700;padding:14px 16px;">Total Pengeluaran</td>
                    <td class="money-red" style="font-weight:700;padding:14px 16px;">Rp <?php echo number_format($totalKeluar,0,',','.'); ?></td></tr></tfoot>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center" style="padding:20px;color:#9ca3af;font-size:13px;">
            <p>SIKASRA &copy; <?php echo date('Y'); ?> — Sistem Pengelolaan Keuangan Kas Asrama</p>
        </div>
    </div>

<script>
const chartMode = '<?php echo $chartMode; ?>';
const dataPemasukan = [<?php echo $chartMode === 'bulanan' ? implode(',', array_values($statMasuk)) : implode(',', $statMasuk); ?>];
const dataPengeluaran = [<?php echo $chartMode === 'bulanan' ? implode(',', array_values($statKeluar)) : implode(',', $statKeluar); ?>];
const chartLabels = <?php echo $chartLabels; ?>;
const filterBulanJS = <?php echo $filterBulan ? $filterBulan : 0; ?>;

const bgM = chartLabels.map((_,i) => chartMode === 'bulanan' && filterBulanJS && (i+1)!==filterBulanJS ? 'rgba(16,185,129,0.25)' : 'rgba(16,185,129,0.7)');
const bgK = chartLabels.map((_,i) => chartMode === 'bulanan' && filterBulanJS && (i+1)!==filterBulanJS ? 'rgba(239,68,68,0.25)' : 'rgba(239,68,68,0.7)');

new Chart(document.getElementById('chartBulanan').getContext('2d'), {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [
            { label: 'Pemasukan', data: dataPemasukan, backgroundColor: bgM, borderColor: 'rgba(16,185,129,1)', borderWidth: 1, borderRadius: 6, barPercentage: 0.6 },
            { label: 'Pengeluaran', data: dataPengeluaran, backgroundColor: bgK, borderColor: 'rgba(239,68,68,1)', borderWidth: 1, borderRadius: 6, barPercentage: 0.6 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { font: { family: 'Inter', size: 12, weight: '500' }, usePointStyle: true, pointStyle: 'circle', padding: 20 } },
            tooltip: { backgroundColor: 'rgba(31,41,55,0.95)', padding: 12, cornerRadius: 8, callbacks: { label: ctx => ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID') } }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { family: 'Inter', size: 11 }, callback: v => v >= 1000000 ? 'Rp '+(v/1000000)+' jt' : v >= 1000 ? 'Rp '+(v/1000)+' rb' : 'Rp '+v } },
            x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 } } }
        }
    }
});

const tM = <?php echo $totalMasuk ?: 0; ?>;
const tK = <?php echo $totalKeluar ?: 0; ?>;
new Chart(document.getElementById('chartProporsi').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: ['Pemasukan', 'Pengeluaran'],
        datasets: [{ data: [tM, tK], backgroundColor: ['rgba(16,185,129,0.8)', 'rgba(239,68,68,0.8)'], borderColor: ['#fff','#fff'], borderWidth: 3, hoverOffset: 8 }]
    },
    options: {
        responsive: true, maintainAspectRatio: false, cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { family: 'Inter', size: 12, weight: '500' }, usePointStyle: true, pointStyle: 'circle', padding: 16 } },
            tooltip: { backgroundColor: 'rgba(31,41,55,0.95)', padding: 12, cornerRadius: 8, callbacks: { label: ctx => ctx.label + ': Rp ' + ctx.parsed.toLocaleString('id-ID') } }
        }
    }
});
</script>
</body>
</html>
