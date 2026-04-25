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

window.onclick = function (event) {
  const modal = document.getElementById("postModal");

  if (event.target === modal) {
    closeModal();
  }
};