function openModal() {
  document.getElementById("postModal").classList.add("active");
}

function closeModal() {
  document.getElementById("postModal").classList.remove("active");
}

const searchInput = document.getElementById("searchInput");

if (searchInput) {
  searchInput.addEventListener("keyup", function () {
    const value = this.value.toLowerCase();
    const cards = document.querySelectorAll(".card");

    cards.forEach(card => {
      const text = card.innerText.toLowerCase();

      if (text.includes(value)) {
        card.style.display = "block";
      } else {
        card.style.display = "none";
      }
    });
  });
}

document.querySelectorAll(".ajax-delete").forEach(button => {
  button.addEventListener("click", async function (event) {
    event.preventDefault();

    const form = this.closest("form");
    const card = this.closest(".post-card");

    if (!form || !confirm("Delete this post?")) {
      return;
    }

    try {
      const response = await fetch("ajax/delete_post.php", {
        method: "POST",
        body: new FormData(form),
        headers: {
          "X-Requested-With": "XMLHttpRequest"
        }
      });

      const result = await response.json();

      if (result.success) {
        if (card) {
          card.remove();
        }
      } else {
        alert(result.message || "Post could not be deleted.");
      }
    } catch (error) {
      alert("Post could not be deleted.");
    }
  });
});

window.onclick = function (event) {
  const modal = document.getElementById("postModal");

  if (event.target === modal) {
    closeModal();
  }
};
