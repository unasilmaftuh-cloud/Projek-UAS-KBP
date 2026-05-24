<?php

require_once 'classes/Auth.php';

// Inisialisasi Auth (Singleton Pattern)
$auth = Auth::getInstance();

// Panggil method logout dari class Auth
$auth->logout();

// Redirect ke halaman login
header('Location: login.php');
exit;
