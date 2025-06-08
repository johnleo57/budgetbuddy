<?php
// ==================== UPDATED FETCH_GOALS.PHP ====================
session_start();
header('Content-Type: application/json');
include 'config.php';

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['status' => 'error', 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['unique_id'];
$selectedMonth = $_GET['month'] ?? date('Y-m');

// Validate month format YYYY-MM
if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
    echo json_encode(['status' => 'error', 'error' => 'Invalid month format']);
    exit;
}

// Use prepared statements for security
$sql = "SELECT goalID, goalName, targetAmount, currentAmount 
        FROM savinggoals 
        WHERE user_id = ? 
        AND DATE_FORMAT(Date, '%Y-%m') = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user_id, $selectedMonth);
$stmt->execute();
$result = $stmt->get_result();

if ($result) {
    $goals = [];
    while ($row = $result->fetch_assoc()) {
        // Cap the currentAmount at targetAmount for display purposes
        $targetAmount = floatval($row['targetAmount']);
        $actualCurrentAmount = floatval($row['currentAmount']);
        $cappedCurrentAmount = min($actualCurrentAmount, $targetAmount);
        
        $goals[] = [
            'goalID' => $row['goalID'],
            'goalName' => $row['goalName'],
            'targetAmount' => $targetAmount,
            'currentAmount' => $cappedCurrentAmount, // This is now capped
            'actualCurrentAmount' => $actualCurrentAmount // Keep the real amount for editing
        ];
    }
    echo json_encode(['status' => 'success', 'goals' => $goals]);
} else {
    echo json_encode(['status' => 'error', 'error' => 'Failed to fetch goals']);
}
$stmt->close();
?>