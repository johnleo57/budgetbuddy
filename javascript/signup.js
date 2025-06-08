document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector(".signup form");
    const continueBtn = form.querySelector(".button input");
    const errorText = form.querySelector(".error-text");
    const termsCheckbox = document.getElementById("agree-terms");
    const passwordInput = form.querySelector('input[name="password"]');
    const otpField = document.querySelector('.field.input');
    const otpInput = document.getElementById('otp');
    
    // Image upload elements
    const imageUpload = document.getElementById('image-upload');
    const selectImageBtn = document.getElementById('select-image-btn');
    const changeImageBtn = document.getElementById('change-image-btn');
    const imagePreview = document.getElementById('image-preview');
    const noImageText = document.getElementById('no-image-text');

    // Password requirements elements
    const passwordRequirements = {
        length: document.getElementById('req-length'),
        uppercase: document.getElementById('req-uppercase'),
        lowercase: document.getElementById('req-lowercase'),
        number: document.getElementById('req-number'),
        special: document.getElementById('req-special')
    };

    // ======================
    // IMAGE UPLOAD HANDLING
    // ======================
    if (imageUpload && selectImageBtn) {
        // Trigger file input when buttons are clicked
        selectImageBtn.addEventListener('click', function() {
            imageUpload.click();
        });
        
        if (changeImageBtn) {
            changeImageBtn.addEventListener('click', function() {
                imageUpload.click();
            });
        }
        
        // Handle image selection and display preview
        imageUpload.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                
                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    errorText.style.display = "block";
                    errorText.textContent = "Please select a valid image file (JPEG, PNG, GIF)";
                    return;
                }
                
                // Validate file size (e.g., 2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    errorText.style.display = "block";
                    errorText.textContent = "Image size must be less than 2MB";
                    return;
                }
                
                const reader = new FileReader();
                
                reader.onload = function(event) {
                    errorText.style.display = "none";
                    imagePreview.src = event.target.result;
                    imagePreview.style.display = 'block';
                    if (noImageText) noImageText.style.display = 'none';
                    if (selectImageBtn) selectImageBtn.style.display = 'none';
                    if (changeImageBtn) changeImageBtn.style.display = 'block';
                };
                
                reader.readAsDataURL(file);
            }
        });
    }

    // Password validation function
    function validatePassword(password) {
        let isValid = true;
        
        // Check length
        if (password.length < 8) {
            passwordRequirements.length.classList.add('invalid');
            isValid = false;
        } else {
            passwordRequirements.length.classList.remove('invalid');
        }
        
        // Check uppercase
        if (!/[A-Z]/.test(password)) {
            passwordRequirements.uppercase.classList.add('invalid');
            isValid = false;
        } else {
            passwordRequirements.uppercase.classList.remove('invalid');
        }
        
        // Check lowercase
        if (!/[a-z]/.test(password)) {
            passwordRequirements.lowercase.classList.add('invalid');
            isValid = false;
        } else {
            passwordRequirements.lowercase.classList.remove('invalid');
        }
        
        // Check number
        if (!/[0-9]/.test(password)) {
            passwordRequirements.number.classList.add('invalid');
            isValid = false;
        } else {
            passwordRequirements.number.classList.remove('invalid');
        }
        
        // Check special character
        if (!/[!@#$%^&*(),.?":{}|<>_-]/.test(password)) {
            passwordRequirements.special.classList.add('invalid');
            isValid = false;
        } else {
            passwordRequirements.special.classList.remove('invalid');
        }
        
        return isValid;
    }

    // Password input event listener
    passwordInput.addEventListener('input', function() {
        validatePassword(this.value);
    });

    // Single unified continueBtn click handler
    continueBtn.onclick = (e) => {
        e.preventDefault();
        
        // Reset error display
        errorText.style.display = "none";
        
        // 1. Check terms agreement
        if (!termsCheckbox.checked) {
            errorText.style.display = "block";
            errorText.textContent = "You must agree to the terms and conditions";
            termsCheckbox.scrollIntoView({ behavior: 'smooth', block: 'center' });
            termsCheckbox.focus();
            return;
        }
        
        // 2. Check OTP verification
        if (!otpField || otpField.style.display !== 'block' || !otpInput.value) {
            errorText.style.display = "block";
            errorText.textContent = "Please verify your email with OTP first";
            if (otpInput) otpInput.focus();
            return;
        }
        
        // 3. Validate password
        if (!validatePassword(passwordInput.value)) {
            errorText.style.display = "block";
            errorText.textContent = "Password doesn't meet all requirements";
            passwordInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            passwordInput.focus();
            return;
        }
        
        // 4. Check if image is selected
        if (imageUpload && imageUpload.files.length === 0) {
            errorText.style.display = "block";
            errorText.textContent = "Please select a profile image";
            if (selectImageBtn) selectImageBtn.scrollIntoView({ behavior: 'smooth', block: 'center' });
            if (selectImageBtn) selectImageBtn.focus();
            return;
        }
        
        // 5. Submit form via AJAX
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "php/signup.php", true);
        
        xhr.onload = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    let data = xhr.responseText.trim();
                    console.log("Server response:", data); // Debugging
                    if (data === "success") {
                        window.location.href = "login.php?signup=success";
                    } else {
                        errorText.style.display = "block";
                        errorText.textContent = data || "Registration failed. Please try again.";
                    }
                } else {
                    errorText.style.display = "block";
                    errorText.textContent = "Server error occurred. Please try again.";
                }
            }
        };
        
        xhr.onerror = function() {
            errorText.style.display = "block";
            errorText.textContent = "Network error. Please check your connection.";
        };
        
        let formData = new FormData(form);
        xhr.send(formData);
    };

    // ======================
    // TERMS MODAL FUNCTIONS
    // ======================
    function openTerms(type) {
        const modal = document.getElementById('termsModal');
        const termsText = document.getElementById('termsText');
        
        if (type === 'user-agreement') {
            termsText.innerHTML = `
                <h2>User Agreement</h2>
                <p><em>Last Updated: ${new Date().toLocaleDateString()}</em></p>
                
                <h3>1. Acceptance of Terms</h3>
                <p>By using this service, you agree to comply with all terms...</p>
                
                <h3>2. User Responsibilities</h3>
                <ul>
                    <li>You must provide accurate information</li>
                    <li>You are responsible for account security</li>
                    <li>No illegal or harmful activities</li>
                    <li>No spamming other users</li>
                </ul>
                
                <h3>3. Content Policy</h3>
                <p>You retain ownership of your content but grant us license to display it...</p>
            `;
        } else {
            termsText.innerHTML = `
                <h2>Privacy Policy</h2>
                <p><em>Last Updated: ${new Date().toLocaleDateString()}</em></p>
                
                <h3>1. Information We Collect</h3>
                <ul>
                    <li>Account registration details</li>
                    <li>Profile information</li>
                    <li>Usage data and analytics</li>
                    <li>Device and connection information</li>
                </ul>
                
                <h3>2. How We Use Information</h3>
                <p>We use your data to provide and improve our services...</p>
            `;
        }
        
        modal.style.display = 'block';
    }

    function closeTerms() {
        document.getElementById('termsModal').style.display = 'none';
    }

    function acceptTerms() {
        termsCheckbox.checked = true;
        closeTerms();
    }

    // Close modal when clicking outside content
    window.onclick = function(event) {
        const modal = document.getElementById('termsModal');
        if (event.target == modal) {
            closeTerms();
        }
    }

    // Prevent default form submission
    form.onsubmit = (e) => {
        e.preventDefault();
    };
});