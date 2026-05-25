<?php
// config.php
// Lidhja kryesore me MySQL per CarMarketplace.

$host = "127.0.0.1";
$dbname = "car_marketplace";
$dbUser = "root";
$dbPass = "";
$ports = [3306, 3307];

function createPdoConnection(string $host, int $port, string $user, string $pass): PDO {
    return new PDO(
        "mysql:host=$host;port=$port;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
}

function runSqlFile(PDO $pdo, string $filePath): void {
    if (!is_file($filePath)) {
        throw new RuntimeException("SQL setup file not found.");
    }

    $sql = file_get_contents($filePath);
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}

function tableExists(PDO $pdo, string $dbname, string $tableName): bool {
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?"
    );
    $stmt->execute([$dbname, $tableName]);

    return (int)$stmt->fetchColumn() > 0;
}

function ensureDatabaseIsReady(PDO $pdo, string $dbname): void {
    $safeDbName = str_replace('`', '``', $dbname);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$safeDbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$safeDbName`");

    $requiredTables = ['users', 'cars', 'posts'];
    foreach ($requiredTables as $tableName) {
        if (!tableExists($pdo, $dbname, $tableName)) {
            runSqlFile($pdo, __DIR__ . "/database/car_marketplace.sql");
            $pdo->exec("USE `$safeDbName`");
            return;
        }
    }
}

$pdo = null;

foreach ($ports as $port) {
    try {
        $pdo = createPdoConnection($host, $port, $dbUser, $dbPass);
        ensureDatabaseIsReady($pdo, $dbname);
        break;
    } catch (Throwable $e) {
        $pdo = null;
    }
}

if (!$pdo) {
    die("Gabim ne lidhjen me databazen.");
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
?>
