<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['unique_id'];
$currentMonth = date('Y-m'); // Get current year and month

$sql = "SELECT Amount FROM budgets 
        WHERE user_id = '$user_id' AND DATE_FORMAT(Date, '%Y-%m') = '$currentMonth' 
        ORDER BY BudgetID DESC LIMIT 1";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode([
        'success' => true,
        'budget' => $row['Amount']
    ]);
} else {
    echo json_encode(['success' => false, 'budget' => 0]);
}
?>
