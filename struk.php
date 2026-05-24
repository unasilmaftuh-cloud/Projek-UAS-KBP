<?php

require_once 'classes/Auth.php';
require_once 'classes/Pesanan.php';
require_once 'classes/Menu.php';

// Inisialisasi Auth dan cek login
$auth = Auth::getInstance();
$auth->requireLogin();

$id_pesanan = isset($_GET['id']) ? $_GET['id'] : '';
if (empty($id_pesanan)) {
    echo "Data pesanan tidak ditemukan.";
    exit;
}

// Buat object Pesanan dan ambil data berdasarkan ID
$pesananObj = new Pesanan();
$pesanan = $pesananObj->ambilById($id_pesanan);

if (!$pesanan) {
    echo "Data pesanan tidak valid.";
    exit;
}

$metode = isset($pesanan['metode_pembayaran']) ? $pesanan['metode_pembayaran'] : 'Tunai';

// Tarik data uang (antisipasi data lama yang belum punya kolom uang_bayar)
$uang_bayar = isset($pesanan['uang_bayar']) ? $pesanan['uang_bayar'] : 0;
$kembalian = isset($pesanan['kembalian']) ? $pesanan['kembalian'] : 0;

// Buat object Menu untuk mendapatkan harga satuan
$menuObj = new Menu();
$daftarMenu = $menuObj->tampilSemua();

// Buat array mapping nama menu ke harga
$hargaMenu = [];
foreach ($daftarMenu as $m) {
    $hargaMenu[strtolower(trim($m['nama_menu']))] = $m['harga'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #<?php echo $pesanan['id_pesanan']; ?> - Warkop Un Un</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 0 auto; padding: 20px; font-size: 14px; background: #fff; color: #000; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; font-size: 20px; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 12px; }
        .divider { border-top: 2px dashed #000; margin: 10px 0; }
        .info-table, .item-table { width: 100%; border-collapse: collapse; font-size: 12px; }
        .info-table td { padding: 2px 0; }
        .item-table th, .item-table td { padding: 4px 0; text-align: left; }
        .item-table .text-right { text-align: right; }
        .total-row { font-weight: bold; font-size: 14px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; }
        @media print { body { width: 100%; padding: 0; } .no-print { display: none !important; } }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>WARKOP UN UN</h2>
        <p>Jl. Mahasiswa Sukses No. 1, Kebumen</p>
        <p>Telp: 0851-7226-0107</p>
    </div>
    <div class="divider"></div>
    <table class="info-table">
        <tr><td>No. Antrean</td><td>: <b>#<?php echo $pesanan['id_pesanan']; ?></b></td></tr>
        <tr><td>Tanggal</td><td>: <?php echo date('d/m/Y', strtotime($pesanan['tanggal_waktu'])); ?></td></tr>
        <tr><td>Waktu</td><td>: <?php echo date('H:i:s', strtotime($pesanan['tanggal_waktu'])); ?></td></tr>
        <tr><td>Kasir</td><td>: <?php echo isset($_SESSION['nama_kasir']) ? $_SESSION['nama_kasir'] : 'Admin'; ?></td></tr>
        <tr><td>Pelanggan</td><td>: <b><?php echo htmlspecialchars($pesanan['nama_pembeli']); ?></b></td></tr>
        <tr><td>Pembayaran</td><td>: <?php echo $metode; ?></td></tr>
    </table>
    
    <div class="divider"></div>
    
    <table class="item-table">
        <tr><th colspan="2">Rincian Pesanan:</th></tr>
        <?php 
        $rincian = explode(', ', $pesanan['menu']);
        foreach($rincian as $item) {
            $nama_item = $item; $qty = 1;
            if (preg_match('/^(.*?)\s*\((\d+)\)$/', trim($item), $matches)) {
                $nama_item = trim($matches[1]); $qty = (int)$matches[2];
            }
            $key = strtolower($nama_item);
            $harga_satuan = isset($hargaMenu[$key]) ? $hargaMenu[$key] : 0;
            $subtotal = $harga_satuan * $qty;
            
            if ($harga_satuan > 0) {
                echo "<tr><td colspan='2' style='padding-top:8px; font-weight:bold;'>" . htmlspecialchars($nama_item) . "</td></tr>";
                echo "<tr><td style='padding-left:10px;'>" . $qty . " x " . Pesanan::formatRupiah($harga_satuan) . "</td><td class='text-right'>" . Pesanan::formatRupiah($subtotal) . "</td></tr>";
            } else {
                echo "<tr><td colspan='2' style='padding-top:8px;'>- " . htmlspecialchars($item) . "</td></tr>";
            }
        }
        ?>
    </table>
    
    <div class="divider"></div>
    
    <table class="item-table">
        <tr class="total-row">
            <td style="padding-bottom: 10px;">TOTAL TAGIHAN</td>
            <td class="text-right" style="padding-bottom: 10px;"><?php echo Pesanan::formatRupiah($pesanan['total_harga']); ?></td>
        </tr>
        
        <?php if($metode == 'QRIS'): ?>
        <tr class="total-row">
            <td>DIBAYAR (QRIS)</td>
            <td class="text-right"><?php echo Pesanan::formatRupiah($pesanan['total_harga']); ?></td>
        </tr>
        <?php else: ?>
        <tr class="total-row">
            <td>TUNAI</td>
            <td class="text-right"><?php echo Pesanan::formatRupiah($uang_bayar); ?></td>
        </tr>
        <tr class="total-row">
            <td>KEMBALI</td>
            <td class="text-right"><?php echo Pesanan::formatRupiah($kembalian); ?></td>
        </tr>
        <?php endif; ?>
    </table>
    
    <div class="divider"></div>
    
    <div class="footer">
        <p>Terima Kasih</p>
        <p>Silakan Berkunjung Kembali</p>
        <p>-- Layanan Konsumen: warkopunun.com --</p>
        <div class="no-print" style="margin-top: 30px;">
            <button onclick="window.print()" style="padding: 8px 15px; background: #0dcaf0; color: #000; font-weight: bold; border: none; border-radius: 5px; cursor: pointer; margin-right: 5px;">Cetak Ulang</button>
            <button onclick="window.close()" style="padding: 8px 15px; background: #dc3545; color: white; font-weight: bold; border: none; border-radius: 5px; cursor: pointer;">Tutup Tab Ini</button>
        </div>
    </div>
</body>
</html>
