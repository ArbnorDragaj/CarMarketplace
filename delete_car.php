<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config.php";

function redirectToAdmin() {
    header("Location: admin.php");
    exit();
}

function redirectWithAdminDeleteError($message) {
    $_SESSION['admin_error'] = $message;
    redirectToAdmin();
}

function deleteCarUpload($imagePath) {
    $imagePath = trim((string)$imagePath);

    if (strpos($imagePath, 'uploads/cars/') !== 0) {
        return;
    }

    $uploadsRoot = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cars');
    $fullPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagePath));

    if ($uploadsRoot && $fullPath && strpos($fullPath, $uploadsRoot) === 0 && is_file($fullPath)) {
        unlink($fullPath);
    }
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== "admin") {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectToAdmin();
}

$carId = filter_var($_POST['car_id'] ?? null, FILTER_VALIDATE_INT);

if (!$carId) {
    redirectWithAdminDeleteError("Vetura e zgjedhur nuk eshte valide.");
}

try {
    $stmt = $pdo->prepare("SELECT image FROM cars WHERE id = ? LIMIT 1");
    $stmt->execute([$carId]);
    $car = $stmt->fetch();

    if (!$car) {
        redirectWithAdminDeleteError("Vetura nuk u gjet.");
    }

    $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->execute([$carId]);

    deleteCarUpload($car['image'] ?? '');

    header("Location: admin.php?success=deleted");
    exit();
} catch (PDOException $e) {
    redirectWithAdminDeleteError("Vetura nuk mund te fshihet.");
}
?>
