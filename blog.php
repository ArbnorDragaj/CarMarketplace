<?php
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

/* =========================
   ADD ARTICLE
========================= */
if (isset($_POST['new_article'])) {
    $_SESSION['articles'][] = [
        "id" => uniqid(),
        "title" => $_POST['title'],
        "content" => $_POST['content'],
        "author" => $_SESSION['user']['username'],
        "likes" => 0,
        "comments" => []
    ];

    header("Location: blog.php");
    exit;
}

/* =========================
   DELETE ARTICLE
========================= */
if (isset($_POST['delete_article'])) {
    foreach ($_SESSION['articles'] as $key => $article) {
        if ($article['id'] == $_POST['article_id']) {
            unset($_SESSION['articles'][$key]);
        }
    }

    header("Location: blog.php");
    exit;
}

/* =========================
   EDIT ARTICLE
========================= */
if (isset($_POST['edit_article'])) {
    foreach ($_SESSION['articles'] as &$article) {
        if ($article['id'] == $_POST['article_id']) {
            $article['title'] = $_POST['title'];
            $article['content'] = $_POST['content'];
        }
    }
    unset($article);

    header("Location: blog.php");
    exit;
}

/* =========================
   LIKE ARTICLE
========================= */
if (isset($_POST['like_article'])) {
    foreach ($_SESSION['articles'] as &$article) {
        if ($article['id'] == $_POST['article_id']) {
            $article['likes']++;
        }
    }
    unset($article);

    header("Location: blog.php");
    exit;
}

/* =========================
   COMMENT ARTICLE
========================= */
if (isset($_POST['comment_article'])) {
    foreach ($_SESSION['articles'] as &$article) {
        if ($article['id'] == $_POST['article_id']) {
            $article['comments'][] = [
                "author" => $_SESSION['user']['username'],
                "content" => $_POST['comment_content']
            ];
        }
    }
    unset($article);

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

        <h3><?= htmlspecialchars($article['title']) ?></h3>
        <p><?= nl2br(htmlspecialchars($article['content'])) ?></p>

        <p><strong>❤️ <?= $article['likes'] ?></strong></p>

        <!-- LIKE -->
        <form method="post">
            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
            <button name="like_article">Like</button>
        </form>

        <!-- DELETE -->
        <?php if ($_SESSION['user']['username'] === $article['author']): ?>
            <form method="post">
                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                <button name="delete_article">Delete</button>
            </form>

            <!-- EDIT -->
            <form method="post">
                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                <input type="text" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
                <textarea name="content" required><?= htmlspecialchars($article['content']) ?></textarea>
                <button name="edit_article">Save</button>
            </form>
        <?php endif; ?>

        <!-- COMMENTS -->
        <div class="comments">

            <?php foreach ($article['comments'] as $c): ?>
                <p>
                    <strong><?= htmlspecialchars($c['author']) ?>:</strong>
                    <?= htmlspecialchars($c['content']) ?>
                </p>
            <?php endforeach; ?>

            <form method="post">
                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                <textarea name="comment_content" placeholder="Comment..." required></textarea>
                <button name="comment_article">Send</button>
            </form>
<p>honflksfno</p>
        </div>

    </div>

<?php endforeach; ?>

</div>
</section>
