<?php
session_start();

/* USERS */
$users = [
    "admin" => ["password" => "1234", "role" => "admin"],
    "arbnor" => ["password" => "1234", "role" => "user"]
];

/* POSTS FILE */
if (!file_exists("posts.json")) {
    file_put_contents("posts.json", json_encode([]));
}

$posts = json_decode(file_get_contents("posts.json"), true);

/* LOGIN */
if (isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];

    if (isset($users[$u]) && $users[$u]['password'] === $p) {
        $_SESSION['user'] = $u;
        $_SESSION['role'] = $users[$u]['role'];
    } else {
        $error = "Login gabim!";
    }
}

/* LOGOUT */
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: blog.php");
    exit();
}

/* shtojm poste */
if (isset($_POST['add_post']) && $_SESSION['role'] === 'admin') {

    $imagePath = "";

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
            $imagePath = $targetFile;
        }
    }

    $posts[] = [
        "title" => $_POST['title'],
        "content" => $_POST['content'],
        "image" => $imagePath,
        "date" => date("d M Y")
    ];

    file_put_contents("posts.json", json_encode($posts));
    header("Location: blog.php");
    exit();
}

/* fshijm poste */
if (isset($_GET['delete']) && $_SESSION['role'] === 'admin') {
    $id = $_GET['delete'];

    if (!empty($posts[$id]['image']) && file_exists($posts[$id]['image'])) {
        unlink($posts[$id]['image']);
    }

    unset($posts[$id]);
    $posts = array_values($posts);

    file_put_contents("posts.json", json_encode($posts));
    header("Location: blog.php");
    exit();
}
/* per me editu poste */
if (isset($_POST['edit_post']) && $_SESSION['role'] === 'admin') {

    $id = $_POST['id'];

    $posts[$id]['title'] = $_POST['title'];
    $posts[$id]['content'] = $_POST['content'];

    if (!empty($_FILES['image']['name'])) {

        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir);

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile);
            $posts[$id]['image'] = $targetFile;
        }
    }

    file_put_contents("posts.json", json_encode($posts));
    header("Location: blog.php");
    exit();
}
?>





<!DOCTYPE html>
<html>
<head>
    <title>Modern Blog</title>
    <link rel="stylesheet" href="Style/blog.css">
</head>
<body>

<div class="container">

<?php if (!isset($_SESSION['user'])): ?>

<div class="login-box">
    <h2>Login</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>
</div>

<?php else: ?>

<div class="top-bar">
    <h2 style="color:crimson;">Blog Spot</h2>
    <div>
        <span><?php echo $_SESSION['user']; ?></span>
        <a href="?logout=true">Logout</a>
    </div>
</div>

<?php if ($_SESSION['role'] === 'admin'): ?>
<div class="form-box">
    <h3>Create Post</h3>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Title" required>
        <textarea name="content" placeholder="Content" required></textarea>
        <input type="file" name="image">
        <button name="add_post">Publish</button>
    </form>
</div>
<?php endif; ?>

<div class="grid">

<?php foreach ($posts as $id => $post): ?>

<div class="card">
<div class="card-body">

<?php if (!empty($post['image'])): ?>
<img src="<?php echo $post['image']; ?>" class="post-img">
<?php endif; ?>

<?php if (isset($_GET['edit']) && $_GET['edit'] == $id): ?>

<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <input type="text" name="title" value="<?php echo $post['title']; ?>">
    <textarea name="content"><?php echo $post['content']; ?></textarea>
    <input type="file" name="image">
    <button name="edit_post">Save</button>
</form>


<?php else: ?>

<h3><?php echo $post['title']; ?></h3>
<p><?php echo $post['content']; ?></p>
<span><?php echo $post['date']; ?></span>

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

<?php endif; ?>

</div>

<script src="blog.js"></script>
</body>
</html>