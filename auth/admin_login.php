<?php
require_once '../config/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Harap isi semua kolom.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE nama_pengguna = ? AND peran = 'admin'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['kata_sandi'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['nama_pengguna'];
            $_SESSION['role'] = $user['peran'];
            $_SESSION['full_name'] = $user['nama_lengkap'];
            
            header("Location: " . BASE_URL . "index.php");
            exit;
        } else {
            // Check if user exists but is not admin
            $check = $pdo->prepare("SELECT * FROM pengguna WHERE nama_pengguna = ?");
            $check->execute([$username]);
            if ($check->fetch()) {
                $error = "Akun ini bukan Admin.";
            } else {
                $error = "Username atau password salah.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Simbad</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body" style="background: linear-gradient(135deg, hsl(var(--primary-dark)), #1a1a2e);">
    
    <div class="auth-container">
        <div class="auth-card" style="border-top: 5px solid white;">
            <div class="auth-header">
                <h1 style="color: white;">Simbad Admin</h1>
                <p style="color: rgba(255,255,255,0.7);">Portal Khusus Administrator</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error" style="background: rgba(255, 100, 100, 0.2); color: #ffcccc; border-color: rgba(255,255,255,0.2);">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" style="color: rgba(255,255,255,0.8);">Username Admin</label>
                    <input type="text" id="username" name="username" placeholder="admin" required style="background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.2);">
                </div>
                
                <div class="form-group">
                    <label for="password" style="color: rgba(255,255,255,0.8);">Password</label>
                    <input type="password" id="password" name="password" placeholder="password" required style="background: rgba(255,255,255,0.1); color: white; border-color: rgba(255,255,255,0.2);">
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="background: white; color: hsl(var(--primary-dark));">Masuk sebagai Admin</button>
            </form>
            
            <div class="auth-footer" style="color: rgba(255,255,255,0.5);">
                <p>Bukan admin? <a href="login.php" style="color: white; text-decoration: underline;">Login Anggota</a></p>
            </div>
        </div>
    </div>

</body>
</html>
