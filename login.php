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
      <img src="image/undraw_secure_files_re_6vdh.svg">
    </div>
    <section class="form login">
      <header>Welcome To <br>Budget-Buddy</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Email Address</label>
          <input type="text" name="email" placeholder="Enter your email" required>
        </div>
        <div class="field input">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter your password" required>
          <i class="fas fa-eye"></i>
        </div>
        <div class="forgot-password">
          <a href="php/forgot-password.php">Forgot Password?</a>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Login">
        </div>
      </form>
      <div class="link">Not yet signed up? <a href="index.php">Sign up now</a></div>
    </section>
  </div>
  
  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/login.js"></script>
</body>
</html>