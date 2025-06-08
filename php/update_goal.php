<?php
session_start();
include "config.php";

if (!isset($_SESSION['unique_id']) || !isset($_POST['goalId'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

$goalId = intval($_POST['goalId']);
$goalName = mysqli_real_escape_string($conn, $_POST['goal-name']);
$targetAmount = mysqli_real_escape_string($conn, $_POST['target-amount']);
$savedAmount = mysqli_real_escape_string($conn, $_POST['saved-amount']);
$userId = $_SESSION['unique_id'];

$sql = "UPDATE savinggoals SET goalName = '$goalName', targetAmount = '$targetAmount', currentAmount = '$savedAmount' WHERE GoalID = '$goalId' AND user_id = '$userId'";
if (mysqli_query($conn, $sql)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conn)]);
}
?>
