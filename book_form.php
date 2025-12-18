<?php
require_once 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

// RBAC Check: Hanya Admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: books.php");
    exit;
}

$id = $_GET['id'] ?? null;
$book = null;
$error = '';

// Fetch Categories (kategori)
$categories = $pdo->query("SELECT * FROM kategori")->fetchAll();

// Edit Mode - Fetch Book (buku)
if ($id) {
    // Column names updated: cover_image -> gambar_sampul, category_id -> kategori_id
    $stmt = $pdo->prepare("SELECT * FROM buku WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category_id = $_POST['category_id'];
    $stock = (int)$_POST['stock'];
    $publisher = trim($_POST['publisher']);
    $year = trim($_POST['year']);
    
    // File Upload
    $cover_image = $book['gambar_sampul'] ?? null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_name = uniqid() . "." . $ext;
            $upload_dir = 'uploads/'; // Adjusted path
            if (!is_dir($upload_dir)) mkdir($upload_dir);
            move_uploaded_file($_FILES['cover_image']['tmp_name'], $upload_dir . $new_name);
            $cover_image = $new_name;
        } else {
            $error = "Tipe file tidak valid.";
        }
    }

    if (!$error) {
        if ($id) {
            // Update
            $sql = "UPDATE buku SET judul=?, penulis=?, kategori_id=?, stok=?, penerbit=?, tahun_terbit=?, gambar_sampul=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $author, $category_id, $stock, $publisher, $year, $cover_image, $id]);
        } else {
            // Insert
            $sql = "INSERT INTO buku (judul, penulis, kategori_id, stok, penerbit, tahun_terbit, gambar_sampul) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $author, $category_id, $stock, $publisher, $year, $cover_image]);
        }
        header("Location: books.php");
        exit;
    }
}

$page = 'books';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Tambah'; ?> Buku - Simbad</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard-grid">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <div class="top-bar">
                <h1 class="page-title"><?php echo $id ? 'Edit' : 'Tambah'; ?> Buku</h1>
                <a href="books.php" class="btn" style="background: white; border: 1px solid var(--glass-border); color: var(--text-main);">Kembali</a>
            </div>

            <div class="auth-card" style="max-width: 800px; margin: 0 auto; width: 100%;">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Judul Buku</label>
                            <input type="text" name="title" value="<?php echo htmlspecialchars($book['judul'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Penulis</label>
                            <input type="text" name="author" value="<?php echo htmlspecialchars($book['penulis'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="category_id" style="width: 100%; padding: 12px; background: white; border: 1px solid var(--glass-border); color: var(--text-main); border-radius: 8px;">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($book && $book['kategori_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Stok</label>
                            <input type="number" name="stock" value="<?php echo htmlspecialchars($book['stok'] ?? '0'); ?>" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                         <div class="form-group">
                            <label>Penerbit</label>
                            <input type="text" name="publisher" value="<?php echo htmlspecialchars($book['penerbit'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                             <label>Tahun Terbit</label>
                            <input type="number" name="year" value="<?php echo htmlspecialchars($book['tahun_terbit'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Gambar Sampul</label>
                        <input type="file" name="cover_image">
                        <?php if(isset($book['gambar_sampul']) && $book['gambar_sampul']): ?>
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">Saat ini: <?php echo htmlspecialchars($book['gambar_sampul']); ?></p>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <?php echo $id ? 'Simpan Perubahan' : 'Simpan Buku'; ?>
                    </button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
