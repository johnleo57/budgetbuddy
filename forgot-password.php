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
    <section class="form forgot-password">
      <header>Reset Your Password</header>
      <form action="#" method="POST" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Email Address</label>
          <input type="text" name="email" placeholder="Enter your email" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Send Reset Link">
        </div>
      </form>
      <div class="link">Remember your password? <a href="login.php">Login now</a></div>
    </section>
  </div>
  
  <script src="javascript/forgot-password.js"></script>
</body>
</html>