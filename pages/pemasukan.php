<?php
// pemasukan.php - CRUD halaman pemasukan
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

require_once '../config/database.php';
require_once '../classes/Pemasukan.php';

$db = new Database();
$conn = $db->getConnection();
$pemasukan = new Pemasukan($conn);

$msg = ''; $msgType = '';

// Proses tambah pemasukan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'tambah') {
        $data = [
            'tanggal' => $_POST['tanggal'],
            'jumlah' => $_POST['jumlah'],
            'keterangan' => $_POST['keterangan'],
            'sumber_pemasukan' => $_POST['sumber_pemasukan'],
            'created_by' => $_SESSION['user_id']
        ];
        if ($pemasukan->tambahTransaksi('pemasukan', $data)) {
            $msg = 'Data pemasukan berhasil ditambahkan!'; $msgType = 'success';
        } else {
            $msg = 'Gagal menambahkan data!'; $msgType = 'danger';
        }
    } elseif ($_POST['action'] == 'edit') {
        $data = [
            'tanggal' => $_POST['tanggal'],
            'jumlah' => $_POST['jumlah'],
            'keterangan' => $_POST['keterangan'],
            'sumber_pemasukan' => $_POST['sumber_pemasukan']
        ];
        if ($pemasukan->editTransaksi('pemasukan', $_POST['id'], $data)) {
            $msg = 'Data pemasukan berhasil diupdate!'; $msgType = 'success';
        } else {
            $msg = 'Gagal mengupdate data!'; $msgType = 'danger';
        }
    }
}

// Proses hapus
if (isset($_GET['hapus'])) {
    if ($pemasukan->hapusTransaksi('pemasukan', $_GET['hapus'])) {
        $msg = 'Data berhasil dihapus!'; $msgType = 'success';
    } else {
        $msg = 'Gagal menghapus data!'; $msgType = 'danger';
    }
}

// Ambil data untuk edit
$editData = null;
if (isset($_GET['edit'])) {
    $editData = $pemasukan->getTransaksiById('pemasukan', $_GET['edit']);
}

$dataPemasukan = $pemasukan->tampilPemasukan();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemasukan - SIKASRA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="top-navbar">
            <div class="page-title"><h5>Data Pemasukan</h5><small>Kelola pemasukan kas asrama</small></div>
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

            <!-- Form Tambah/Edit -->
            <div class="card-custom mb-4">
                <div class="card-header">
                    <h5><i class="bi bi-plus-circle me-2"></i><?php echo $editData ? 'Edit Pemasukan' : 'Tambah Pemasukan'; ?></h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" class="form-custom">
                        <input type="hidden" name="action" value="<?php echo $editData ? 'edit' : 'tambah'; ?>">
                        <?php if($editData): ?><input type="hidden" name="id" value="<?php echo $editData['id']; ?>"><?php endif; ?>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" value="<?php echo $editData ? $editData['tanggal'] : date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jumlah (Rp)</label>
                                <input type="number" class="form-control" name="jumlah" placeholder="0" value="<?php echo $editData ? $editData['jumlah'] : ''; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sumber</label>
                                <select class="form-select" name="sumber_pemasukan" required>
                                    <option value="">Pilih sumber</option>
                                    <?php $sumbers = ['Iuran Bulanan','Donasi','Kegiatan','Denda','Lainnya'];
                                    foreach($sumbers as $s): ?>
                                        <option value="<?php echo $s; ?>" <?php echo ($editData && $editData['sumber_pemasukan']==$s)?'selected':''; ?>><?php echo $s; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Keterangan</label>
                                <input type="text" class="form-control" name="keterangan" placeholder="Keterangan" value="<?php echo $editData ? $editData['keterangan'] : ''; ?>">
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn-success-custom"><i class="bi bi-check-lg"></i> Simpan</button>
                            <?php if($editData): ?><a href="pemasukan.php" class="btn-primary-custom ms-2"><i class="bi bi-x-lg"></i> Batal</a><?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="card-custom">
                <div class="card-header"><h5><i class="bi bi-table me-2"></i>Data Pemasukan</h5></div>
                <div class="card-body" style="padding:0;">
                    <table class="table-custom">
                        <thead><tr><th>No</th><th>Tanggal</th><th>Sumber</th><th>Keterangan</th><th>Jumlah</th><th>Dibuat Oleh</th><th>Aksi</th></tr></thead>
                        <tbody>
                        <?php $no=1; while($row=$dataPemasukan->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                <td><?php echo $row['sumber_pemasukan']; ?></td>
                                <td><?php echo $row['keterangan']; ?></td>
                                <td class="money-green">Rp <?php echo number_format($row['jumlah'],0,',','.'); ?></td>
                                <td><?php echo $row['nama_lengkap'] ?? '-'; ?></td>
                                <td>
                                    <a href="pemasukan.php?edit=<?php echo $row['id']; ?>" class="btn-warning-custom"><i class="bi bi-pencil"></i></a>
                                    <a href="pemasukan.php?hapus=<?php echo $row['id']; ?>" class="btn-danger-custom" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if($no==1): ?><tr><td colspan="7" class="text-center" style="padding:24px;color:#9ca3af;">Belum ada data pemasukan</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
