<?php
session_start();
include "config.php"; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

// Get the month and category from the request
$selectedMonth = $_GET['month'] ?? date('Y-m');
$categoryId = $_GET['category'] ?? null;

// Validate month format YYYY-MM
if (!preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid month format']);
    exit();
}

$user_id = $_SESSION['unique_id'];

// Get user full name for display in report
$userName = '';
$userQuery = "SELECT fname, lname FROM users WHERE unique_id = ? LIMIT 1";
$stmtUser = $conn->prepare($userQuery);
$stmtUser->bind_param("s", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
if ($resultUser && $rowUser = $resultUser->fetch_assoc()) {
    $userName = trim($rowUser['fname'] . ' ' . $rowUser['lname']);
}

// Convert month string YYYY-MM to formatted month and year e.g. "March 2024"
$reportMonthStr = date("F Y", strtotime($selectedMonth . '-01'));

// Include TCPDF
require_once('TCPDF-main/tcpdf.php');

// Create new PDF document with UTF-8 support
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('BudgetBuddy');

if ($categoryId) {
    // Category-specific report
    generateCategoryReport($pdf, $conn, $user_id, $selectedMonth, $categoryId, $reportMonthStr, $userName);
} else {
    // Overall report (existing functionality)
    generateOverallReport($pdf, $conn, $user_id, $selectedMonth, $reportMonthStr, $userName);
}

function generateCategoryReport($pdf, $conn, $user_id, $selectedMonth, $categoryId, $reportMonthStr, $userName) {
    // Get category name
    $categoryQuery = "SELECT CategoryName FROM Categories WHERE CategoryID = ?";
    $stmt = $conn->prepare($categoryQuery);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $categoryResult = $stmt->get_result();
    
    if (!$categoryResult || $categoryResult->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Category not found']);
        exit();
    }
    
    $categoryRow = $categoryResult->fetch_assoc();
    $categoryName = $categoryRow['CategoryName'];
    
    // Get category expenses
    $expensesQuery = "SELECT e.exp_name, e.exp_price, DATE_FORMAT(e.Date, '%Y-%m-%d') AS expense_date, 
                             e.Description, c.CategoryName
                      FROM Expenses e
                      JOIN Categories c ON e.CategoryID = c.CategoryID
                      WHERE e.user_id = ? AND e.CategoryID = ? AND DATE_FORMAT(e.Date, '%Y-%m') = ?
                      ORDER BY e.Date DESC";
    
    $stmt = $conn->prepare($expensesQuery);
    $stmt->bind_param("sis", $user_id, $categoryId, $selectedMonth);
    $stmt->execute();
    $expensesResult = $stmt->get_result();
    
    $expenses = [];
    $totalCategoryExpenses = 0;
    
    if ($expensesResult && $expensesResult->num_rows > 0) {
        while ($row = $expensesResult->fetch_assoc()) {
            $expenses[] = $row;
            $totalCategoryExpenses += $row['exp_price'];
        }
    }
    
    // Get budget for percentage calculation
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
    
    $budgetPercentage = $budgetAmount > 0 ? ($totalCategoryExpenses / $budgetAmount) * 100 : 0;
    
    // Set PDF properties
    $pdf->SetTitle($categoryName . ' Category Report - ' . $reportMonthStr);
    $pdf->SetSubject($categoryName . ' Category Summary');
    $pdf->SetMargins(15, 20, 15);
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 12);
    
    // Add Report Title
    $pdf->SetFont('dejavusans', 'B', 16);
    $pdf->Cell(0, 12, $categoryName . ' Category Report: ' . $reportMonthStr, 0, 1, 'C');
    $pdf->SetFont('dejavusans', '', 12);
    if ($userName !== '') {
        $pdf->Cell(0, 8, 'For: ' . $userName, 0, 1, 'C');
    }
    $pdf->Ln(10);
    
    // Summary metrics
    $pdf->SetFont('dejavusans', 'B', 14);
    $pdf->Cell(0, 10, 'Category Summary', 0, 1, 'L');
    $pdf->SetFont('dejavusans', '', 12);
    
    $pdf->Cell(60, 8, 'Total ' . $categoryName . ' Expenses:', 0, 0, 'L');
    $pdf->Cell(0, 8, '₱ ' . number_format($totalCategoryExpenses, 2), 0, 1, 'L');
    
    $pdf->Cell(60, 8, 'Percentage of Budget:', 0, 0, 'L');
    $pdf->Cell(0, 8, number_format($budgetPercentage, 2) . '%', 0, 1, 'L');
    
    $pdf->Cell(60, 8, 'Number of Transactions:', 0, 0, 'L');
    $pdf->Cell(0, 8, count($expenses), 0, 1, 'L');
    
    if (count($expenses) > 0) {
        $averageExpense = $totalCategoryExpenses / count($expenses);
        $pdf->Cell(60, 8, 'Average per Transaction:', 0, 0, 'L');
        $pdf->Cell(0, 8, '₱ ' . number_format($averageExpense, 2), 0, 1, 'L');
    }
    
    $pdf->Ln(10);
    
    // Detailed expenses table
    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->Cell(0, 10, 'Detailed ' . $categoryName . ' Expenses', 0, 1, 'L');
    $pdf->SetFont('dejavusans', '', 10);
    
    if (count($expenses) > 0) {
        // Calculate table width - get usable width
        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();
        $usableWidth = $pageWidth - $margins['left'] - $margins['right'];
        
        // Distribute width across 3 columns (removed Description column)
        $w = [
            $usableWidth * 0.5,  // Expense Name - 50% of width
            $usableWidth * 0.25, // Amount - 25% of width  
            $usableWidth * 0.25  // Date - 25% of width
        ];
        
        $header = ['Expense Name', 'Amount (₱)', 'Date'];
        foreach ($header as $i => $heading) {
            $pdf->Cell($w[$i], 8, $heading, 1, 0, 'C');
        }
        $pdf->Ln();
        
        foreach ($expenses as $expense) {
            $pdf->Cell($w[0], 8, $expense['exp_name'], 1);
            $pdf->Cell($w[1], 8, '₱ ' . number_format(floatval($expense['exp_price']), 2), 1, 0, 'R');
            $pdf->Cell($w[2], 8, $expense['expense_date'], 1, 0, 'C');
            $pdf->Ln();
        }
    } else {
        $pdf->Cell(0, 8, 'No expenses found for this category in the selected month.', 0, 1, 'C');
    }
    
    $pdf->Ln(10);
    
    // Category insights
    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->Cell(0, 10, 'Category Insights', 0, 1, 'L');
    $pdf->SetFont('dejavusans', '', 10);
    
    if ($budgetPercentage > 50) {
        $pdf->MultiCell(0, 8, "• Your {$categoryName} expenses are consuming {$budgetPercentage}% of your total budget. Consider reducing spending in this category.", 0, 'L');
        $pdf->Ln(2);
    } elseif ($budgetPercentage > 30) {
        $pdf->MultiCell(0, 8, "• Your {$categoryName} expenses represent {$budgetPercentage}% of your budget. Monitor this category to ensure it stays within reasonable limits.", 0, 'L');
        $pdf->Ln(2);
    } else {
        $pdf->MultiCell(0, 8, "• Your {$categoryName} expenses are well controlled at {$budgetPercentage}% of your budget. Good job managing this category!", 0, 'L');
        $pdf->Ln(2);
    }
    
    if (count($expenses) > 0) {
        $averageExpense = $totalCategoryExpenses / count($expenses);
        $pdf->MultiCell(0, 8, "• You made " . count($expenses) . " {$categoryName} transaction(s) this month with an average of ₱" . number_format($averageExpense, 2) . " per transaction.", 0, 'L');
        $pdf->Ln(2);
        
        // Find highest expense
        $highestExpense = max(array_column($expenses, 'exp_price'));
        $highestExpenseItem = array_filter($expenses, function($exp) use ($highestExpense) {
            return $exp['exp_price'] == $highestExpense;
        });
        $highestExpenseItem = reset($highestExpenseItem);
        
        $pdf->MultiCell(0, 8, "• Your highest {$categoryName} expense was ₱" . number_format($highestExpense, 2) . " for \"{$highestExpenseItem['exp_name']}\".", 0, 'L');
    }
    
    // Output PDF
    $filename = strtolower(str_replace(' ', '_', $categoryName)) . '_category_report_' . $selectedMonth . '.pdf';
    $pdf->Output($filename, 'D');
}

function generateOverallReport($pdf, $conn, $user_id, $selectedMonth, $reportMonthStr, $userName) {
    // Fetch report data (existing functionality)
    $reportQuery = "SELECT reportData FROM reports WHERE user_id = ? AND DATE_FORMAT(Date, '%Y-%m') = ? LIMIT 1";
    $stmt = $conn->prepare($reportQuery);
    $stmt->bind_param("ss", $user_id, $selectedMonth);
    $stmt->execute();
    $reportResult = $stmt->get_result();

    if ($reportResult->num_rows > 0) {
        $reportRow = $reportResult->fetch_assoc();
        $reportData = json_decode($reportRow['reportData'], true);

        // Safely extract data from reportData with defaults
        $budgetAmount = isset($reportData['budget']) ? floatval($reportData['budget']) : 0;
        $totalExpenses = isset($reportData['total_expenses']) ? floatval($reportData['total_expenses']) : 0;
        $balance = isset($reportData['balance']) ? floatval($reportData['balance']) : 0;
        $savingsRate = isset($reportData['savings_rate']) ? floatval($reportData['savings_rate']) : 0;
        $savingGoals = isset($reportData['savingGoals']) && is_array($reportData['savingGoals']) ? $reportData['savingGoals'] : [];
        $insights = isset($reportData['expensesInsights']) && is_array($reportData['expensesInsights']) ? $reportData['expensesInsights'] : [];
        $savingsRateInsights = isset($reportData['savingsRateInsights']) && is_array($reportData['savingsRateInsights']) ? $reportData['savingsRateInsights'] : [];
        $expenseBreakdown = isset($reportData['expenseBreakdown']) && is_array($reportData['expenseBreakdown']) ? $reportData['expenseBreakdown'] : [];

        // Set PDF properties
        $pdf->SetTitle('Monthly Summary Report');
        $pdf->SetSubject('Monthly Summary');
        $pdf->SetMargins(15, 20, 15);
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 12);

        // Add Report Month Title
        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->Cell(0, 12, 'Monthly Summary Report: ' . $reportMonthStr, 0, 1, 'C');
        $pdf->SetFont('dejavusans', '', 12);
        if ($userName !== '') {
            $pdf->Cell(0, 8, 'For: ' . $userName, 0, 1, 'C');
        }
        $pdf->Ln(6);

        // Add "Key Financial Metrics" label on the left side above the metrics
        $pageWidth = $pdf->getPageWidth();
        $margins = $pdf->getMargins();
        $usableWidth = $pageWidth - $margins['left'] - $margins['right'];
        $colWidth = $usableWidth / 4;
        $leftMargin = $margins['left'];

        // Print the label on left side (not centered)
        $pdf->SetFont('dejavusans', 'B', 14);
        $pdf->SetX($leftMargin);
        $pdf->Cell(0, 10, 'Key Financial Metrics', 0, 1, 'L');

        $pdf->SetFont('dejavusans', 'B', 12);

        // Labels line
        $pdf->SetX($leftMargin);
        $pdf->Cell($colWidth, 8, 'Budget', 0, 0, 'C');
        $pdf->Cell($colWidth, 8, 'Total Expenses', 0, 0, 'C');
        $pdf->Cell($colWidth, 8, 'Balance', 0, 0, 'C');
        $pdf->Cell($colWidth, 8, 'Savings Rate', 0, 1, 'C');

        // Values line
        $pdf->SetFont('dejavusans', '', 12);
        $pdf->SetX($leftMargin);
        $pdf->Cell($colWidth, 8, '₱ ' . number_format($budgetAmount, 2), 0, 0, 'C');
        $pdf->Cell($colWidth, 8, '₱ ' . number_format($totalExpenses, 2), 0, 0, 'C');
        $pdf->Cell($colWidth, 8, '₱ ' . number_format($balance, 2), 0, 0, 'C');
        $pdf->Cell($colWidth, 8, number_format($savingsRate, 2) . '%', 0, 1, 'C');

        $pdf->Ln(10);

        // Expense Breakdown Table
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Expense Breakdown', 0, 1, 'L');
        $pdf->SetFont('dejavusans', '', 10);

        $w = [50, 40, 40, 50];
        $header = ['Category', 'Amount Spent (₱)', '% of Total Expenses', 'Date'];
        foreach ($header as $i => $heading) {
            $pdf->Cell($w[$i], 8, $heading, 1, 0, 'C');
        }
        $pdf->Ln();

        foreach ($expenseBreakdown as $expense) {
            $pdf->Cell($w[0], 8, $expense['category'], 1);
            $pdf->Cell($w[1], 8, '₱ ' . number_format(floatval($expense['amount_spent']), 2), 1, 0, 'R');
            $pdf->Cell($w[2], 8, number_format(floatval($expense['percentage']), 2) . '%', 1, 0, 'R');
            $pdf->Cell($w[3], 8, $expense['date'], 1, 0, 'C');
            $pdf->Ln();
        }
        $pdf->Ln(10);
        
        // Saving Goals vs. Actual Performance
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Saving Goals vs. Actual Performance', 0, 1, 'L');
        $pdf->SetFont('dejavusans', '', 10);

        // Table header for saving goals
        $w = [50, 35, 35, 35, 35];
        $header = ['Goal Name', 'Target Amount (₱)', 'Amount Saved (₱)', 'Variance (₱)', '% Progress'];
        foreach ($header as $i => $heading) {
            $pdf->Cell($w[$i], 8, $heading, 1, 0, 'C');
        }
        $pdf->Ln();

        // Table data rows
        foreach ($savingGoals as $goal) {
            $pdf->Cell($w[0], 8, $goal['goalName'], 1);
            $pdf->Cell($w[1], 8, '₱ ' . number_format(floatval($goal['targetAmount']), 2), 1, 0, 'R');
            $pdf->Cell($w[2], 8, '₱ ' . number_format(floatval($goal['currentAmount']), 2), 1, 0, 'R');
            $pdf->Cell($w[3], 8, '₱ ' . number_format(floatval($goal['variance']), 2), 1, 0, 'R');
            $pdf->Cell($w[4], 8, number_format(floatval($goal['progressPercent']), 2) . '%', 1, 0, 'R');
            $pdf->Ln();
        }
        $pdf->Ln(10);

        // Insights and Recommendations
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Insights and Recommendations', 0, 1, 'L');
        $pdf->SetFont('dejavusans', '', 10);

        foreach ($insights as $insight) {
            $pdf->MultiCell(0, 8, $insight, 0, 'L');
            $pdf->Ln(2);
        }
        foreach ($savingsRateInsights as $savingsInsight) {
            $pdf->MultiCell(0, 8, $savingsInsight, 0, 'L');
            $pdf->Ln(2);
        }

        // Output PDF, force download
        $pdf->Output('monthly_summary_report_' . $selectedMonth . '.pdf', 'D');
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No report found for the selected month']);
        exit;
    }
}
?>