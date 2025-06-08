<?php
session_start();
include_once "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // First verify OTP status
    if (!isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
        die("OTP verification required");
    }
}
$fname = mysqli_real_escape_string($conn, $_POST['fname']);
$lname = mysqli_real_escape_string($conn, $_POST['lname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (!empty($fname) && !empty($lname) && !empty($email) && !empty($password)) {
    // Password validation checks
    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters long!";
        exit();
    }
    if (!preg_match('/[A-Z]/', $password)) {
        echo "Password must contain at least one uppercase letter!";
        exit();
    }
    if (!preg_match('/[a-z]/', $password)) {
        echo "Password must contain at least one lowercase letter!";
        exit();
    }
    if (!preg_match('/[0-9]/', $password)) {
        echo "Password must contain at least one number!";
        exit();
    }
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>_-]/', $password)) {
        echo "Password must contain at least one special character!";
        exit();
    }

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $sql = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
        if (mysqli_num_rows($sql) > 0) {
            echo "$email - This email already exists!";
        } else {
            if (isset($_FILES['image'])) {
                $img_name = $_FILES['image']['name'];
                $img_type = $_FILES['image']['type'];
                $tmp_name = $_FILES['image']['tmp_name'];
                
                $img_explode = explode('.', $img_name);
                $img_ext = end($img_explode);

                $extensions = ["jpeg", "png", "jpg"];
                if (in_array($img_ext, $extensions) === true) {
                    $types = ["image/jpeg", "image/jpg", "image/png"];
                    if (in_array($img_type, $types) === true) {
                        $time = time();
                        $new_img_name = $time . $img_name;
                        if (move_uploaded_file($tmp_name, "images/" . $new_img_name)) {
                            $ran_id = rand(time(), 100000000);
                            // Use password_hash instead of md5
                            $encrypt_pass = password_hash($password, PASSWORD_DEFAULT);
                            $insert_query = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img)
                            VALUES ({$ran_id}, '{$fname}', '{$lname}', '{$email}', '{$encrypt_pass}', '{$new_img_name}')");
                            if ($insert_query) {
                                echo "success"; // Just return success without logging in
                            } else {
                                echo "Something went wrong. Please try again!";
                            }
                        }
                    } else {
                        echo "Please upload an image file - jpeg, png, jpg";
                    }
                } else {
                    echo "Please upload an image file - jpeg, png, jpg";
                }
            }
        }
    } else {
        echo "$email is not a valid email!";
    }
} else {
    echo "All input fields are required!";
}
unset($_SESSION['otp']);
unset($_SESSION['otp_email']);
unset($_SESSION['otp_expire']);
unset($_SESSION['otp_verified']);
unset($_SESSION['otp_verified_email']);
?>
