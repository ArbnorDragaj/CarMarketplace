<?php
session_start();

/* USERS */
$users = [
    "admin" => ["password" => "1234", "role" => "admin"],
    "arbnor" => ["password" => "1234", "role" => "user"]
];

/* POSTS FILE */
if (!file_exists("posts.json")) {
    file_put_contents("posts.json", json_encode([]));
}

$posts = json_decode(file_get_contents("posts.json"), true);

/* LOGIN */
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

/* LOGOUT */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: blog.php");
    exit();
}

/* ADD POST */
if (isset($_POST['add_post']) && $_SESSION['role'] === 'admin') {

    $imagePath = "";

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
            $imagePath = $targetFile;
        }
    }

    $posts[] = [
        "title" => $_POST['title'],
        "content" => $_POST['content'],
        "image" => $imagePath,
        "date" => date("d M Y")
    ];

    file_put_contents("posts.json", json_encode($posts));
    header("Location: blog.php");
    exit();
}

/* DELETE POST */
if (isset($_GET['delete']) && $_SESSION['role'] === 'admin') {
    $id = $_GET['delete'];

    if (!empty($posts[$id]['image']) && file_exists($posts[$id]['image'])) {
        unlink($posts[$id]['image']);
    }

    unset($posts[$id]);
    $posts = array_values($posts);

    file_put_contents("posts.json", json_encode($posts));
    header("Location: blog.php");
    exit();
}
?>
