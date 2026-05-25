document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".contact-form-box form");

    if (!form) {
        return;
    }

    form.addEventListener("submit", function (e) {
        const name = form.querySelector('input[name="name"]').value.trim();
        const email = form.querySelector('input[name="email"]').value.trim();
        const subject = form.querySelector('input[name="subject"]').value.trim();
        const message = form.querySelector('textarea[name="message"]').value.trim();

        const nameRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,70}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        let errorMsg = "";

        if (!nameRegex.test(name)) {
            errorMsg = "Emri duhet të ketë 2-70 karaktere dhe vetëm shkronja.";
        } else if (!emailRegex.test(email)) {
            errorMsg = "Email-i nuk është valid.";
        } else if (subject.length < 3 || subject.length > 120) {
            errorMsg = "Subject duhet të ketë 3-120 karaktere.";
        } else if (message.length < 10 || message.length > 2000) {
            errorMsg = "Mesazhi duhet të ketë 10-2000 karaktere.";
        }

        if (errorMsg !== "") {
            e.preventDefault();

            const existingError = document.querySelector(".alert.error");
            if (existingError) {
                existingError.remove();
            }

            const errorDiv = document.createElement("div");
            errorDiv.className = "alert error";
            errorDiv.innerText = errorMsg;

            form.prepend(errorDiv);
        }
    });
});
