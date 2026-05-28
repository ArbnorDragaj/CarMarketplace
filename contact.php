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


    // Nese projekti ka PHPMailer te instaluar, perdoret SMTP.
    if (!empty($smtp['enabled']) && is_file($autoloadPath)) {
        try {
            require_once $autoloadPath;

            if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);

                $mail->isSMTP();
                $mail->Host = $smtp['host'] ?? '';
                $mail->SMTPAuth = true;
                $mail->Username = $smtp['username'] ?? '';
                $mail->Password = $smtp['password'] ?? '';
                $mail->Port = (int)($smtp['port'] ?? 587);

                $mail->SMTPSecure = ($smtp['encryption'] ?? 'tls') === 'ssl'
                    ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                    : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

                $mail->CharSet = 'UTF-8';
                $mail->setFrom($fromEmail, $mailConfig['from_name'] ?? 'CarMarketplace Website');
                $mail->addAddress($toEmail, $mailConfig['to_name'] ?? 'CarMarketplace Team');
                $mail->addReplyTo($email, $name);
                $mail->Subject = $subjectLine;
                $mail->Body = $body;

                return $mail->send();
            }
        } catch (Throwable $e) {
            return false;
        }
    }

    // Fallback me funksionin standard mail().
    // Ne XAMPP zakonisht kerkon konfigurim SMTP.
    $headers = [
        "MIME-Version: 1.0",
        "Content-Type: text/plain; charset=UTF-8",
        "From: " . cleanHeaderValue($mailConfig['from_name'] ?? 'CarMarketplace Website') . " <" . $fromEmail . ">",
        "Reply-To: " . cleanHeaderValue($name) . " <" . $email . ">"
    ];

    return @mail($toEmail, $subjectLine, $body, implode("\r\n", $headers));
}

function validateContactForm(string $name, string $email, string $subject, string $message): string {
    $nameRegex = "/^[\p{L}\s'\-]{2,70}$/u";

    if (!preg_match($nameRegex, $name)) {
        return "Emri duhet te kete 2-70 karaktere dhe te permbaje vetem shkronja.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Email-i nuk eshte valid.";
    }

    if (strlen($subject) < 3 || strlen($subject) > 120) {
        return "Subject duhet te kete 3-120 karaktere.";
    }

    if (strlen($message) < 10 || strlen($message) > 2000) {
        return "Mesazhi duhet te kete 10-2000 karaktere.";
    }

    return "";
}


try {
    ensureContactMessagesTable($pdo);
} catch (PDOException $e) {
    $error = "Nuk mund te pergatitet tabela e mesazheve. Provoni perseri me vone.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $token = $_POST['csrf_token'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!hash_equals($_SESSION['contact_csrf_token'], $token)) {
        $error = "Kerkesa nuk eshte valide. Rifresko faqen dhe provo perseri.";
    } else {
        $error = validateContactForm($name, $email, $subject, $message);
    }

    if ($error === "") {
        try {
            $emailWasSent = sendContactEmail($name, $email, $subject, $message);
            $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

            $stmt = $pdo->prepare(
                "INSERT INTO contact_messages (user_id, name, email, subject, message, email_sent)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $userId,
                $name,
                $email,
                $subject,
                $message,
                $emailWasSent ? 1 : 0
            ]);

            setcookie("contact_name", $name, time() + (86400 * 30), "/");
            setcookie("contact_email", $email, time() + (86400 * 30), "/");

            $_SESSION['contact_csrf_token'] = bin2hex(random_bytes(32));

            $success = $emailWasSent
                ? "Mesazhi u ruajt ne databaze dhe email-i u dergua me sukses."
                : "Mesazhi u ruajt ne databaze. Per dergim real email-i, konfiguroni SMTP/mail ne XAMPP.";

            $subject = "";
            $message = "";
        } catch (PDOException $e) {
            $error = "Ndodhi nje gabim gjate ruajtjes se mesazhit. Provoni perseri.";
        } catch (Throwable $e) {
            $error = "Ndodhi nje gabim i papritur. Provoni perseri.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt - CarMarketPlace</title>
    <link rel="stylesheet" href="Style/style.css?v=3">
    <link rel="stylesheet" href="Style/contact.css?v=4">
    <script src="Script/contact.js?v=4" defer></script>
</head>
<body>
    

<?php require "Includes/header.php"; ?>

<section class="contact-hero">
    <div class="contact-hero-text">
        <span>WE'D LOVE TO HEAR FROM YOU</span>
        <h1>Contact Us</h1>
        <p>Have a question or need help? Fill out the form and our team will get back to you as soon as possible.</p>
    </div>
</section>

<section class="contact-section">
    <div class="contact-form-box">
        <h2>Send Us a Message</h2>
        <p class="form-note">Your message will be saved securely and sent to our team by email.</p>

        <?php if ($error): ?>
            <div class="alert error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success"><?php echo e($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="contact.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo e($_SESSION['contact_csrf_token']); ?>">

            <label for="name">Full Name</label>
            <input id="name" type="text" name="name" placeholder="Your name"
                   value="<?php echo e($name); ?>" maxlength="70" required>

            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" placeholder="Your email"
                   value="<?php echo e($email); ?>" maxlength="160" required>

            <label for="subject">Subject</label>
            <input id="subject" type="text" name="subject" placeholder="How can we help?"
                   value="<?php echo e($subject); ?>" maxlength="120" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" placeholder="Write your message here..." maxlength="2000" required><?php echo e($message); ?></textarea>

            <button type="submit" name="send_message">Send Message</button>
        </form>
    </div>

    <div class="contact-info-box">
        <h2>Get in Touch</h2>

        <div class="info-item">
            <div class="icon">AD</div>
            <div>
                <h3>Address</h3>
                <p>Rr. Skenderbeu, Prishtine, Kosove</p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon">PH</div>
            <div>
                <h3>Phone</h3>
                <p>+383 44 123 456</p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon">@</div>
            <div>
                <h3>Email</h3>
                <p>info@carmarketplace.com</p>
            </div>
        </div>

        <div class="info-item">
            <div class="icon">HR</div>
            <div>
                <h3>Working Hours</h3>
                <p>Mon - Fri: 09:00 - 18:00</p>
                <p>Sat: 10:00 - 15:00</p>
            </div>
        </div>
    </div>
</section>

<section class="contact-cta">
    <div>
        <h2>Looking for your dream car?</h2>
        <p>Check out our latest listings.</p>
    </div>
    <a href="models.php">Browse Cars</a>
</section>

<?php require "Includes/footer.php"; ?>

</body>
</html>
