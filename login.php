<?php
session_start();

$users = [
    "admin" => ["password" => "1234", "role" => "admin"],
    "arbnor"  => ["password" => "1234", "role" => "user"]
];

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $users[$username]['role'];

        header("Location: index.php");
        exit();
    } else {
        $error = "Username ose password gabim!";
    }
}
?>
<!DOCTYPE html>
<html >
<head>
    <meta charset="UTF-8">
    <title>Login</title>

<link href="Style/login.css" rel="stylesheet" type="text/css">

</head>

<body>

    <div class="login-box">
        <h2>Login</h2>

   <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

        <form method="post">
            <div class="input-box">
                <input type="text" name="username" placeholder="Username">
            </div>

            <div class="input-box">
                <input type="password" name="password" placeholder="Password">
            </div>

            <button class="login-btn">Login</button>
        </form>
    </div>

</body>
</html>