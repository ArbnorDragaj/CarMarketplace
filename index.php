<?php
$site_name = "CarMarketPlace";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'CarMarketPlace'; ?></title>
    <link rel="stylesheet" href="Style/style.css?v=3">
    <link rel="stylesheet" href="Style/index.css?v=2">
</head>
<body>
    <?php include 'Includes/header.php'; ?>

    <main class="home-page">
        <section class="hero-section">
            <div class="hero-content">
                <span class="hero-label">Premium car marketplace</span>
                <h2>Find your <strong>Perfect</strong> car</h2>
                <p>Quality, guarantee, <strong>stability</strong> with us</p>

                <div class="hero-actions">
                    <a class="btn" href="models.php">Eksploro</a>
                    <a class="ghost-link" href="contact.php">Kerko keshille</a>
                </div>
            </div>
        </section>

        <section class="client-panel">
            <div class="client-stat">
                <span>12+</span>
                <p>Vetura aktive</p>
            </div>
            <div class="client-stat">
                <span>4</span>
                <p>Brende premium</p>
            </div>
            <div class="client-stat">
                <span>24h</span>
                <p>Kontakt i shpejte</p>
            </div>
        </section>

        <section class="client-benefits">
            <article>
                <span>01</span>
                <h3>Zgjedhje e qarte</h3>
                <p>Shiko veturat sipas brendit, tipit dhe karburantit.</p>
            </article>
            <article>
                <span>02</span>
                <h3>Cmime transparente</h3>
                <p>Krahaso modelet pa u humbur ne informata te panevojshme.</p>
            </article>
            <article>
                <span>03</span>
                <h3>Qasje e kontrolluar</h3>
                <p>Sherbimet hapen pas login-it per pervoje me te sigurt.</p>
            </article>
        </section>
    </main>

    <?php include 'Includes/footer.php'; ?>
</body>
</html>
