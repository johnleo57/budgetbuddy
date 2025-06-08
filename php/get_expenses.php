<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['unique_id'];

$sql = "SELECT e.*, c.CategoryName 
        FROM Expenses e 
        LEFT JOIN Categories c ON e.CategoryID = c.CategoryID 
        WHERE e.user_id = ? 
        ORDER BY e.Date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to fetch expenses']);
}
?>
