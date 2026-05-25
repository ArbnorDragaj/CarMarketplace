<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config.php";

$error = "";
$success = "";
$username = "";
$email = "";

function getSafeRegisterRedirect($value) {
    $allowedRedirects = ['index.php', 'models.php', 'blog.php', 'contact.php', 'rreth-nesh.php'];
    $value = trim((string)$value);

    if ($value === '') {
        return '';
    }

    $path = parse_url($value, PHP_URL_PATH);
    $fileName = basename($path ?: $value);

    return in_array($fileName, $allowedRedirects, true) ? $fileName : '';
}

$redirect = getSafeRegisterRedirect($_POST['redirect'] ?? $_GET['redirect'] ?? '');
$back = getSafeRegisterRedirect($_POST['back'] ?? $_GET['back'] ?? '');

if ($back === '' || $back === 'models.php') {
    $back = 'index.php';
}

if (isset($_SESSION['user_id'], $_SESSION['user'])) {
    header("Location: " . ($redirect !== '' ? $redirect : "index.php"));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username === '' || $email === '' || $password === '' || $confirm_password === '') {
        $error = "Ju lutem plotësoni të gjitha fushat.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email nuk është valid.";
    } elseif (strlen($username) < 3) {
        $error = "Username duhet të ketë së paku 3 karaktere.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username mund te kete vetem shkronja, numra dhe underscore.";
    } elseif (strlen($password) < 6) {
        $error = "Password duhet të ketë së paku 6 karaktere.";
    } elseif ($password !== $confirm_password) {
        $error = "Password-at nuk përputhen.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $email]);

            if ($stmt->fetch()) {
                $error = "Ky username ose email ekziston.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashedPassword, 'user']);

                $success = "Regjistrimi u krye me sukses. Tani mund të kyçeni.";
                $username = "";
                $email = "";
            }
        } catch (PDOException $e) {
            $error = "Ndodhi një gabim. Provoni përsëri.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <link href="Style/login.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .success {
            margin-bottom: 16px;
            color: #7dffad;
            text-align: center;
        }
        .register-link {
            text-align: center;
            margin-top: 18px;
            color: #d8dde4;
        }
        .register-link a {
            color: #f5c400;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <h2>Register</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success"><?php echo e($success); ?></div>
        <?php endif; ?>

        <form method="post" action="register.php" autocomplete="off">
            <?php if ($redirect !== ''): ?>
                <input type="hidden" name="redirect" value="<?php echo e($redirect); ?>">
                <input type="hidden" name="back" value="<?php echo e($back); ?>">
            <?php endif; ?>

            <div class="input-box">
                <input type="text" name="username" placeholder="Username" autocomplete="off" required value="<?php echo e($username); ?>">
            </div>

            <div class="input-box">
                <input type="email" name="email" placeholder="Email" autocomplete="off" required value="<?php echo e($email); ?>">
            </div>

            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="off" required>
                <i class="fa-solid fa-eye toggle" id="eye" onclick="togglePassword('password', 'eye')"></i>
            </div>

            <div class="input-box">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" autocomplete="off" required>
                <i class="fa-solid fa-eye toggle" id="eye2" onclick="togglePassword('confirm_password', 'eye2')"></i>
            </div>

            <button type="submit" name="register" class="login-btn">Register</button>
        </form>

        <p class="register-link">
            Ke llogari?
            <a href="login.php<?php echo $redirect !== '' ? '?redirect=' . urlencode($redirect) . '&back=' . urlencode($back) : ''; ?>">Login</a>
        </p>
    </div>

    <script>
        function togglePassword(inputId, eyeId) {
            var pass = document.getElementById(inputId);
            var eye = document.getElementById(eyeId);

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
