<?php
session_start();
include "config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['goalID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No goal ID provided']);
    exit();
}

$goalID = intval($data['goalID']);
$userID = $_SESSION['unique_id'];

// Use prepared statements for security
$sql = "DELETE FROM savinggoals WHERE goalID = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $goalID, $userID);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Goal deleted successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Goal not found or already deleted']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete goal: ' . $stmt->error]);
}

$stmt->close();
?>
