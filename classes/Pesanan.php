<?php

require_once __DIR__ . '/Database.php';

class Pesanan {
    private $idPesanan;
    private $tanggalWaktu;
    private $namaKasir;
    private $namaPembeli;
    private $menu;
    private $jumlah;
    private $totalHarga;
    private $metodePembayaran;
    private $uangBayar;
    private $kembalian;
    private $db;
    private $koneksi;

    public function __construct($data = null) {
        $this->db = Database::getInstance();
        $this->koneksi = $this->db->getKoneksi();

        if ($data !== null) {
            $this->idPesanan       = isset($data['id_pesanan'])        ? $data['id_pesanan']        : null;
            $this->tanggalWaktu    = isset($data['tanggal_waktu'])     ? $data['tanggal_waktu']     : null;
            $this->namaKasir       = isset($data['nama_kasir'])        ? $data['nama_kasir']        : null;
            $this->namaPembeli     = isset($data['nama_pembeli'])      ? $data['nama_pembeli']      : '';
            $this->menu            = isset($data['menu'])              ? $data['menu']              : '';
            $this->jumlah          = isset($data['jumlah'])            ? $data['jumlah']            : 0;
            $this->totalHarga      = isset($data['total_harga'])       ? $data['total_harga']       : 0;
            $this->metodePembayaran = isset($data['metode_pembayaran']) ? $data['metode_pembayaran'] : 'Tunai';
            $this->uangBayar       = isset($data['uang_bayar'])        ? $data['uang_bayar']        : 0;
            $this->kembalian       = isset($data['kembalian'])        ? $data['kembalian']        : 0;
        }
    }

    // GETTER

    public function getIdPesanan() {
        return $this->idPesanan;
    }

    public function getTanggalWaktu() {
        return $this->tanggalWaktu;
    }

    public function getNamaKasir() {
        return $this->namaKasir;
    }

    public function getNamaPembeli() {
        return $this->namaPembeli;
    }

    public function getMenu() {
        return $this->menu;
    }

    public function getJumlah() {
        return $this->jumlah;
    }

    public function getTotalHarga() {
        return $this->totalHarga;
    }

    public function getMetodePembayaran() {
        return $this->metodePembayaran;
    }

    public function getUangBayar() {
        return $this->uangBayar;
    }

    public function getKembalian() {
        return $this->kembalian;
    }

    // SETTER

    public function setIdPesanan($id) {
        $this->idPesanan = $id;
    }

    public function setNamaPembeli($nama) {
        $this->namaPembeli = $nama;
    }

    public function setMenu($menu) {
        $this->menu = $menu;
    }

    public function setJumlah($jumlah) {
        $this->jumlah = $jumlah;
    }

    public function setTotalHarga($total) {
        $this->totalHarga = $total;
    }

    public function setMetodePembayaran($metode) {
        $this->metodePembayaran = $metode;
    }

    public function setUangBayar($uang) {
        $this->uangBayar = $uang;
    }

    public function setKembalian($kembalian) {
        $this->kembalian = $kembalian;
    }

    public function tampilSemua() {
        $query  = "SELECT * FROM pesanan ORDER BY id_pesanan DESC";
        $result = mysqli_query($this->koneksi, $query);

        $dataPesanan = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $dataPesanan[] = $row;
        }
        return $dataPesanan;
    }

    public function ambilById($id_pesanan) {
        $id = $this->db->escape($id_pesanan);
        $query  = "SELECT * FROM pesanan WHERE id_pesanan = '$id'";
        $result = mysqli_query($this->koneksi, $query);

        return mysqli_fetch_assoc($result);
    }

    public function simpanGroup($data) {
        date_default_timezone_set('Asia/Jakarta');
        $waktuSekarang = date('Y-m-d H:i:s');

        $namaPembeli = $this->db->escape($data['nama_pembeli']);
        $menu        = $this->db->escape($data['menu']);
        $jumlah      = $this->db->escape($data['jumlah']);
        $totalHarga  = $this->db->escape($data['total_harga']);
        $metode      = $this->db->escape($data['metode_pembayaran']);
        $uangBayar   = $this->db->escape($data['uang_bayar']);
        $kembalian   = $this->db->escape($data['kembalian']);

        $query  = "INSERT INTO pesanan 
                   (tanggal_waktu, nama_pembeli, menu, jumlah, total_harga, metode_pembayaran, uang_bayar, kembalian) 
                   VALUES 
                   ('$waktuSekarang', '$namaPembeli', '$menu', '$jumlah', '$totalHarga', '$metode', '$uangBayar', '$kembalian')";

        return mysqli_query($this->koneksi, $query);
    }

    public function edit($data) {
        require_once __DIR__ . '/Menu.php';
        date_default_timezone_set('Asia/Jakarta');

        $waktuSekarang = date('Y-m-d H:i:s');

        $menuObj = new Menu();
        $menu    = $menuObj->ambilById($data['id_menu']);

        $totalHarga  = $this->hitungTotal($menu['harga'], $data['jumlah']);
        $idPesanan   = $this->db->escape($data['id_pesanan']);
        $namaPembeli = $this->db->escape($data['nama_pembeli']);
        $namaMenu    = $this->db->escape($menu['nama_menu']);
        $jumlah      = $this->db->escape($data['jumlah']);

        $query  = "UPDATE pesanan 
                   SET tanggal_waktu = '$waktuSekarang', 
                       nama_pembeli = '$namaPembeli', 
                       menu = '$namaMenu', 
                       jumlah = '$jumlah', 
                       total_harga = '$totalHarga' 
                   WHERE id_pesanan = '$idPesanan'";

        return mysqli_query($this->koneksi, $query);
    }

    public function hapus($id_pesanan) {
        $id = $this->db->escape($id_pesanan);
        $query = "DELETE FROM pesanan WHERE id_pesanan = '$id'";
        return mysqli_query($this->koneksi, $query);
    }

    public function hitungTotal($harga, $jumlah) {
        return $harga * $jumlah;
    }

    public static function formatRupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }

    public static function tampilAlert($pesan, $tipe = 'success') {
        $alertClass = ($tipe == 'success') ? 'alert-success' : 'alert-danger';
        $icon = ($tipe == 'success') ? '&#10003;' : '&#10007;';

        return "<div class='{$alertClass} alert-dismissible fade show' role='alert'>"
             . "<strong>{$icon}</strong> {$pesan}"
             . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>"
             . "</div>";
    }

    public function validasi($data) {
        $errors = [];

        if (empty($data['nama_pembeli'])) {
            $errors[] = "Nama pembeli tidak boleh kosong!";
        }

        if (empty($data['id_menu'])) {
            $errors[] = "Pilih menu makanan!";
        }

        if (empty($data['jumlah']) || !is_numeric($data['jumlah']) || $data['jumlah'] <= 0) {
            $errors[] = "Jumlah harus angka positif!";
        }

        return $errors;
    }
}
