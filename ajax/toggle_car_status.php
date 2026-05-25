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
    $stmt = $pdo->prepare("SELECT status FROM cars WHERE id = ? LIMIT 1");
    $stmt->execute([$carId]);
    $car = $stmt->fetch();

    if (!$car) {
        sendJsonResponse(false, 'Vetura nuk u gjet.', [], 404);
    }

    $newStatus = $car['status'] === 'active' ? 'inactive' : 'active';

    $stmt = $pdo->prepare("UPDATE cars SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $carId]);

    sendJsonResponse(true, 'Statusi u ndryshua me sukses.', [
        'id' => $carId,
        'status' => $newStatus,
        'label' => $newStatus === 'active' ? 'Aktive' : 'Joaktive',
        'counts' => getCarCounts($pdo)
    ]);
} catch (PDOException $e) {
    sendJsonResponse(false, 'Statusi i vetures nuk mund te ndryshohet.', [], 500);
}
?>
