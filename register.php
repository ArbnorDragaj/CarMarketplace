<?php
session_start();


$error = "";
$success = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Ju lutem plotësoni të gjitha fushat.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email nuk është valid.";
    } elseif (strlen($password) < 6) {
        $error = "Password duhet të ketë së paku 6 karaktere.";
    } elseif ($password !== $confirm_password) {
        $error = "Password-at nuk përputhen.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);

            if ($stmt->rowCount() > 0) {
                $error = "Ky username ose email ekziston.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password, role)
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([$username, $email, $hashedPassword, 'user']);

                $success = "Regjistrimi u krye me sukses. Tani mund të kyçeni.";
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
    <title>Register - CarMarketplace</title>
    <link rel="stylesheet" href="Style/register.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">
        <h1>Register</h1>

        <?php if (!empty($error)): ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="message success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username"
                       value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
            </div>

            <div class="input-group">
                <input type="email" name="email" placeholder="Email"
                       value="<?= isset($email) ? htmlspecialchars($email) : '' ?>">
            </div>

            <div class="input-group">
                <input type="password" name="password" placeholder="Password">
            </div>

            <div class="input-group">
                <input type="password" name="confirm_password" placeholder="Confirm Password">
            </div>

            <button type="submit" name="register">Register</button>
        </form>

        <p class="auth-link">
            Already have an account?
            <a href="login.php">Login</a>
        </p>
    </div>
</div>

</body>
</html>