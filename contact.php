<?php

$error = "";
$success = "";

if(isset($_POST['submit'])){

$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$message = $_POST['message'];

if(empty($name) || empty($email) || empty($phone) || empty($message)){
$error = "Ju lutem plotësoni të gjitha fushat!";
}

elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
$error = "Email i pavlefshëm!";
}

else{
$success = "Mesazhi u dërgua me sukses!";
}

}
?>