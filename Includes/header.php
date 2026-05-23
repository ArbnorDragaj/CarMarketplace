<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($site_name)) {
    $site_name = "CarMarketPlace";
}
?>

<header class="main-header">
    <div class="container header-container">

        <div class="logo">
            <h1>
                <a href="index.php"><?php echo htmlspecialchars($site_name); ?></a>
            </h1>
        </div>

        <nav class="navbar">
            <ul class="nav-links">
                <li><a href="index.php">Ballina</a></li>
                <li><a href="rreth-nesh.php">Rreth Nesh</a></li>
                <li><a href="models.php">Shërbimet</a></li>
                <li><a href="contact.php">Kontakti</a></li>
                <li><a href="blog.php" class="blog-link">Blog</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <li>
        <a href="admin.php" class="<?php echo ($currentPage == 'admin.php') ? 'active' : ''; ?>">
            Admin
        </a>
    </li>
<?php endif; ?>
            </ul>
        </nav>

        <div class="user-actions">
            <?php if (isset($_SESSION['user'])): ?>
                <div class="user-box">
                    <span class="user-name">👤 <?php echo htmlspecialchars($_SESSION['user']); ?></span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="login-btn">Kyçu</a>
            <?php endif; ?>

        </div>

    </div>
</header>