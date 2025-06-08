<?php
// ==================== UPDATED GET_GOAL.PHP ====================
session_start();
include "config.php";

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

if (!isset($_GET['goalID'])) {
    echo json_encode(['status' => 'error', 'message' => 'No goal ID provided']);
    exit();
}

$goalID = intval($_GET['goalID']);
$userID = $_SESSION['unique_id'];

$sql = "SELECT goalName, targetAmount, currentAmount, Date 
        FROM savinggoals 
        WHERE goalID = $goalID AND user_id = '$userID' 
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo json_encode(['status' => 'error', 'message' => 'Goal not found']);
    exit();
}

$goal = mysqli_fetch_assoc($result);

// Cap the currentAmount at targetAmount for editing form display
$targetAmount = floatval($goal['targetAmount']);
$actualCurrentAmount = floatval($goal['currentAmount']);
$cappedCurrentAmount = min($actualCurrentAmount, $targetAmount);

$goal['currentAmount'] = $cappedCurrentAmount; // Return capped amount for form display

echo json_encode(['status' => 'success', 'goal' => $goal]);
?>
