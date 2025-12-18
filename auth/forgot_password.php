<?php
require_once '../config/config.php';

$step = 1;
$error = '';
$success = '';
$user_id = null;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // STEP 1: Verify Identity
    if (isset($_POST['verify_identity'])) {
        $username = trim($_POST['username']);
        $fullname = trim($_POST['fullname']);

        if (empty($username) || empty($fullname)) {
            $error = "Harap isi Nama Pengguna dan Nama Lengkap.";
        } else {
            $stmt = $pdo->prepare("SELECT id FROM pengguna WHERE nama_pengguna = ? AND nama_lengkap = ?");
            $stmt->execute([$username, $fullname]);
            $user = $stmt->fetch();

            if ($user) {
                $step = 2; 
                $user_id = $user['id']; 
            } else {
                $error = "Data tidak ditemukan. Pastikan Nama Pengguna dan Nama Lengkap sesuai.";
            }
        }
    }

    // STEP 2: Reset Password
    if (isset($_POST['reset_password'])) {
        $user_id = $_POST['user_id'];
        $new_pass = trim($_POST['new_password']);
        $confirm_pass = trim($_POST['confirm_password']);
        $step = 2; // Stay on step 2 if error

        if (empty($new_pass) || empty($confirm_pass)) {
            $error = "Harap isi password baru.";
        } elseif ($new_pass !== $confirm_pass) {
            $error = "Password tidak cocok.";
        } else {
            // Update Password
            $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE pengguna SET kata_sandi = ? WHERE id = ?");
            if ($stmt->execute([$hashed, $user_id])) {
                $success = "Password berhasil diubah! Silakan login.";
                $step = 3; // Success state
            } else {
                $error = "Gagal mengubah password.";
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
    <title>Lupa Kata Sandi - Simbad</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body">
    
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Pemulihan Akun</h1>
                <p>Atur ulang kata sandi Anda</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert" style="background: rgba(0, 200, 100, 0.2); color: hsl(var(--success)); border: 1px solid rgba(0, 200, 100, 0.3);">
                    <?php echo htmlspecialchars($success); ?>
                </div>
                <a href="login.php" class="btn btn-primary btn-block" style="text-align: center; text-decoration: none;">Ke Halaman Login</a>
            <?php endif; ?>

            <?php if ($step === 1 && !$success): ?>
            <!-- STEP 1 FORM -->
            <form method="POST" action="">
                <p style="margin-bottom: 20px; font-size: 0.9rem; color: var(--text-muted); text-align: center;">
                    Masukkan Nama Pengguna dan Nama Lengkap Anda untuk verifikasi.
                </p>
                <div class="form-group">
                    <label>Nama Pengguna</label>
                    <input type="text" name="username" required placeholder="Contoh: budi">
                </div>
                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="fullname" required placeholder="Contoh: Budi Santoso">
                </div>
                <button type="submit" name="verify_identity" class="btn btn-primary btn-block">Verifikasi Akun</button>
            </form>
            <?php endif; ?>

            <?php if ($step === 2 && !$success): ?>
            <!-- STEP 2 FORM -->
            <form method="POST" action="">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                <p style="margin-bottom: 20px; font-size: 0.9rem; color: hsl(var(--success)); text-align: center; font-weight: bold;">
                    Akun ditemukan! Silakan buat password baru.
                </p>
                <div class="form-group">
                    <label>Kata Sandi Baru</label>
                    <input type="password" name="new_password" required placeholder="Password baru">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Kata Sandi</label>
                    <input type="password" name="confirm_password" required placeholder="Ulangi password">
                </div>
                <button type="submit" name="reset_password" class="btn btn-primary btn-block">Simpan Password</button>
            </form>
            <?php endif; ?>
            
            <?php if (!$success): ?>
            <div class="auth-footer">
                <a href="login.php" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;">&larr; Kembali ke Login</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
