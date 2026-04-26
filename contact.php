<?php
$site_name = "CarMarketplace";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kontroll login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Merr cookies nëse ekzistojnë
$name = $_COOKIE['contact_name'] ?? "";
$email = $_COOKIE['contact_email'] ?? "";

$error = "";
$success = "";

// Kur submit forma
if (isset($_POST['send_message'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    $nameRegex = "/^[a-zA-ZëËçÇ\s]{2,50}$/";
    $emailRegex = "/^[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,}$/";

    if (!preg_match($nameRegex, $name)) {
        $error = "Emri nuk është valid!";
    } elseif (!preg_match($emailRegex, $email)) {
        $error = "Email-i nuk është valid!";
    } elseif (strlen($subject) < 3) {
        $error = "Subject duhet të ketë së paku 3 karaktere!";
    } elseif (strlen($message) < 10) {
        $error = "Mesazhi duhet të ketë së paku 10 karaktere!";
    } else {
        // Ruaj në cookie
        setcookie("contact_name", $name, time() + (86400 * 30), "/");
        setcookie("contact_email", $email, time() + (86400 * 30), "/");

        // Këtu mundesh me shtu DB ose email

        $success = "Mesazhi u dërgua me sukses!";
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakti - CarMarketPlace</title>

    <link rel="stylesheet" href="Style/style.css">
    <link rel="stylesheet" href="Style/contact.css">
</head>
<body>

<?php include "Includes/header.php"; ?>

<section class="contact-hero">
    <div class="contact-hero-text">
        <span>WE'D LOVE TO HEAR FROM YOU</span>
        <h1>Contact Us</h1>
        <p>Have a question or need help? Fill out the form and our team will get back to you.</p>
    </div>
</section>

<section class="contact-section">
    <div class="contact-form-box">
        <h2>Send Us a Message</h2>

        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="name"
                   value="<?php echo htmlspecialchars($name); ?>" required>

            <label>Email</label>
            <input type="email" name="email"
                   value="<?php echo htmlspecialchars($email); ?>" required>

            <label>Subject</label>
            <input type="text" name="subject" required>

            <label>Message</label>
            <textarea name="message" required></textarea>

            <button type="submit" name="send_message">Send Message</button>
        </form>
    </div>

    <div class="contact-info-box">
        <h2>Get in Touch</h2>

        <p>📍 Prishtinë, Kosovë</p>
        <p>📞 +383 44 123 456</p>
        <p>✉️ info@carmarketplace.com</p>
        <p>⏰ Mon - Fri: 09:00 - 18:00</p>
    </div>
</section>

<section class="contact-cta">
    <div>
        <h2>Looking for your dream car?</h2>
        <p>Check out our latest listings.</p>
    </div>
    <a href="sherbimet.php">Browse Cars</a>
</section>

<script src="contact.js"></script>

<?php include "Includes/footer.php"; ?>

</body>
</html>