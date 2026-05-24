<?php

require_once 'classes/Auth.php';
require_once 'classes/Admin.php';
require_once 'classes/Menu.php';
require_once 'classes/Pesanan.php';

// Inisialisasi Auth dan cek login
$auth = Auth::getInstance();
$auth->requireLogin();

// Ambil data user yang sedang login
$user = $auth->getUser();

// Buat object Menu dan Pesanan
$menuObj = new Menu();
$pesananObj = new Pesanan();

$aksi = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';
    if (!in_array($aksi, ['hapus', 'hapus_menu', 'update_meja'])) {
        header('Location: index.php');
        exit;
    }
} else {
    $aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
}

if (isset($_GET['aksi'])) {
    $aksi = $_GET['aksi'];
}

// AKSI: UPDATE MEJA (Khusus Admin)

if ($aksi === 'update_meja') {
    $auth->requireAdmin();
    $jumlah_meja = isset($_POST['jumlah_meja']) ? (int)$_POST['jumlah_meja'] : 20;

    // Gunakan method class Admin
    $admin = new Admin();
    $admin->updateMeja($jumlah_meja);

    header('Location: dashboard_admin.php?status=sukses_meja');
    exit;
}

// AKSI: TAMBAH MENU (Khusus Admin)
if ($aksi === 'tambah_menu') {
    $auth->requireAdmin();

    $admin = new Admin();
    if ($admin->tambahMenu($_POST)) {
        header('Location: dashboard_admin.php?status=sukses_menu');
    } else {
        header('Location: dashboard_admin.php?status=gagal_menu');
    }
    exit;
}

// AKSI: EDIT MENU (Khusus Admin)
if ($aksi === 'edit_menu') {
    $auth->requireAdmin();

    $admin = new Admin();
    if ($admin->editMenu($_POST)) {
        header('Location: dashboard_admin.php?status=sukses_menu');
    } else {
        header('Location: dashboard_admin.php?status=gagal_menu');
    }
    exit;
}

// AKSI: HAPUS MENU (Khusus Admin)
if ($aksi === 'hapus_menu') {
    $auth->requireAdmin();

    $admin = new Admin();
    if ($admin->hapusMenu($_GET['id'])) {
        header('Location: dashboard_admin.php?status=sukses_menu');
    } else {
        header('Location: dashboard_admin.php?status=gagal_menu');
    }
    exit;
}

// AKSI: TAMBAH PESANAN (Semua User)
if ($aksi === 'tambah') {
    $input_nama = isset($_POST['nama_pembeli']) ? trim($_POST['nama_pembeli']) : '';
    $input_meja = isset($_POST['no_meja']) ? $_POST['no_meja'] : '';
    $nama_pembeli = ($input_meja !== '') ? $input_meja . ' - ' . $input_nama : $input_nama;

    $metode_pembayaran = isset($_POST['metode_pembayaran']) ? $_POST['metode_pembayaran'] : 'Tunai';
    $pesanan_array = isset($_POST['pesanan']) ? $_POST['pesanan'] : [];

    // Ambil input uang bayar dan hapus titiknya
    $string_bayar = isset($_POST['uang_bayar']) ? $_POST['uang_bayar'] : '0';
    $uang_bayar = (int)str_replace('.', '', $string_bayar);

    $errors = [];
    if (empty($input_nama)) {
        $errors[] = "Nama pembeli tidak boleh kosong!";
    }
    if (empty($input_meja)) {
        $errors[] = "Nomor meja / opsi bungkus wajib dipilih!";
    }

    $item_dipesan = [];
    foreach ($pesanan_array as $id_menu => $jumlah) {
        if ($jumlah > 0) {
            $item_dipesan[$id_menu] = $jumlah;
        }
    }
    if (empty($item_dipesan)) {
        $errors[] = "Pilih minimal satu menu!";
    }

    if (count($errors) > 0) {
        $_SESSION['errors'] = $errors;
        header('Location: index.php?status=gagal');
        exit;
    }

    $arr_menu_gabungan = [];
    $total_bayar = 0;
    $total_qty = 0;

    foreach ($item_dipesan as $id_menu => $jumlah) {
        $m = $menuObj->ambilById($id_menu);
        $arr_menu_gabungan[] = $m['nama_menu'] . " (" . $jumlah . ")";
        $total_bayar += ($m['harga'] * $jumlah);
        $total_qty += $jumlah;
    }

    // Hitung Kembalian Otomatis
    if ($metode_pembayaran === 'QRIS') {
        $uang_bayar = $total_bayar; // QRIS selalu pas
        $kembalian = 0;
    } else {
        $kembalian = $uang_bayar - $total_bayar;
    }

    $string_menu = implode(', ', $arr_menu_gabungan);

    $data_pesanan = [
        'nama_pembeli'      => $nama_pembeli,
        'menu'              => $string_menu,
        'jumlah'            => $total_qty,
        'total_harga'       => $total_bayar,
        'metode_pembayaran' => $metode_pembayaran,
        'uang_bayar'        => $uang_bayar,
        'kembalian'         => $kembalian
    ];

    if ($pesananObj->simpanGroup($data_pesanan)) {
        // Kurangi stok setelah pesanan berhasil disimpan
        foreach ($item_dipesan as $id_menu => $jumlah) {
            $menuObj->kurangiStok($id_menu, $jumlah);
        }
        header('Location: index.php?status=sukses');
    } else {
        header('Location: index.php?status=gagal');
    }
    exit;
}

// AKSI: EDIT PESANAN (Khusus Admin)
if ($aksi === 'edit') {
    $auth->requireAdmin();

    $data = [
        'id_pesanan'  => $_POST['id_pesanan'],
        'nama_pembeli' => trim($_POST['nama_pembeli']),
        'id_menu'     => $_POST['id_menu'],
        'jumlah'      => $_POST['jumlah']
    ];

    $errors = $pesananObj->validasi($data);
    if (count($errors) > 0) {
        $_SESSION['errors'] = $errors;
        header('Location: data.php?status=gagal_edit');
        exit;
    }

    $admin = new Admin();
    if ($admin->editPesanan($data)) {
        header('Location: data.php?status=sukses_edit');
    } else {
        header('Location: data.php?status=gagal_edit');
    }
    exit;
}

// AKSI: HAPUS PESANAN (Khusus Admin)
if ($aksi === 'hapus') {
    $auth->requireAdmin();

    $id_pesanan = isset($_GET['id']) ? $_GET['id'] : '';
    if (empty($id_pesanan)) {
        header('Location: data.php?status=gagal_hapus');
        exit;
    }

    $admin = new Admin();
    if ($admin->hapusPesanan($id_pesanan)) {
        header('Location: data.php?status=sukses_hapus');
    } else {
        header('Location: data.php?status=gagal_hapus');
    }
    exit;
}

// Jika tidak ada aksi yang cocok
header('Location: index.php');
exit;
