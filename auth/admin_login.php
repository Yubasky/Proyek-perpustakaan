<?php
require_once '../config/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // Admin pass is plaintext now

    if (empty($username) || empty($password)) {
        $error = "Harap isi semua kolom.";
    } else {
        // Query ke tabel 'admin'
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE nama_pengguna = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Plain text comparison check
        if ($user && $user['kata_sandi'] === $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['nama_pengguna'];
            $_SESSION['role'] = 'admin'; // Force role admin
            $_SESSION['full_name'] = $user['nama_lengkap'];
            
            header("Location: " . BASE_URL . "index.php");
            exit;
        } else {
             $error = "Username atau password salah.";
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
<body class="auth-body" style="background: linear-gradient(135deg, #0f172a, #1e3a8a);"> <!-- Dark Blue Background -->
    
    <div class="auth-container">
        <!-- Darker Card Style -->
        <div class="auth-card" style="background: rgba(15, 23, 42, 0.9); border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <div class="auth-header">
                <h1 style="color: #60a5fa;">Admin Portal</h1>
                <p style="color: #94a3b8;">Masuk untuk mengelola perpustakaan</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error" style="background: rgba(220, 38, 38, 0.2); border-color: rgba(220, 38, 38, 0.3); color: #fca5a5;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" style="color: #cbd5e1;">Username Admin</label>
                    <input type="text" id="username" name="username" placeholder="admin" required 
                           style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: white;">
                </div>
                
                <div class="form-group">
                    <label for="password" style="color: #cbd5e1;">Password</label>
                    <input type="password" id="password" name="password" placeholder="admin123" required 
                           style="background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.1); color: white;">
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="background: #2563eb; color: white;">Masuk Dashboard</button>
            </form>
            
            <div class="auth-footer">
                <p style="color: #64748b;">Bukan admin? <a href="login.php" style="color: #60a5fa; text-decoration: none;">Login Anggota</a></p>
            </div>
        </div>
    </div>

</body>
</html>
