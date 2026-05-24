<?php

class Database {
    private static $instance = null;
    
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "warkop";
    
    protected $koneksi;

    private function __construct() {
        $this->koneksi = mysqli_connect($this->host, $this->username, $this->password, $this->database);
        
        if (!$this->koneksi) {
            die("Koneksi database gagal: " . mysqli_connect_error());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getKoneksi() {
        return $this->koneksi;
    }

    public function escape($string) {
        return mysqli_real_escape_string($this->koneksi, $string);
    }

    public function tutupKoneksi() {
        if ($this->koneksi) {
            mysqli_close($this->koneksi);
            self::$instance = null;
        }
    }
    
    private function __clone() {}

    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
