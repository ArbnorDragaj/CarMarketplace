<?php
session_start();
// duhet mu regjistru
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

// posti shabllon
if(!isset($_SESSION['articles'])){
    $_SESSION['articles'] = [
        ["id"=>1,"title"=>"BMW M5 Facts","author"=>"admin","content"=>"BMW M5 ka motor V8...","likes"=>0,"comments"=>[]],
        ["id"=>2,"title"=>"Audi A6 Teknologji","author"=>"user","content"=>"Audi A6 ka sistem infotainment...","likes"=>0,"comments"=>[]]
    ];
}

// Me postu diqka
if(isset($_POST['new_article'])){
    $_SESSION['articles'][] = [
        "id"=>uniqid(),
        "title"=>$_POST['title'],
        "author"=>$_SESSION['user']['username'],
        "content"=>$_POST['content'],
        "likes"=>0,
        "comments"=>[]
    ];
    header("Location: blog.php");
    exit;
}

// me fshi postimin
if(isset($_POST['delete_article'])){
    foreach($_SESSION['articles'] as $key => $article){
        if($article['id'] == $_POST['article_id'] &&
           $_SESSION['user']['username'] === $article['author']){
            unset($_SESSION['articles'][$key]);
        }
    }
    header("Location: blog.php");
    exit;
}

// me ba edit postimin
if(isset($_POST['edit_article'])){
    foreach($_SESSION['articles'] as &$article){
        if($article['id'] == $_POST['article_id'] &&
           $_SESSION['user']['username'] === $article['author']){
            $article['title'] = $_POST['title'];
            $article['content'] = $_POST['content'];
        }
    }
    unset($article);
    header("Location: blog.php");
    exit;
}

// like 
if(isset($_POST['like_article'])){
    foreach($_SESSION['articles'] as &$article){
        if($article['id'] == $_POST['article_id']){
            $article['likes']++;
        }
    }
    unset($article);
    header("Location: blog.php");
    exit;
}

// komentet
if(isset($_POST['comment_article'])){
    foreach($_SESSION['articles'] as &$article){
        if($article['id'] == $_POST['article_id']){
            $article['comments'][] = [
                "author"=>$_SESSION['user']['username'],
                "content"=>$_POST['comment_content']
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

<!--logjia add post -->
<form method="post" class="new-article-form">
    <input type="text" name="title" placeholder="Title" required>
    <textarea name="content" placeholder="Write something..." required></textarea>
    <button name="new_article">Post</button>
</form>

<div class="articles-container">

<?php foreach($_SESSION['articles'] as $article): ?>
<div class="article-card">

    <div class="article-header">
        <h3><?= htmlspecialchars($article['title']) ?></h3>
        <span><?= htmlspecialchars($article['author']) ?></span>
    </div>

    <p><?= nl2br(htmlspecialchars($article['content'])) ?></p>
    <p class="likes">❤️ <?= $article['likes'] ?></p>

    <!-- like -->
    <form method="post">
        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
        <button name="like_article">Like</button>
    </form>

    <!-- ACTIONS -->
    <?php if($_SESSION['user']['username'] === $article['author']): ?>

        <!-- DELETE -->
        <form method="post">
            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
            <button name="delete_article">Delete</button>
        </form>

        <!-- EDIT TOGGLE -->
        <button onclick="toggleEdit('e<?= $article['id'] ?>')">Edit</button>

        <!-- EDIT FORM -->
        <div class="comments" id="e<?= $article['id'] ?>">
            <form method="post">
                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                <input type="text" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
                <textarea name="content" required><?= htmlspecialchars($article['content']) ?></textarea>
                <button name="edit_article">Save</button>
            </form>
        </div>

    <?php endif; ?>

    <!-- COMMENT BUTTON -->
    <button onclick="toggleComment('c<?= $article['id'] ?>')">Comment</button>

    <!-- COMMENTS -->
    <div class="comments" id="c<?= $article['id'] ?>">
        
        <?php foreach($article['comments'] as $c): ?>
            <p><strong><?= htmlspecialchars($c['author']) ?>:</strong> <?= htmlspecialchars($c['content']) ?></p>
        <?php endforeach; ?>

        <form method="post">
            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
            <textarea name="comment_content" placeholder="Comment..." required></textarea>
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
    el.style.display = (el.style.display === 'block') ? 'none' : 'block';
}

function toggleEdit(id){
    let el = document.getElementById(id);
    el.style.display = (el.style.display === 'block') ? 'none' : 'block';
}
</script>

<?php include 'includes/footer.php'; ?>
