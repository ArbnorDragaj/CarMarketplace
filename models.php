<?php
session_start();
require 'carCL.php';

$site_name = "CarMarketPlace";
//set favorite brand

if (isset($_GET['brand'])) {
    $_SESSION['favorite_brand'] = $_GET['brand'];
}

//get favorite brand

$favorite = $_SESSION['favorite_brand'] ?? "None";


$cars = [

    new Car("Audi", "RS7", "Petrol", "Sedan", 2023, 85000, "img/audi-sedan.png"),
    new Car("Audi", "R8", "Petrol", "Sport", 2022, 150000, "img/audi-sport.jpg"),
    new Car("Audi", "Q5", "Diesel", "SUV", 2021, 45000, "img/audi-suv.jpg"),

    new Car("BMW", "3 Series", "Petrol", "Sedan", 2023, 50000, "img/bmw-sedan.jpg"),
    new Car("BMW", "M4", "Petrol", "Sport", 2022, 90000, "img/bmw-sport.jpg"),
    new Car("BMW", "X5", "Diesel", "SUV", 2021, 70000, "img/bmw-suv.jpg"),

    new Car("Mercedes", "E-Class", "Petrol", "Sedan", 2023, 60000, "img/mercedes-sedan.jpg"),
    new Car("Mercedes", "AMG GT", "Petrol", "Sport", 2022, 140000, "img/mercedes-sport.jpg"),
    new Car("Mercedes", "G-Class", "Petrol", "SUV", 2023, 130000, "img/mercedes-suv.jpg"),

    new Car("Tesla", "Model S", "Electric", "Sedan", 2023, 90000, "img/tesla-sedan.jpg"),
    new Car("Tesla", "Roadster", "Electric", "Sport", 2022, 200000, "img/tesla-sport.jpg"),
    new Car("Tesla", "Model X", "Electric", "SUV", 2023, 110000, "img/tesla-suv.jpg"),

];



foreach ($cars as $car) {

    if ($car->getBrand() == $favorite) {
        echo "<p><strong>⭐ " . $car->getFullName() . " (Favorite)</strong></p>";
    } else {
        echo "<p>" . $car->getFullName() . "</p>";
    }
}


?>