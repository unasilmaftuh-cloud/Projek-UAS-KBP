<?php

require_once __DIR__ . '/Database.php';

class User {
    private $idKasir;
    private $username;
    private $password;
    private $namaLengkap;
    private $db;

    protected $role = 'kasir';
    protected $isLogin = false;

    public function __construct($data = null) {
        $this->db = Database::getInstance();

        if ($data !== null) {
            $this->idKasir    = isset($data['id_kasir'])    ? $data['id_kasir']    : null;
            $this->username   = isset($data['username'])    ? $data['username']    : '';
            $this->password   = isset($data['password'])    ? $data['password']    : '';
            $this->namaLengkap = isset($data['nama_lengkap']) ? $data['nama_lengkap'] : '';
        }
    }

    // Getter: Mendapatkan ID Kasir
    public function getIdKasir() {
        return $this->idKasir;
    }

    // Getter: Mendapatkan Username
    public function getUsername() {
        return $this->username;
    }

    // Getter: Mendapatkan Nama Lengkap
    public function getNamaLengkap() {
        return $this->namaLengkap;
    }

    // Getter: Mendapatkan Role 
    public function getRole() {
        return $this->role;
    }

    // Getter: Cek apakah user sudah login
    public function getIsLogin() {
        return $this->isLogin;
    }

    // Setter: Mengubah Nama Lengkap
    public function setNamaLengkap($nama) {
        $this->namaLengkap = $nama;
    }

    // Setter: Mengubah Username
    public function setUsername($username) {
        $this->username = $username;
    }

    // Setter: Mengubah Password (dengan enkripsi MD5
    public function setPassword($password) {
        $this->password = md5($password);
    }

    // Setter: Set status logi
    public function setIsLogin($status) {
        $this->isLogin = $status;
    }
     
    public function simpanKeSession() {
        $_SESSION['login_status']     = true;
        $_SESSION['nama_kasir']       = $this->namaLengkap;
        $_SESSION['username_kasir']   = $this->username;
    }
     
    public static function cariByLogin($username, $password) {
        $db = Database::getInstance();
        $koneksi = $db->getKoneksi();

        $user = $db->escape($username);
        $pass = md5($password); // Enkripsi MD5 sesuai database lama

        $query  = "SELECT * FROM kasir WHERE username = '$user' AND password = '$pass'";
        $result = mysqli_query($koneksi, $query);

        if (mysqli_num_rows($result) === 1) {
            return mysqli_fetch_assoc($result);
        }
        return null;
    }
     
    public function isAdmin() {
        return false;
    }
}
