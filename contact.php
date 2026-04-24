<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    if(!preg_match("/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/i",$email)){
        $error = "Email i pavlefshëm!";
    } elseif(!preg_match("/^\+355\d{8,9}$/",$phone)){
        $error = "Numër telefoni i pavlefshëm!";
    } else {
        $success = "Mesazhi u dërgua me sukses!";
    }
}
?>
<section class="contact-form">
<h2>Na Kontaktoni</h2>
<?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if($success) echo "<p style='color:green;'>$success</p>"; ?>
<form method="POST" action="">
    <input type="text" name="name" placeholder="Emri juaj" value="<?= isset($_SESSION['user']) ? $_SESSION['user'] : '' ?>">
    <input type="email" name="email" placeholder="Email">
    <input type="text" name="phone" placeholder="Numri i telefonit">
    <textarea name="message" placeholder="Mesazhi juaj"></textarea>
    <button type="submit">Dërgo</button>
</form>
</section>

<!DOCTYPE html>
<html lang="en">
<head>

<link rel="stylesheet" href="contact.css">
    
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>