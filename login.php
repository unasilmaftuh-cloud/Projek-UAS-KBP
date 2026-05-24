<?php

require_once 'classes/Auth.php';

// Inisialisasi Auth (Singleton Pattern)
$auth = Auth::getInstance();

// Jika sudah login, redirect ke index
$auth->redirectJikaSudahLogin();

$error = '';

// Proses login jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Panggil method login dari class Auth
    if ($auth->login($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = $auth->getError();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Kasir - Warkop Un Un</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <div class="card p-4 mx-3">
        <div class="text-center mb-4">
            <h3 class="text-primary"><i class="bi bi-shop"></i> Warkop Un Un</h3>
            <p class="text-muted">Silakan login kasir dahulu</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">Masuk <i class="bi bi-box-arrow-in-right"></i></button>
        </form>
    </div>
</body>
</html>
