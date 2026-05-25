<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config.php";

$site_name = "CarMarketPlace";
$error = "";
$carId = filter_var($_GET['id'] ?? $_POST['car_id'] ?? null, FILTER_VALIDATE_INT);

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION['role'] ?? '') !== "admin") {
    header("Location: index.php");
    exit();
}

if (!$carId) {
    header("Location: admin.php");
    exit();
}

function uploadReplacementCarImage($file, &$error) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Foto nuk mund te ngarkohet.";
        return false;
    }

    $uploadDir = __DIR__ . "/uploads/cars/";
    $publicDir = "uploads/cars/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($fileExt, $allowedExtensions, true)) {
        $error = "Lejohen vetem fotot JPG, JPEG, PNG ose WEBP.";
        return false;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $error = "Foto eshte shume e madhe. Maksimumi 5MB.";
        return false;
    }

    $newFileName = uniqid("car_", true) . "." . $fileExt;
    $destination = $uploadDir . $newFileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $error = "Foto nuk mund te ruhet.";
        return false;
    }

    return $publicDir . $newFileName;
}

function deleteOldUploadedImage($imagePath) {
    $imagePath = trim((string)$imagePath);

    if (strpos($imagePath, 'uploads/cars/') !== 0) {
        return;
    }

    $fullPath = __DIR__ . "/" . $imagePath;

    if (is_file($fullPath)) {
        unlink($fullPath);
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? LIMIT 1");
    $stmt->execute([$carId]);
    $car = $stmt->fetch();
} catch (PDOException $e) {
    $car = null;
    $error = "Vetura nuk mund te lexohet nga databaza.";
}

if (!$car) {
    header("Location: admin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $fuel = trim($_POST['fuel'] ?? '');
    $bodyType = trim($_POST['type'] ?? '');
    $year = trim($_POST['year'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if ($brand === '' || $model === '' || $fuel === '' || $bodyType === '' || $year === '' || $price === '') {
        $error = "Ju lutem plotesoni te gjitha fushat.";
    } elseif (!filter_var($year, FILTER_VALIDATE_INT) || (int)$year < 1900 || (int)$year > ((int)date('Y') + 1)) {
        $error = "Viti i vetures nuk eshte valid.";
    } elseif (!is_numeric($price) || (float)$price <= 0) {
        $error = "Cmimi duhet te jete numer pozitiv.";
    } elseif (!in_array($status, ['active', 'inactive'], true)) {
        $error = "Statusi nuk eshte valid.";
    } else {
        $newImage = uploadReplacementCarImage($_FILES['car_image'] ?? null, $error);

        if ($newImage !== false) {
            try {
                if ($newImage !== null) {
                    $stmt = $pdo->prepare("
                        UPDATE cars
                        SET brand = ?, model = ?, fuel = ?, body_type = ?, year = ?, price = ?, status = ?, image = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$brand, $model, $fuel, $bodyType, (int)$year, (float)$price, $status, $newImage, $carId]);
                    deleteOldUploadedImage($car['image'] ?? '');
                } else {
                    $stmt = $pdo->prepare("
                        UPDATE cars
                        SET brand = ?, model = ?, fuel = ?, body_type = ?, year = ?, price = ?, status = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([$brand, $model, $fuel, $bodyType, (int)$year, (float)$price, $status, $carId]);
                }

                header("Location: admin.php?success=edited");
                exit();
            } catch (PDOException $e) {
                $error = "Vetura nuk mund te perditesohet.";
            }
        }
    }

    $car = array_merge($car, [
        'brand' => $brand,
        'model' => $model,
        'fuel' => $fuel,
        'body_type' => $bodyType,
        'year' => $year,
        'price' => $price,
        'status' => $status
    ]);
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <title>Edito veturen - CarMarketPlace</title>
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
                <h1>Edito veturen</h1>
                <p>Ndrysho te dhenat kryesore te vetures dhe ruaji ne databaze.</p>
            </div>
            <a href="admin.php" class="back-admin-link">Kthehu te admini</a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="admin-message error-message"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="admin-form-card edit-panel">
            <div class="current-car-preview">
                <img src="<?php echo e($car['image'] ?: 'img/default-car.jpg'); ?>" alt="<?php echo e($car['brand'] . ' ' . $car['model']); ?>">
                <div>
                    <h2><?php echo e($car['brand'] . ' ' . $car['model']); ?></h2>
                    <p>Foto aktuale ruhet nese nuk zgjedh foto te re.</p>
                </div>
            </div>

            <form method="POST" class="car-form" enctype="multipart/form-data">
                <input type="hidden" name="car_id" value="<?php echo e($car['id']); ?>">

                <div class="form-row">
                    <input type="text" name="brand" placeholder="Marka" required value="<?php echo e($car['brand']); ?>">
                    <input type="text" name="model" placeholder="Modeli" required value="<?php echo e($car['model']); ?>">
                </div>

                <div class="form-row">
                    <input type="text" name="fuel" placeholder="Karburanti" required value="<?php echo e($car['fuel']); ?>">
                    <input type="text" name="type" placeholder="Tipi" required value="<?php echo e($car['body_type']); ?>">
                </div>

                <div class="form-row">
                    <input type="number" name="year" placeholder="Viti" min="1900" max="<?php echo date('Y') + 1; ?>" required value="<?php echo e($car['year']); ?>">
                    <input type="number" name="price" placeholder="Cmimi" min="1" step="0.01" required value="<?php echo e($car['price']); ?>">
                </div>

                <div class="form-row">
                    <select name="status" required>
                        <option value="active" <?php echo $car['status'] === 'active' ? 'selected' : ''; ?>>Aktive</option>
                        <option value="inactive" <?php echo $car['status'] === 'inactive' ? 'selected' : ''; ?>>Joaktive</option>
                    </select>
                </div>

                <div class="upload-box">
                    <label for="car_image">Foto e re (opsionale)</label>
                    <input type="file" name="car_image" id="car_image" accept="image/*">
                </div>

                <button type="submit">Ruaj ndryshimet</button>
            </form>
        </div>
    </div>
</section>

<?php include 'Includes/footer.php'; ?>

</body>
</html>
