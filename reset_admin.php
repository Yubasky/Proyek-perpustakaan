<?php
require_once 'config/config.php';

$new_password = 'password123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Cek apakah admin ada
    $stmt = $pdo->prepare("SELECT id FROM pengguna WHERE nama_pengguna = 'admin'");
    $stmt->execute();
    if ($stmt->fetch()) {
        // Update
        $update = $pdo->prepare("UPDATE pengguna SET kata_sandi = ? WHERE nama_pengguna = 'admin'");
        $update->execute([$hashed_password]);
        echo "<h1>Sukses!</h1>";
        echo "<p>Password untuk user <strong>admin</strong> telah di-reset menjadi: <strong>$new_password</strong></p>";
        echo "<p>Hash baru: $hashed_password</p>";
        echo "<a href='auth/login.php'>Login Sekarang</a>";
    } else {
        // Create if not exists
        $insert = $pdo->prepare("INSERT INTO pengguna (nama_pengguna, kata_sandi, nama_lengkap, peran) VALUES ('admin', ?, 'Administrator', 'admin')");
        $insert->execute([$hashed_password]);
        echo "<h1>Sukses Dibuat!</h1>";
        echo "<p>User <strong>admin</strong> tidak ditemukan, jadi saya membuatnya baru.</p>";
        echo "<p>Password: <strong>$new_password</strong></p>";
        echo "<a href='auth/login.php'>Login Sekarang</a>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
