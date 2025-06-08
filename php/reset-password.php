<?php 
  session_start();
  include_once "php/config.php";
  
  $token = mysqli_real_escape_string($conn, $_POST['token']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);
  $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);
  
  if(!empty($token) && !empty($password) && !empty($cpassword)){
    if($password === $cpassword){
      $sql = mysqli_query($conn, "SELECT * FROM users WHERE reset_token = '{$token}' AND token_expiry > NOW()");
      if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);
        $enc_pass = password_hash($password, PASSWORD_DEFAULT);
        
        $update_sql = mysqli_query($conn, "UPDATE users SET password = '{$enc_pass}', reset_token = NULL, token_expiry = NULL WHERE unique_id = {$row['unique_id']}");
        
        if($update_sql){
          echo "success";
        }else{
          echo "Something went wrong. Please try again!";
        }
      }else{
        echo "Invalid or expired token!";
      }
    }else{
      echo "Passwords do not match!";
    }
  }else{
    echo "All input fields are required!";
  }
?>