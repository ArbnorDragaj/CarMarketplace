<?php 
    $site_name = "CarMarketPlace";
    include 'Includes/header.php'; // Përdor header-in që ke te fotoja
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'CarMarketPlace'; ?></title>
    <link rel="stylesheet" href="Style/rreth-nesh.css">
    <link rel="stylesheet" href="Style/style.css">

</head>
<body>


<div class="about-container">
    <section class="about-hero">
        <div class="hero-content">
            <h1>Mbi <span class="text-red">CarMarketPlace</span></h1>
            <p>Destinacioni juaj i parë për makina cilësore dhe besueshmëri maksimale.</p>
        </div>
    </section>

    <section class="stats-grid">
        <div class="stat-card">
            <h2 class="text-red">500+</h2>
            <p>Makina të Shitura</p>
        </div>
        <div class="stat-card">
            <h2 class="text-red">10+</h2>
            <p>Vite Përvojë</p>
        </div>
        <div class="stat-card">
            <h2 class="text-red">2500</h2>
            <p>Klientë të Lumtur</p>
        </div>
    </section>

    <section class="about-info">
        <div class="info-box">
            <h3>Misioni Ynë</h3>
            <p>Të ofrojmë transparencë të plotë në tregun e makinave, duke siguruar që çdo blerës të gjejë mjetin perfekt pa dyshime në cilësi.</p>
        </div>
        <div class="info-box">
            <h3>Vizioni Ynë</h3>
            <p>Të bëhemi platforma lider në rajon për shitblerjen e makinave përmes inovacionit dhe shërbimit të shkëlqyer ndaj klientit.</p>
        </div>
    </section>
</div>

<?php include 'Includes/footer.php'; ?>