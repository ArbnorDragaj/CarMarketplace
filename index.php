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

?>

