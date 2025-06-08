<?php
session_start();

// Include the 'config.php' file with the correct path
include 'php/config.php';

if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit();
}

$user_id = $_SESSION['unique_id']; // Retrieve 'unique_id' from the session

$message = array(); // Initialize an array to store messages.

if (isset($_POST['update_profile'])) {
    $update_name = mysqli_real_escape_string($conn, $_POST['update_name']);
    $update_lname = mysqli_real_escape_string($conn, $_POST['update_lname']);
    $update_email = mysqli_real_escape_string($conn, $_POST['update_email']);

    // Update user's name and email
    $updateProfileQuery = "UPDATE users SET fname = '$update_name', lname = '$update_lname', email = '$update_email' WHERE unique_id = $user_id";

    if (mysqli_query($conn, $updateProfileQuery)) {
        $message[] = 'Profile information updated successfully.';
    } else {
        $message[] = 'Failed to update profile information: ' . mysqli_error($conn);
    }

    $old_pass = $_POST['old_pass'];
    $update_pass = mysqli_real_escape_string($conn, md5($_POST['update_pass']));
    $new_pass = mysqli_real_escape_string($conn, md5($_POST['new_pass']));
    $confirm_pass = mysqli_real_escape_string($conn, md5($_POST['confirm_pass']));

    if (!empty($update_pass) || !empty($new_pass) || !empty($confirm_pass)) {
        if ($update_pass !== $old_pass) {
            $message[] = 'Old password does not match.';
        } elseif ($new_pass !== $confirm_pass) {
            $message[] = 'New password and confirm password do not match.';
        } else {
            // Update the user's password
            $updatePasswordQuery = "UPDATE users SET password = '$new_pass' WHERE unique_id = $user_id";

            if (mysqli_query($conn, $updatePasswordQuery)) {
                $message[] = 'Password updated successfully.';
            } else {
                $message[] = 'Failed to update password: ' . mysqli_error($conn);
            }
        }
    }

    // Handle profile image update
    if ($_FILES['update_image']['size'] > 0) {
        $update_image = $_FILES['update_image']['name'];
        $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
        $update_image_folder = 'php/images/' . $update_image;

        if (move_uploaded_file($update_image_tmp_name, $update_image_folder)) {
            // Update the image in the database
            $updateImageQuery = "UPDATE users SET img = '$update_image' WHERE unique_id = $user_id";

            if (mysqli_query($conn, $updateImageQuery)) {
                $message[] = 'Image updated successfully.';
            } else {
                $message[] = 'Failed to update image: ' . mysqli_error($conn);
            }
        } else {
            $message[] = 'Failed to upload image.';
        }
    }
}

$select = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = $user_id");

if (mysqli_num_rows($select) > 0) {
    $fetch = mysqli_fetch_assoc($select);
}
$sql = "SELECT * FROM users WHERE unique_id = '{$_SESSION['unique_id']}'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head><style>
.navbar_acc {
    position: relative;
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-toggle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
}

.dropdown-menu {
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: opacity 0.3s ease, transform 0.3s ease;
    position: absolute;
    right: 0;
    top: 50px;
    background-color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    overflow: hidden;
    z-index: 1000;
    min-width: 140px;
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}


.dropdown-menu a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: black;
    font-weight: 500;
}

.dropdown-menu a:hover {
    background-color: #b1b6b9;
    transition: background 0.2s ease;
}

.navbar_acc {
    position: relative;
}

.profile-wrapper {
    position: relative;
    width: 45px;
    height: 45px;
    cursor: pointer;
}

.profile-img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    display: block;
}

.dropdown-arrow {
    position: absolute;
    bottom: 0;
    right: 0;
    background-color: #333; /* match your sidebar bg */
    border-radius: 50%;
    padding: 3px;
    font-size: 10px;
    color: white;
    transition: transform 0.3s ease;
}

/* Rotate arrow when active */
.rotate {
    transform: rotate(180deg);
}

.dropdown-menu {
    position: absolute;
    top: 60px;
    right: 0;
    background: #ffffff;
    padding: 10px;
    border-radius: 8px;
    display: none;
    flex-direction: column;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    z-index: 100;
}

.dropdown-menu.show {
    display: flex;
}

</style>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Budget-Buddy Update profile</title>
   <link rel="icon" type="image/x-icon" href="image/Cent-Tipid.png">
   <link rel="stylesheet" href="css/profile.css">

</head>
<body>
<header>
        <div class="containers">
            <div class="navbar">
                <div class="navbar_logo">

                    <h2>Budget-Buddy</h2>
                </div>
                <div class="menu_items">
                    <a href="home.php">Home</a>
                    <a href="about.php">About</a>
                </div>
                <div class="navbar_acc">
                    <div class="profile-wrapper">
                        <img src="php/images/<?php echo $row['img']; ?>" alt="Profile" class="profile-img">
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </div>
                    <div class="dropdown-menu">
                        <a href="profile.php">Profile</a>
                        <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>">Logout</a>
                    </div>
                </div>
            </div>
        </div>

<section class="dashboard">
<div class="update-profile">

   <?php
      $select = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
      if(mysqli_num_rows($select) > 0){
         $fetch = mysqli_fetch_assoc($select);
      }
   ?>

   <form action="" method="post" enctype="multipart/form-data">
     
      
        <?php
         if ($fetch['img'] == '') {
            echo '<img src="php/images/' . $row['img'] . '" alt="">';
         }else{
            echo '<img src="php/images/'.$fetch['img'].'">';
         }
         ?>

         <div class="prof">
        <span>Change Your Picture :</span>
            <input type="file" name="update_image" accept="image/jpg, image/jpeg, image/png" class="imageBox">
        </div>
        
        <?php
         if(isset($message)){
            foreach($message as $message){
               echo '<div class="message">'.$message.'</div>';
            }
         }
      ?>
      <div class="flex">
         <div class="inputBox">
            <span>Your First Name :</span>
            <input type="text" name="update_name" value="<?php echo $fetch['fname']; ?>" class="box">
            <span>Your Last Name :</span>
            <input type="text" name="update_lname" value="<?php echo $fetch['lname']; ?>" class="box">
            <span>Your Email :</span>
            <input type="email" name="update_email" value="<?php echo $fetch['email']; ?>" class="box">
            
         </div>
         <div class="inputBox">
            <input type="hidden" name="old_pass" value="<?php echo $fetch['password']; ?>">
            <span>Old password :</span>
            <input type="password" name="update_pass" placeholder="Enter Previous password" class="box">
            <span>New password :</span>
            <input type="password" name="new_pass" placeholder="Enter New password" class="box">
            <span>Confirm password :</span>
            <input type="password" name="confirm_pass" placeholder="Confirm New password" class="box">
         </div>
      </div>
      
      <input type="submit" value="Update Profile" name="update_profile" class="btn">
   </form>

</div>
        </header>
        <script>
document.addEventListener('DOMContentLoaded', () => {
    const profileWrapper = document.querySelector('.profile-wrapper');
    const dropdown = document.querySelector('.dropdown-menu');
    const arrow = document.querySelector('.dropdown-arrow');

    profileWrapper.addEventListener('click', () => {
        dropdown.classList.toggle('show');
        arrow.classList.toggle('rotate');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.profile-wrapper')) {
            dropdown.classList.remove('show');
            arrow.classList.remove('rotate');
        }
    });
});
</script>

<script src="js/home.js"></script>
    
</body>
</html>
