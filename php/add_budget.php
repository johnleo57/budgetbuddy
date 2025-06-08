<?php
session_start();
include "config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['unique_id'];
$budget_amount = $_POST['budget_amount'];
$currentMonth = date('Y-m'); // Get current year-month (e.g. "2025-05")
$monthStartDate = $currentMonth . '-01'; // Set date as the first day of the month

// Check if a budget already exists for this user and month
$sql = "SELECT BudgetID FROM budgets WHERE user_id = '$user_id' AND DATE_FORMAT(Date, '%Y-%m') = '$currentMonth'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Update existing budget
    $update_sql = "UPDATE budgets SET Amount = '$budget_amount' WHERE user_id = '$user_id' AND DATE_FORMAT(Date, '%Y-%m') = '$currentMonth'";
    $success = mysqli_query($conn, $update_sql);
} else {
    // Insert new budget with first day of the month
    $insert_sql = "INSERT INTO budgets (user_id, Date, Amount) VALUES ('$user_id', '$monthStartDate', '$budget_amount')";
    $success = mysqli_query($conn, $insert_sql);
}

if ($success) {
    echo json_encode(["success" => true]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "error" => mysqli_error($conn)]);
}
?>
