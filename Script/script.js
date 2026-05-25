const headerHTML = `
      <header class="main-header">
    <div class="container">
        <div class="logo">
            <h1><a href="index.php"><?php echo $site_name; ?></a></h1>
        </div>
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Ballina</a></li>
                <li><a href="rreth-nesh.php">Rreth Nesh</a></li>
                <li><a href="models.php">Shërbimet</a></li>
                <li><a href="contact.php">Kontakti</a></li>
                <li><a href="blog.php" class="blog">Blog</a></li>
            </ul>
        </nav>
    </div>
</header>
`;

// 2. Insert the header into any element with id="main-header"
document.addEventListener("DOMContentLoaded", function () {
  const headerContainer = document.getElementById("main-header");
  if (headerContainer) {
    headerContainer.innerHTML = headerHTML;
  }
});