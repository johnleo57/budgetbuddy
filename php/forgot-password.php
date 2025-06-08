<?php
session_start();

require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\HPMailer\Exception;

$success = '';
$error = '';

// DB connection — change these values accordingly
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "budgetbuddy";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match('/@(gmail\.com|gordoncollege\.edu\.ph)$/', $email)) {
        $error = "Only @gmail.com and @gordoncollege.edu.ph emails are allowed.";
    } else {
        // Check if email exists in DB
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            // Email not found, but we do not reveal that to user for security
            $success = "If an account with that email exists, a password reset link has been sent.";
        } else {
            // Email found, generate reset token and expiry (1 hour from now)
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store token and expiry in DB
            $stmt->close();
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
            $stmt->bind_param("sss", $token, $expiry, $email);
            if ($stmt->execute()) {
                // Send email with reset link
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = '202311523@gordoncollege.edu.ph'; // your Gmail
                    $mail->Password   = 'duvrivnirspmnqdm';               // your Gmail app password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        ]
                    ];

                    $mail->setFrom('202311523@gordoncollege.edu.ph', 'BudgetBuddy');
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Password Reset Request';

                    $resetLink = 'http://localhost/budgetbuddy/reset-password.php?email=' . urlencode($email) . '&token=' . $token;

                    $mail->Body = "
                        <p>Dear user,</p>
                        <p>You requested a password reset. Click the link below to reset your password:</p>
                        <p><a href='$resetLink'>$resetLink</a></p>
                        <p>If you did not request this, please ignore this email.</p>
                        <br>
                        <p>Regards,<br>Your Website Team</p>
                    ";

                    $mail->send();
                    $success = "If an account with that email exists, a password reset link has been sent.";
                } catch (Exception $e) {
                    $error = "Failed to send email. Please try again later.";
                    // Uncomment below to debug:
                    // $error .= ' Mailer Error: ' . $mail->ErrorInfo;
                }
            } else {
                $error = "Failed to generate reset link. Please try again later.";
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>
  <link rel="stylesheet" href="..\css/style.css">

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Forgot Password</title>
</head>

<body>
      <img class="wave" src="../image/Background.png">

  <div class="form-container">
    <div class="img">
      <img src="../image/undraw_secure_login_pdn4.svg">
    </div>
    <section class="form forgot-password">
      <header>Reset Your Password</header>
      <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
      <form action="#" method="POST" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
          <label>Email Address</label>
          <input type="text" name="email" placeholder="Example@gmail.com" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Send Reset Link">
        </div>
      </form>
      <div class="link">Remember your password? <a href="..\login.php">Login now</a></div>
    </section>
  </div>
  <script src="javascript/forgot-password.js"></script>
</body>

</html>