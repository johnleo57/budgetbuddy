<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['unique_id'];
    $category_id = $_POST['category_id'];
    $exp_name = $_POST['exp_name'];
    $exp_price = $_POST['exp_price'];
    $description = $_POST['description'] ?? '';
    $date = date('Y-m-d');

    // Fetch the category name
    $cat_query = "SELECT CategoryName FROM Categories WHERE CategoryID = ?";
    $cat_stmt = $conn->prepare($cat_query);
    $cat_stmt->bind_param("i", $category_id);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();

    if ($cat_result->num_rows > 0) {
        $cat_row = $cat_result->fetch_assoc();
        $category_name = $cat_row['CategoryName'];

        // Insert expense
        $stmt = $conn->prepare("INSERT INTO Expenses (user_id, CategoryID, CategoryName, Date, exp_name, exp_price, Description) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssds", $user_id, $category_id, $category_name, $date, $exp_name, $exp_price, $description);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid category']);
    }
}
?>
