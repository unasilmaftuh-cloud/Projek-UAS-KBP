<?php

require_once __DIR__ . '/User.php';

class Admin extends User {

    private $adminKey = 'ADMIN_PRIVILEGE_2026';

    protected $role = 'admin';

    public function __construct($data = null) {
        parent::__construct($data);
    }
    
    // Getter
    public function getAdminKey() {
        return $this->adminKey;
    }
    
    // Setter
    public function setAdminKey($key) {
        $this->adminKey = $key;
    }

    public function isAdmin() {
        return true;
    }

    public function tambahMenu($data) {
        require_once __DIR__ . '/Menu.php';
        $menu = new Menu();
        return $menu->tambah($data);
    }

    public function editMenu($data) {
        require_once __DIR__ . '/Menu.php';
        $menu = new Menu();
        return $menu->edit($data);
    }

    public function hapusMenu($id_menu) {
        require_once __DIR__ . '/Menu.php';
        $menu = new Menu();
        return $menu->hapus($id_menu);
    }

    public function editPesanan($data) {
        require_once __DIR__ . '/Pesanan.php';
        $pesanan = new Pesanan();
        return $pesanan->edit($data);
    }

    public function hapusPesanan($id_pesanan) {
        require_once __DIR__ . '/Pesanan.php';
        $pesanan = new Pesanan();
        return $pesanan->hapus($id_pesanan);
    }

    public function updateMeja($jumlah) {
        $file = 'setting_meja.txt';
        return file_put_contents($file, (int)$jumlah) !== false;
    }

    public function getStatistik() {
        require_once __DIR__ . '/Pesanan.php';
        $pesanan = new Pesanan();
        $daftarPesanan = $pesanan->tampilSemua();

        $statistik = [
            'totalTransaksi'   => count($daftarPesanan),
            'totalItemTerjual' => array_sum(array_column($daftarPesanan, 'jumlah')),
            'totalPendapatan'  => 0,
            'pendapatanTunai'  => 0,
            'pendapatanQRIS'   => 0
        ];

        foreach ($daftarPesanan as $p) {
            $statistik['totalPendapatan'] += $p['total_harga'];
            $metode = isset($p['metode_pembayaran']) ? $p['metode_pembayaran'] : 'Tunai';
            if ($metode == 'QRIS') {
                $statistik['pendapatanQRIS'] += $p['total_harga'];
            } else {
                $statistik['pendapatanTunai'] += $p['total_harga'];
            }
        }

        return $statistik;
    }
}
