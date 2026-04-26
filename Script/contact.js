document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        let name = form.querySelector('input[name="name"]').value.trim();
        let email = form.querySelector('input[name="email"]').value.trim();
        let subject = form.querySelector('input[name="subject"]').value.trim();
        let message = form.querySelector('textarea[name="message"]').value.trim();

        let nameRegex = /^[a-zA-ZëËçÇ\s]{2,50}$/;
        let emailRegex = /^[\w\.-]+@[\w\.-]+\.[a-zA-Z]{2,}$/;

        let errorMsg = "";

        if (!nameRegex.test(name)) {
            errorMsg = "Emri nuk është valid!";
        } else if (!emailRegex.test(email)) {
            errorMsg = "Email-i nuk është valid!";
        } else if (subject.length < 3) {
            errorMsg = "Subject duhet të ketë së paku 3 karaktere!";
        } else if (message.length < 10) {
            errorMsg = "Mesazhi duhet të ketë së paku 10 karaktere!";
        }

        if (errorMsg !== "") {
            e.preventDefault();

            let existingError = document.querySelector(".alert.error");
            if (existingError) existingError.remove();

            let errorDiv = document.createElement("div");
            errorDiv.className = "alert error";
            errorDiv.innerText = errorMsg;

            form.prepend(errorDiv);
        }
    });
});