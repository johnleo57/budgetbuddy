<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['unique_id'];

// Get month from GET parameter, fallback to current month
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

// Validate $month format YYYY-MM
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    echo json_encode(['success' => false, 'error' => 'Invalid month format']);
    exit;
}

$expensesQuery = "SELECT CategoryName, SUM(exp_price) AS total_spent, DATE_FORMAT(Date, '%Y-%m-%d') AS expense_date 
                  FROM Expenses 
                  WHERE user_id = '$user_id' AND DATE_FORMAT(Date, '%Y-%m') = '$month' 
                  GROUP BY CategoryName 
                  ORDER BY total_spent DESC";

$expensesResult = mysqli_query($conn, $expensesQuery);
$expensesData = [];
$totalExpenses = 0;

if ($expensesResult) {
    while ($row = mysqli_fetch_assoc($expensesResult)) {
        $expensesData[] = $row;
        $totalExpenses += $row['total_spent'];
    }
}

$responseData = [];
foreach ($expensesData as $expense) {
    $percentage = $totalExpenses > 0 ? ($expense['total_spent'] / $totalExpenses) * 100 : 0;
    $responseData[] = [
        'category' => $expense['CategoryName'],
        'amount_spent' => number_format($expense['total_spent'], 2, '.', ''),
        'percentage' => number_format($percentage, 2, '.', ''),
        'date' => $expense['expense_date']
    ];
}

echo json_encode([
    'success' => true,
    'total_expenses' => number_format($totalExpenses, 2, '.', ''),
    'data' => $responseData
]);
?>
