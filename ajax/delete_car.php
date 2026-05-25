<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

function sendJsonResponse($success, $message, $data = [], $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit();
}

function getCarCounts(PDO $pdo) {
    return [
        'total' => (int)$pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn(),
        'active' => (int)$pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'active'")->fetchColumn(),
        'inactive' => (int)$pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'inactive'")->fetchColumn()
    ];
}

function deleteUploadedCarImage($imagePath) {
    $imagePath = trim((string)$imagePath);

    if (strpos($imagePath, 'uploads/cars/') !== 0) {
        return;
    }

    $projectRoot = dirname(__DIR__);
    $uploadsRoot = realpath($projectRoot . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'cars');
    $fullPath = realpath($projectRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $imagePath));

    if ($uploadsRoot && $fullPath && strpos($fullPath, $uploadsRoot) === 0 && is_file($fullPath)) {
        unlink($fullPath);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Kerkesa nuk eshte valide.', [], 405);
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    sendJsonResponse(false, 'Nuk keni qasje per kete veprim.', [], 403);
}

$carId = filter_var($_POST['car_id'] ?? null, FILTER_VALIDATE_INT);

if (!$carId) {
    sendJsonResponse(false, 'Vetura e zgjedhur nuk eshte valide.', [], 400);
}

try {
    $stmt = $pdo->prepare("SELECT image FROM cars WHERE id = ? LIMIT 1");
    $stmt->execute([$carId]);
    $car = $stmt->fetch();

    if (!$car) {
        sendJsonResponse(false, 'Vetura nuk u gjet.', [], 404);
    }

    $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->execute([$carId]);

    deleteUploadedCarImage($car['image'] ?? '');

    sendJsonResponse(true, 'Vetura u fshi me sukses.', [
        'id' => $carId,
        'counts' => getCarCounts($pdo)
    ]);
} catch (PDOException $e) {
    sendJsonResponse(false, 'Vetura nuk mund te fshihet.', [], 500);
}
?>
