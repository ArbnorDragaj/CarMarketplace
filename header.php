<?php session_start(); ?>

<header class="main-header">
    <div class="container">
        <div class="logo">
            <h1><a href="index.php"><?php echo $site_name; ?></a></h1>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Ballina</a></li>
                <li><a href="rreth-nesh.php">Rreth Nesh</a></li>
                <li><a href="sherbimet.php">Shërbimet</a></li>
                <li><a href="kontakti.php">Kontakti</a></li>
                <li><a href="blog.php" class="blog">Blog</a></li>

                     <?php if (isset($_SESSION['user'])): ?>
                    <li class="user-info">
                        👤 <?php echo htmlspecialchars($_SESSION['user']); ?>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>