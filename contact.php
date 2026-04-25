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