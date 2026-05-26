<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config.php";

$categories = ["Sports Car", "Luxury", "Classic", "Electric", "SUV"];
$error = "";

// Kontrollon a eshte useri logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (!function_exists('e')) {
    function e($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

// Kontrollon a eshte admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Merr user_id nga session ose nga tabela users
function getCurrentUserId($pdo) {
    if (isset($_SESSION['user_id'])) {
        return (int)$_SESSION['user_id'];
    }

    if (!isset($_SESSION['user'])) {
        return null;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$_SESSION['user']]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = (int)$user['id'];
        return (int)$user['id'];
    }

    return null;
}

// Validimi i te dhenave
function validatePost($title, $content, $category, $categories) {
    if (trim($title) === "" || strlen(trim($title)) < 3) {
        return "Titulli duhet te kete se paku 3 karaktere.";
    }

    if (trim($content) === "" || strlen(trim($content)) < 10) {
        return "Permbajtja duhet te kete se paku 10 karaktere.";
    }

    if (!in_array($category, $categories, true)) {
        return "Kategoria nuk eshte valide.";
    }

    return "";
}

// Upload i fotos
function uploadImage() {
    if (empty($_FILES['image']['name'])) {
        return null;
    }

    $targetDir = "uploads/blog/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ["jpg", "jpeg", "png", "gif", "webp"];
    $fileName = basename($_FILES['image']['name']);
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    if ($_FILES['image']['size'] > 3 * 1024 * 1024) {
        return null;
    }

    $newName = time() . "_" . rand(1000, 9999) . "." . $ext;
    $targetFile = $targetDir . $newName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        return $targetFile;
    }

    return null;
}

$userId = getCurrentUserId($pdo);

// ADD POST
if (isset($_POST['add_post']) && isAdmin()) {
    $title = trim($_POST['title'] ?? "");
    $content = trim($_POST['content'] ?? "");
    $category = $_POST['category'] ?? "";

    $error = validatePost($title, $content, $category, $categories);

    if ($error === "" && $userId !== null) {
        try {
            $imagePath = uploadImage();

            $stmt = $pdo->prepare(
                "INSERT INTO posts (user_id, title, category, content, image)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->execute([$userId, $title, $category, $content, $imagePath]);

            header("Location: blog.php");
            exit();
        } catch (PDOException $e) {
            $error = "Postimi nuk u shtua.";
        }
    }
}

// EDIT POST
if (isset($_POST['edit_post']) && isAdmin()) {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? "");
    $content = trim($_POST['content'] ?? "");
    $category = $_POST['category'] ?? "";

    $error = validatePost($title, $content, $category, $categories);

    if ($error === "" && $id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $oldPost = $stmt->fetch();

            if ($oldPost) {
                $newImage = uploadImage();

                if ($newImage !== null) {
                    if (!empty($oldPost['image']) && file_exists($oldPost['image'])) {
                        unlink($oldPost['image']);
                    }

                    $stmt = $pdo->prepare(
                        "UPDATE posts 
                         SET title = ?, category = ?, content = ?, image = ?
                         WHERE id = ?"
                    );

                    $stmt->execute([$title, $category, $content, $newImage, $id]);
                } else {
                    $stmt = $pdo->prepare(
                        "UPDATE posts 
                         SET title = ?, category = ?, content = ?
                         WHERE id = ?"
                    );

                    $stmt->execute([$title, $category, $content, $id]);
                }
            }

            header("Location: blog.php");
            exit();
        } catch (PDOException $e) {
            $error = "Postimi nuk u perditesua.";
        }
    }
}

// DELETE POST
if (isset($_POST['delete_post']) && isAdmin()) {
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $oldPost = $stmt->fetch();

            if ($oldPost) {
                if (!empty($oldPost['image']) && file_exists($oldPost['image'])) {
                    unlink($oldPost['image']);
                }

                $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->execute([$id]);
            }

            header("Location: blog.php");
            exit();
        } catch (PDOException $e) {
            $error = "Postimi nuk u fshi.";
        }
    }
}

// READ POSTS
try {
    $stmt = $pdo->prepare(
        "SELECT posts.*, users.username AS author
         FROM posts
         LEFT JOIN users ON posts.user_id = users.id
         ORDER BY posts.created_at DESC"
    );

    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    $posts = [];
    $error = "Postimet nuk mund te lexohen.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog</title>
    <link rel="stylesheet" href="Style/style.css?v=3">
    <link rel="stylesheet" href="Style/blog.css">
</head>
<body>

<?php include "Includes/header.php"; ?>

<main class="blog-page">
<div class="blog-container">

    <section class="blog-hero">
        <span class="blog-kicker">Car Marketplace</span>
        <h1>Blog</h1>
        <p>Read the latest posts, guides, and marketplace updates.</p>
    </section>

    <p class="blog-user">
        Signed in as <strong><?= e($_SESSION['user']) ?></strong>
    </p>

    <?php if ($error !== ""): ?>
        <p class="error-message"><?= e($error) ?></p>
    <?php endif; ?>

    <?php if (isAdmin()): ?>
        <div class="post-form-box">
            <h2>Add New Post</h2>

            <form method="POST" enctype="multipart/form-data">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>

                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= e($cat) ?>"><?= e($cat) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="content">Content:</label>
                <textarea id="content" name="content" required></textarea>

                <label for="image">Image:</label>
                <input type="file" id="image" name="image" accept="image/*">

                <button type="submit" name="add_post">Add Post</button>
            </form>
        </div>
    <?php endif; ?>

    <div class="search-box">
        <input type="text" id="searchInput" placeholder="Search posts...">
    </div>

    <h2 class="section-heading">All Posts</h2>

    <div id="postsGrid">
        <?php if (count($posts) === 0): ?>
            <p class="empty-state">No posts found.</p>
        <?php endif; ?>

        <?php foreach ($posts as $post): ?>
            <div class="post-card card" id="post-<?= (int)$post['id'] ?>">

                <?php if (!empty($post['image'])): ?>
                    <img src="<?= e($post['image']) ?>" alt="Post image" class="post-img">
                <?php else: ?>
                    <div class="no-img">No Image</div>
                <?php endif; ?>

                <?php if (isset($_GET['edit']) && (int)$_GET['edit'] === (int)$post['id'] && isAdmin()): ?>

                    <div class="card-body">
                    <h3>Edit Post</h3>

                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">

                        <label for="edit-title-<?= (int)$post['id'] ?>">Title:</label>
                        <input type="text" id="edit-title-<?= (int)$post['id'] ?>" name="title" value="<?= e($post['title']) ?>" required>

                        <label for="edit-category-<?= (int)$post['id'] ?>">Category:</label>
                        <select id="edit-category-<?= (int)$post['id'] ?>" name="category" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= e($cat) ?>" 
                                    <?= ($post['category'] === $cat) ? "selected" : "" ?>>
                                    <?= e($cat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <label for="edit-content-<?= (int)$post['id'] ?>">Content:</label>
                        <textarea id="edit-content-<?= (int)$post['id'] ?>" name="content" required><?= e($post['content']) ?></textarea>

                        <label for="edit-image-<?= (int)$post['id'] ?>">Change Image:</label>
                        <input type="file" id="edit-image-<?= (int)$post['id'] ?>" name="image" accept="image/*">

                        <button type="submit" name="edit_post">Save</button>
                        <a class="cancel-link" href="blog.php">Cancel</a>
                    </form>
                    </div>

                <?php else: ?>

                    <div class="card-body">
                    <span class="tag"><?= e($post['category']) ?></span>
                    <h3><?= e($post['title']) ?></h3>

                    <p><?= e($post['content']) ?></p>

                    <div class="card-footer">
                        <span class="author"><?= e($post['author'] ?? "Unknown") ?></span>
                        <span class="date"><?= e(date("d M Y", strtotime($post['created_at']))) ?></span>
                    </div>

                    <?php if (isAdmin()): ?>
                        <div class="post-actions">
                            <a href="blog.php?edit=<?= (int)$post['id'] ?>">Edit</a>
                            <form method="POST" onsubmit="return confirm('Delete this post?');">
                                <input type="hidden" name="id" value="<?= (int)$post['id'] ?>">
                                <button type="submit" name="delete_post" class="ajax-delete" data-id="<?= (int)$post['id'] ?>">Delete</button>
                            </form>
                        </div>
                    <?php endif; ?>
                    </div>

                <?php endif; ?>

            </div>
        <?php endforeach; ?>
    </div>

</div>
</main>

<script src="Script/blog.js"></script>

<?php include "Includes/footer.php"; ?>

</body>
</html>
