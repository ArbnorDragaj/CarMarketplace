<?php
$site_name = "CarMarketPlace";
$page_title = "Rreth Nesh - CarMarketPlace";

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$stats = [
    ['value' => '500+', 'label' => 'Makina te shitura'],
    ['value' => '10+', 'label' => 'Vite pervoje'],
    ['value' => '2500+', 'label' => 'Kliente te kenaqur']
];

$features = [
    [
        'title' => 'Login i sigurt',
        'text' => 'Llogarite ruhen ne databaze me password te hash-uar dhe role te ndara per user/admin.'
    ],
    [
        'title' => 'CRUD veturash',
        'text' => 'Admini mund te shtoje, editoje, aktivizoje, caktivizoje dhe fshije vetura nga paneli.'
    ],
    [
        'title' => 'AJAX pa refresh',
        'text' => 'Statusi dhe fshirja e veturave ne admin panel punojne pa rifreskim te faqes.'
    ]
];
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?></title>
    <link rel="stylesheet" href="Style/style.css?v=3">
    <link rel="stylesheet" href="Style/rreth-nesh.css">
</head>
<body>

<?php include 'Includes/header.php'; ?>

<main class="about-container">
    <section class="about-hero">
        <div class="hero-content">
            <span class="about-label">Rreth platformes</span>
            <h1>Mbi <span class="text-red">CarMarketPlace</span></h1>
            <p>Destinacion per makina cilesore, informata te qarta dhe menaxhim te sigurt te veturave.</p>
        </div>
    </section>

    <section class="stats-grid">
        <?php foreach ($stats as $stat): ?>
            <article class="stat-card">
                <h2 class="text-red"><?php echo e($stat['value']); ?></h2>
                <p><?php echo e($stat['label']); ?></p>
            </article>
        <?php endforeach; ?>
    </section>

    <section class="about-info">
        <article class="info-box">
            <h3>Misioni yne</h3>
            <p>Te ofrojme transparence ne tregun e makinave, duke siguruar qe cdo bleres te gjeje veturen e duhur me me pak paqartesi.</p>
        </article>

        <article class="info-box">
            <h3>Vizioni yne</h3>
            <p>Te ndertojme nje platforme te thjeshte dhe funksionale ku klientet shohin veturat, ndersa admini i menaxhon ato me siguri.</p>
        </article>
    </section>

    <section class="features-section">
        <div class="section-heading">
            <span class="about-label">Funksionalitetet e reja</span>
            <h2>Cfare ofron sistemi</h2>
        </div>

        <div class="features-grid">
            <?php foreach ($features as $feature): ?>
                <article class="feature-card">
                    <h3><?php echo e($feature['title']); ?></h3>
                    <p><?php echo e($feature['text']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php include 'Includes/footer.php'; ?>

</body>
</html>
