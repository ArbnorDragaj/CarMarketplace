<?php
session_start();

//set favorite brand

if (isset($_GET['brand'])) {
    $_SESSION['favorite_brand'] = $_GET['brand'];
}

//get favorite brand

$favorite = $_SESSION['favorite_brand'] ?? "None";

//output

echo "<h2>Select Your Favorite Brand</h2>";

echo "
<a href='?brand=Audi'>Audi</a> |
<a href='?brand=BMW'>BMW</a> |
<a href='?brand=Tesla'>Tesla</a> |
<a href='?brand=Toyota'>Toyota</a>
";

echo "<hr>";

echo "<h3>Your favorite brand: " . $favorite . "</h3>";


$cars = [
    ["brand" => "Audi", "model" => "A5"],
    ["brand" => "BMW", "model" => "X5"],
    ["brand" => "Tesla", "model" => "Model 3"],
    ["brand" => "Toyota", "model" => "RAV4"]
];

echo "<hr>";
echo "<h2>Car List</h2>";



foreach ($cars as $car) {

    if ($car['brand'] == $favorite) {
        echo "<p><strong>⭐ " . $car['brand'] . " " . $car['model'] . " (Favorite)</strong></p>";
    } else {
        echo "<p>" . $car['brand'] . " " . $car['model'] . "</p>";
    }
}


?>