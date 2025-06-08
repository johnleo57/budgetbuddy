<?php
session_start();
include_once "php/config.php";

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Create a connection
$conn = new mysqli($hostname, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Database connection error: " . $conn->connect_error);
}

// Fetch total users
$result = $conn->query("SELECT COUNT(*) AS total_users FROM users"); // Changed to users table
$total_users = $result->fetch_assoc()['total_users'];

// Fetch new expenses (example query, adjust as needed)
$result = $conn->query("SELECT COUNT(*) AS new_expenses FROM expenses WHERE DATE(date) = CURDATE()");
$new_expenses = $result->fetch_assoc()['new_expenses'];

// Fetch active goals (example query, adjust as needed)
$result = $conn->query("SELECT COUNT(*) AS active_goals FROM savinggoals WHERE currentAmount < targetAmount"); // Adjusted to savinggoals table
$active_goals = $result->fetch_assoc()['active_goals'];

// Fetch admins logged in (example query, adjust as needed)
$result = $conn->query("SELECT COUNT(*) AS logged_in_admins FROM admin WHERE last_login >= NOW() - INTERVAL 1 DAY");
$logged_in_admins = $result->fetch_assoc()['logged_in_admins'];

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BudgetBuddy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="d-flex" id="wrapper">
        <div class="bg-dark text-white border-end" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-4 fs-4 fw-bold border-bottom">
                BudgetBuddy Admin
            </div>
            <div class="list-group list-group-flush">
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white fw-bold active">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-people me-2"></i> User Management
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-cash-stack me-2"></i> Manage Expenses
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-piggy-bank me-2"></i> Manage Goals
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-file-earmark-bar-graph me-2"></i> Monthly Reports
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-journal-check me-2"></i> Audit Log
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-dark text-white">
                    <i class="bi bi-gear me-2"></i> Settings
                </a>
            </div>
        </div>
        <div id="page-content-wrapper" class="flex-grow-1">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">
                        <i class="bi bi-list"></i> Toggle Sidebar
                    </button>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            <li class="nav-item active">
                                <a class="nav-link" href="#">Home</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Admin User
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="#">Profile</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Logout</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="container-fluid py-4">
                <h1 class="mt-4 mb-4">Dashboard</h1>

                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-primary shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                            Total Users
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $total_users; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-people h3 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-success shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                            New Expenses (Today)
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $new_expenses; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-wallet2 h3 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-info shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                            Active Goals
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $active_goals; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-piggy-bank h3 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="card border-warning shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                            Admins Logged In (24h)
                                        </div>
                                        <div class="h5 mb-0 fw-bold text-gray-800"><?php echo $logged_in_admins; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="bi bi-person-circle h3 text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <h6 class="m-0 fw-bold text-primary">Recent Activity (Last 24 Hours)</h6>
                            </div>
                            <div class="card-body">
                                <p>This section would display recent audit log entries, e.g., "User  John Doe added an expense," "Admin Jane reset password for Mary Smith."</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="js/script.js"></script>
</body>
</html>
