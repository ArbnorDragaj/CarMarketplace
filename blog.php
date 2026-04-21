<?php
session_start();

/* USERS */
$users = [
    "admin" => ["password" => "1234", "role" => "admin"],
    "arbnor" => ["password" => "1234", "role" => "user"]
];

/* INIT POSTS SESSION */
if (!isset($_SESSION['posts'])) {
    $_SESSION['posts'] = [];
}

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

    $imageName = "";

    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $imageName);
    }

    $_SESSION['posts'][] = [
        "title" => $_POST['title'],
        "content" => $_POST['content'],
        "image" => $imageName,
        "date" => date("d M Y")
    ];
}

/* DELETE POST */
if (isset($_GET['delete']) && $_SESSION['role'] === 'admin') {
    $id = $_GET['delete'];
    unset($_SESSION['posts'][$id]);
    $_SESSION['posts'] = array_values($_SESSION['posts']);
    header("Location: blog.php");
    exit();
}

/* EDIT POST */
if (isset($_POST['edit_save']) && $_SESSION['role'] === 'admin') {
    $id = $_POST['id'];

    $_SESSION['posts'][$id]['title'] = $_POST['title'];
    $_SESSION['posts'][$id]['content'] = $_POST['content'];

    header("Location: blog.php");
    exit();
}
?>
