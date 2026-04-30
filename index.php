<?php
// Mund të shtosh variabla këtu për të ndryshuar titullin e faqes në mënyrë dinamike
$site_name = "CarMarketPlace";


session_start();


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'CarMarketPlace'; ?></title>
    <link rel="stylesheet" href="Style/index.css">
    <link rel="stylesheet" href="Style/style.css">

</head>
<body>
    <?php include 'Includes/header.php'; ?>

<!-- <header class="main-header">
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
            </ul>
        </nav>
    </div>
</header> -->

<p>Find your <strong>Perfect</strong> car
<br>
Quality, Guarantee, <strong>Stability</strong> with Us
</p>
<button class="btn"><a href="models.php">Eksploro</a></button>
<br>
<br>
<br>
<br>
<br>
<br>
    <?php include 'Includes/footer.php'; ?>

</body>
