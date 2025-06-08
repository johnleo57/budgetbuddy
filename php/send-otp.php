<?php
session_start();
require_once 'config.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (isset($_SESSION['last_otp_request']) && 
    (time() - $_SESSION['last_otp_request']) < 60) {
    die(json_encode(['status' => 'error', 'message' => 'Please wait before requesting another OTP']));
}
$_SESSION['last_otp_request'] = time();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify database connection
    if (!$conn) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
        exit();
    }
    
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        exit();
    }
    
    // Check if email exists
    $sql = "SELECT * FROM users WHERE email = '{$email}'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
        exit();
    }
    
    // Generate 6-digit OTP
    $otp = rand(100000, 999999);
    
    // Store in session
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_email'] = $email;
    $_SESSION['otp_expire'] = time() + 300; // 5 minutes
    
    // Create PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = '202311523@gordoncollege.edu.ph'; // Your email
        $mail->Password   = 'duvrivnirspmnqdm'; // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        ]
                    ];
        
        // Recipients
        $mail->setFrom('no-reply@budgetbuddy.com', 'BudgetBuddy');
        $mail->addAddress($email);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your BudgetBuddy Verification Code';
        $mail->Body    = "
            <h2>BudgetBuddy Verification</h2>
            <p>Your OTP code is: <strong>$otp</strong></p>
            <p>This code will expire in 5 minutes.</p>
        ";
        $mail->AltBody = "Your OTP code is: $otp\nThis code will expire in 1 minutes.";
        
        $mail->send();
        
        // For testing, still return OTP in response (remove in production)
        echo json_encode([
            'status' => 'success',
            'message' => 'OTP sent to your email',
            'otp' => $otp // Remove this in production
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}",
            'otp' => $otp // For debugging
        ]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>