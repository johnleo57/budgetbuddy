<?php
session_start();
include "php/config.php";

if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit();
}

$sql = "SELECT * FROM users WHERE unique_id = '{$_SESSION['unique_id']}'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
//logic for the budget
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $month = date('Y-m');
    $budgetQuery = "SELECT Amount FROM Budgets WHERE user_id = '{$row['unique_id']}' AND DATE_FORMAT(Date, '%Y-%m') = '$month' LIMIT 1";
    $budgetResult = mysqli_query($conn, $budgetQuery);
    $budgetAmount = 0;

    if ($budgetResult && mysqli_num_rows($budgetResult) > 0) {
        $budgetRow = mysqli_fetch_assoc($budgetResult);
        $budgetAmount = $budgetRow['Amount'];
    }

    // Fetch saved goals for the user
    $goalsQuery = "SELECT * FROM savinggoals WHERE user_id = '{$row['unique_id']}'";
    $goalsResult = mysqli_query($conn, $goalsQuery);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">
    <title>BudgetBuddy Home</title>
    <link rel="icon" type="image/x-icon" href="image/budgetbuddy.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>

    <style>
        /* Styles for the sidebar and tabs */
        .sidebar {
            width: 200px;
            position: fixed;
            height: 100%;
            background-color: rgb(64, 100, 100);
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .tab {
            display: flex;
            align-items: center;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .tab:hover {
            background-color: #e0e0e0;
        }

        .tab i {
            margin-right: 10px;
        }

        .content {
            margin-left: 220px; /* Space for the sidebar */
            padding: 20px;
        }

        .hidden {
            display: none;
        }
    </style>
         <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<header>
    <div class="sidebar">
        <h2>BudgetBuddy</h2>
        <div class="tab" id="dashboardTab">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </div>
        <div class="tab" id="savingGoalsTab">
            <i class="fas fa-piggy-bank"></i> Saving Goals
        </div>
        <div class="tab" id="monthlySummaryTab">
            <i class="fas fa-chart-line"></i> Monthly Report
        </div>
    </div>
    <div class="container">
        <div class="navbar">
            <div class="navbar_logo">
                <h2 id="navbarTitle">Dashboard</h2> 
            </div>
            <div class="menu_items">
                <a href="home.php" class="active">Home</a>
                <a href="about.php">About</a>
            </div>
            <div class="navbar_acc">
                <div class="profile-wrapper">
                    <img src="php/images/<?php echo $row['img']; ?>" alt="Profile" class="profile-img">
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </div>
                <div class="dropdown-menu">
                    <a href="profile.php">Profile</a>
                    <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <div class="content" id="dashboardContent">
        <div class="container">
            <div class="hero_section">
                <div class="acc_details">
                    <div class="acc_txt">
                        <h3>Hello, <span><?php echo $row['fname'] . ' ' . $row['lname']; ?></span> </h3>
                        <h5>Welcome to Budget-Buddy</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="error_message">
                <p>Please Enter Budget Amount</p>
            </div>
            <ul class="cards">
                <li>
                    <i class="bx bx-money"></i>
                    <span class="info">
                        <h3><span>₱</span><span class="card budget_card">0</span></h3>
                        <p>Budget</p>
                    </span>
                </li>
                <li>
                    <i class="bx bx-credit-card"></i>
                    <span class="info">
                        <h3><span>₱ </span><span class="expenses_card">0</span></h3>
                        <p>Expenses</p>
                    </span>
                </li>
                <li>
                    <i class="bx bx-dollar"></i>
                    <span class="info">
                        <h3><span> </span><span class="balance_card">0</span></h3>
                        <p>Balance</p>
                    </span>                    
                </li>
            </ul>
        </div>

        <div class="containers">
            <div class="budget_content">
                <div class="ur_budget">
                    <form id="budgetForm">
                        <label for="budget">Your Budget:</label>
                        <input type="number" name="budget_amount" placeholder="Enter Budget" required>
                        <input type="hidden" name="month" value="<?php echo date('Y-m'); ?>">
                        <button class="btn" type="submit">Add Budget</button>
                    </form>
                </div>
                <div class="ur_expenses">
                    <form id="expensesForm" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center; max-width: 500px;">
                        <select name="category_id" required style="width: 150px;">
                            <option value="">Select Category</option>
                            <?php
                            $cat_sql = "SELECT * FROM Categories";
                            $cat_result = mysqli_query($conn, $cat_sql);
                            while ($cat = mysqli_fetch_assoc($cat_result)) {
                                echo '<option value="' . $cat['CategoryID'] . '">' . $cat['CategoryName'] . '</option>';
                            }
                            ?>
                        </select>
                        <input type="text" name="exp_name" placeholder="Expenses Name" required style="flex: 1; min-width: 150px;">
                        <input type="number" name="exp_price" placeholder="Amount" required style="width: 150px;">
                        <div style="width: 30%;">
                            <button class="btn" type="submit" style="width: 28%;">Add Expenses</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <section class="table_content">
                <div class="tbl_data">
                    <h3>Expenses</h3>
                    <?php
                    $expenses_sql = "SELECT e.*, c.CategoryName FROM Expenses e
                                    JOIN Categories c ON e.CategoryID = c.CategoryID
                                    WHERE e.user_id = '{$_SESSION['unique_id']}'
                                    ORDER BY e.Date DESC";
                    $expenses_result = mysqli_query($conn, $expenses_sql);
                    if ($expenses_result && mysqli_num_rows($expenses_result) > 0) {
                        while ($expense = mysqli_fetch_assoc($expenses_result)) {
                            echo '
                            <ul class="tbl_tr_content" data-exp-id="' . $expense['exp_id'] . '">
                                <li>' . $expense['exp_id'] . '</li>
                                <li class="exp_name">' . htmlspecialchars($expense['exp_name']) . '</li>
                                <li class="exp_price">₱ <span>' . number_format($expense['exp_price'], 2) . '</span></li>
                                <li>' . $expense['CategoryName'] . '</li>
                                <li class="actions">
                                    <button class="edit-btn" data-exp-id="' . $expense['exp_id'] . '">Edit</button>
                                    
                                    <div class="edit-controls hidden" data-exp-id="' . $expense['exp_id'] . '">
                                        <button class="save-edit-btn">Save</button>
                                        <button class="cancel-edit-btn-expense" >Cancel</button>
                                        
                                    </div>
                                </li>
                            </ul>';
                        }
                    } else {
                        echo "<p>No expenses found.</p>";
                    }
                    ?>
                </div>
            </section>
        </div>
    </div>

    <div class="content hidden" id="savingGoalsContent">
        <div class="saving-goals-container">
            <div class="saving-goals-header">
                <h3>Hello, <span><?php echo $row['fname'] . ' ' . $row['lname']; ?></span> </h3>
                <p>Add a new saving goal and track your progress.</p>
            </div>
            <div class="saving-goals-form-wrapper">
                <div id="notification" class="notification hidden"></div>
                <form id="saving-goals-form" class="form-container">
                    <div class="form-group">
                        <label for="goal-name">Goal Name</label>
                        <input type="text" id="goal-name" name="goal-name" placeholder="e.g., New Phone" required>
                    </div>
                    <div class="form-group">
                        <label for="target-amount">Target Amount (₱)</label>
                        <input type="number" id="target-amount" name="target-amount" placeholder="e.g., 15000" min="1" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="saved-amount">Amount Saved (₱)</label>
                        <input type="number" id="saved-amount" name="saved-amount" placeholder="e.g., 5000" min="0" step="0.01" required>
                        <small class="validation-message" id="saved-amount-message"></small>
                    </div>
                    <div id="goal-form-buttons">
                        <button type="submit" class="save-goal-btn">Save Goal</button>
                        <button type="button" class="cancel-btn">Cancel</button>
                    </div>
                </form>
            </div>

            <div class="saved-goals-section">
                <h4>Saved Goals</h4>
                <div class="saved-goals-list">
                    <?php
if ($goalsResult && mysqli_num_rows($goalsResult) > 0) {
    while ($goal = mysqli_fetch_assoc($goalsResult)) {
        $goalName = htmlspecialchars($goal['goalName']);
        $targetAmount = floatval($goal['targetAmount']);
        $actualCurrentAmount = floatval($goal['currentAmount']);
        
        // Cap the displayed amount at target amount
        $displayedCurrentAmount = min($actualCurrentAmount, $targetAmount);
        $rawPercentage = ($displayedCurrentAmount / $targetAmount) * 100;
        $progressPercentage = min(100, $rawPercentage);
        $isCompleted = $displayedCurrentAmount >= $targetAmount;

        // Generate HTML for each goal
        echo "
        <div class='saved-goal-card " . ($isCompleted ? 'goal-completed' : '') . "'>
            <h4 class='goal-name'>{$goalName}</h4>
            <p class='target-amount'>Target: ₱" . number_format($targetAmount) . "</p>
            <p class='saved-amount'>Saved: ₱" . number_format($displayedCurrentAmount) . "</p>
            " . ($isCompleted ? "<p class='goal-status'>🎉 Goal Achieved!</p>" : "") . "
            <div class='progress-container'>
                <span class='progress-text-above'>" . round($progressPercentage) . "%</span>
                <div class='progress-bar-container'>
                    <div class='progress-bar " . ($isCompleted ? 'completed' : '') . "' style='width: {$progressPercentage}%;'></div>
                </div>
            </div>
            <div class='goal-actions'>
                <button class='edit-goal-btn' onclick='editGoal({$goal['goalID']})'>Edit</button>
                <button class='delete-goal-btn' onclick='deleteGoal({$goal['goalID']})'>Delete</button>
            </div>
        </div>
        ";
    }
} else {
    echo "<p>No saved goals found.</p>";
}
?>
                </div>
            </div>

            <!-- Goal Achievement Modal -->
            <div id="goal-achievement-modal" class="modal hidden">
                <div class="modal-content achievement">
                    <div class="achievement-icon">🎉</div>
                    <h3>Congratulations!</h3>
                    <p id="achievement-message">Goal Achieved!</p>
                    <button id="achievement-ok-btn" class="ok-btn">OK</button>
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div id="delete-confirmation-modal" class="modal hidden">
                <div class="modal-content">
                    <p>Are you sure you want to delete this goal?</p>
                    <div class="modal-buttons">
                        <button id="confirm-delete-btn" class="yes-btn">Yes</button>
                        <button id="cancel-delete-btn" class="cancel-btn">Cancel</button>
                    </div>
                </div>
            </div>

            <!-- Success Notification Modal -->
            <div id="success-notification-modal" class="modal hidden">
                <div class="modal-content success">
                    <p>Goal deleted successfully</p>
                </div>
            </div>
        </div>
    </div>

<style>
  /* Modal container */
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
  }

  /* Hide modal by default */
  .modal.hidden {
    display: none;
  }

  /* Modal content box */
  .modal-content {
    background-color: #445760;
    padding: 20px 30px;
    border-radius: 8px;
    max-width: 320px;
    width: 90%;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    text-align: center;
    font-family: Arial, sans-serif;
  }

  /* Goal Achievement Modal Styles */
  .modal-content.achievement {
    background: linear-gradient(135deg, #4CAF50, #45a049);
    color: white;
    max-width: 400px;
    animation: celebrationPulse 0.6s ease-out;
  }

  .achievement-icon {
    font-size: 3rem;
    margin-bottom: 15px;
    animation: bounce 1s infinite;
  }

  .modal-content.achievement h3 {
    margin: 0 0 10px 0;
    font-size: 1.5rem;
  }

  .modal-content.achievement p {
    margin: 0 0 20px 0;
    font-size: 1.1rem;
  }

  .ok-btn {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid white;
    padding: 10px 25px;
    border-radius: 25px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
  }

  .ok-btn:hover {
    background-color: white;
    color: #4CAF50;
  }

  /* Goal Completed Styles */
  .saved-goal-card.goal-completed {
    border: 2px solid #4CAF50;
    background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(69, 160, 73, 0.05));
  }

  .progress-bar.completed {
    background: linear-gradient(90deg, #4CAF50, #45a049);
    box-shadow: 0 2px 4px rgba(76, 175, 80, 0.3);
  }

  .goal-status {
    color: #4CAF50;
    font-weight: bold;
    font-size: 1.1rem;
    margin: 5px 0;
    text-align: center;
  }

  /* Validation Message Styles */
  .validation-message {
    display: block;
    margin-top: 5px;
    font-size: 0.85rem;
    color: #dc3545;
    min-height: 20px;
  }

  .validation-message.success {
    color: #28a745;
  }

  .validation-message.warning {
    color: #ffc107;
  }

  /* Animations */
  @keyframes celebrationPulse {
    0% { transform: scale(0.8); opacity: 0; }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); opacity: 1; }
  }

  @keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
  }

  /* Buttons container */
  .modal-buttons {
    margin-top: 20px;
    display: flex;
    justify-content: center;
    gap: 15px;
  }

  /* Yes button style */
  .modal-buttons .yes-btn {
    background-color: #d9534f;
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }

  .modal-buttons .yes-btn:hover {
    background-color: #c9302c;
  }

  /* Cancel button style */
  .modal-buttons .cancel-btn {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 10px 25px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background-color 0.3s ease;
  }

  .modal-buttons .cancel-btn:hover {
    background-color: #5a6268;
  }

  /* Success modal content style overrides */
  .modal-content.success {
    background-color: rgb(50, 116, 65);
    color: #fff;
    font-weight: bold;
  }
</style>

        </div>
    </div>

    <div class="content hidden" id="monthlySummaryContent">
    <h2 class="section-title" style="cursor: pointer;">
        Monthly Summary Report
    </h2>
    
    <!-- Month and Category Selection -->
    <div style="margin-left: 70px; margin-top: 20px; display: flex; gap: 20px; align-items: center;">
        <label for="monthPicker" style="display: flex; align-items: center; gap: 10px;">
            Select Month:
            <input type="month" id="monthPicker" name="monthPicker" value="<?php echo date('Y-m'); ?>" style="padding: 5px;">
        </label>
        
        <label for="categorySelector" style="display: flex; align-items: center; gap: 10px;">
            Select Category:
            <select id="categorySelector" name="categorySelector" style="padding: 5px; min-width: 150px;">
                <option value="">Overall</option>
            </select>
        </label>
        
        <button id="resetFiltersBtn" style="padding: 5px 15px; background-color: #f0f0f0; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">
            Reset Filters
        </button>
    </div>

    <h3 style="margin-left: 70px; font-size:19px; margin-top: 20px;">For: <span><?php echo $row['fname'] . ' ' . $row['lname']; ?></span> </h3>
    <p style="margin-left: 70px;">Generated: <span><?php echo date("F j, Y, g:i a"); ?></span></p>

    <!-- Report Type Indicator -->
    <div id="reportTypeIndicator" style="margin-left: 70px; margin-top: 10px; padding: 10px; background-color: #e8f4fd; border-left: 4px solid #2196F3; display: none;">
        <strong style="color:#000000">Viewing:</strong> <span id="reportTypeText" style="color:#000000">Overall Report</span>
    </div>

    <!-- Key Financial Metrics -->
    <section class="report-section" style="margin-top: 80px;">
        <h3 class="section-heading" style="color: #000000; margin-top: -40px;">Key Financial Metrics</h3>
        <div class="metrics-grid">
            <div class="metric-card">
                <p class="metric-label">Budget</p>
                <p class="metric-value budget-value">₱ 0.00</p>
            </div>
            <div class="metric-card">
                <p class="metric-label" id="expensesLabel">Total Expenses</p>
                <p class="metric-value expenses-value">₱ 0.00</p>
            </div>
            <div class="metric-card">
                <p class="metric-label">Balance</p>
                <p class="metric-value balance-value">₱ 0.00</p>
            </div>
            <div class="metric-card savings-rate-card">
                <p class="metric-label">Savings Rate</p>
                <p class="metric-value savings-rate-value">0.00%</p>
            </div>
        </div>
    </section>

    <!-- Expense Breakdown -->
    <section class="report-section">
        <h3 class="section-heading" style="color: #000000;" id="expenseBreakdownTitle">Expense Breakdown</h3>
        <p class="total-expenses-display"></p>
        <div class="expense-flex-container" id="expenseFlexContainer">
            <table class="summary-table" id="expenseTable">
                <thead>
                    <tr id="expenseTableHeader">
                        <th>Category</th>
                        <th>Amount Spent</th>
                        <th>% of Total Expenses</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows will be populated here -->
                </tbody>
            </table>
            <canvas id="expensePieChart" style="max-width: 500px; height: 400px;"></canvas>
        </div>
    </section>

    <!-- Saving Goals Section (hidden when category is selected) -->
    <section class="report-section" id="savingGoals">
        <h3 class="section-heading" style="color: #000000;">Saving Goals vs. Actual Performance</h3>
        <div class="saving-goals-flex-container">
            <table class="summary-table" id="goals-table">
                <thead>
                    <tr>
                        <th>Goal Name</th>
                        <th>Target Amount (₱)</th>
                        <th>Amount Saved (₱)</th>
                        <th>Variance (₱)</th>
                        <th>% Progress</th>
                    </tr>
                </thead>
                <tbody id="goals-table-body">
                    <!-- Rows will be populated here -->
                </tbody>
            </table>
            <canvas id="savingGoalsPieChart" style="max-width: 400px; height: 350px;"></canvas>
        </div>
    </section>

    <!-- Insights -->
    <section class="report-section" id="insights">
        <h3 class="section-heading" style="color: #000000;">Insights and Recommendations</h3>
        <ul class="insight-list">
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </section>
    
    <a href="php/generate_report.php" class="download-report-btn">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon-download" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 4v12" />
        </svg>
        <span style="margin-left: 10px;">Download Monthly Summary Report</span>
    </a>
</div>

</header>

<script>
    //sections
const initialBudget = <?php echo isset($budgetAmount) ? (int)$budgetAmount : 0; ?>;
document.addEventListener('DOMContentLoaded', () => {
    const budgetCardEl = document.querySelector('.budget_card');
    budgetCardEl.textContent = initialBudget;

    const navbarTitle = document.getElementById('navbarTitle');

    const dashboardTab = document.getElementById('dashboardTab');
    const savingGoalsTab = document.getElementById('savingGoalsTab');
    const monthlySummaryTab = document.getElementById('monthlySummaryTab');

    const dashboardContent = document.getElementById('dashboardContent');
    const savingGoalsContent = document.getElementById('savingGoalsContent');
    const monthlySummaryContent = document.getElementById('monthlySummaryContent');

    // Function to show the selected tab
    function showTab(tabContent, title) {
        dashboardContent.classList.add('hidden');
        savingGoalsContent.classList.add('hidden');
        monthlySummaryContent.classList.add('hidden');

        tabContent.classList.remove('hidden');
        navbarTitle.textContent = title; // Update title
    }

    // Check localStorage for the current tab
    const currentTab = localStorage.getItem('currentTab');
    if (currentTab) {
        if (currentTab === 'dashboard') {
            showTab(dashboardContent, "Dashboard");
        } else if (currentTab === 'savingGoals') {
            showTab(savingGoalsContent, "Saving Goals");
        } else if (currentTab === 'monthlySummary') {
            showTab(monthlySummaryContent, "Monthly Report");
            fetchMonthlySummary(); // Fetch data when this tab is opened
        }
    } else {
        // Default to dashboard if no tab is saved
        showTab(dashboardContent, "Dashboard");
    }

    // Event listeners for tab clicks
    dashboardTab.addEventListener('click', () => {
        showTab(dashboardContent, "Dashboard");
        localStorage.setItem('currentTab', 'dashboard'); // Save current tab
    });

    savingGoalsTab.addEventListener('click', () => {
        showTab(savingGoalsContent, "Saving Goals");
        localStorage.setItem('currentTab', 'savingGoals'); // Save current tab
    });

    monthlySummaryTab.addEventListener('click', () => {
        location.reload(); // Reload the page when the Monthly Report tab is clicked
        localStorage.setItem('currentTab', 'monthlySummary'); // Save current tab
        // The following fetch calls will not be executed since the page will reload
        // fetchMonthlySummary(); // Fetch data when this tab is opened
        // fetchExpenseBreakdown(); // Fetch expense breakdown data
        // fetchSavingGoals(); // Fetch saving goals data
    });
});


</script>
<script>
    //for the arrow in the profile
document.addEventListener('DOMContentLoaded', () => {
    const profileWrapper = document.querySelector('.profile-wrapper');
    const dropdown = document.querySelector('.dropdown-menu');
    const arrow = document.querySelector('.dropdown-arrow');

    profileWrapper.addEventListener('click', () => {
        dropdown.classList.toggle('show');
        arrow.classList.toggle('rotate');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.profile-wrapper')) {
            dropdown.classList.remove('show');
            arrow.classList.remove('rotate');
        }
    });
});
</script>

<script src="javascript/home.js"></script>
</body>
</html>
