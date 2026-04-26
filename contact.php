<?php
$site_name = "CarMarketplace";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$name = $_COOKIE['contact_name'] ?? "";
$email = $_COOKIE['contact_email'] ?? "";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";
$error = "";

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
        setcookie("contact_name", $name, time() + (86400 * 30), "/");
        setcookie("contact_email", $email, time() + (86400 * 30), "/");
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
    
    <link rel="stylesheet" href="Style/contact.css">
    <?php include "Includes/header.php"; ?>
        <link rel="stylesheet" href="Style/style.css">

</head>
<body>

require "Includes/header.php";
?>

<link rel="stylesheet" href="style/contact.css">
<link rel="stylesheet" href="style/style.css">

<section class="contact-hero">
    <div class="contact-hero-text">
        <span>WE'D LOVE TO HEAR FROM YOU</span>
        <h1>Contact Us</h1>
        <p>Have a question or need help? Fill out the form and our team will get back to you as soon as possible.</p>
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
            <input type="text" name="name" placeholder="Your name"
                   value="<?php echo htmlspecialchars($name); ?>" required>

            <label>Email Address</label>
            <input type="email" name="email" placeholder="Your email"
                   value="<?php echo htmlspecialchars($email); ?>" required>

            <label>Subject</label>
            <input type="text" name="subject" placeholder="How can we help?" required>

            <label>Message</label>
            <textarea name="message" placeholder="Write your message here..." required></textarea>

            <button type="submit" name="send_message">Send Message</button>
        </form>
    </div>

    <div class="contact-info-box">
        <h2>Get in Touch</h2>

        <div class="info-item">
            <div class="icon">📍</div>
            <div>
                <h3>Address</h3>
                <p>Rr. Skënderbeu, Prishtinë, Kosova</p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon">📞</div>
            <div>
                <h3>Phone</h3>
                <p>+383 44 123 456</p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon">✉️</div>
            <div>
                <h3>Email</h3>
                <p>info@carmarketplace.com</p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon">⏰</div>
            <div>
                <h3>Working Hours</h3>
                <p>Mon - Fri: 09:00 - 18:00</p>
                <p>Sat: 10:00 - 15:00</p>
            </div>
        </div>
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

<?php require "Includes/footer.php"; ?>