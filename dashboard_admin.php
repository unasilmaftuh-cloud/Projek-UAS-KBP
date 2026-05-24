<?php

require_once 'classes/Auth.php';
require_once 'classes/Admin.php';
require_once 'classes/Menu.php';

// Inisialisasi Auth dan cek admin
$auth = Auth::getInstance();
$auth->requireLogin();
$auth->requireAdmin();

// Ambil data user admin dari Auth
$user = $auth->getUser();  // Ini adalah object Admin (karena username = 'admin')

// Ambil daftar pesanan dan menu
$menuObj = new Menu();
$daftarMenu = $menuObj->tampilSemua();

// Kategorikan menu menggunakan method class Menu
$kategori = $menuObj->kategorikan($daftarMenu);
$menuMakanan  = $kategori['makanan'];
$menuCemilan  = $kategori['cemilan'];
$menuKopi     = $kategori['kopi'];
$menuNonKopi  = $kategori['nonkopi'];

// Ambil statistik menggunakan method class Admin
$statistik = $user->getStatistik();
$totalTransaksi   = $statistik['totalTransaksi'];
$totalItemTerjual = $statistik['totalItemTerjual'];
$totalPendapatan  = $statistik['totalPendapatan'];
$pendapatanTunai  = $statistik['pendapatanTunai'];
$pendapatanQRIS   = $statistik['pendapatanQRIS'];
$totalMenu        = count($daftarMenu);

$jumlahMeja = file_exists('setting_meja.txt') ? (int)file_get_contents('setting_meja.txt') : 20;

$alert = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'sukses_menu') {
        $alert = Pesanan::tampilAlert('Data Menu berhasil diperbarui!', 'success');
    } elseif ($_GET['status'] == 'gagal_menu') {
        $alert = Pesanan::tampilAlert('Gagal memproses data menu.', 'danger');
    } elseif ($_GET['status'] == 'sukses_meja') {
        $alert = Pesanan::tampilAlert('Jumlah meja berhasil di-update!', 'success');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; } .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); } .dashboard-title { font-weight: 700; color: #2c3e50; } .stat-card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.2s; background: #fff; } .stat-card:hover { transform: translateY(-5px); } .icon-box { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; } .bg-icon-primary { background: #e0e7ff; color: #4f46e5; } .bg-icon-success { background: #dcfce7; color: #16a34a; } .bg-icon-info { background: #e0f2fe; color: #0284c7; } .bg-icon-warning { background: #fef3c7; color: #d97706; } .stat-value { font-size: 2rem; font-weight: 800; color: #1e293b; margin-top: 10px; } .stat-label { color: #64748b; font-size: 0.9rem; font-weight: 500; } .custom-table-card { border-radius: 15px; border: none; box-shadow: 0 5px 15px rgba(0,0,0,0.05); } .table thead th { border-bottom: 2px solid #f1f5f9; color: #64748b; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; } .table tbody td { padding: 1rem 0.5rem; vertical-align: middle; color: #334155; font-weight: 500; }</style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark mb-4"><div class="container"><a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-shop"></i> Warkop Un Un</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav ms-auto align-items-center"><li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-house"></i> Beranda</a></li><li class="nav-item"><a class="nav-link" href="data.php"><i class="bi bi-list-ul"></i> Data Pesanan</a></li><li class="nav-item"><a class="nav-link active fw-bold" href="dashboard_admin.php"><i class="bi bi-speedometer2"></i> Dashboard Admin</a></li><li class="nav-item ms-lg-3 mt-2 mt-lg-0 border-start ps-lg-3"><span class="text-white me-2"><i class="bi bi-person-circle"></i> <b>Bos Besar</b><span class="badge bg-warning text-dark ms-1">Admin</span></span><a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3"><i class="bi bi-box-arrow-right"></i> Keluar</a></li></ul></div></div></nav>
    <div class="container">
        <?php echo $alert; ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><div><h2 class="dashboard-title mb-1">Dashboard Admin</h2><p class="text-muted">Selamat datang, Bos! Berikut ringkasan keseluruhan data warkop.</p></div></div>
        
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3"><div class="stat-card p-4"><div class="icon-box bg-icon-primary mb-2"><i class="bi bi-receipt"></i></div><div class="stat-value"><?php echo $totalTransaksi; ?></div><div class="stat-label">Total Transaksi</div></div></div>
            <div class="col-md-6 col-lg-3"><div class="stat-card p-4"><div class="icon-box bg-icon-success mb-2"><i class="bi bi-cash-stack"></i></div><div class="stat-value text-success"><?php echo Pesanan::formatRupiah($totalPendapatan); ?></div><div class="stat-label">Total Pendapatan</div><div class="mt-3 pt-2 border-top"><small class="text-muted d-block"><i class="bi bi-cash"></i> Tunai: <b><?php echo Pesanan::formatRupiah($pendapatanTunai); ?></b></small><small class="text-muted d-block"><i class="bi bi-phone"></i> QRIS: <b><?php echo Pesanan::formatRupiah($pendapatanQRIS); ?></b></small></div></div></div>
            <div class="col-md-6 col-lg-3"><div class="stat-card p-4"><div class="icon-box bg-icon-info mb-2"><i class="bi bi-cart-check"></i></div><div class="stat-value"><?php echo $totalItemTerjual; ?></div><div class="stat-label">Porsi Terjual</div></div></div>
            <div class="col-md-6 col-lg-3"><div class="stat-card p-4"><div class="icon-box bg-icon-warning mb-2"><i class="bi bi-cup-hot"></i></div><div class="stat-value"><?php echo $totalMenu; ?></div><div class="stat-label">Total Menu Aktif</div></div></div>
        </div>

        <div class="card custom-table-card p-3 mb-4 border-start border-4 border-primary">
            <div class="d-flex justify-content-between align-items-center">
                <div><h6 class="fw-bold mb-0 text-dark"><i class="bi bi-gear-fill text-primary"></i> Pengaturan Warung</h6><small class="text-muted">Atur jumlah meja yang tersedia untuk pelanggan di form Kasir.</small></div>
                <form action="proses.php" method="POST" class="d-flex align-items-center mb-0"><input type="hidden" name="aksi" value="update_meja"><label class="me-2 fw-bold small text-muted">Total Meja:</label><input type="number" class="form-control text-center fw-bold me-2 border-primary text-primary" name="jumlah_meja" value="<?php echo $jumlahMeja; ?>" style="width: 80px;" min="1" max="100" onfocus="this.select()" required><button type="submit" class="btn btn-primary fw-bold text-white btn-sm"><i class="bi bi-save"></i> Simpan</button></form>
            </div>
        </div>

        <div class="card custom-table-card p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3"><div><h5 class="fw-bold mb-0 text-dark">Daftar Menu & Harga</h5><small class="text-muted">Kelola jualan Bos di sini</small></div><button class="btn btn-primary fw-bold rounded-pill shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalTambahMenu"><i class="bi bi-plus-circle"></i> Tambah Menu Baru</button></div>
            
            <?php if(count($menuMakanan) > 0): ?>
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-egg-fried text-warning fs-5"></i> Kategori: Makanan Berat</h6>
            <div class="table-responsive mb-4"><table class="table table-borderless table-hover"><thead><tr><th width="10%">ID</th><th width="45%">NAMA MENU</th><th width="25%">HARGA & STOK</th><th width="20%" class="text-end">AKSI KELOLA</th></tr></thead><tbody class="border-top">
                <?php foreach($menuMakanan as $menu): ?>
                <tr><td class="text-muted">#<?php echo $menu['id_menu']; ?></td><td><span class="fw-bold fs-6"><?php echo htmlspecialchars($menu['nama_menu']); ?></span></td><td class="text-success fw-bold fs-5"><?php echo Pesanan::formatRupiah($menu['harga']); ?><br><span class="badge bg-secondary fs-6 text-white mt-1">Stok: <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?></span></td><td class="text-end"><button class="btn btn-sm btn-light text-warning fw-bold border shadow-sm me-1" data-bs-toggle="modal" data-bs-target="#editMenu<?php echo $menu['id_menu']; ?>"><i class="bi bi-pencil-square"></i> Edit</button><a href="proses.php?aksi=hapus_menu&id=<?php echo $menu['id_menu']; ?>" class="btn btn-sm btn-light text-danger fw-bold border shadow-sm" onclick="return confirm('Yakin hapus menu ini?')"><i class="bi bi-trash"></i></a></td></tr>
                <div class="modal fade" id="editMenu<?php echo $menu['id_menu']; ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow"><form action="proses.php" method="POST"><div class="modal-header bg-light"><h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-warning"></i> Edit Menu</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-4"><input type="hidden" name="aksi" value="edit_menu"><input type="hidden" name="id_menu" value="<?php echo $menu['id_menu']; ?>"><div class="mb-3"><label class="form-label fw-bold">Nama Menu</label><input type="text" class="form-control form-control-lg" name="nama_menu" value="<?php echo htmlspecialchars($menu['nama_menu']); ?>" required></div><div class="mb-3"><label class="form-label fw-bold">Harga Jual (Rp)</label><input type="number" class="form-control form-control-lg text-success fw-bold" name="harga" value="<?php echo $menu['harga']; ?>" onfocus="this.select()" required></div><div class="mb-3"><label class="form-label fw-bold">Update Stok</label><input type="number" class="form-control form-control-lg text-primary fw-bold" name="stok" value="<?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?>" onfocus="this.select()" required></div></div><div class="modal-footer bg-light"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning fw-bold text-dark">Simpan</button></div></form></div></div></div>
                <?php endforeach; ?>
            </tbody></table></div>
            <?php endif; ?>

            <?php if(count($menuCemilan) > 0): ?>
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-basket2 text-danger fs-5"></i> Kategori: Cemilan Nongkrong</h6>
            <div class="table-responsive mb-4"><table class="table table-borderless table-hover"><thead><tr><th width="10%">ID</th><th width="45%">NAMA MENU</th><th width="25%">HARGA & STOK</th><th width="20%" class="text-end">AKSI KELOLA</th></tr></thead><tbody class="border-top">
                <?php foreach($menuCemilan as $menu): ?>
                <tr><td class="text-muted">#<?php echo $menu['id_menu']; ?></td><td><span class="fw-bold fs-6"><?php echo htmlspecialchars($menu['nama_menu']); ?></span></td><td class="text-success fw-bold fs-5"><?php echo Pesanan::formatRupiah($menu['harga']); ?><br><span class="badge bg-secondary fs-6 text-white mt-1">Stok: <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?></span></td><td class="text-end"><button class="btn btn-sm btn-light text-warning fw-bold border shadow-sm me-1" data-bs-toggle="modal" data-bs-target="#editMenu<?php echo $menu['id_menu']; ?>"><i class="bi bi-pencil-square"></i> Edit</button><a href="proses.php?aksi=hapus_menu&id=<?php echo $menu['id_menu']; ?>" class="btn btn-sm btn-light text-danger fw-bold border shadow-sm" onclick="return confirm('Yakin hapus menu ini?')"><i class="bi bi-trash"></i></a></td></tr>
                <div class="modal fade" id="editMenu<?php echo $menu['id_menu']; ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow"><form action="proses.php" method="POST"><div class="modal-header bg-light"><h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-warning"></i> Edit Menu</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-4"><input type="hidden" name="aksi" value="edit_menu"><input type="hidden" name="id_menu" value="<?php echo $menu['id_menu']; ?>"><div class="mb-3"><label class="form-label fw-bold">Nama Menu</label><input type="text" class="form-control form-control-lg" name="nama_menu" value="<?php echo htmlspecialchars($menu['nama_menu']); ?>" required></div><div class="mb-3"><label class="form-label fw-bold">Harga Jual (Rp)</label><input type="number" class="form-control form-control-lg text-success fw-bold" name="harga" value="<?php echo $menu['harga']; ?>" onfocus="this.select()" required></div><div class="mb-3"><label class="form-label fw-bold">Update Stok</label><input type="number" class="form-control form-control-lg text-primary fw-bold" name="stok" value="<?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?>" onfocus="this.select()" required></div></div><div class="modal-footer bg-light"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning fw-bold text-dark">Simpan</button></div></form></div></div></div>
                <?php endforeach; ?>
            </tbody></table></div>
            <?php endif; ?>

            <?php if(count($menuKopi) > 0): ?>
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cup-hot fs-5" style="color: #5c3a21;"></i> Kategori: Aneka Kopi</h6>
            <div class="table-responsive mb-4"><table class="table table-borderless table-hover"><thead><tr><th width="10%">ID</th><th width="45%">NAMA MENU</th><th width="25%">HARGA & STOK</th><th width="20%" class="text-end">AKSI KELOLA</th></tr></thead><tbody class="border-top">
                <?php foreach($menuKopi as $menu): ?>
                <tr><td class="text-muted">#<?php echo $menu['id_menu']; ?></td><td><span class="fw-bold fs-6"><?php echo htmlspecialchars($menu['nama_menu']); ?></span></td><td class="text-success fw-bold fs-5"><?php echo Pesanan::formatRupiah($menu['harga']); ?><br><span class="badge bg-secondary fs-6 text-white mt-1">Stok: <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?></span></td><td class="text-end"><button class="btn btn-sm btn-light text-warning fw-bold border shadow-sm me-1" data-bs-toggle="modal" data-bs-target="#editMenu<?php echo $menu['id_menu']; ?>"><i class="bi bi-pencil-square"></i> Edit</button><a href="proses.php?aksi=hapus_menu&id=<?php echo $menu['id_menu']; ?>" class="btn btn-sm btn-light text-danger fw-bold border shadow-sm" onclick="return confirm('Yakin hapus menu ini?')"><i class="bi bi-trash"></i></a></td></tr>
                <div class="modal fade" id="editMenu<?php echo $menu['id_menu']; ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow"><form action="proses.php" method="POST"><div class="modal-header bg-light"><h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-warning"></i> Edit Menu</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-4"><input type="hidden" name="aksi" value="edit_menu"><input type="hidden" name="id_menu" value="<?php echo $menu['id_menu']; ?>"><div class="mb-3"><label class="form-label fw-bold">Nama Menu</label><input type="text" class="form-control form-control-lg" name="nama_menu" value="<?php echo htmlspecialchars($menu['nama_menu']); ?>" required></div><div class="mb-3"><label class="form-label fw-bold">Harga Jual (Rp)</label><input type="number" class="form-control form-control-lg text-success fw-bold" name="harga" value="<?php echo $menu['harga']; ?>" onfocus="this.select()" required></div><div class="mb-3"><label class="form-label fw-bold">Update Stok</label><input type="number" class="form-control form-control-lg text-primary fw-bold" name="stok" value="<?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?>" onfocus="this.select()" required></div></div><div class="modal-footer bg-light"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning fw-bold text-dark">Simpan</button></div></form></div></div></div>
                <?php endforeach; ?>
            </tbody></table></div>
            <?php endif; ?>

            <?php if(count($menuNonKopi) > 0): ?>
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-cup-straw text-info fs-5"></i> Kategori: Minuman Non-Kopi</h6>
            <div class="table-responsive"><table class="table table-borderless table-hover"><thead><tr><th width="10%">ID</th><th width="45%">NAMA MENU</th><th width="25%">HARGA & STOK</th><th width="20%" class="text-end">AKSI KELOLA</th></tr></thead><tbody class="border-top">
                <?php foreach($menuNonKopi as $menu): ?>
                <tr><td class="text-muted">#<?php echo $menu['id_menu']; ?></td><td><span class="fw-bold fs-6"><?php echo htmlspecialchars($menu['nama_menu']); ?></span></td><td class="text-success fw-bold fs-5"><?php echo Pesanan::formatRupiah($menu['harga']); ?><br><span class="badge bg-secondary fs-6 text-white mt-1">Stok: <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?></span></td><td class="text-end"><button class="btn btn-sm btn-light text-warning fw-bold border shadow-sm me-1" data-bs-toggle="modal" data-bs-target="#editMenu<?php echo $menu['id_menu']; ?>"><i class="bi bi-pencil-square"></i> Edit</button><a href="proses.php?aksi=hapus_menu&id=<?php echo $menu['id_menu']; ?>" class="btn btn-sm btn-light text-danger fw-bold border shadow-sm" onclick="return confirm('Yakin hapus menu ini?')"><i class="bi bi-trash"></i></a></td></tr>
                <div class="modal fade" id="editMenu<?php echo $menu['id_menu']; ?>" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow"><form action="proses.php" method="POST"><div class="modal-header bg-light"><h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-warning"></i> Edit Menu</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body p-4"><input type="hidden" name="aksi" value="edit_menu"><input type="hidden" name="id_menu" value="<?php echo $menu['id_menu']; ?>"><div class="mb-3"><label class="form-label fw-bold">Nama Menu</label><input type="text" class="form-control form-control-lg" name="nama_menu" value="<?php echo htmlspecialchars($menu['nama_menu']); ?>" required></div><div class="mb-3"><label class="form-label fw-bold">Harga Jual (Rp)</label><input type="number" class="form-control form-control-lg text-success fw-bold" name="harga" value="<?php echo $menu['harga']; ?>" onfocus="this.select()" required></div><div class="mb-3"><label class="form-label fw-bold">Update Stok</label><input type="number" class="form-control form-control-lg text-primary fw-bold" name="stok" value="<?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?>" onfocus="this.select()" required></div></div><div class="modal-footer bg-light"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-warning fw-bold text-dark">Simpan</button></div></form></div></div></div>
                <?php endforeach; ?>
            </tbody></table></div>
            <?php endif; ?>
            
        </div>
    </div>
    <div class="modal fade" id="modalTambahMenu" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow"><form action="proses.php" method="POST"><div class="modal-header bg-primary text-white"><h5 class="modal-title fw-bold"><i class="bi bi-plus-circle"></i> Tambah Menu Baru</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body p-4"><input type="hidden" name="aksi" value="tambah_menu"><div class="mb-3"><label class="form-label fw-bold">Nama Menu / Layanan</label><input type="text" class="form-control form-control-lg" name="nama_menu" placeholder="Contoh: Es Kopi Susu" required></div><div class="mb-3"><label class="form-label fw-bold">Harga Jual (Rp)</label><input type="number" class="form-control form-control-lg text-success fw-bold" name="harga" placeholder="Contoh: 8000" onfocus="this.select()" required></div><div class="mb-3"><label class="form-label fw-bold">Stok Awal</label><input type="number" class="form-control form-control-lg text-primary fw-bold" name="stok" placeholder="Contoh: 50" onfocus="this.select()" required></div></div><div class="modal-footer bg-light"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary fw-bold">Tambahkan</button></div></form></div></div></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
