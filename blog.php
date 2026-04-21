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

?>
