// Load posts
function loadPosts() {
    fetch("blog.php?get_posts=1")
        .then(res => res.json())
        .then(data => renderPosts(data));
}

// Render posts
function renderPosts(posts) {
    const container = document.getElementById("posts");
    container.innerHTML = "";

    posts.forEach((post, index) => {
        container.innerHTML += `
            <div style="border:1px solid #ccc; margin:10px; padding:10px;">
                <h3>${post.title}</h3>
                <p>${post.content}</p>
                <small>${post.date}</small>
                ${role === "admin" ? `<br><button onclick="deletePost(${index})">Delete</button>` : ""}
            </div>
        `;
    });
}

// Add post
function addPost() {
    const title = document.getElementById("title").value;
    const content = document.getElementById("content").value;

    fetch("blog.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `action=add&title=${encodeURIComponent(title)}&content=${encodeURIComponent(content)}`
    })
    .then(res => res.json())
    .then(data => {
        renderPosts(data);
    });
}

// Delete post
function deletePost(id) {
    fetch("blog.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `action=delete&id=${id}`
    })
    .then(res => res.json())
    .then(data => {
        renderPosts(data);
    });
}
// Delete confirm
document.querySelectorAll(".delete").forEach(btn => {
    btn.addEventListener("click", function(e) {
        if (!confirm("A je i sigurt që don me fshi këtë post?")) {
            e.preventDefault();
        }
    });
});

document.querySelectorAll(".delete").forEach(btn => {
    btn.addEventListener("click", function(e) {
        if (!confirm("A je i sigurt që don me fshi këtë post?")) {
            e.preventDefault();
        }
    });
});

// Init
loadPosts();