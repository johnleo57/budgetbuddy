const form = document.querySelector(".login form"),
      continueBtn = form.querySelector(".button input"),
      errorText = form.querySelector(".error-text");

form.onsubmit = (e) => {
    e.preventDefault(); // Prevent the default form submission
}

continueBtn.onclick = () => {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/login.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                let data = xhr.response;
                if (data.includes("success user")) {
                    location.href = "home.php"; // Redirect to user dashboard
                } else if (data.includes("success admin")) {
                    location.href = "admin_dashboard.php"; // Redirect to admin dashboard
                } else {
                    errorText.style.display = "block";
                    errorText.textContent = data; // Display error message
                }
            }
        }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}
