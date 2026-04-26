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

// filters
$brandFilter = $_GET['brandFilter'] ?? '';
$bodyFilter  = $_GET['body'] ?? '';
$fuelFilter  = $_GET['fuel'] ?? '';


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




?>

<?php include 'header.php'; ?>

<link rel="stylesheet" href="Style/models.css">

<div class="models-page">
    <h1 class="models-title">Our Cars</h1>

        <div class="favorite-links">
            <a href="?brand=Audi">Audi</a>
            <a href="?brand=BMW">BMW</a>
            <a href="?brand=Mercedes">Mercedes</a>
            <a href="?brand=Tesla">Tesla</a>
        </div>

        <form method="GET" class="filters-box">

        <select name="brandFilter">
            <option value="">All Brands</option>
            <option value="Audi" <?php if ($brandFilter == "Audi") echo "selected"; ?>>Audi</option>
            <option value="BMW" <?php if ($brandFilter == "BMW") echo "selected"; ?>>BMW</option>
            <option value="Mercedes" <?php if ($brandFilter == "Mercedes") echo "selected"; ?>>Mercedes</option>
            <option value="Tesla" <?php if ($brandFilter == "Tesla") echo "selected"; ?>>Tesla</option>
        </select>

        <select name="body">
            <option value="">All Types</option>
            <option value="Sedan" <?php if ($bodyFilter == "Sedan") echo "selected"; ?>>Sedan</option>
            <option value="SUV" <?php if ($bodyFilter == "SUV") echo "selected"; ?>>SUV</option>
            <option value="Sport" <?php if ($bodyFilter == "Sport") echo "selected"; ?>>Sport</option>
        </select>

        <select name="fuel">
            <option value="">All Fuel</option>
            <option value="Petrol" <?php if ($fuelFilter == "Petrol") echo "selected"; ?>>Petrol</option>
            <option value="Diesel" <?php if ($fuelFilter == "Diesel") echo "selected"; ?>>Diesel</option>
            <option value="Electric" <?php if ($fuelFilter == "Electric") echo "selected"; ?>>Electric</option>
        </select>

        <button type="submit">Filter</button>
        <a href="models.php" class="clear-filter">Clear</a>

    </form>

    <div class="models-grid">

        <?php foreach ($cars as $car):
        
            if ($brandFilter && $car->getBrand() != $brandFilter) continue;
            if ($bodyFilter && $car->getBody() != $bodyFilter) continue;
            if ($fuelFilter && $car->getFuel() != $fuelFilter) continue;

             $isFavorite = ($car->getBrand() == $favorite);
        ?>

            <div class="model-card <?php echo $isFavorite ? 'favorite-car' : ''; ?>">
                <img src="<?php echo $car->getImage(); ?>" alt="<?php echo $car->getFullName(); ?>">

                <div class="model-info">
                    <?php if ($isFavorite): ?>
                        <span class="favorite-label">⭐ Favorite</span>
                    <?php endif; ?>

                    <h3><?php echo $car->getFullName(); ?></h3>
                    <p><?php echo $car->getYear(); ?> | <?php echo $car->getFuel(); ?> | <?php echo $car->getBody(); ?></p>
                    <span class="model-price">€<?php echo number_format($car->getPrice()); ?></span>
                </div>
            </div>


        <?php endforeach; ?>

    </div>
</div>

<?php include 'footer.php'; ?>