<?php
require_once 'config/config.php';

// Password yang diinginkan user sepertinya 'admin123' berdasarkan teks di DB
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

echo "<h1>Perbaikan Akun Admin</h1>";

try {
    // 1. Cari user dengan nama 'Admin' atau 'admin'
    $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE nama_pengguna = 'Admin' OR nama_pengguna = 'admin'");
    $stmt->execute();
    $users = $stmt->fetchAll();

    if ($users) {
        foreach ($users as $user) {
            echo "<p>Ditemukan user ID: <strong>" . $user['id'] . "</strong>, Username: <strong>" . $user['nama_pengguna'] . "</strong></p>";
            
            // Update passwordnya ke hash yang benar
            $update = $pdo->prepare("UPDATE pengguna SET kata_sandi = ? WHERE id = ?");
            $update->execute([$hashed_password, $user['id']]);
            
            echo "<p style='color: green;'>-> Password berhasil diperbaiki (di-hash)!</p>";
        }
    } else {
        echo "<p style='color: red;'>Tidak ditemukan user 'Admin' atau 'admin'.</p>";
        // Buat baru jika tidak ada
        $insert = $pdo->prepare("INSERT INTO pengguna (nama_pengguna, kata_sandi, nama_lengkap, peran) VALUES ('Admin', ?, 'Administrator', 'admin')");
        $insert->execute([$hashed_password]);
        echo "<p style='color: blue;'>User 'Admin' baru telah dibuat.</p>";
    }

    echo "<h3>Selesai! Silakan coba login sekarang.</h3>";
    echo "<ul>";
    echo "<li>Username: <strong>Admin</strong> (atau admin)</li>";
    echo "<li>Password: <strong>$new_password</strong></li>";
    echo "</ul>";
    echo "<a href='auth/admin_login.php'>Ke Halaman Login Admin</a>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
