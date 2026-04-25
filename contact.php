<?php
session_start();

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $message = htmlspecialchars(trim($_POST['message']));

    
    // Validimi
    if(empty($name) || empty($email) || empty($phone) || empty($message)){
        $error = "Ju lutem plotësoni të gjitha fushat!";
    }
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Email i pavlefshëm!";
    } 
    elseif(!preg_match("/^\+355\d{8,9}$/",$phone)){
        $error = "Numër telefoni i pavlefshëm! (Shembull: +355XXXXXXXXX)";
    } 
    else {
        $success = "Mesazhi u dërgua me sukses!";
        
        // Pas suksesit i zbrazim fushat
        $name = $email = $phone = $message = "";
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
</head>
<body>

<section class="contact-form">
    <h2>Na Kontaktoni</h2>

    <?php if($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if($success): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" id="contactForm">

        <input type="text" name="name" placeholder="Emri juaj"
        value="<?= isset($name) ? $name : '' ?>">

        <input type="email" name="email" placeholder="Email"
        value="<?= isset($email) ? $email : '' ?>">

        <input type="text" name="phone" placeholder="Numri i telefonit (+383...)"
        value="<?= isset($phone) ? $phone : '' ?>">

        <textarea name="message" placeholder="Mesazhi juaj"><?= isset($message) ? $message : '' ?></textarea>

        <button type="submit">Dërgo</button>
    </form>
</section>

<script src="contact.js"></script>

</body>
</html>

       