<?php
session_start();
include "config.php";

if (!isset($_SESSION['unique_id'])) {
  echo json_encode(['success' => false, 'error' => 'Unauthorized']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['exp_id'], $data['exp_name'], $data['exp_price'])) {
  echo json_encode(['success' => false, 'error' => 'Invalid data']);
  exit;
}

$exp_id = intval($data['exp_id']);
$exp_name = mysqli_real_escape_string($conn, $data['exp_name']);
$exp_price = floatval($data['exp_price']);
$user_id = $_SESSION['unique_id'];

// Verify that this expense belongs to the logged-in user
$checkSql = "SELECT * FROM Expenses WHERE exp_id = $exp_id AND user_id = '$user_id'";
$checkRes = mysqli_query($conn, $checkSql);
if (mysqli_num_rows($checkRes) === 0) {
  echo json_encode(['success' => false, 'error' => 'Expense not found or permission denied']);
  exit;
}

$updateSql = "UPDATE Expenses SET exp_name = '$exp_name', exp_price = $exp_price WHERE exp_id = $exp_id";
if (mysqli_query($conn, $updateSql)) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'error' => 'Failed to update']);
}
?>
