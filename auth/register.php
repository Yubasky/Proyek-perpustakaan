<?php
require_once '../config/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($full_name) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Harap isi semua kolom.";
    } elseif ($password !== $confirm_password) {
        $error = "Kata sandi tidak cocok.";
    } else {
        // Check if username exists in pengguna
        $stmt = $pdo->prepare("SELECT id FROM pengguna WHERE nama_pengguna = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Nama pengguna sudah digunakan.";
        } else {
            // Register User (Member always hashed)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Note: 'role' column removed from schema for 'pengguna' table in this version based on your separation request,
            // OR if it exists, default is fine. But 'database.sql' above removed 'peran' from 'pengguna' inserts?
            // Checking database.sql: Table `pengguna` structure: `id`, `nama_pengguna`, `kata_sandi`, `nama_lengkap`, (`dibuat_pada`). 
            // Wait, I removed `peran` from `pengguna` table in the new `database.sql` logic! 
            // Let's explicitly insert into strictly defined columns.

            $stmt = $pdo->prepare("INSERT INTO pengguna (nama_pengguna, kata_sandi, nama_lengkap) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $full_name])) {
                $success = "Pendaftaran berhasil! Anda sekarang dapat masuk.";
            } else {
                $error = "Pendaftaran gagal. Silakan coba lagi.";
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
    <title>Daftar - Perpustakaan Simbad</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body">
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Daftar</h1>
                <p>Buat akun anggota baru</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert" style="background: rgba(0, 200, 100, 0.2); color: hsl(var(--success)); border: 1px solid rgba(0, 200, 100, 0.3);">
                    <?php echo htmlspecialchars($success); ?> <a href="login.php" style="color: inherit; font-weight: bold;">Masuk di sini</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Nama Lengkap" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="username">Nama Pengguna</label>
                    <input type="text" id="username" name="username" placeholder="Pilih nama pengguna" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" placeholder="Buat kata sandi" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Kata Sandi</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi kata sandi" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            </form>
            
            <div class="auth-footer">
                <p>Sudah punya akun? <a href="login.php" style="color: hsl(var(--primary)); text-decoration: none;">Masuk</a></p>
            </div>
        </div>
    </div>

</body>
</html>
