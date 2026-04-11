<?php
include 'includes/header.php';
include 'includes/navbar.php';

session_start();

// Protect page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Init articles
if (!isset($_SESSION['articles'])) {
    $_SESSION['articles'] = [];
}

// ADD ARTICLE
if (isset($_POST['new_article'])) {
    // add article logic
    header("Location: blog.php");
    exit;
}

// DELETE ARTICLE
if (isset($_POST['delete_article'])) {
    // delete logic
    header("Location: blog.php");
    exit;
}

// EDIT ARTICLE
if (isset($_POST['edit_article'])) {
    // edit logic
    header("Location: blog.php");
    exit;
}

// LIKE ARTICLE
if (isset($_POST['like_article'])) {
    // like logic
    header("Location: blog.php");
    exit;
}

// COMMENT ARTICLE
if (isset($_POST['comment_article'])) {
    // comment logic
    header("Location: blog.php");
    exit;
}
?>

<section class="articles">

<h2>Blog</h2>

<!-- ADD ARTICLE FORM -->
<form method="post">
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="content" placeholder="Content..." required></textarea>
    <button name="new_article">Post</button>
</form>

<!-- ARTICLES LIST -->
<div class="articles-container">

<?php foreach ($_SESSION['articles'] as $article): ?>

    <div class="article-card">

        <h3><?= $article['title'] ?? '' ?></h3>
        <p><?= $article['content'] ?? '' ?></p>

        <!-- LIKE -->
        <form method="post">
            <button name="like_article">Like</button>
        </form>

        <!-- DELETE -->
        <form method="post">
            <button name="delete_article">Delete</button>
        </form>

        <!-- EDIT -->
        <form method="post">
            <button name="edit_article">Edit</button>
        </form>

        <!-- COMMENTS -->
        <div class="comments">
            <form method="post">
                <textarea name="comment_content" placeholder="Comment"></textarea>
                <button name="comment_article">Send</button>
            </form>
        </div>

    </div>

<?php endforeach; ?>

</div>
</section>

<script>
function toggleComment(id){
    let el = document.getElementById(id);
    el.style.display = el.style.display === 'block' ? 'none' : 'block';
}
</script>

<?php include 'includes/footer.php'; ?>
