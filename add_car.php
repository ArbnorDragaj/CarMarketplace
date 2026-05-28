<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config.php";

function redirectToAdmin() {
    header("Location: admin.php");
    exit();
}

function redirectWithAdminError($message) {
    $_SESSION['admin_error'] = $message;
    redirectToAdmin();
}

function uploadCarPhoto($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        redirectWithAdminError("Ju lutem ngarkoni nje foto per veturen.");
    }

    $uploadDir = __DIR__ . "/uploads/cars/";
    $publicDir = "uploads/cars/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($fileExt, $allowedExtensions, true)) {
        redirectWithAdminError("Lejohen vetem fotot JPG, JPEG, PNG ose WEBP.");
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        redirectWithAdminError("Foto eshte shume e madhe. Maksimumi 5MB.");
    }

    $newFileName = uniqid("car_", true) . "." . $fileExt;
    $destination = $uploadDir . $newFileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        redirectWithAdminError("Foto nuk mund te ruhet. Provoni perseri.");
    }

    return $publicDir . $newFileName;
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== "admin") {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectToAdmin();
}

$brand = trim($_POST['brand'] ?? '');
$model = trim($_POST['model'] ?? '');
$fuel = trim($_POST['fuel'] ?? '');
$bodyType = trim($_POST['type'] ?? '');
$year = trim($_POST['year'] ?? '');
$price = trim($_POST['price'] ?? '');

if ($brand === '' || $model === '' || $fuel === '' || $bodyType === '' || $year === '' || $price === '') {
    redirectWithAdminError("Ju lutem plotesoni te gjitha fushat.");
}

if (!filter_var($year, FILTER_VALIDATE_INT) || (int)$year < 1900 || (int)$year > ((int)date('Y') + 1)) {
    redirectWithAdminError("Viti i vetures nuk eshte valid.");
}

if (!is_numeric($price) || (float)$price <= 0) {
    redirectWithAdminError("Cmimi duhet te jete numer pozitiv.");
}

$imagePath = uploadCarPhoto($_FILES['car_image'] ?? null);

try {
    $stmt = $pdo->prepare("
        INSERT INTO cars (user_id, brand, model, year, price, fuel, body_type, image, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')
    ");

    $stmt->execute([
        $_SESSION['user_id'],
        $brand,
        $model,
        (int)$year,
        (float)$price,
        $fuel,
        $bodyType,
        $imagePath,
    ]);

    header("Location: admin.php?success=added");
    exit();
} catch (PDOException $e) {
    if (is_file(__DIR__ . "/" . $imagePath)) {
        unlink(__DIR__ . "/" . $imagePath);
    }

    redirectWithAdminError("Vetura nuk mund te ruhet ne databaze.");
}
?>
