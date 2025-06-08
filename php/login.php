<?php 
session_start();
include_once "config.php";

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (!empty($email) && !empty($password)) {
    // Check in users table
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
    
    if (mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);
        $enc_pass = $row['password'];
        
        // Verify the password using password_verify
        if (password_verify($password, $enc_pass)) {
            $_SESSION['unique_id'] = $row['unique_id'];
            echo "success user"; // Indicate successful user login
            exit();
        } else {
            echo "Email or Password is Incorrect!";
        }
    } else {
        // Check in admin table
        $sqlAdmin = mysqli_query($conn, "SELECT * FROM admin WHERE Email = '{$email}'");
        
        if (mysqli_num_rows($sqlAdmin) > 0) {
            $rowAdmin = mysqli_fetch_assoc($sqlAdmin);
            $enc_admin_pass = $rowAdmin['password'];
            
            // Verify the password using password_verify
            if (password_verify($password, $enc_admin_pass)) {
                $_SESSION['admin_id'] = $rowAdmin['admin_id'];
                echo "success admin"; // Indicate successful admin login
                exit();
            } else {
                echo "Email or Password is Incorrect!";
            }
        } else {
            echo "$email - This email does not exist!";
        }
    }
} else {
    echo "All input fields are required!";
}
// After successful admin login
$updateLastLogin = mysqli_query($conn, "UPDATE admin SET last_login = NOW() WHERE admin_id = '{$rowAdmin['admin_id']}'");

?>
