<?php

require_once 'classes/Auth.php';
require_once 'classes/Menu.php';
require_once 'classes/Pesanan.php';

$auth = Auth::getInstance();
$auth->requireLogin();

$menuObj = new Menu();
$daftarMenu = $menuObj->tampilSemua();

$kategori = $menuObj->kategorikan($daftarMenu);
$menuMakanan  = $kategori['makanan'];
$menuCemilan  = $kategori['cemilan'];
$menuKopi     = $kategori['kopi'];
$menuNonKopi  = $kategori['nonkopi'];

$jumlahMeja = file_exists('setting_meja.txt') ? (int)file_get_contents('setting_meja.txt') : 20;

$alert = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'sukses') {
        $alert = Pesanan::tampilAlert('Pesanan berhasil disimpan!', 'success');
    } elseif ($_GET['status'] == 'gagal') {
        $alert = Pesanan::tampilAlert('Gagal menyimpan pesanan.', 'danger');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir - Warkop Un Un</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; } .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .card-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0 !important; }
        .menu-card { transition: transform 0.2s ease; cursor: pointer; border-radius: 12px;} .menu-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(102,126,234,0.4); }
        .harga { color: #28a745; font-weight: bold; font-size: 1rem; } #keranjang-container::-webkit-scrollbar { width: 6px; } #keranjang-container::-webkit-scrollbar-thumb { background-color: #ccc; border-radius: 10px; }
        .kategori-title { position: relative; padding-bottom: 10px; margin-bottom: 20px; font-weight: bold; }
        .kategori-title::after { content: ''; position: absolute; left: 0; bottom: 0; width: 50px; height: 3px; border-radius: 5px; } 
        .kategori-makanan::after { background-color: #fd7e14; } .kategori-cemilan::after { background-color: #e83e8c; } .kategori-kopi::after { background-color: #5c3a21; } .kategori-nonkopi::after { background-color: #0dcaf0; }
        .search-box:focus { box-shadow: none; border-color: #667eea; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark"><div class="container"><a class="navbar-brand" href="index.php"><i class="bi bi-shop"></i> Warkop Un Un</a><button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button><div class="collapse navbar-collapse" id="navbarNav"><ul class="navbar-nav ms-auto align-items-center"><li class="nav-item"><a class="nav-link active" href="index.php"><i class="bi bi-house"></i> Beranda</a></li><li class="nav-item"><a class="nav-link" href="data.php"><i class="bi bi-list-ul"></i> Data Pesanan</a></li><?php if($auth->cekAdmin()): ?><li class="nav-item"><a class="nav-link fw-bold text-warning" href="dashboard_admin.php"><i class="bi bi-speedometer2"></i> Dashboard Admin</a></li><?php endif; ?><li class="nav-item ms-lg-3 mt-2 mt-lg-0 border-start ps-lg-3"><span class="text-white me-2"><i class="bi bi-person-circle"></i> Kasir: <b><?php echo isset($_SESSION['nama_kasir']) ? $_SESSION['nama_kasir'] : 'Admin'; ?></b></span><a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Yakin mau logout, Bos?')"><i class="bi bi-box-arrow-right"></i> Keluar</a></li></ul></div></div></nav>
    <div class="container mt-4">
        <?php echo $alert; ?>
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-book"></i> Daftar Menu Warkop</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="mb-4">
                            <div class="input-group input-group-lg shadow-sm" style="border-radius: 10px; overflow: hidden; border: 1px solid #ced4da;">
                                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="text" id="inputPencarian" class="form-control border-0 search-box ps-1" placeholder="Cari nama makanan, minuman, atau cemilan..." onkeyup="filterPencarian()">
                            </div>
                        </div>

                        <?php if(count($menuMakanan) > 0): ?>
                        <div class="kategori-container">
                            <h4 class="kategori-title kategori-makanan text-dark"><i class="bi bi-egg-fried text-warning"></i> Makanan Berat</h4>
                            <div class="row mb-4"><?php foreach ($menuMakanan as $menu): ?><div class="col-md-6 col-lg-4 mb-3 menu-item-wrapper"><div class="card menu-card h-100 border-light" onclick="tambahKeKeranjang(<?php echo $menu['id_menu']; ?>, '<?php echo addslashes($menu['nama_menu']); ?>', <?php echo $menu['harga']; ?>, <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?>)"><div class="card-body text-center p-3 d-flex flex-column justify-content-center bg-light rounded"><i class="bi bi-plus-circle-fill text-warning fs-4 mb-2 opacity-75"></i><h6 class="card-title mb-1"><?php echo htmlspecialchars($menu['nama_menu']); ?></h6><p class="harga mb-0"><?php echo Pesanan::formatRupiah($menu['harga']); ?></p><small class="text-muted fw-bold">Sisa Stok: <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?></small></div></div></div><?php endforeach; ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if(count($menuCemilan) > 0): ?>
                        <div class="kategori-container">
                            <h4 class="kategori-title kategori-cemilan text-dark mt-2"><i class="bi bi-basket2 text-danger"></i> Cemilan Nongkrong</h4>
                            <div class="row mb-4"><?php foreach ($menuCemilan as $menu): ?><div class="col-md-6 col-lg-4 mb-3 menu-item-wrapper"><div class="card menu-card h-100 border-light" onclick="tambahKeKeranjang(<?php echo $menu['id_menu']; ?>, '<?php echo addslashes($menu['nama_menu']); ?>', <?php echo $menu['harga']; ?>, <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?>)"><div class="card-body text-center p-3 d-flex flex-column justify-content-center bg-light rounded"><i class="bi bi-plus-circle-fill text-danger fs-4 mb-2 opacity-75"></i><h6 class="card-title mb-1"><?php echo htmlspecialchars($menu['nama_menu']); ?></h6><p class="harga mb-0"><?php echo Pesanan::formatRupiah($menu['harga']); ?></p><small class="text-muted fw-bold">Sisa Stok: <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?></small></div></div></div><?php endforeach; ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if(count($menuKopi) > 0): ?>
                        <div class="kategori-container">
                            <h4 class="kategori-title kategori-kopi text-dark mt-2"><i class="bi bi-cup-hot" style="color: #5c3a21;"></i> Aneka Kopi</h4>
                            <div class="row mb-4"><?php foreach ($menuKopi as $menu): ?><div class="col-md-6 col-lg-4 mb-3 menu-item-wrapper"><div class="card menu-card h-100 border-light" onclick="tambahKeKeranjang(<?php echo $menu['id_menu']; ?>, '<?php echo addslashes($menu['nama_menu']); ?>', <?php echo $menu['harga']; ?>, <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?>)"><div class="card-body text-center p-3 d-flex flex-column justify-content-center bg-light rounded"><i class="bi bi-plus-circle-fill fs-4 mb-2 opacity-75" style="color: #5c3a21;"></i><h6 class="card-title mb-1"><?php echo htmlspecialchars($menu['nama_menu']); ?></h6><p class="harga mb-0"><?php echo Pesanan::formatRupiah($menu['harga']); ?></p><small class="text-muted fw-bold">Sisa Stok: <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?></small></div></div></div><?php endforeach; ?></div>
                        </div>
                        <?php endif; ?>

                        <?php if(count($menuNonKopi) > 0): ?>
                        <div class="kategori-container">
                            <h4 class="kategori-title kategori-nonkopi text-dark mt-2"><i class="bi bi-cup-straw text-info"></i> Minuman Non-Kopi</h4>
                            <div class="row"><?php foreach ($menuNonKopi as $menu): ?><div class="col-md-6 col-lg-4 mb-3 menu-item-wrapper"><div class="card menu-card h-100 border-light" onclick="tambahKeKeranjang(<?php echo $menu['id_menu']; ?>, '<?php echo addslashes($menu['nama_menu']); ?>', <?php echo $menu['harga']; ?>, <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?>)"><div class="card-body text-center p-3 d-flex flex-column justify-content-center bg-light rounded"><i class="bi bi-plus-circle-fill text-info fs-4 mb-2 opacity-75"></i><h6 class="card-title mb-1"><?php echo htmlspecialchars($menu['nama_menu']); ?></h6><p class="harga mb-0"><?php echo Pesanan::formatRupiah($menu['harga']); ?></p><small class="text-muted fw-bold">Sisa Stok: <?php echo isset($menu['stok']) ? $menu['stok'] : 0; ?></small></div></div></div><?php endforeach; ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <div id="pesan-kosong" class="text-center text-muted my-5" style="display: none;">
                            <i class="bi bi-search fs-1 opacity-25"></i>
                            <p class="mt-2">Menu yang dicari tidak ditemukan.</p>
                        </div>

                </div></div>
            </div>
            <div class="col-lg-4">
                <form action="proses.php" method="POST" id="formPesanan" onsubmit="return validasiForm()">
                    <input type="hidden" name="aksi" value="tambah">
                    <div class="card sticky-top" style="top: 20px;"><div class="card-header bg-success text-white" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);"><h5 class="mb-0"><i class="bi bi-calculator"></i> Sistem Kasir</h5></div>
                        <div class="card-body p-3">
                            <div class="row mb-3">
                                <div class="col-7 pe-1"><label class="form-label fw-bold small"><i class="bi bi-person"></i> Nama Pelanggan</label><input type="text" class="form-control" name="nama_pembeli" placeholder="Ketik nama..." required></div>
                                <div class="col-5 ps-1"><label class="form-label fw-bold small"><i class="bi bi-geo-alt"></i> Meja</label><select class="form-select" name="no_meja" required><option value="" disabled selected>Pilih...</option><option value="Bungkus" class="fw-bold text-primary">&#128230; Bungkus</option><?php for($i=1; $i<=$jumlahMeja; $i++): ?><option value="Meja <?php echo $i; ?>">Meja <?php echo $i; ?></option><?php endfor; ?></select></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small"><i class="bi bi-wallet2"></i> Metode Pembayaran</label>
                                <select class="form-select border-success" id="metode_pembayaran" name="metode_pembayaran" onchange="ubahMetode()">
                                    <option value="Tunai">&#128181; Tunai (Cash)</option>
                                    <option value="QRIS">&#128241; QRIS / Transfer</option>
                                </select>
                            </div>

                            <hr><label class="form-label fw-bold small"><i class="bi bi-bag-check"></i> Rincian Pesanan</label>
                            <div id="keranjang-container" style="max-height: 200px; overflow-y: auto; overflow-x: hidden;"><div class="text-center text-muted my-4"><i class="bi bi-cart-x fs-1 opacity-25"></i><p class="mt-2 small">Klik menu di kiri untuk menambah.</p></div></div>
                            <hr><div class="d-flex justify-content-between align-items-center mb-2"><span class="fw-bold">Total Tagihan:</span><h4 class="text-success mb-0 fw-bold" id="total-harga-tampil">Rp 0</h4></div>
                            
                            <div class="mb-3 p-3 bg-light rounded border border-success border-opacity-25">
                                <label for="uang_bayar" class="form-label fw-bold small"><i class="bi bi-cash-coin"></i> Uang Dibayar (Rp)</label>
                                <input type="text" class="form-control form-control-lg fw-bold text-end mb-2 text-primary" id="uang_bayar" name="uang_bayar" placeholder="0" inputmode="numeric" onfocus="this.select()" oninput="formatUangBayar(this)">
                                <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-2"><span class="fw-bold small text-secondary">Kembalian:</span><h5 class="text-muted mb-0 fw-bold" id="kembalian-tampil">Rp 0</h5></div>
                            </div>
                            <div class="d-grid gap-2"><button type="submit" class="btn btn-success btn-lg shadow-sm" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none;"><i class="bi bi-send-check"></i> Selesaikan Pesanan</button></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SCRIPT PENCARIAN (LIVE FILTER)
        function filterPencarian() {
            let input = document.getElementById('inputPencarian').value.toLowerCase();
            let kotakMenu = document.getElementsByClassName('menu-item-wrapper');
            let adaYangTampil = false;

            for (let i = 0; i < kotakMenu.length; i++) {
                let namaMenu = kotakMenu[i].querySelector('.card-title').innerText.toLowerCase();
                if (namaMenu.includes(input)) {
                    kotakMenu[i].style.display = "";
                    adaYangTampil = true;
                } else {
                    kotakMenu[i].style.display = "none";
                }
            }

            document.getElementById('pesan-kosong').style.display = adaYangTampil ? "none" : "block";
        }

        // SCRIPT KERANJANG
        let keranjang = {}; let grandTotalGlobal = 0;
        function tambahKeKeranjang(id_menu, nama_menu, harga, max_stok) { 
            if (max_stok <= 0) { alert('Maaf Bos, stok ' + nama_menu + ' lagi kosong!'); return; }
            if (keranjang[id_menu]) { 
                if (keranjang[id_menu].jumlah < max_stok) { keranjang[id_menu].jumlah += 1; } 
                else { alert('Mentok Bos! Stok sisa ' + max_stok); }
            } else { 
                keranjang[id_menu] = { nama: nama_menu, harga: harga, jumlah: 1, max: max_stok }; 
            } 
            renderKeranjang(); 
        }
        function ubahMetode() {
            let metode = document.getElementById('metode_pembayaran').value; let inputBayar = document.getElementById('uang_bayar');
            if (metode === 'QRIS') { inputBayar.value = grandTotalGlobal.toLocaleString('id-ID'); inputBayar.setAttribute('readonly', true); inputBayar.classList.add('bg-success', 'text-white', 'bg-opacity-75'); } 
            else { inputBayar.value = ''; inputBayar.removeAttribute('readonly'); inputBayar.classList.remove('bg-success', 'text-white', 'bg-opacity-75'); } hitungKembalian();
        }
        function updateGrandTotal() {
            let grandTotal = 0; for (let id in keranjang) { grandTotal += keranjang[id].harga * keranjang[id].jumlah; } grandTotalGlobal = grandTotal;
            document.getElementById('total-harga-tampil').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID'); ubahMetode();
        }
        function formatUangBayar(input) { let value = input.value.replace(/[^0-9]/g, ''); if (value !== '') { input.value = parseInt(value, 10).toLocaleString('id-ID'); } else { input.value = ''; } hitungKembalian(); }
        function hitungKembalian() {
            let inputString = document.getElementById('uang_bayar').value; let uangBayar = parseInt(inputString.replace(/\./g, ''), 10); let kembalianTampil = document.getElementById('kembalian-tampil');
            if (grandTotalGlobal === 0 || isNaN(uangBayar)) { kembalianTampil.innerText = 'Rp 0'; kembalianTampil.className = 'text-muted mb-0 fw-bold'; return; }
            let kembalian = uangBayar - grandTotalGlobal;
            if (kembalian < 0) { kembalianTampil.innerText = 'Uang Kurang!'; kembalianTampil.className = 'text-danger mb-0 fw-bold'; } else { kembalianTampil.innerText = 'Rp ' + kembalian.toLocaleString('id-ID'); kembalianTampil.className = 'text-primary mb-0 fw-bold'; }
        }
        function ubahJumlah(id_menu, nilai_baru) { 
            let jumlah = parseInt(nilai_baru); if (isNaN(jumlah) || jumlah < 0) { jumlah = 0; } 
            if (jumlah > keranjang[id_menu].max) { alert('Mentok Bos! Stok sisa ' + keranjang[id_menu].max); jumlah = keranjang[id_menu].max; }
            keranjang[id_menu].jumlah = jumlah; updateGrandTotal(); renderKeranjang();
        }
        function hapusItem(id_menu) { delete keranjang[id_menu]; renderKeranjang(); }
        function renderKeranjang() {
            let container = document.getElementById('keranjang-container'); let html = ''; let adaIsi = false;
            for (let id in keranjang) {
                adaIsi = true; let item = keranjang[id]; let hargaFormat = item.harga.toLocaleString('id-ID');
                html += `<div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom"><div style="width: 60%;"><strong class="small">${item.nama}</strong><br><small class="text-muted">Rp ${hargaFormat}</small></div><div class="d-flex align-items-center" style="width: 40%;"><input type="number" class="form-control form-control-sm text-center fw-bold px-1" name="pesanan[${id}]" value="${item.jumlah}" min="0" max="${item.max}" onfocus="this.select()" oninput="ubahJumlah('${id}', this.value)"><button type="button" class="btn btn-sm btn-outline-danger ms-1 px-2 py-1" onclick="hapusItem('${id}')"><i class="bi bi-trash"></i></button></div></div>`;
            }
            if (adaIsi) { container.innerHTML = html; } else { container.innerHTML = `<div class="text-center text-muted my-4"><i class="bi bi-cart-x fs-1 opacity-25"></i><p class="mt-2 small">Klik menu di kiri untuk menambah.</p></div>`; } updateGrandTotal();
        }
        function validasiForm() {
            let itemValid = 0; for (let id in keranjang) { if (keranjang[id].jumlah > 0) itemValid++; }
            if (itemValid === 0) { alert('Keranjang masih kosong!'); return false; }
            let stringBayar = document.getElementById('uang_bayar').value; let uangBayar = parseInt(stringBayar.replace(/\./g, ''), 10);
            if (isNaN(uangBayar) || uangBayar < grandTotalGlobal) { alert('Tunggu dulu Bos! Uang pembayaran kurang.'); document.getElementById('uang_bayar').focus(); return false; } return true; 
        }
    </script>
</body>
</html>
