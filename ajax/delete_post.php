<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config.php";

header("Content-Type: application/json");

function respond($success, $message) {
    echo json_encode(["success" => $success, "message" => $message]);
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    respond(false, "Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, "Invalid request method.");
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    respond(false, "Invalid post id.");
}

try {
    $stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $post = $stmt->fetch();

    if (!$post) {
        respond(false, "Post not found.");
    }

    if (!empty($post['image'])) {
        $imagePath = __DIR__ . "/../" . ltrim($post['image'], "/\\");
        if (is_file($imagePath)) {
            unlink($imagePath);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->execute([$id]);

    respond(true, "Post deleted successfully.");
} catch (PDOException $e) {
    respond(false, "Database error occurred.");
}
?>
