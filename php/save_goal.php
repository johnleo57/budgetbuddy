<?php
session_start();
include "config.php";

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $goalName = mysqli_real_escape_string($conn, $_POST['goalName']);
    $targetAmount = mysqli_real_escape_string($conn, $_POST['targetAmount']);
    $currentAmount = mysqli_real_escape_string($conn, $_POST['currentAmount']);
    $userId = $_SESSION['unique_id'];

    // Optional: sanitize and validate the Date input, default to today if missing
    if (isset($_POST['Date']) && !empty($_POST['Date'])) {
        $date = mysqli_real_escape_string($conn, $_POST['Date']);
        // Optional: you can validate the date format here if needed
    } else {
        $date = date('Y-m-d'); // default to current date
    }

    // Check if we are updating an existing goal
    if (isset($_POST['editingGoalId']) && !empty($_POST['editingGoalId'])) {
        $goalID = intval($_POST['editingGoalId']);
        $sql = "UPDATE savinggoals 
                SET goalName = '$goalName', targetAmount = '$targetAmount', currentAmount = '$currentAmount', Date = '$date' 
                WHERE goalID = $goalID AND user_id = '$userId'";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Goal updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating goal: ' . mysqli_error($conn)]);
        }
    } else {
        // Insert new goal with Date
        $sql = "INSERT INTO savinggoals (user_id, goalName, targetAmount, currentAmount, Date) 
                VALUES ('$userId', '$goalName', '$targetAmount', '$currentAmount', '$date')";

        if (mysqli_query($conn, $sql)) {
            echo json_encode(['status' => 'success', 'message' => 'Goal saved successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error saving goal: ' . mysqli_error($conn)]);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
