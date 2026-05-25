<?php
$site_name = "CarMarketPlace";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$name = $_COOKIE['contact_name'] ?? "";
$email = $_COOKIE['contact_email'] ?? "";

$success = "";
$error = "";

if (isset($_POST['send_message'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $nameRegex = "/^[\p{L}\s]{2,50}$/u";
    $emailRegex = "/^[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,}$/";

    if (!preg_match($nameRegex, $name)) {
        $error = "Emri nuk eshte valid!";
    } elseif (!preg_match($emailRegex, $email)) {
        $error = "Email-i nuk eshte valid!";
    } elseif (strlen($subject) < 3) {
        $error = "Subject duhet te kete se paku 3 karaktere!";
    } elseif (strlen($message) < 10) {
        $error = "Mesazhi duhet te kete se paku 10 karaktere!";
    } else {
        setcookie("contact_name", $name, time() + (86400 * 30), "/");
        setcookie("contact_email", $email, time() + (86400 * 30), "/");
        $success = "Mesazhi u dergua me sukses!";
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt - CarMarketPlace</title>
    <link rel="stylesheet" href="Style/style.css?v=3">
    <link rel="stylesheet" href="Style/contact.css?v=2">
</head>
<body>

<?php require "Includes/header.php"; ?>

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
            <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="Your name"
                   value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" required>

            <label>Email Address</label>
            <input type="email" name="email" placeholder="Your email"
                   value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>

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
            <div class="icon">AD</div>
            <div>
                <h3>Address</h3>
                <p>Rr. Skenderbeu, Prishtine, Kosove</p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon">PH</div>
            <div>
                <h3>Phone</h3>
                <p>+383 44 123 456</p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon">@</div>
            <div>
                <h3>Email</h3>
                <p>info@carmarketplace.com</p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon">HR</div>
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
    <a href="models.php">Browse Cars</a>
</section>

<?php require "Includes/footer.php"; ?>

</body>
</html>
