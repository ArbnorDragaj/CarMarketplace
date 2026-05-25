<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config.php";

$site_name = "CarMarketPlace";
$error = "";
$success = "";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION['role'] ?? '') !== "admin") {
    header("Location: index.php");
    exit();
}

function uploadCarImage($file, &$error) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $error = "Ju lutem ngarkoni një foto për veturën.";
        return null;
    }

    $uploadDir = __DIR__ . "/uploads/cars/";
    $publicDir = "uploads/cars/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = $file['name'];
    $fileTmp = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($fileExt, $allowedExtensions, true)) {
        $error = "Lejohen vetëm fotot JPG, JPEG, PNG ose WEBP.";
        return null;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $error = "Foto është shumë e madhe. Maksimumi 5MB.";
        return null;
    }

    $newFileName = uniqid("car_", true) . "." . $fileExt;
    $destination = $uploadDir . $newFileName;

    if (!move_uploaded_file($fileTmp, $destination)) {
        $error = "Foto nuk mund të ruhet. Provoni përsëri.";
        return null;
    }

    return $publicDir . $newFileName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_car_status'])) {
    $carId = filter_input(INPUT_POST, 'car_id', FILTER_VALIDATE_INT);

    if (!$carId) {
        $error = "Vetura e zgjedhur nuk eshte valide.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT status FROM cars WHERE id = ? LIMIT 1");
            $stmt->execute([$carId]);
            $car = $stmt->fetch();

            if (!$car) {
                $error = "Vetura nuk u gjet.";
            } else {
                $newStatus = $car['status'] === 'active' ? 'inactive' : 'active';
                $stmt = $pdo->prepare("UPDATE cars SET status = ? WHERE id = ?");
                $stmt->execute([$newStatus, $carId]);

                header("Location: admin.php?success=status");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Statusi i vetures nuk mund te ndryshohet.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_car'])) {
    $carId = filter_input(INPUT_POST, 'car_id', FILTER_VALIDATE_INT);

    if (!$carId) {
        $error = "Vetura e zgjedhur nuk eshte valide.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT image FROM cars WHERE id = ? LIMIT 1");
            $stmt->execute([$carId]);
            $car = $stmt->fetch();

            if (!$car) {
                $error = "Vetura nuk u gjet.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
                $stmt->execute([$carId]);

                if (!empty($car['image']) && strpos($car['image'], 'uploads/cars/') === 0) {
                    $imagePath = __DIR__ . "/" . $car['image'];
                    if (is_file($imagePath)) {
                        unlink($imagePath);
                    }
                }

                header("Location: admin.php?success=deleted");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Vetura nuk mund te fshihet.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_car'])) {
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $fuel = trim($_POST['fuel'] ?? '');
    $bodyType = trim($_POST['type'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $price = trim($_POST['price'] ?? '');

    if ($brand === '' || $model === '' || $fuel === '' || $bodyType === '' || $year === '' || $price === '') {
        $error = "Ju lutem plotësoni të gjitha fushat.";
    } elseif (!filter_var($year, FILTER_VALIDATE_INT) || (int)$year < 1900 || (int)$year > ((int)date('Y') + 1)) {
        $error = "Viti i veturës nuk është valid.";
    } elseif (!is_numeric($price) || (float)$price <= 0) {
        $error = "Çmimi duhet të jetë numër pozitiv.";
    } else {
        $imagePath = uploadCarImage($_FILES['car_image'] ?? null, $error);

        if ($imagePath !== null) {
            try {
                $stmt = $pdo->prepare("\n                    INSERT INTO cars (user_id, brand, model, year, price, fuel, body_type, image, status)\n                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')\n                ");

                $stmt->execute([
                    $_SESSION['user_id'] ?? null,
                    $brand,
                    $model,
                    (int)$year,
                    (float)$price,
                    $fuel,
                    $bodyType,
                    $imagePath,
                ]);

                header("Location: admin.php?success=added");
                exit();
            } catch (PDOException $e) {
                $error = "Vetura nuk mund të ruhet në databazë.";
            }
        }
    }
}

if (isset($_GET['success']) && $_GET['success'] === 'added') {
    $success = "Vetura u shtua me sukses.";
} elseif (isset($_GET['success']) && $_GET['success'] === 'status') {
    $success = "Statusi i vetures u ndryshua me sukses.";
} elseif (isset($_GET['success']) && $_GET['success'] === 'deleted') {
    $success = "Vetura u fshi me sukses.";
}

try {
    $totalCars = (int)$pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
    $activeCars = (int)$pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'active'")->fetchColumn();
    $inactiveCars = (int)$pdo->query("SELECT COUNT(*) FROM cars WHERE status = 'inactive'")->fetchColumn();

    $stmt = $pdo->prepare("SELECT id, brand, model, fuel, body_type, year, price, image, status FROM cars ORDER BY id DESC");
    $stmt->execute();
    $cars = $stmt->fetchAll();
} catch (PDOException $e) {
    $cars = [];
    $totalCars = 0;
    $activeCars = 0;
    $inactiveCars = 0;
    $error = "Nuk mund të lexohen veturat nga databaza.";
}
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

        <?php if (!empty($error)): ?>
            <div class="admin-message error-message"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="admin-message success-message"><?php echo e($success); ?></div>
        <?php endif; ?>

        <div class="admin-stats">
            <div class="stat-card">
                <span>Total vetura</span>
                <h3 id="totalCars"><?php echo e($totalCars); ?></h3>
            </div>

            <div class="stat-card">
                <span>Veturat aktive</span>
                <h3 id="activeCars"><?php echo e($activeCars); ?></h3>
            </div>

            <div class="stat-card">
                <span>Joaktive</span>
                <h3 id="inactiveCars"><?php echo e($inactiveCars); ?></h3>
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
                        <input type="number" name="year" placeholder="Viti p.sh. 2023" min="1900" max="<?php echo date('Y') + 1; ?>" required>
                        <input type="number" name="price" placeholder="Çmimi p.sh. 85000" min="1" step="0.01" required>
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
                    Fotot ruhen automatikisht në folderin <strong>uploads/cars/</strong> dhe të dhënat ruhen në MySQL.
                </p>
            </div>

        </div>

        <div class="cars-table-card">
            <div class="table-header">
                <h2>Veturat në sistem</h2>
                <p>Këtu shfaqen të gjitha veturat aktive dhe joaktive nga databaza.</p>
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
                        <?php if (empty($cars)): ?>
                            <tr>
                                <td colspan="7">Nuk ka vetura të regjistruara.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($cars as $car): ?>
                            <tr id="car-row-<?php echo e($car['id']); ?>">
                                <td>
                                    <img src="<?php echo e($car['image'] ?: 'img/default-car.jpg'); ?>" alt="Car" class="car-admin-img">
                                </td>

                                <td>
                                    <strong>
                                        <?php echo e($car['brand'] . " " . $car['model']); ?>
                                    </strong>
                                    <span><?php echo e($car['fuel']); ?></span>
                                </td>

                                <td><?php echo e($car['body_type']); ?></td>

                                <td><?php echo e($car['year']); ?></td>

                                <td>€<?php echo e(number_format((float)$car['price'], 0)); ?></td>

                                <td>
                                    <span id="status-<?php echo e($car['id']); ?>" class="status <?php echo $car['status'] === 'active' ? 'active-status' : 'inactive-status'; ?>">
                                        <?php echo $car['status'] === 'active' ? 'Aktive' : 'Joaktive'; ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="action-buttons">
                                        <form method="POST" action="admin.php" style="display:inline;">
                                            <input type="hidden" name="car_id" value="<?php echo e($car['id']); ?>">
                                        <button
                                            type="submit"
                                            name="toggle_car_status"
                                            class="action-btn <?php echo $car['status'] === 'active' ? 'deactivate-btn' : 'activate-btn'; ?> toggle-status-btn"
                                            data-id="<?php echo e($car['id']); ?>"
                                            data-status="<?php echo e($car['status']); ?>"
                                        >
                                            <?php echo $car['status'] === 'active' ? 'Çaktivizo' : 'Aktivizo'; ?>
                                        </button>
                                        </form>

                                        <form method="POST" action="admin.php" style="display:inline;" onsubmit="return confirm('A jeni i sigurt qe doni ta fshini kete veture?');">
                                            <input type="hidden" name="car_id" value="<?php echo e($car['id']); ?>">
                                        <button
                                            type="submit"
                                            name="delete_car"
                                            class="action-btn delete-btn delete-car-btn"
                                            data-id="<?php echo e($car['id']); ?>"
                                        >
                                            Fshij
                                        </button>
                                        </form>
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
