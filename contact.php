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