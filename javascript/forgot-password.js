const form = document.querySelector(".forgot-password form"),
continueBtn = form.querySelector(".button input"),
errorText = form.querySelector(".error-text");

form.onsubmit = (e)=>{
    e.preventDefault();
}

continueBtn.onclick = ()=>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/forgot-password.php", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              let data = xhr.response;
              if(data === "success"){
                errorText.style.display = "block";
                errorText.textContent = "Password reset link sent to your email!";
                errorText.style.color = "green";
              }else{
                  errorText.style.display = "block";
                  errorText.textContent = data;
              }
          }
      }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}