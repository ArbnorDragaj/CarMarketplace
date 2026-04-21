<?php
session_start();


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