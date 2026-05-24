<?php
session_start();
require_once "config/db.php";

$categories = ["Sports Car", "Luxury", "Classic", "Electric", "SUV"];
$erorr="";

//ketu kontrollojm se a osht useri loged in

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

//krijojm ni funksion i cili na mbron nga XSS

function e($value){
    return htmlspecialchars((string)$value , ENT_QUOTES ,'UTF-8');
}

//kontrollojm a eshte useri admin

function isAdmin(){
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
if (isset($_POST['add_post']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $imagePath = uploadImage();

    $posts[] = [
        "title" => $_POST['title'],
        "content" => $_POST['content'],
        "category" => $_POST['category'],
        "image" => $imagePath,
        "date" => date("d M Y"),
        "author" => $_SESSION['user']
    ];

    file_put_contents("posts.json", json_encode($posts, JSON_PRETTY_PRINT));
    header("Location: blog.php");
    exit();
}

if (isset($_GET['delete']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $id = $_GET['delete'];

    if (isset($posts[$id])) {
        if (!empty($posts[$id]['image']) && file_exists($posts[$id]['image'])) {
            unlink($posts[$id]['image']);
        }

        unset($posts[$id]);
        $posts = array_values($posts);
        file_put_contents("posts.json", json_encode($posts, JSON_PRETTY_PRINT));
    }

    header("Location: blog.php");
    exit();
}

if (isset($_POST['edit_post']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $id = $_POST['id'];

    if (isset($posts[$id])) {
        $posts[$id]['title'] = $_POST['title'];
        $posts[$id]['content'] = $_POST['content'];
        $posts[$id]['category'] = $_POST['category'];

        $newImage = uploadImage();
        if ($newImage !== "") {
            $posts[$id]['image'] = $newImage;
        }

        file_put_contents("posts.json", json_encode($posts, JSON_PRETTY_PRINT));
    }

    header("Location: blog.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog Spot</title>
    <link rel="stylesheet" href="Style/blog.css">
    <link rel="stylesheet" href="Style/style.css">

    <?php include "Includes/header.php"; ?>
</head>
<body>

<?php if (!isset($_SESSION['user'])): ?>

<div class="login-page">
    <div class="login-box">
        <h1>Blog Spot</h1>
        <p>Welcome back. Please login to continue.</p>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button name="login">Login</button>
        </form>
    </div>
</div>

<?php else: ?>

<div class="app">

    <nav class="navbar">
    </nav>

    <section class="hero">
        <div>
            <h1>
  Welcome back,
  <span style="color: #e70909;">
    <?php echo htmlspecialchars($_SESSION['user']); ?>
 </span>
</h1>
            <p>Discover stories, ideas and inspiration.</p>
        </div>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            <button class="open-modal" onclick="openModal()">+ Create New Post</button>
        <?php endif; ?>
    </section>

    <main class="layout">

        <section class="content">
            <h2 class="section-title">Latest Posts</h2>

            <div class="grid">
                <?php foreach (array_reverse($posts, true) as $id => $post): ?>
                    <div class="card">

                        <?php if (!empty($post['image'])): ?>
                            <img src="<?php echo htmlspecialchars($post['image']); ?>" class="post-img">
                        <?php else: ?>
                            <div class="no-img">Blog Spot</div>
                        <?php endif; ?>

                        <div class="card-body">

                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $id): ?>

                                <form method="POST" enctype="multipart/form-data" class="edit-form">
                                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                                    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>

                                    <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>

                                    <select name="category">
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?php echo $cat; ?>" <?php echo (($post['category'] ?? '') === $cat) ? 'selected' : ''; ?>>
                                                <?php echo $cat; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <input type="file" name="image">
                                    <button name="edit_post">Save Changes</button>
                                </form>

                            <?php else: ?>

                                <span class="date"><?php echo htmlspecialchars($post['date']); ?></span>

                                <h3><?php echo htmlspecialchars($post['title']); ?></h3>

                                <p><?php echo htmlspecialchars($post['content']); ?></p>

                                <div class="card-footer">
                                    <div class="author">
                                        <span class="avatar">👤</span>
                                        <?php echo htmlspecialchars($post['author'] ?? 'admin'); ?>
                                    </div>

                                    <span class="tag"><?php echo htmlspecialchars($post['category'] ?? 'Life'); ?></span>
                                </div>

                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <div class="actions">
                                        <a href="?edit=<?php echo $id; ?>" class="edit">Edit</a>
                                        <a href="?delete=<?php echo $id; ?>" class="delete" onclick="return confirm('A je i sigurt?')">Delete</a>
                                    </div>
                                <?php endif; ?>

                            <?php endif; ?>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        
        <aside class="sidebar">
            <div class="side-box">
                <input type="text" id="searchInput" placeholder="Search posts...">
            </div>

            <div class="side-box">
                <h3>Categories</h3>
                <?php foreach ($categories as $cat): ?>
                    <div class="cat-row">
                        <span><?php echo $cat; ?></span>
                        <b><?php echo count(array_filter($posts, fn($p) => ($p['category'] ?? '') === $cat)); ?></b>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="side-box">
                <h3>Recent Posts</h3>
                <?php foreach (array_slice(array_reverse($posts), 0, 4) as $post): ?>
                    <div class="recent">
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?php echo htmlspecialchars($post['image']); ?>">
                        <?php endif; ?>
                        <div>
                            <strong><?php echo htmlspecialchars($post['title']); ?></strong>
                            <small><?php echo htmlspecialchars($post['date']); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </aside>

    </main>
</div>

<?php if ($_SESSION['role'] === 'admin'): ?>
<div class="modal" id="postModal">
    <div class="modal-box">
        <button class="close" onclick="closeModal()">×</button>
        <h2>Create New Post</h2>

        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Post title" required>

            <textarea name="content" placeholder="Write your post..." required></textarea>

            <select name="category">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>

            <input type="file" name="image">
            <button name="add_post">Publish Post</button>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="Script/blog.js"></script>
 <?php include "Includes/footer.php"; ?>


<?php endif; ?>

</body>
</html>