<?php

require_once 'classes/Auth.php';
require_once 'classes/Pesanan.php';
require_once 'classes/Menu.php';

// Inisialisasi Auth dan cek login
$auth = Auth::getInstance();
$auth->requireLogin();

$isAdmin = $auth->cekAdmin();

// Buat object Pesanan untuk mengambil data
$pesananObj = new Pesanan();
$daftarPesanan = $pesananObj->tampilSemua();

// Buat object Menu untuk dropdown edit
$menuObj = new Menu();
$daftarMenu = $menuObj->tampilSemua();

$alert = '';
if (isset($_GET['status'])) {
    switch ($_GET['status']) {
        case 'sukses_edit': $alert = Pesanan::tampilAlert('Pesanan berhasil diperbarui!', 'success'); break;
        case 'gagal_edit': $alert = Pesanan::tampilAlert('Gagal memperbarui pesanan.', 'danger'); break;
        case 'sukses_hapus': $alert = Pesanan::tampilAlert('Pesanan berhasil dihapus!', 'success'); break;
        case 'gagal_hapus': $alert = Pesanan::tampilAlert('Gagal menghapus pesanan.', 'danger'); break;
        case 'gagal_akses': $alert = Pesanan::tampilAlert('Akses Ditolak!', 'danger'); break;
    }
}

$editMode = false;
$dataEdit = null;
if (isset($_GET['edit']) && $isAdmin) {
    $id_pesanan = $_GET['edit'];
    $dataEdit = $pesananObj->ambilById($id_pesanan);
    if ($dataEdit) {
        $editMode = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Data Pesanan - Warkop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>body { background-color: #f8f9fa; } .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); } .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); } .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0 !important; } .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; } .table th { background-color: #f8f9fa; } .total-row { background-color: #e9ecef; font-weight: bold; } .harga { color: #28a745; font-weight: bold; } .badge-makanan { background-color: #ffc107; color: #000; } .badge-minuman { background-color: #0dcaf0; color: #000; } .badge-campur { background-color: #343a40; color: #fff; }</style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark"><div class="container"><a class="navbar-brand" href="index.php"><i class="bi bi-shop"></i> Warkop Un Un</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav ms-auto align-items-center"><li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house"></i> Beranda</a></li><li class="nav-item"><a class="nav-link active" href="data.php"><i class="bi bi-list-ul"></i> Data Pesanan</a></li><?php if($isAdmin): ?><li class="nav-item"><a class="nav-link fw-bold text-warning" href="dashboard_admin.php"><i class="bi bi-speedometer2"></i> Dashboard Admin</a></li><?php endif; ?><li class="nav-item ms-lg-3 mt-2 mt-lg-0 border-start ps-lg-3"><span class="text-white me-2"><i class="bi bi-person-circle"></i> Kasir: <b><?php echo isset($_SESSION['nama_kasir']) ? $_SESSION['nama_kasir'] : 'Admin'; ?></b><?php if($isAdmin): ?> <span class="badge bg-warning text-dark ms-1">Admin</span> <?php endif; ?></span><a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3"><i class="bi bi-box-arrow-right"></i> Keluar</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <?php echo $alert; ?>
        <?php if ($editMode && $isAdmin): ?><div class="card mb-4"><div class="card-header"><h5 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Pesanan</h5></div><div class="card-body"><form action="proses.php" method="POST"><input type="hidden" name="aksi" value="edit"><input type="hidden" name="id_pesanan" value="<?php echo $dataEdit['id_pesanan']; ?>"><div class="row"><div class="col-md-4"><div class="mb-3"><label class="form-label">Nama Pembeli</label><input type="text" class="form-control" name="nama_pembeli" value="<?php echo htmlspecialchars($dataEdit['nama_pembeli']); ?>" required></div></div><div class="col-md-4"><div class="mb-3"><label class="form-label">Pilih Menu Ulang (Edit mereset pesanan campur ke 1 menu)</label><select class="form-select" name="id_menu" required><?php foreach ($daftarMenu as $menu): $selected = ($menu['nama_menu'] == $dataEdit['menu']) ? 'selected' : ''; ?><option value="<?php echo $menu['id_menu']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($menu['nama_menu']); ?> - <?php echo Pesanan::formatRupiah($menu['harga']); ?></option><?php endforeach; ?></select></div></div><div class="col-md-2"><div class="mb-3"><label class="form-label">Jumlah</label><input type="number" class="form-control" name="jumlah" value="<?php echo $dataEdit['jumlah']; ?>" min="1" required></div></div><div class="col-md-2"><div class="mb-3"><label class="form-label">&nbsp;</label><div class="d-grid"><button type="submit" class="btn btn-primary" onclick="return confirm('Peringatan: Mengedit pesanan campur akan mengubahnya menjadi 1 menu saja. Lanjutkan?')"><i class="bi bi-check-lg"></i> Update</button></div></div></div></div></form><div class="text-end"><a href="data.php" class="btn btn-secondary btn-sm"><i class="bi bi-x-lg"></i> Batal</a></div></div></div><?php endif; ?>
        <div class="card"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0"><i class="bi bi-list-ul"></i> Data Pesanan</h5><a href="index.php" class="btn btn-light btn-sm text-primary fw-bold"><i class="bi bi-plus-lg"></i> Tambah</a></div><div class="card-body">
            <?php if (count($daftarPesanan) > 0): ?>
                <div class="table-responsive"><table class="table table-hover align-middle"><thead><tr><th>No</th><th>Waktu Transaksi</th><th>Nama Pembeli</th><th>Menu</th><th class="text-center">Total Item</th><th class="text-center">Pembayaran</th><th class="text-end">Total</th><th class="text-center">Aksi</th></tr></thead><tbody>
                    <?php $no = 1; $grandTotal = 0; foreach ($daftarPesanan as $pesanan): 
                        $grandTotal += $pesanan['total_harga'];
                        $namaMenuAsli = htmlspecialchars($pesanan['menu']); 
                        $namaMenuHTML = str_replace(', ', '<br>', $namaMenuAsli);
                        
                        $isMinuman = false; $kataKunciMinuman = ['es ', 'kopi', 'teh', 'jus', 'susu', 'air', 'yakult'];
                        foreach ($kataKunciMinuman as $kata) { if (strpos(strtolower($pesanan['menu']), $kata) !== false) { $isMinuman = true; break; } }
                        
                        if (strpos($namaMenuAsli, ',') !== false) {
                            $badgeClass = 'badge-campur'; $iconClass = 'bi-collection'; $labelTxt = 'Pesanan Paket';
                        } else {
                            $badgeClass = $isMinuman ? 'badge-minuman' : 'badge-makanan'; $iconClass = $isMinuman ? 'bi-cup-straw' : 'bi-egg-fried'; $labelTxt = $isMinuman ? 'Minuman' : 'Makanan';
                        }
                        
                        $metodeBayar = isset($pesanan['metode_pembayaran']) ? $pesanan['metode_pembayaran'] : 'Tunai';
                        $badgeMetode = ($metodeBayar == 'QRIS') ? 'bg-primary' : 'bg-success';
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td><td><small class="text-muted fw-bold"><i class="bi bi-clock-history"></i> <?php echo isset($pesanan['tanggal_waktu']) ? $pesanan['tanggal_waktu'] : '-'; ?></small></td><td class="fw-bold"><?php echo htmlspecialchars($pesanan['nama_pembeli']); ?></td>
                        <td><?php echo $namaMenuHTML; ?><br><span class="badge rounded-pill <?php echo $badgeClass; ?> mt-1"><i class="bi <?php echo $iconClass; ?>"></i> <?php echo $labelTxt; ?></span></td>
                        <td class="text-center"><span class="badge bg-secondary rounded-pill px-3"><?php echo $pesanan['jumlah']; ?></span></td>
                        <td class="text-center"><span class="badge <?php echo $badgeMetode; ?> rounded-pill"><i class="bi bi-wallet2"></i> <?php echo $metodeBayar; ?></span></td>
                        <td class="text-end harga"><?php echo Pesanan::formatRupiah($pesanan['total_harga']); ?></td>
                        
                        <td class="text-center">
                            <a href="struk.php?id=<?php echo $pesanan['id_pesanan']; ?>" target="_blank" class="btn btn-info btn-sm text-dark fw-bold mb-1" title="Cetak Struk"><i class="bi bi-printer"></i></a>
                            <?php if($isAdmin): ?>
                                <a href="data.php?edit=<?php echo $pesanan['id_pesanan']; ?>" class="btn btn-warning btn-sm text-dark mb-1" title="Edit"><i class="bi bi-pencil"></i></a> 
                                <a href="proses.php?aksi=hapus&id=<?php echo $pesanan['id_pesanan']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Hapus pesanan ini?')" title="Hapus"><i class="bi bi-trash"></i></a>
                            <?php endif; ?>
                        </td>
                        
                    </tr><?php endforeach; ?>
                </tbody><?php if($isAdmin): ?><tfoot><tr class="total-row"><td colspan="6" class="text-end">Grand Total:</td><td class="text-end harga fs-5 text-success"><?php echo Pesanan::formatRupiah($grandTotal); ?></td><td></td></tr></tfoot><?php endif; ?></table></div>
            <?php else: ?><div class="alert alert-info text-center">Belum ada data pesanan.</div><?php endif; ?>
        </div></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
