<?php
require_once 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// Return Book Logic (Admin Only)
if (isset($_GET['return'])) {
    if ($_SESSION['role'] !== 'admin') {
         die("Akses ditolak.");
    }
    $loan_id = $_GET['return'];
    $book_id = $_GET['book_id'];

    // Update Loan Status
    $stmt = $pdo->prepare("UPDATE peminjaman SET status = 'dikembalikan', tanggal_kembali = NOW() WHERE id = ?");
    $stmt->execute([$loan_id]);

    // Restore Stock
    $stmt = $pdo->prepare("UPDATE buku SET stok = stok + 1 WHERE id = ?");
    $stmt->execute([$book_id]);

    header("Location: loans.php");
    exit;
}

$page = 'loans';

// Fetch Loans Logic (RBAC)
// Table: peminjaman, pengguna, buku
$query = "SELECT peminjaman.*, pengguna.nama_lengkap, buku.judul as book_title, buku.gambar_sampul 
          FROM peminjaman 
          JOIN pengguna ON peminjaman.pengguna_id = pengguna.id 
          JOIN buku ON peminjaman.buku_id = buku.id";

if ($_SESSION['role'] !== 'admin') {
    // Member only sees their own
    $query .= " WHERE peminjaman.pengguna_id = " . $_SESSION['user_id'];
    $query .= " ORDER BY peminjaman.tanggal_pinjam DESC";
} else {
    // Admin sees all
    $query .= " ORDER BY peminjaman.status ASC, peminjaman.tanggal_pinjam DESC";
}

$loans = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman - Simbad</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-grid">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <h1 class="page-title">Data Peminjaman</h1>
                <div class="user-info">
                   <?php if($_SESSION['role'] === 'admin'): ?>
                       <a href="loan_form.php" class="btn btn-primary">+ Peminjaman Baru</a>
                   <?php endif; ?>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <?php if($_SESSION['role'] === 'admin'): ?>
                            <th>Peminjam</th>
                            <?php endif; ?>
                            <th>Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <?php if($_SESSION['role'] === 'admin'): ?>
                            <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                         <?php if (empty($loans)): ?>
                            <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">Tidak ada riwayat peminjaman.</td></tr>
                        <?php else: ?>
                            <?php foreach ($loans as $loan): ?>
                            <tr>
                                <?php if($_SESSION['role'] === 'admin'): ?>
                                <td><?php echo htmlspecialchars($loan['nama_lengkap']); ?></td>
                                <?php endif; ?>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <?php if($loan['gambar_sampul']): ?>
                                            <img src="uploads/<?php echo htmlspecialchars($loan['gambar_sampul']); ?>" style="width: 30px; height: 45px; object-fit: cover; border-radius: 4px;">
                                        <?php else: ?>
                                             <div style="width: 30px; height: 45px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">📚</div>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($loan['book_title']); ?>
                                    </div>
                                </td>
                                <td><?php echo date('d M Y', strtotime($loan['tanggal_pinjam'])); ?></td>
                                <td>
                                    <?php 
                                        if ($loan['tanggal_kembali']) {
                                            echo date('d M Y', strtotime($loan['tanggal_kembali']));
                                        } else {
                                            echo '-';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($loan['status'] == 'dipinjam'): ?>
                                        <span class="badge badge-warning">Dipinjam</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Dikembalikan</span>
                                    <?php endif; ?>
                                </td>
                                <?php if($_SESSION['role'] === 'admin'): ?>
                                <td>
                                    <?php if ($loan['status'] == 'dipinjam'): ?>
                                        <a href="?return=<?php echo $loan['id']; ?>&book_id=<?php echo $loan['buku_id']; ?>" 
                                           class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: hsl(var(--success)); color: white;"
                                           onclick="return confirm('Konfirmasi pengembalian?')">
                                           Kembalikan
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted); font-size: 0.8rem;">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
