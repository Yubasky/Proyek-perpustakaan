<?php
require_once 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$error = '';

// Pre-select book if passed from book list
$selected_book_id = $_GET['book_id'] ?? null;

// Fetch Users (Admins only need list of all users, but code simplifies if we allow self-borrow for members)
// If Member: Only show themselves or auto-set.
$users = [];
if ($_SESSION['role'] === 'admin') {
    $users = $pdo->query("SELECT * FROM pengguna ORDER BY nama_lengkap")->fetchAll();
} else {
    // Member borrowing for themselves
    $users = [['id' => $_SESSION['user_id'], 'nama_lengkap' => $_SESSION['full_name'], 'nama_pengguna' => $_SESSION['username']]];
}

// Fetch Books (Available stock only)
$books = $pdo->query("SELECT * FROM buku WHERE stok > 0 ORDER BY judul")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $book_id = $_POST['book_id'];
    $loan_date = date('Y-m-d');

    // Security check: Member cannot borrow for others
    if ($_SESSION['role'] !== 'admin' && $user_id != $_SESSION['user_id']) {
        $error = "Anda tidak dapat meminjam untuk orang lain.";
    } elseif (empty($user_id) || empty($book_id)) {
        $error = "Harap pilih peminjam dan buku.";
    } else {
        // Decrease Stock
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO peminjaman (pengguna_id, buku_id, tanggal_pinjam, status) VALUES (?, ?, ?, 'dipinjam')");
            $stmt->execute([$user_id, $book_id, $loan_date]);

            $stmt = $pdo->prepare("UPDATE buku SET stok = stok - 1 WHERE id = ?");
            $stmt->execute([$book_id]);

            $pdo->commit();
            header("Location: loans.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Gagal memproses peminjaman: " . $e->getMessage();
        }
    }
}

$page = 'loans';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Buku - Simbad</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-grid">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                 <h1 class="page-title">Form Peminjaman</h1>
                <a href="<?php echo ($_SESSION['role'] === 'admin') ? 'loans.php' : 'books.php'; ?>" class="btn" style="background: white; border: 1px solid var(--glass-border); color: var(--text-main);">Kembali</a>
            </div>

             <div class="auth-card" style="max-width: 600px; margin: 0 auto; width: 100%;">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Nama Peminjam</label>
                        <select name="user_id" style="width: 100%; padding: 12px; background: white; border: 1px solid var(--glass-border); color: var(--text-main); border-radius: 8px;" required <?php echo ($_SESSION['role'] !== 'admin') ? 'readonly' : ''; ?>>
                            <?php foreach($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo ($user['id'] == $_SESSION['user_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['nama_lengkap']); ?> (<?php echo htmlspecialchars($user['nama_pengguna']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Judul Buku</label>
                         <select name="book_id" style="width: 100%; padding: 12px; background: white; border: 1px solid var(--glass-border); color: var(--text-main); border-radius: 8px;" required>
                            <option value="">Pilih Buku</option>
                            <?php foreach($books as $book): ?>
                                <option value="<?php echo $book['id']; ?>" <?php echo ($selected_book_id == $book['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($book['judul']); ?> (Stok: <?php echo $book['stok']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Pinjam</label>
                        <input type="text" value="<?php echo date('d M Y'); ?>" readonly disabled style="color: var(--text-muted);">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Konfirmasi Peminjaman</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
