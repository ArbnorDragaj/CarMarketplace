<?php
// config.php
// Lidhja me databazën MySQL për CarMarketplace

$host = "localhost";
$dbname = "carmarketplace";
$dbUser = "root";
$dbPass = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    // Mos e shfaq gabimin teknik te përdoruesi
    die("Gabim në lidhjen me databazën.");
}

// Funksion ndihmës për mbrojtje nga XSS kur shfaqen të dhëna në HTML
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
