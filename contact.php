<?php
session_start();

$error = "";
$success = "";

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $message = htmlspecialchars(trim($_POST['message']));