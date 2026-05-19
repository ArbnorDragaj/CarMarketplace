<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/classes/carCL.php';

$site_name = "CarMarketPlace";


if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

/*
    Vlerat e lejuara për filtrat.
*/
$allowedBrands = ["Audi", "BMW", "Mercedes", "Tesla"];
$allowedBodies = ["Sedan", "SUV", "Sport"];
$allowedFuels  = ["Petrol", "Diesel", "Electric"];


function getAllowedFilter($key, $allowedValues) {
    if (!isset($_GET[$key])) {
        return '';
    }

    $value = trim($_GET[$key]);

    if (in_array($value, $allowedValues, true)) {
        return $value;
    }

    return '';
}

/*
    Favorite brand ruhet në session vetëm nëse brendi është valid.
*/
if (isset($_GET['brand']) && in_array($_GET['brand'], $allowedBrands, true)) {
    $_SESSION['favorite_brand'] = $_GET['brand'];
}

$favorite = $_SESSION['favorite_brand'] ?? "None";

/*
    Filtrat e faqes.
*/
$brandFilter = getAllowedFilter('brandFilter', $allowedBrands);
$bodyFilter  = getAllowedFilter('body', $allowedBodies);
$fuelFilter  = getAllowedFilter('fuel', $allowedFuels);


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


$filteredCars = [];

foreach ($cars as $car) {
    if ($brandFilter !== '' && $car->getBrand() !== $brandFilter) {
        continue;
    }

    if ($bodyFilter !== '' && $car->getBody() !== $bodyFilter) {
        continue;
    }

    if ($fuelFilter !== '' && $car->getFuel() !== $fuelFilter) {
        continue;
    }

    $filteredCars[] = $car;
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>Models</title>

    <link rel="stylesheet" href="Style/style.css">
    <link rel="stylesheet" href="Style/models.css">
</head>
<body>

<?php include 'Includes/header.php'; ?>

<div class="models-page">
    <h1 class="models-title">Our Cars</h1>

    <div class="favorite-links">
        <?php foreach ($allowedBrands as $brand): ?>
            <a href="?brand=<?php echo urlencode($brand); ?>">
                <?php echo e($brand); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="models-layout">

        <form method="GET" class="filters-box">

            <select name="brandFilter">
                <option value="">All Brands</option>

                <?php foreach ($allowedBrands as $brand): ?>
                    <option value="<?php echo e($brand); ?>" <?php echo ($brandFilter === $brand) ? 'selected' : ''; ?>>
                        <?php echo e($brand); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="body">
                <option value="">All Types</option>

                <?php foreach ($allowedBodies as $body): ?>
                    <option value="<?php echo e($body); ?>" <?php echo ($bodyFilter === $body) ? 'selected' : ''; ?>>
                        <?php echo e($body); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="fuel">
                <option value="">All Fuel</option>

                <?php foreach ($allowedFuels as $fuel): ?>
                    <option value="<?php echo e($fuel); ?>" <?php echo ($fuelFilter === $fuel) ? 'selected' : ''; ?>>
                        <?php echo e($fuel); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Filter</button>
            <a href="models.php" class="clear-filter">Clear</a>

        </form>

        <div class="models-content">
            <div class="models-grid">

                <?php if (count($filteredCars) > 0): ?>

                    <?php foreach ($filteredCars as $car): ?>
                        <?php $isFavorite = ($car->getBrand() === $favorite); ?>

                        <div class="model-card <?php echo $isFavorite ? 'favorite-car' : ''; ?>">
                            <img src="<?php echo e($car->getImage()); ?>" alt="<?php echo e($car->getFullName()); ?>">

                            <div class="model-info">
                                <?php if ($isFavorite): ?>
                                    <span class="favorite-label">⭐ Favorite</span>
                                <?php endif; ?>

                                <h3><?php echo e($car->getFullName()); ?></h3>

                                <p>
                                    <?php echo e($car->getYear()); ?> |
                                    <?php echo e($car->getFuel()); ?> |
                                    <?php echo e($car->getBody()); ?>
                                </p>

                                <span class="model-price">
                                    €<?php echo number_format((float)$car->getPrice()); ?>
                                </span>
                            </div>
                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p class="no-cars-message">
                        No cars found for the selected filters.
                    </p>

                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<?php include 'Includes/footer.php'; ?>

</body>
</html>