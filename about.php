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

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE-edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="css/about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <title>Budget-Buddy About</title>
    <link rel="icon" type="image/x-icon" href="Budgetbuddy.png">
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
            color:#e0e0e0;
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
</head>
<body>
<header>
    <div class="sidebar">
        <h2>BudgetBuddy</h2>
        <a href="#aboutUs" class="tab" id="AboutUsTab">
            <i class="fas fa-info-circle"></i> About Us
        </a>
        <a href="#features" class="tab" id="FeaturesTab">
            <i class="fas fa-star"></i> Features
        </a>
        <a href="#developers" class="tab" id="DeveloperTab">
            <i class="fas fa-person-booth"></i> Developer
        </a>
    </div>
    <div class="container">
        <div class="navbar">
            <div class="navbar_logo">
                <h2 id="navbarTitle">About</h2>
            </div>
            <div class="menu_items">
                <a href="home.php">Home</a>
                <a href="about.php" class="active">About</a>
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
</header>

<main class="container">
    <!-- About Us Section -->
    <section id="aboutUs" class="about-section">
        <h2>About Budget-Buddy</h2>
        <p>
            Budget-Buddy is a financial management tool designed to help individuals, 
            especially those with limited incomes, take control of their finances. 
            Our mission is to simplify budgeting and make financial planning accessible to everyone.
        </p>
        
        <div class="mission-vision">
            <div class="mission">
                <h3>Our Mission</h3>
                <p>To empower individuals with the tools and knowledge they need to achieve financial stability.</p>
            </div>
            <div class="vision">
                <h3>Our Vision</h3>
                <p>A world where everyone can manage their finances confidently and build a secure future.</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <h2>Key Features</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-wallet"></i>
                <h3>Expense Tracking</h3>
                <p>Monitor your spending habits and identify areas to save.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-pie"></i>
                <h3>Budget Planning</h3>
                <p>Create customized budgets for different spending categories.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-bell"></i>
                <h3>Bill Reminders</h3>
                <p>Never miss a payment with our notification system.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h3>Financial Reports</h3>
                <p>Visualize your financial progress with detailed reports.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-calendar-check"></i>
                <h3>Download Monthly Report</h3>
                <p>Save locally your Monthly report.</p>
            </div>
        </div>
    </section>

    <!-- Developers Section -->
    <section id="developers" class="developers-section">
        <h2>Meet The Team</h2>
        <div class="developers-grid">
            <div class="developer-card">
                <img src="image/leo.jpg" alt="John Leo Baticolon">
                <h3>John Leo Baticolon</h3>
                <p class="role">Lead Developer</p>
                <p>
                    A creative individual with expertise in UI/UX design and full-stack development.
                    Passionate about creating intuitive user experiences.
                </p>
                <div class="social-links">
                    <a href="https://github.com/johnleo57"><i class="fab fa-github"></i></a>
                    <a href="https://www.linkedin.com/in/john-leo-baticolon-a4411b368/"><i class="fab fa-linkedin"></i></a>
                    <a href="mailto:goblinslayer57777777@gmail.com"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
            
            <div class="developer-card">
                <img src="image/Paul.jpg" alt="John Paul Cabaltera">
                <h3>John Paul Cabaltera</h3>
                <p class="role">Backend Developer</p>
                <p>
                    Specializes in database architecture and server-side logic.
                    Committed to building secure and efficient systems.
                </p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-github"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                    <a href="#"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <h2>Contact Us</h2>
        <p>Have questions or feedback? We'd love to hear from you!</p>
        <div class="contact-info">
            <p><i class="fas fa-phone"></i> 09511184462</p>
            <p><i class="fas fa-envelope"></i> budgetbuddy@gmail.com</p>
            <p><i class="fas fa-map-marker-alt"></i> Gordon College, Olongapo City</p>
        </div>
    </div>
</section>

<script>
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
</body>
</html>