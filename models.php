<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/classes/carCL.php';
require_once __DIR__ . '/config/db.php';


if (!isset($pdo) || !$pdo instanceof PDO) {
    die("Database connection is not available.");
}

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
    Filtrat e faqes
*/
$brandFilter = getAllowedFilter('brandFilter', $allowedBrands);
$bodyFilter  = getAllowedFilter('body', $allowedBodies);
$fuelFilter  = getAllowedFilter('fuel', $allowedFuels);

$cars = [];
$dbError = '';

try {
    $sql = "SELECT brand, model, fuel, body_type, year, price, image
            FROM cars
            WHERE status = :status";

    $params = [
        ':status' => 'active'
    ];

    if ($brandFilter !== '') {
        $sql .= " AND brand = :brand";
        $params[':brand'] = $brandFilter;
    }

    if ($bodyFilter !== '') {
        $sql .= " AND body_type = :body_type";
        $params[':body_type'] = $bodyFilter;
    }

    if ($fuelFilter !== '') {
        $sql .= " AND fuel = :fuel";
        $params[':fuel'] = $fuelFilter;
    }

    $sql .= " ORDER BY brand ASC, model ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $rows = $stmt->fetchAll();

    foreach ($rows as $row) {
        $cars[] = new Car(
            $row['brand'],
            $row['model'],
            $row['fuel'],
            $row['body_type'],
            $row['year'],
            $row['price'],
            $row['image']
        );
    }
} catch (PDOException $e) {
    $dbError = "Cars could not be loaded at the moment.";
}

$filteredCars = $cars;
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

                <?php if ($dbError !== ''): ?>

                    <p class="no-cars-message">
                        <?php echo e($dbError); ?>
                    </p>

                <?php elseif (count($filteredCars) > 0): ?>

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