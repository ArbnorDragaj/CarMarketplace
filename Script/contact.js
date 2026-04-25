document.getElementById("contactForm").addEventListener("submit", function(e){

    let name = document.querySelector("input[name='name']").value.trim();
    let email = document.querySelector("input[name='email']").value.trim();
    let phone = document.querySelector("input[name='phone']").value.trim();
    let message = document.querySelector("textarea[name='message']").value.trim();

    if(name === "" || email === "" || phone === "" || message === ""){
        alert("Ju lutem plotësoni të gjitha fushat!");
        e.preventDefault();
        return;
    }

    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    if(!email.match(emailPattern)){
        alert("Email i pavlefshëm!");
        e.preventDefault();
        return;
    }

    let phonePattern = /^\+383\d{8,9}$/;
    if(!phone.match(phonePattern)){
        alert("Numër telefoni i pavlefshëm! (+383...)");
        e.preventDefault();
        return;
    }
});