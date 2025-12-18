<?php
require_once 'config/config.php';

echo "<h1>Debug Login Simbad</h1>";

// 1. Cek Koneksi
echo "<h2>1. Cek Koneksi Database</h2>";
if ($pdo) {
    echo "<p style='color: green;'>Koneksi Berhasil.</p>";
} else {
    echo "<p style='color: red;'>Koneksi Gagal.</p>";
    exit;
}

// 2. Cek Tabel
echo "<h2>2. Cek Tabel 'pengguna'</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM pengguna");
    $count = $stmt->fetchColumn();
    echo "<p style='color: green;'>Tabel 'pengguna' ditemukan. Total baris: $count</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p>Kemungkinan Anda belum mengimport database.sql yang baru.</p>";
}

// 3. Cek User 'admin'
echo "<h2>3. Cek User 'admin'</h2>";
$stmt = $pdo->prepare("SELECT * FROM pengguna WHERE nama_pengguna = ?");
$stmt->execute(['admin']);
$user = $stmt->fetch();

if ($user) {
    echo "<p style='color: green;'>User 'admin' ditemukan.</p>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
    // Tes Password
    $test_pass = 'password123';
    if (password_verify($test_pass, $user['kata_sandi'])) {
         echo "<p style='color: green;'>Password 'password123' VALID.</p>";
    } else {
         echo "<p style='color: red;'>Password 'password123' TIDAK COCOK dengan hash di database.</p>";
    }

} else {
    echo "<p style='color: red;'>User 'admin' TIDAK ditemukan.</p>";
}
?>
