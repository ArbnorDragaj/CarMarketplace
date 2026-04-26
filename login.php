<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'userCl.php';

$users = [
    new User("arbnor", "1234", "user"),
    new User("admin", "1234", "admin")
];

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    foreach ($users as $user) {
        if ($user->getUsername() === $username && $user->checkPassword($password)) {
            $_SESSION['user'] = $user->getUsername();
            $_SESSION['role'] = $user->getRole();

            header("Location: index.php");
            exit();
        }
    }

    $error = "Username ose password gabim.";
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <link href="Style/login.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

    <div class="login-box">
        <h2>Login</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" autocomplete="off">
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" autocomplete="off" required>
            </div>

            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="off" required>
                <i class="fa-solid fa-eye toggle" id="eye" onclick="togglePassword()"></i>
            </div>

            <button type="submit" name="login" class="login-btn">Login</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            var pass = document.getElementById("password");
            var eye = document.getElementById("eye");

            if (pass.type === "password") {
                pass.type = "text";
                eye.classList.remove("fa-eye");
                eye.classList.add("fa-eye-slash");
            } else {
                pass.type = "password";
                eye.classList.remove("fa-eye-slash");
                eye.classList.add("fa-eye");
            }
        }
    </script>

</body>
</html>