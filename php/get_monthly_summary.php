<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['unique_id'];
$currentDate = date('Y-m-d');

$selectedMonth = isset($_GET['month']) && preg_match('/^\d{4}-\d{2}$/', $_GET['month']) ? $_GET['month'] : date('Y-m');

$budgetQuery = "SELECT Amount FROM budgets WHERE user_id = ? AND DATE_FORMAT(Date, '%Y-%m') = ? LIMIT 1";
$stmt = $conn->prepare($budgetQuery);
$stmt->bind_param("ss", $user_id, $selectedMonth);
$stmt->execute();
$budgetResult = $stmt->get_result();
$budgetAmount = 0;

if ($budgetResult && $budgetResult->num_rows > 0) {
    $budgetRow = $budgetResult->fetch_assoc();
    $budgetAmount = $budgetRow['Amount'];
}

$expensesQuery = "SELECT SUM(exp_price) AS total_expenses FROM Expenses WHERE user_id = ? AND DATE_FORMAT(Date, '%Y-%m') = ?";
$stmt = $conn->prepare($expensesQuery);
$stmt->bind_param("ss", $user_id, $selectedMonth);
$stmt->execute();
$expensesResult = $stmt->get_result();
$totalExpenses = 0;

if ($expensesResult && $expensesResult->num_rows > 0) {
    $expensesRow = $expensesResult->fetch_assoc();
    $totalExpenses = $expensesRow['total_expenses'];
}

$balance = $budgetAmount - $totalExpenses;
$savingsRate = $budgetAmount > 0 ? (($balance) / $budgetAmount) * 100 : 0;

// Fetch expense breakdown grouped by category
$expenseBreakdownQuery = "SELECT c.CategoryName, SUM(e.exp_price) AS total_spent, DATE_FORMAT(e.Date, '%Y-%m-%d') AS expense_date
                          FROM Expenses e
                          JOIN Categories c ON e.CategoryID = c.CategoryID
                          WHERE e.user_id = ? AND DATE_FORMAT(e.Date, '%Y-%m') = ?
                          GROUP BY c.CategoryName
                          ORDER BY total_spent DESC";
$stmt = $conn->prepare($expenseBreakdownQuery);
$stmt->bind_param("ss", $user_id, $selectedMonth);
$stmt->execute();
$expenseBreakdownResult = $stmt->get_result();

$expenseBreakdownData = [];
foreach ($expenseBreakdownResult as $expense) {
    $percentage = $totalExpenses > 0 ? ($expense['total_spent'] / $totalExpenses) * 100 : 0;
    $expenseBreakdownData[] = [
        'category' => $expense['CategoryName'],
        'amount_spent' => number_format($expense['total_spent'], 2, '.', ''),
        'percentage' => number_format($percentage, 2, '.', ''),
        'date' => $expense['expense_date']
    ];
}

// Fetch saving goals
$goalsQuery = "SELECT goalName, targetAmount, currentAmount FROM savinggoals WHERE user_id = ?";
$stmt = $conn->prepare($goalsQuery);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$goalsResult = $stmt->get_result();

$savingGoals = [];
if ($goalsResult && $goalsResult->num_rows > 0) {
    while ($goal = $goalsResult->fetch_assoc()) {
        $target = floatval($goal['targetAmount']);
        $current = floatval($goal['currentAmount']);
        $variance = $target - $current;
        $progressPercent = $target > 0 ? ($current / $target) * 100 : 0;

        $savingGoals[] = [
            'goalName' => $goal['goalName'],
            'targetAmount' => number_format($target, 2, '.', ''),
            'currentAmount' => number_format($current, 2, '.', ''),
            'variance' => number_format($variance, 2, '.', ''),
            'progressPercent' => number_format($progressPercent, 2, '.', '')
        ];
    }
}

// Insights
$expensesInsights = [];
if ($totalExpenses > $budgetAmount) {
    $expensesInsights[] = "You have exceeded your budget by ₱ " . number_format($totalExpenses - $budgetAmount, 2) . ". Review your spending to reduce expenses.";
} else {
    $expensesInsights[] = "You are within your budget. Great job managing your expenses!";
}

$savingsRateInsights = [];
if ($savingsRate <= 40) {
    $savingsRateInsights[] = "Your overall savings rate is below 40%. Consider cutting discretionary spending to increase your savings rate.";
} elseif ($savingsRate > 40 && $savingsRate <= 70) {
    $savingsRateInsights[] = "Your savings rate is good. Keep up the consistent saving habit!";
} else {
    $savingsRateInsights[] = "Excellent savings rate! You're doing very well with managing your finances.";
}

// Prepare report data with expense breakdown included
$reportData = [
    'budget' => number_format($budgetAmount, 2, '.', ''),
    'total_expenses' => number_format($totalExpenses, 2, '.', ''),
    'balance' => number_format($balance, 2, '.', ''),
    'savings_rate' => number_format($savingsRate, 2, '.', ''),
    'expenseBreakdown' => $expenseBreakdownData,
    'savingGoals' => $savingGoals,
    'expensesInsights' => $expensesInsights,
    'savingsRateInsights' => $savingsRateInsights
];

$reportDataJson = json_encode($reportData);

// Insert or update report in reports table
$checkReportSql = "SELECT reportID FROM reports WHERE user_id = ? AND DATE_FORMAT(Date, '%Y-%m') = ? LIMIT 1";
$stmt = $conn->prepare($checkReportSql);
$stmt->bind_param("ss", $user_id, $selectedMonth);
$stmt->execute();
$checkReportResult = $stmt->get_result();

if ($checkReportResult && $checkReportResult->num_rows > 0) {
    // Update existing report
    $updateSql = "UPDATE reports SET reportData = ? WHERE user_id = ? AND DATE_FORMAT(Date, '%Y-%m') = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sss", $reportDataJson, $user_id, $selectedMonth);
    if (!$stmt->execute()) {
        error_log("Failed to update report for user: $user_id month: $selectedMonth. Error: " . $stmt->error);
    }
} else {
    // Insert new report using current date (month's first day)
    $reportDate = $selectedMonth . '-01';
    $insertSql = "INSERT INTO reports (user_id, Date, reportData) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->bind_param("sss", $user_id, $reportDate, $reportDataJson);
    if (!$stmt->execute()) {
        error_log("Failed to insert report for user: $user_id date: $reportDate. Error: " . $stmt->error);
    }
}

// Return the result (showing data for the selected month)
echo json_encode([
    'success' => true,
    'budget' => '₱ ' . number_format($budgetAmount, 2),
    'total_expenses' => '₱ ' . number_format($totalExpenses, 2),
    'balance' => '₱ ' . number_format($balance, 2),
    'savings_rate' => number_format($savingsRate, 2) . '%',
    'expenseBreakdown' => $expenseBreakdownData,
    'savingGoals' => $savingGoals,
    'expensesInsights' => $expensesInsights,
    'savingsRateInsights' => $savingsRateInsights
]);
?>

