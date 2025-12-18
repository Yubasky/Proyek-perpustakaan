<aside class="sidebar">
    <div class="sidebar-brand">
        <span>📚</span> Simbad
    </div>
    
    <ul class="nav-links">
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>index.php" class="nav-link <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
                <span>🏠</span> Beranda
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>books.php" class="nav-link <?php echo ($page == 'books') ? 'active' : ''; ?>">
                <span>📖</span> Daftar Buku
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo BASE_URL; ?>loans.php" class="nav-link <?php echo ($page == 'loans') ? 'active' : ''; ?>">
                <span>🔄</span> Peminjaman
            </a>
        </li>
    </ul>

    <div class="user-info" style="margin-top: auto;">
        <span style="color: var(--text-muted); font-size: 0.9rem;">
            Masuk sebagai <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        </span>
    </div>
</aside>
