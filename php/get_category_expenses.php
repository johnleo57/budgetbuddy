<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['unique_id'];

// Get month and category from GET parameters
$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$categoryId = isset($_GET['category']) ? $_GET['category'] : null;

// Validate $month format YYYY-MM
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    echo json_encode(['success' => false, 'error' => 'Invalid month format']);
    exit;
}

if (!$categoryId) {
    echo json_encode(['success' => false, 'error' => 'Category ID is required']);
    exit;
}

// Get category name
$categoryQuery = "SELECT CategoryName FROM Categories WHERE CategoryID = ?";
$stmt = $conn->prepare($categoryQuery);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$categoryResult = $stmt->get_result();
$categoryName = '';

if ($categoryResult && $categoryResult->num_rows > 0) {
    $categoryRow = $categoryResult->fetch_assoc();
    $categoryName = $categoryRow['CategoryName'];
} else {
    echo json_encode(['success' => false, 'error' => 'Category not found']);
    exit;
}

// Get expenses for the specific category and month
$expensesQuery = "SELECT e.exp_name, e.exp_price, DATE_FORMAT(e.Date, '%Y-%m-%d') AS expense_date, 
                         e.Description, c.CategoryName
                  FROM Expenses e
                  JOIN Categories c ON e.CategoryID = c.CategoryID
                  WHERE e.user_id = ? AND e.CategoryID = ? AND DATE_FORMAT(e.Date, '%Y-%m') = ?
                  ORDER BY e.Date DESC";

$stmt = $conn->prepare($expensesQuery);
$stmt->bind_param("sis", $user_id, $categoryId, $month);
$stmt->execute();
$expensesResult = $stmt->get_result();

$expenses = [];
$totalCategoryExpenses = 0;

if ($expensesResult && $expensesResult->num_rows > 0) {
    while ($row = $expensesResult->fetch_assoc()) {
        $expenses[] = [
            'exp_name' => $row['exp_name'],
            'exp_price' => number_format($row['exp_price'], 2, '.', ''),
            'expense_date' => $row['expense_date'],
            'category' => $row['CategoryName']
        ];
        $totalCategoryExpenses += $row['exp_price'];
    }
}

// Get budget for the month to calculate percentage
$budgetQuery = "SELECT Amount FROM budgets WHERE user_id = ? AND DATE_FORMAT(Date, '%Y-%m') = ? LIMIT 1";
$stmt = $conn->prepare($budgetQuery);
$stmt->bind_param("ss", $user_id, $month);
$stmt->execute();
$budgetResult = $stmt->get_result();
$budgetAmount = 0;

if ($budgetResult && $budgetResult->num_rows > 0) {
    $budgetRow = $budgetResult->fetch_assoc();
    $budgetAmount = $budgetRow['Amount'];
}

// Calculate percentage of budget used by this category
$budgetPercentage = $budgetAmount > 0 ? ($totalCategoryExpenses / $budgetAmount) * 100 : 0;

echo json_encode([
    'success' => true,
    'category_name' => $categoryName,
    'total_category_expenses' => number_format($totalCategoryExpenses, 2, '.', ''),
    'budget_percentage' => number_format($budgetPercentage, 2, '.', ''),
    'expenses' => $expenses
]);
?>