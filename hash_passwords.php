<?php
include_once "php/config.php"; // Include your database connection

// Fetch all admin users
$sql = "SELECT * FROM admin";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $admin_id = $row['admin_id'];
        $plainPassword = $row['password']; // Get the plain text password

        // Hash the password
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

        // Update the password in the database
        $updateSql = "UPDATE admin SET password = '$hashedPassword' WHERE admin_id = '$admin_id'";
        mysqli_query($conn, $updateSql);
    }
    echo "Passwords have been hashed and updated successfully.";
} else {
    echo "Error fetching admin users: " . mysqli_error($conn);
}
?>
