<?php
require_once __DIR__ . '/Database.php';

class Menu {
    private $idMenu;
    private $namaMenu;
    private $harga;
    private $stok;
    private $db;
    private $koneksi; 

    public function __construct($data = null) {
        $this->db = Database::getInstance();
        $this->koneksi = $this->db->getKoneksi();

        if ($data !== null) {
            $this->idMenu   = isset($data['id_menu'])   ? $data['id_menu']   : null;
            $this->namaMenu = isset($data['nama_menu']) ? $data['nama_menu'] : '';
            $this->harga    = isset($data['harga'])     ? $data['harga']     : 0;
            $this->stok     = isset($data['stok'])      ? $data['stok']      : 0;
        }
    }

    // GETTER

    public function getIdMenu() {
        return $this->idMenu;
    }

    public function getNamaMenu() {
        return $this->namaMenu;
    }

    public function getHarga() {
        return $this->harga;
    }

    public function getStok() {
        return $this->stok;
    }

    // SETTER

    public function setIdMenu($id) {
        $this->idMenu = $id;
    }

    public function setNamaMenu($nama) {
        $this->namaMenu = $nama;
    }

    public function setHarga($harga) {
        $this->harga = $harga;
    }

    public function setStok($stok) {
        $this->stok = $stok;
    }

    public function tampilSemua() {
        $query  = "SELECT * FROM menu ORDER BY nama_menu ASC";
        $result = mysqli_query($this->koneksi, $query);

        $dataMenu = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $dataMenu[] = $row;
        }
        return $dataMenu;
    }

    public function ambilById($id_menu) {
        $id = $this->db->escape($id_menu);
        $query  = "SELECT * FROM menu WHERE id_menu = '$id'";
        $result = mysqli_query($this->koneksi, $query);

        return mysqli_fetch_assoc($result);
    }

    public function tambah($data) {
        $nama = $this->db->escape($data['nama_menu']);
        $harga = $this->db->escape($data['harga']);
        $stok  = isset($data['stok']) ? $this->db->escape($data['stok']) : 0;

        $query  = "INSERT INTO menu (nama_menu, harga, stok) 
                   VALUES ('$nama', '$harga', '$stok')";
        return mysqli_query($this->koneksi, $query);
    }

    public function edit($data) {
        $id    = $this->db->escape($data['id_menu']);
        $nama  = $this->db->escape($data['nama_menu']);
        $harga = $this->db->escape($data['harga']);
        $stok  = isset($data['stok']) ? $this->db->escape($data['stok']) : 0;

        $query  = "UPDATE menu 
                   SET nama_menu = '$nama', harga = '$harga', stok = '$stok' 
                   WHERE id_menu = '$id'";
        return mysqli_query($this->koneksi, $query);
    }

    public function hapus($id_menu) {
        $id = $this->db->escape($id_menu);
        $query = "DELETE FROM menu WHERE id_menu = '$id'";
        return mysqli_query($this->koneksi, $query);
    }

    public function kurangiStok($id_menu, $jumlah) {
        $id  = $this->db->escape($id_menu);
        $qty = $this->db->escape($jumlah);

        $query = "UPDATE menu SET stok = stok - $qty WHERE id_menu = '$id'";
        return mysqli_query($this->koneksi, $query);
    }

    public static function hitungTotalMenu() {
        $db = Database::getInstance();
        $koneksi = $db->getKoneksi();

        $query  = "SELECT COUNT(*) as total FROM menu";
        $result = mysqli_query($koneksi, $query);
        $row    = mysqli_fetch_assoc($result);

        return $row['total'];
    }

    public function kategorikan($daftarMenu = null) {
        if ($daftarMenu === null) {
            $daftarMenu = $this->tampilSemua();
        }

        $menuMakanan  = [];
        $menuCemilan  = [];
        $menuKopi     = [];
        $menuNonKopi  = [];

        foreach ($daftarMenu as $menu) {
            $namaLengkap = strtolower($menu['nama_menu']);
            $isKopi     = false;
            $isNonKopi  = false;
            $isCemilan  = false;

            // Cek Kopi
            $kataKunciKopi = ['kopi', 'cappuccino', 'mochaccino', 'espresso', 'latte', 'americano'];
            foreach ($kataKunciKopi as $kata) {
                if (strpos($namaLengkap, $kata) !== false) {
                    $isKopi = true;
                    break;
                }
            }

            // Cek Non-Kopi
            $kataKunciNonKopi = ['es ', 'teh', 'jus', 'susu', 'air', 'yakult', 'milo', 'jeruk', 'nutrisari', 'soda', 'lemon'];
            if (!$isKopi) {
                foreach ($kataKunciNonKopi as $kata) {
                    if (strpos($namaLengkap, $kata) !== false) {
                        $isNonKopi = true;
                        break;
                    }
                }
            }

            // Cek Cemilan
            $kataKunciCemilan = ['roti', 'pisang', 'kentang', 'pancong', 'mendoan', 'tahu', 'cireng', 'gorengan', 'singkong', 'kerupuk', 'snack'];
            foreach ($kataKunciCemilan as $kata) {
                if (strpos($namaLengkap, $kata) !== false) {
                    $isCemilan = true;
                    break;
                }
            }

            // Distribusi ke array kategori
            if ($isKopi) {
                $menuKopi[] = $menu;
            } elseif ($isNonKopi) {
                $menuNonKopi[] = $menu;
            } elseif ($isCemilan) {
                $menuCemilan[] = $menu;
            } else {
                $menuMakanan[] = $menu;
            }
        }

        return [
            'makanan' => $menuMakanan,
            'cemilan' => $menuCemilan,
            'kopi'    => $menuKopi,
            'nonkopi' => $menuNonKopi
        ];
    }
}
