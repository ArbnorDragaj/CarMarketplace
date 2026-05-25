<?php
$site_name = "CarMarketPlace";
$currentPage = "contact.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "config.php";

$name = $_COOKIE['contact_name'] ?? "";
$email = $_COOKIE['contact_email'] ?? "";
$subject = "";
$message = "";
$success = "";
$error = "";

if (empty($_SESSION['contact_csrf_token'])) {
    $_SESSION['contact_csrf_token'] = bin2hex(random_bytes(32));
}

function cleanHeaderValue(string $value): string {
    return trim(preg_replace('/[\r\n]+/', ' ', $value));
}

function ensureContactMessagesTable(PDO $pdo): void {
    $sql = "
        CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(160) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            email_sent TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            CONSTRAINT fk_contact_messages_user
                FOREIGN KEY (user_id) REFERENCES users(id)
                ON DELETE SET NULL
                ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";

    $pdo->exec($sql);
}

function loadMailConfig(): array {
    $defaultConfig = [
        'enabled' => true,
        'to_email' => 'carmarketplace@gmail.com',
        'to_name' => 'CarMarketplace Team',
        'from_email' => 'no-reply@carmarketplace.local',
        'from_name' => 'CarMarketplace Website',
        'smtp' => [
            'enabled' => false,
            'host' => '',
            'username' => '',
            'password' => '',
            'port' => 587,
            'encryption' => 'tls'
        ]
    ];

    $configPath = __DIR__ . "/config/mail.php";
    if (is_file($configPath)) {
        $customConfig = require $configPath;
        if (is_array($customConfig)) {
            $defaultConfig = array_replace_recursive($defaultConfig, $customConfig);
        }
    }

    return $defaultConfig;
}

function buildContactEmailBody(string $name, string $email, string $subject, string $message): string {
    return "Mesazh i ri nga forma Contact - CarMarketplace\n\n" .
        "Emri: " . $name . "\n" .
        "Email: " . $email . "\n" .
        "Subject: " . $subject . "\n\n" .
        "Mesazhi:\n" . $message . "\n\n" .
        "Data: " . date('Y-m-d H:i:s') . "\n";
}

function sendContactEmail(string $name, string $email, string $subject, string $message): bool {
    $mailConfig = loadMailConfig();

    if (empty($mailConfig['enabled'])) {
        return false;
    }

    $toEmail = filter_var($mailConfig['to_email'], FILTER_VALIDATE_EMAIL)
        ? $mailConfig['to_email']
        : 'carmarketplace@gmail.com';

    $fromEmail = filter_var($mailConfig['from_email'], FILTER_VALIDATE_EMAIL)
        ? $mailConfig['from_email']
        : $toEmail;

    $subjectLine = "CarMarketplace Contact: " . cleanHeaderValue($subject);
    $body = buildContactEmailBody($name, $email, $subject, $message);

    $autoloadPath = __DIR__ . "/vendor/autoload.php";
    $smtp = $mailConfig['smtp'] ?? [];
