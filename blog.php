<?php
session_start();

$users = [
    "admin" => ["password" => "1234", "role" => "admin"],
    "arbnor" => ["password" => "1234", "role" => "user"]
];

if (!file_exists("posts.json")) {
    file_put_contents("posts.json", json_encode([]));
}

$posts = json_decode(file_get_contents("posts.json"), true);
if (!is_array($posts)) $posts = [];

if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    if (isset($users[$u]) && $users[$u]['password'] === $p) {
        $_SESSION['user'] = $u;
        $_SESSION['role'] = $users[$u]['role'];
    } else {
        $error = "Login gabim!";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: blog.php");
    exit();
}

function uploadImage() {
    if (empty($_FILES['image']['name'])) return "";

    $targetDir = "images/";
    if (!is_dir($targetDir)) mkdir($targetDir);

    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . $fileName;

    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
        return $targetFile;
    }

    return "";
}

if (isset($_POST['add_post']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $imagePath = uploadImage();

    $posts[] = [
        "title" => $_POST['title'],
        "content" => $_POST['content'],
        "category" => $_POST['category'],
        "image" => $imagePath,
        "date" => date("d M Y"),
        "author" => $_SESSION['user']
    ];

    file_put_contents("posts.json", json_encode($posts, JSON_PRETTY_PRINT));
    header("Location: blog.php");
    exit();
}

if (isset($_GET['delete']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $id = $_GET['delete'];

    if (isset($posts[$id])) {
        if (!empty($posts[$id]['image']) && file_exists($posts[$id]['image'])) {
            unlink($posts[$id]['image']);
        }

        unset($posts[$id]);
        $posts = array_values($posts);
        file_put_contents("posts.json", json_encode($posts, JSON_PRETTY_PRINT));
    }

    header("Location: blog.php");
    exit();
}

if (isset($_POST['edit_post']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $id = $_POST['id'];

    if (isset($posts[$id])) {
        $posts[$id]['title'] = $_POST['title'];
        $posts[$id]['content'] = $_POST['content'];
        $posts[$id]['category'] = $_POST['category'];

        $newImage = uploadImage();
        if ($newImage !== "") {
            $posts[$id]['image'] = $newImage;
        }

        file_put_contents("posts.json", json_encode($posts, JSON_PRETTY_PRINT));
    }

    header("Location: blog.php");
    exit();
}

$categories = ["Sports Car", "Luxury", "Classic", "Electric", "SUV"];
?>
