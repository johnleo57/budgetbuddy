<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['unique_id'];
$currentMonth = date('Y-m');

// Fetch the total budget amount for the current month
$budgetSql = "SELECT Amount FROM budgets WHERE user_id = '$user_id' AND Month = '$currentMonth' LIMIT 1";
$budgetResult = mysqli_query($conn, $budgetSql);
$budgetAmount = 0;
if ($budgetResult && mysqli_num_rows($budgetResult) > 0) {
    $row = mysqli_fetch_assoc($budgetResult);
    $budgetAmount = floatval($row['Amount']);
}

// Fetch total expenses for the current month
$expensesSql = "SELECT SUM(exp_price) AS total_expenses FROM Expenses WHERE user_id = '$user_id' AND DATE_FORMAT(Date, '%Y-%m') = '$currentMonth'";
$expensesResult = mysqli_query($conn, $expensesSql);
$totalExpenses = 0;
if ($expensesResult && mysqli_num_rows($expensesResult) > 0) {
    $row = mysqli_fetch_assoc($expensesResult);
    $totalExpenses = floatval($row['total_expenses']);
}

// Fetch expenses by category - to detect overspending
$categoryExpensesSql = "SELECT CategoryName, SUM(exp_price) AS category_total FROM Expenses WHERE user_id = '$user_id' AND DATE_FORMAT(Date, '%Y-%m') = '$currentMonth' GROUP BY CategoryName";
$categoryExpensesResult = mysqli_query($conn, $categoryExpensesSql);

$categoryExpenses = [];
if ($categoryExpensesResult) {
    while ($row = mysqli_fetch_assoc($categoryExpensesResult)) {
        $categoryExpenses[$row['CategoryName']] = floatval($row['category_total']);
    }
}

// Fetch saving goals
$savingGoalsSql = "SELECT goalName, targetAmount, currentAmount FROM savinggoals WHERE user_id = '$user_id'";
$savingGoalsResult = mysqli_query($conn, $savingGoalsSql);
$savingGoals = [];
if ($savingGoalsResult) {
    while ($row = mysqli_fetch_assoc($savingGoalsResult)) {
        $savingGoals[] = $row;
    }
}

// Build insights
$insights = [];

// Insight 1: Check if total expenses exceed budget
if ($budgetAmount > 0) {
    if ($totalExpenses > $budgetAmount) {
        $overAmount = $totalExpenses - $budgetAmount;
        $insights[] = "You exceeded your total budget by ₱".number_format($overAmount, 2)." this month.";
    } else {
        $insights[] = "Good job! You are within your budget this month.";
    }
} else {
    $insights[] = "You have not set a budget for this month. Consider setting one to manage your expenses.";
}

// Insight 2: Identify overspending on any category > 30% of total budget (or arbitrary threshold)
foreach ($categoryExpenses as $category => $amount) {
    if ($budgetAmount > 0) {
        $categoryShare = ($amount / $budgetAmount) * 100;
        if ($categoryShare > 30) {
            $insights[] = "You spent ".number_format($categoryShare, 1)."% of your budget on $category this month. Monitor this category to better control your expenses.";
        }
    }
}

// Insight 3: Saving goals progress
foreach ($savingGoals as $goal) {
    $progress = 0;
    if ($goal['targetAmount'] > 0) {
        $progress = ($goal['currentAmount'] / $goal['targetAmount']) * 100;
    }
    if ($progress >= 100) {
        $insights[] = "You have achieved your savings goal for '{$goal['goalName']}'. Great work!";
    } elseif ($progress >= 75) {
        $insights[] = "You're on track with your savings goal '{$goal['goalName']}'. Keep going!";
    } elseif ($progress >= 40) {
        $insights[] = "You have made some progress on '{$goal['goalName']}'. Consider increasing your savings to reach your target faster.";
    } else {
        $insights[] = "Your savings for '{$goal['goalName']}' is low. Try to allocate more funds toward this goal.";
    }
}

// Additional insights - detect if certain categories increased significantly compared to last month (optional)
// Here just a placeholder to show possibility
// Could implement comparing current month vs previous month expenses grouped by category
// Leaving this out for brevity

// Return insights
echo json_encode([
    'success' => true,
    'insights' => $insights
]);
?>

