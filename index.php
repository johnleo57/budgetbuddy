<?php 
  session_start();
  if(isset($_SESSION['unique_id'])){
    header("location: home.php");
  }
?>

<?php include_once "header.php"; ?>
<body>
    <img class="wave" src="image/Background.png">
    <div class="form-container">
        <div class="img">
            <img src="image/undraw_secure_login_pdn4.svg">
        </div>
        <section class="form signup">
            <header>Create an Account</header>
            <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
                <div class="error-text"></div>
                <div class="name-details">
                    <div class="field input">
                        <label>First Name</label>
                        <input type="text" name="fname" placeholder="First name" required>
                    </div>
                    <div class="field input">
                        <label>Last Name</label>
                        <input type="text" name="lname" placeholder="Last name" required>
                    </div>
                </div>
                <div class="field input">
                    <label>Email Address</label>
                    <input type="text" name="email" id="email" placeholder="Enter your email" required>
                </div>
                <div class="field input">
                    <label>OTP Verification</label>
                    <input type="text" name="otp" id="otp" placeholder="Enter 6-digit OTP" maxlength="6">
                    <button type="button" id="send-otp-btn" class="otp-btn">Send OTP</button>
                    <div class="otp-timer">Time remaining: <span id="countdown">05:00</span></div>
                </div>
                <div class="field input">
                    <label>Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter new password" required>
                    <div class="password-requirements">
                        <p>Password must contain:</p>
                        <ul>
                            <li id="req-length">At least 8 characters</li>
                            <li id="req-uppercase">At least one uppercase letter</li>
                            <li id="req-lowercase">At least one lowercase letter</li>
                            <li id="req-number">At least one number</li>
                            <li id="req-special">At least one special character</li>
                        </ul>
                    </div>
                </div>
                <div class="field image">
                    <label>Select Image</label>
                    <div class="image-upload-container">
                        <div class="image-preview">
                            <img id="image-preview" src="" alt="Preview" style="display: none;">
                            <span id="no-image-text">No image selected</span>
                        </div>
                        <div class="image-upload-buttons">
                            <input type="file" name="image" id="image-upload" accept="image/x-png,image/gif,image/jpeg,image/jpg" required style="display: none;">
                            <button type="button" id="select-image-btn" class="image-btn">Select Image</button>
                            <button type="button" id="change-image-btn" class="image-btn" style="display: none;">Change Image</button>
                        </div>
                    </div>
                </div>                
                <div class="terms-checkbox">
                    <input type="checkbox" id="agree-terms" name="agree_terms" required>
                    <label for="agree-terms">
                        I agree to the <span class="terms-links" onclick="openTerms('user-agreement')">User Agreement</span>
                        and <span class="terms-links" onclick="openTerms('privacy-policy')">Privacy Policy</span>
                    </label>
                </div>
                <!-- Terms Modal Container -->
                <div id="termsModal" class="terms-modal">
                    <div class="terms-content">
                        <span class="close-terms" onclick="closeTerms()">&times;</span>
                        <div id="termsText" class="terms-text"></div>
                        <button onclick="acceptTerms()" class="button">I Agree</button>
                    </div>
                </div>
                <div class="field button">
                    <input type="submit" name="submit" value="Sign Up">
                </div>
            </form>
            <div class="link">Already have an account? <a href="login.php">Login now</a></div>
        </section>
    </div>
    <script src="javascript/pass-show-hide.js"></script>
    <script src="javascript/signup.js"></script>
    <script src="javascript/otp.js"></script>
</body>
</html>