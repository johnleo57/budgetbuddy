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

// Get categories that have expenses in the selected month for this user
$categoriesQuery = "SELECT DISTINCT c.CategoryID, c.CategoryName 
                    FROM Categories c 
                    INNER JOIN Expenses e ON c.CategoryID = e.CategoryID 
                    WHERE e.user_id = ? AND DATE_FORMAT(e.Date, '%Y-%m') = ? 
                    ORDER BY c.CategoryName";

$stmt = $conn->prepare($categoriesQuery);
$stmt->bind_param("ss", $user_id, $month);
$stmt->execute();
$result = $stmt->get_result();

$categories = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'categoryId' => $row['CategoryID'],
            'categoryName' => $row['CategoryName']
        ];
    }
}

echo json_encode([
    'success' => true,
    'categories' => $categories
]);
?>