<?php
require_once 'config/config.php';

// Cek Auth
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// Logika Hapus (Hanya Admin)
if (isset($_GET['delete'])) {
    if ($_SESSION['role'] !== 'admin') {
        die("Akses ditolak.");
    }
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM buku WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: books.php");
    exit;
}

// Logika Pinjam (Member) - Sederhana: Klik pinjam langsung proses/redirect
// Untuk kesederhanaan, tombol pinjam akan mengarah ke loan_form.php dengan parameter buku_id

$search = $_GET['search'] ?? '';
$page_num = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page_num - 1) * $limit;

// Query dengan nama tabel baru (buku, kategori)
$query = "SELECT buku.*, kategori.nama_kategori as category_name FROM buku 
          LEFT JOIN kategori ON buku.kategori_id = kategori.id 
          WHERE judul LIKE ? OR penulis LIKE ? 
          ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($query);
$stmt->bindValue(1, "%$search%", PDO::PARAM_STR);
$stmt->bindValue(2, "%$search%", PDO::PARAM_STR);
$stmt->bindValue(3, $limit, PDO::PARAM_INT);
$stmt->bindValue(4, $offset, PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll();

// Hitung total untuk pagination
$count_query = "SELECT COUNT(*) FROM buku WHERE judul LIKE ? OR penulis LIKE ?";
$stmt_count = $pdo->prepare($count_query);
$stmt_count->execute(["%$search%", "%$search%"]);
$total_books = $stmt_count->fetchColumn();
$total_pages = ceil($total_books / $limit);

$page = 'books';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku - Simbad</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard-grid">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <h1 class="page-title">Daftar Buku</h1>
                
                <?php if($_SESSION['role'] === 'admin'): ?>
                <div class="user-info">
                   <a href="book_form.php" class="btn btn-primary">+ Tambah Buku</a>
                </div>
                <?php endif; ?>
            </div>

            <div class="table-container">
                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <form method="GET" style="display: flex; gap: 10px;">
                        <input type="text" name="search" placeholder="Cari buku..." value="<?php echo htmlspecialchars($search); ?>" 
                               style="padding: 10px; border-radius: 8px; border: 1px solid var(--glass-border); background: white; color: var(--text-main); width: 300px;">
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Sampul</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($books)): ?>
                            <tr><td colspan="6" style="text-align: center; color: var(--text-muted);">Tidak ada buku ditemukan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($books as $book): ?>
                            <tr>
                                <td>
                                    <?php if($book['gambar_sampul']): ?>
                                        <div style="width: 50px; height: 75px; background-image: url('uploads/<?php echo htmlspecialchars($book['gambar_sampul']); ?>'); background-size: cover; border-radius: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);"></div>
                                    <?php else: ?>
                                        <div style="width: 50px; height: 75px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #999;">📚</div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($book['judul']); ?></strong></td>
                                <td><?php echo htmlspecialchars($book['penulis']); ?></td>
                                <td><span class="badge badge-success"><?php echo htmlspecialchars($book['category_name']); ?></span></td>
                                <td><?php echo $book['stok']; ?></td>
                                <td>
                                    <?php if($_SESSION['role'] === 'admin'): ?>
                                        <!-- Opsi Admin -->
                                        <a href="book_form.php?id=<?php echo $book['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: #eee; color: #333;">Edit</a>
                                        <a href="?delete=<?php echo $book['id']; ?>" class="btn" style="padding: 5px 10px; font-size: 0.8rem; background: rgba(255, 80, 80, 0.1); color: var(--error);" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                    <?php else: ?>
                                        <!-- Opsi Member -->
                                        <?php if($book['stok'] > 0): ?>
                                            <a href="loan_form.php?book_id=<?php echo $book['id']; ?>" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.8rem;">Pinjam</a>
                                        <?php else: ?>
                                            <span class="badge badge-warning" style="font-size: 0.8rem;">Stok Habis</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div style="margin-top: 20px; display: flex; gap: 5px; justify-content: center;">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                           class="btn" 
                           style="background: <?php echo ($i == $page_num) ? 'hsl(var(--primary))' : 'white'; ?>; color: <?php echo ($i == $page_num) ? 'white' : 'var(--text-main)'; ?>; border: 1px solid var(--glass-border);">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
