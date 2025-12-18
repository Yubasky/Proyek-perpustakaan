<?php
require_once 'config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// Fetch Stats with new table names
$stats = [
    'books' => $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn(),
    'users' => $pdo->query("SELECT COUNT(*) FROM pengguna")->fetchColumn(),
    'active_loans' => $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn()
];

$page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Simbad</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-grid">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <h1 class="page-title">Beranda</h1>
                <a href="auth/logout.php" class="btn-logout">Keluar</a>
            </div>

            <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                <div class="stat-card" style="background: var(--glass-bg); padding: 25px; border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                    <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">Total Buku</h3>
                    <p style="font-size: 2.5rem; font-weight: 700; color: var(--text-main);"><?php echo $stats['books']; ?></p>
                </div>
                
                <div class="stat-card" style="background: var(--glass-bg); padding: 25px; border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                    <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">Peminjaman Aktif</h3>
                    <p style="font-size: 2.5rem; font-weight: 700; color: hsl(var(--secondary));"><?php echo $stats['active_loans']; ?></p>
                </div>

                <div class="stat-card" style="background: var(--glass-bg); padding: 25px; border-radius: var(--radius-md); border: 1px solid var(--glass-border);">
                    <h3 style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">Anggota Terdaftar</h3>
                    <p style="font-size: 2.5rem; font-weight: 700; color: hsl(var(--primary));"><?php echo $stats['users']; ?></p>
                </div>
            </div>

            <div style="margin-top: 40px;">
                <h2 style="margin-bottom: 20px; font-size: 1.2rem;">Menu Cepat</h2>
                <div style="display: flex; gap: 15px;">
                    <a href="books.php" class="btn btn-primary">Lihat Buku</a>
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <a href="loans.php" class="btn" style="background: white; border: 1px solid var(--glass-border); color: var(--text-main); box-shadow: 0 2px 5px rgba(0,0,0,0.05);">Kelola Peminjaman</a>
                    <?php else: ?>
                         <a href="loans.php" class="btn" style="background: white; border: 1px solid var(--glass-border); color: var(--text-main); box-shadow: 0 2px 5px rgba(0,0,0,0.05);">Riwayat Saya</a>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>
</body>
</html>
