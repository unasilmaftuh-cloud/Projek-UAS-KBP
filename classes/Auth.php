<?php

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Admin.php';

class Auth {
    private static $instance = null;
    private $user = null;            
    private $error = '';            

    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['login_status']) && $_SESSION['login_status'] === true) {
            $this->muatUserDariSession();
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Auth();
        }
        return self::$instance;
    }

    // GETTER

    public function getUser() {
        return $this->user;
    }

    public function getError() {
        return $this->error;
    }

    // SETTER

    public function setError($pesan) {
        $this->error = $pesan;
    }

    public function login($username, $password) {
        $dataUser = User::cariByLogin($username, $password);

        if ($dataUser !== null) {
            if (strtolower($dataUser['username']) === 'admin') {
                $this->user = new Admin($dataUser);
            } else {
                $this->user = new User($dataUser);
            }

            $this->user->setIsLogin(true);
            $this->user->simpanKeSession();

            return true;
        }

        $this->error = "Username atau Password salah, Bos!";
        return false;
    }


    public function logout() {
        session_unset();
        session_destroy();
        $this->user = null;
        self::$instance = null;
        return true;
    }

    public function cekLogin() {
        return isset($_SESSION['login_status']) && $_SESSION['login_status'] === true;
    }

    public function cekAdmin() {
        if (!$this->cekLogin()) {
            return false;
        }
        return (isset($_SESSION['username_kasir']) && strtolower($_SESSION['username_kasir']) === 'admin');
    }

    public function requireLogin() {
        if (!$this->cekLogin()) {
            header('Location: login.php');
            exit;
        }
    }

    public function requireAdmin() {
        if (!$this->cekAdmin()) {
            header('Location: index.php');
            exit;
        }
    }

    public function redirectJikaSudahLogin() {
        if ($this->cekLogin()) {
            header('Location: index.php');
            exit;
        }
    }

    private function muatUserDariSession() {
        $db = Database::getInstance();
        $koneksi = $db->getKoneksi();

        $username = $_SESSION['username_kasir'];
        $user = $db->escape($username);

        $query  = "SELECT * FROM kasir WHERE username = '$user'";
        $result = mysqli_query($koneksi, $query);

        if ($result && mysqli_num_rows($result) === 1) {
            $dataUser = mysqli_fetch_assoc($result);

            if (strtolower($dataUser['username']) === 'admin') {
                $this->user = new Admin($dataUser);
            } else {
                $this->user = new User($dataUser);
            }
            $this->user->setIsLogin(true);
        }
    }
    
    private function __clone() {}
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
