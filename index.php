<?php
session_start();
include 'includes/header.php';
include 'includes/navbar.php';
require_once 'classes/Car.php';

// Për shembull, lista e makinave në Ballina
$cars = [
    new Car("BMW M5", 2022, 55000),
    new Car("Audi A6", 2021, 40000),
    new Car("Mercedes C-Class", 2020, 35000)
];

// Lexo cookie për ngjyrë personalizimi
$themeColor = isset($_COOKIE['themeColor']) ? $_COOKIE['themeColor'] : 'blue';
?>

<section class="home-intro" style="color:<?= $themeColor ?>">
    <h1>Mirësevini në AutoShqip</h1>
    <p>Shiko veturat më të fundit dhe lajmet më interesante!</p>
    <p>Shiko veturat më të fundit dhe lajmet më interesante!</p>
</section>
<section class="cars-preview">
    <h2>Veturat kryesore</h2>
    <div class="car-container">
        <?php foreach($cars as $car): ?>
        <div class="car-card" style="background-image: url('../assets/images/<?= strtolower(str_replace(' ','',$car->getName())) ?>.jpg');">
            <div class="overlay">
                <h3><?= $car->getName() ?></h3>
                <p>Viti: <?= $car->getYear() ?></p>
                <p>Çmimi: <?= $car->getPrice() ?>€</p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

?>

