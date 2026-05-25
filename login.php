<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config.php";

$error = "";
$success = "";
$username = "";

function getSafeRedirect($value) {
    $allowedRedirects = ['index.php', 'models.php', 'blog.php', 'contact.php', 'rreth-nesh.php'];
    $value = trim((string)$value);

    if ($value === '') {
        return '';
    }

    $path = parse_url($value, PHP_URL_PATH);
    $fileName = basename($path ?: $value);

    return in_array($fileName, $allowedRedirects, true) ? $fileName : '';
}

$redirect = getSafeRedirect($_POST['redirect'] ?? $_GET['redirect'] ?? '');
$back = getSafeRedirect($_POST['back'] ?? $_GET['back'] ?? '');

if ($back === '' || $back === 'models.php') {
    $back = 'index.php';
}

if (isset($_GET['logged_out'])) {
    $success = "Jeni shkycur me sukses.";
} elseif (($_GET['message'] ?? '') === 'services') {
    $success = "Per te perdorur sherbimet duhet te kyqeni.";
}

if (isset($_SESSION['user_id'], $_SESSION['user'])) {
    if ($redirect !== '') {
        header("Location: " . $redirect);
    } else {
        header("Location: " . (($_SESSION['role'] ?? '') === 'admin' ? "admin.php" : "index.php"));
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Ju lutem plotësoni username dhe password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($redirect !== '') {
                    header("Location: " . $redirect);
                } elseif ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            }

            $error = "Username ose password gabim.";
        } catch (PDOException $e) {
            $error = "Ndodhi një gabim gjatë kyçjes. Provoni përsëri.";
        }
    }
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
            <div class="error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="error" style="color:#7dffad;"><?php echo e($success); ?></div>
        <?php endif; ?>

        <form method="post" action="login.php" autocomplete="off">
            <?php if ($redirect !== ''): ?>
                <input type="hidden" name="redirect" value="<?php echo e($redirect); ?>">
                <input type="hidden" name="back" value="<?php echo e($back); ?>">
            <?php endif; ?>

            <div class="input-box">
                <input type="text" name="username" placeholder="Username" autocomplete="off" required value="<?php echo e($username); ?>">
            </div>

            <div class="input-box">
                <input type="password" id="password" name="password" placeholder="Password" autocomplete="off" required>
                <i class="fa-solid fa-eye toggle" id="eye" onclick="togglePassword()"></i>
            </div>

            <button type="submit" name="login" class="login-btn">Login</button>
        </form>

        <p style="text-align:center; margin-top:18px; color:#d8dde4;">
            Nuk ke llogari?
            <a href="register.php<?php echo $redirect !== '' ? '?redirect=' . urlencode($redirect) . '&back=' . urlencode($back) : ''; ?>" style="color:#f5c400; font-weight:bold; text-decoration:none;">Register</a>
        </p>

        <?php if ($redirect !== ''): ?>
            <p class="back-link">
                <a href="<?php echo e($back); ?>">Kthehu mbrapa</a>
            </p>
        <?php endif; ?>
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
