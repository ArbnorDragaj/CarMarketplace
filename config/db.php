<?php
// config/db.php
// Lidhja me databazen MySQL duke perdorur PDO.
// Provohet fillimisht porti 3306, pastaj 3307.

$host = "127.0.0.1";
$dbname = "car_marketplace";
$username = "root";
$password = "";

$ports = [3306, 3307];

$pdo = null;

foreach ($ports as $port) {
    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );

        break;
    } catch (PDOException $e) {
        $pdo = null;
    }
}

if (!$pdo) {
    die("Database connection failed. Please check config/db.php");
}