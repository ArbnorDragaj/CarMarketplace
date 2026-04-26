<?php
$site_name = "CarMarketplace";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$name = $_COOKIE['contact_name'] ?? "";
$email = $_COOKIE['contact_email'] ?? "";

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

require "Includes/header.php";
?>