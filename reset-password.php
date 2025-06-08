<?php 
  session_start();
  include_once "php/config.php";
  
  if(isset($_GET['token'])){
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE reset_token = '{$token}' AND token_expiry > NOW()");
    
    if(mysqli_num_rows($sql) > 0){
      $row = mysqli_fetch_assoc($sql);
?>

<?php include_once "header.php"; ?>
<body>
  <img class="wave" src="image/Background.png">

  <div class="form-container">
    <div class="img">
      <img src="image/undraw_secure_files_re_6vdh.svg">
    </div>
    <section class="form reset-password">
      <header>Set New Password</header>
      <form action="#" method="POST" autocomplete="off">
        <input type="hidden" name="token" value="<?php echo $token ?>">
        <div class="error-text"></div>
        <div class="field input">
          <label>New Password</label>
          <input type="password" name="password" placeholder="Enter new password" required>
        </div>
        <div class="field input">
          <label>Confirm Password</label>
          <input type="password" name="cpassword" placeholder="Confirm new password" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Reset Password">
        </div>
      </form>
    </section>
  </div>
  
  <script src="javascript/reset-password.js"></script>
</body>
</html>

<?php
    }else{
      header("location: login.php");
    }
  }else{
    header("location: login.php");
  }
?>