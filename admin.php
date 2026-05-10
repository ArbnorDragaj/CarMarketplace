<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'classes/carCL.php';

$site_name = "CarMarketPlace";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}



if ($_SESSION['role'] !== "admin") {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['cars'])) {
    $_SESSION['cars'] = [
        [
            "brand" => "Audi",
            "model" => "RS7",
            "fuel" => "Petrol",
            "type" => "Sedan",
            "year" => 2023,
            "price" => 85000,
            "image" => "img/audi-sedan.png",
            "status" => "active"
        ],
        [
            "brand" => "Audi",
            "model" => "R8",
            "fuel" => "Petrol",
            "type" => "Sport",
            "year" => 2022,
            "price" => 150000,
            "image" => "img/audi-sport.jpg",
            "status" => "active"
        ],
        [
            "brand" => "BMW",
            "model" => "M4",
            "fuel" => "Petrol",
            "type" => "Sport",
            "year" => 2022,
            "price" => 90000,
            "image" => "img/bmw-sport.jpg",
            "status" => "active"
        ],
        [
            "brand" => "Mercedes",
            "model" => "E-Class",
            "fuel" => "Petrol",
            "type" => "Sedan",
            "year" => 2023,
            "price" => 60000,
            "image" => "img/mercedes-sedan.jpg",
            "status" => "active"
        ],
        [
            "brand" => "Tesla",
            "model" => "Model S",
            "fuel" => "Electric",
            "type" => "Sedan",
            "year" => 2023,
            "price" => 90000,
            "image" => "img/tesla-sedan.jpg",
            "status" => "active"
        ]
    ];
}

if (isset($_POST['add_car'])) {
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $fuel = trim($_POST['fuel'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $price = trim($_POST['price'] ?? '');

    $imagePath = '';

    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === 0) {
        $uploadDir = "uploads/cars/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = $_FILES['car_image']['name'];
        $fileTmp = $_FILES['car_image']['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($fileExt, $allowedTypes)) {
            $newFileName = uniqid("car_", true) . "." . $fileExt;
            $imagePath = $uploadDir . $newFileName;
            move_uploaded_file($fileTmp, $imagePath);
        }
    }

    if (
        $brand !== '' &&
        $model !== '' &&
        $fuel !== '' &&
        $type !== '' &&
        $year !== '' &&
        $price !== '' &&
        $imagePath !== ''
    ) {
        $_SESSION['cars'][] = [
            "brand" => $brand,
            "model" => $model,
            "fuel" => $fuel,
            "type" => $type,
            "year" => $year,
            "price" => $price,
            "image" => $imagePath,
            "status" => "active"
        ];

        header("Location: admin.php");
        exit();
    }
}

if (isset($_GET['deactivate'])) {
    $id = $_GET['deactivate'];

    if (isset($_SESSION['cars'][$id])) {
        $_SESSION['cars'][$id]['status'] = "inactive";
    }

    header("Location: admin.php");
    exit();
}

if (isset($_GET['activate'])) {
    $id = $_GET['activate'];

    if (isset($_SESSION['cars'][$id])) {
        $_SESSION['cars'][$id]['status'] = "active";
    }

    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    if (isset($_SESSION['cars'][$id])) {
        unset($_SESSION['cars'][$id]);
        $_SESSION['cars'] = array_values($_SESSION['cars']);
    }

    header("Location: admin.php");
    exit();
}

$totalCars = count($_SESSION['cars']);

$activeCars = count(array_filter($_SESSION['cars'], function ($car) {
    return $car['status'] === 'active';
}));

$inactiveCars = $totalCars - $activeCars;
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - CarMarketPlace</title>
    <link rel="stylesheet" href="Style/style.css">
    <link rel="stylesheet" href="Style/admin.css">
</head>
<body>

<?php include 'Includes/header.php'; ?>

<section class="admin-cars-section">
    <div class="admin-cars-container">

        <div class="admin-top">
            <div>
                <span class="admin-label">Admin Panel</span>
                <h1>Menaxho Veturat</h1>
                <p>Shto vetura të reja, menaxho statusin dhe kontrollo veturat aktive në platformë.</p>
            </div>
        </div>

        <div class="admin-stats">
            <div class="stat-card">
                <span>Total vetura</span>
                <h3><?php echo $totalCars; ?></h3>
            </div>

            <div class="stat-card">
                <span>Veturat aktive</span>
                <h3><?php echo $activeCars; ?></h3>
            </div>

            <div class="stat-card">
                <span>Joaktive</span>
                <h3><?php echo $inactiveCars; ?></h3>
            </div>
        </div>

        <div class="admin-grid">

            <div class="admin-form-card">
                <h2>Shto veturë të re</h2>

                <form method="POST" class="car-form" enctype="multipart/form-data">
                    <div class="form-row">
                        <input type="text" name="brand" placeholder="Marka p.sh. Audi" required>
                        <input type="text" name="model" placeholder="Modeli p.sh. RS7" required>
                    </div>

                    <div class="form-row">
                        <input type="text" name="fuel" placeholder="Karburanti p.sh. Petrol" required>
                        <input type="text" name="type" placeholder="Tipi p.sh. Sedan" required>
                    </div>

                    <div class="form-row">
                        <input type="number" name="year" placeholder="Viti p.sh. 2023" required>
                        <input type="number" name="price" placeholder="Çmimi p.sh. 85000" required>
                    </div>

                    <div class="upload-box">
                        <label for="car_image">Foto e veturës</label>
                        <input type="file" name="car_image" id="car_image" accept="image/*" required>
                    </div>

                    <button type="submit" name="add_car">Shto veturën</button>
                </form>
            </div>

            <div class="admin-info-card">
                <h2>Udhëzim</h2>
                <p>
                    Plotëso të dhënat e veturës dhe ngarko foton nga kompjuteri.
                    Fotot ruhen automatikisht në folderin <strong>uploads/cars/</strong>.
                </p>
            </div>

        </div>

        <div class="cars-table-card">
            <div class="table-header">
                <h2>Veturat në sistem</h2>
                <p>Këtu shfaqen të gjitha veturat aktive dhe joaktive.</p>
            </div>

            <div class="cars-table-wrapper">
                <table class="cars-table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Vetura</th>
                            <th>Tipi</th>
                            <th>Viti</th>
                            <th>Çmimi</th>
                            <th>Statusi</th>
                            <th>Veprime</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($_SESSION['cars'] as $index => $car): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="Car" class="car-admin-img">
                                </td>

                                <td>
                                    <strong>
                                        <?php echo htmlspecialchars($car['brand'] . " " . $car['model']); ?>
                                    </strong>
                                    <span><?php echo htmlspecialchars($car['fuel']); ?></span>
                                </td>

                                <td><?php echo htmlspecialchars($car['type']); ?></td>

                                <td><?php echo htmlspecialchars($car['year']); ?></td>

                                <td>€<?php echo number_format($car['price']); ?></td>

                                <td>
                                    <?php if ($car['status'] === 'active'): ?>
                                        <span class="status active-status">Aktive</span>
                                    <?php else: ?>
                                        <span class="status inactive-status">Joaktive</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="action-buttons">
                                        <?php if ($car['status'] === 'active'): ?>
                                            <a href="admin.php?deactivate=<?php echo $index; ?>" class="action-btn deactivate-btn">
                                                Çaktivizo
                                            </a>
                                        <?php else: ?>
                                            <a href="admin.php?activate=<?php echo $index; ?>" class="action-btn activate-btn">
                                                Aktivizo
                                            </a>
                                        <?php endif; ?>

                                        <a href="admin.php?delete=<?php echo $index; ?>" class="action-btn delete-btn" onclick="return confirm('A je i sigurt që dëshiron ta fshish këtë veturë?');">
                                            Fshij
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

    </div>
</section>

<?php include 'Includes/footer.php'; ?>

</body>
</html>