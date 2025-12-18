<?php
require_once '../config/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Harap isi semua kolom.";
    } else {
        // Updated table: pengguna, column: nama_pengguna
        $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE nama_pengguna = ?");
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
            $error = "Nama pengguna atau kata sandi salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Perpustakaan Simbad</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body">
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Simbad</h1>
                <p>Sistem Manajemen Perpustakaan</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Nama Pengguna</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan nama pengguna" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="Masukkan kata sandi" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </form>
            
            <div class="auth-footer">
                <p>Belum punya akun? <a href="register.php" style="color: hsl(var(--primary)); text-decoration: none;">Daftar</a></p>
                <div style="margin-top: 15px; border-top: 1px solid var(--glass-border); padding-top: 15px;">
                    <a href="admin_login.php" style="font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Masuk sebagai Admin</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
