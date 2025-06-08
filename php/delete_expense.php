<?php
session_start();
include "config.php";
header('Content-Type: application/json');
if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}
if (isset($_GET['exp_id'])) {
    $exp_id = intval($_GET['exp_id']);
    $sql = "DELETE FROM Expenses WHERE expID = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $exp_id, $_SESSION['unique_id']);
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Expenses deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Expenses not found or already deleted']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete Expenses: ' . $stmt->error]);
    }
}
?>