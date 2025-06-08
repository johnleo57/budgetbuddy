<?php
session_start();
include_once "config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['status' => 'error', 'message' => ''];
    
    if (!isset($_POST['email']) || !isset($_POST['otp'])) {
        $response['message'] = 'Email and OTP are required';
        echo json_encode($response);
        exit();
    }

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $userOtp = mysqli_real_escape_string($conn, $_POST['otp']);

    // Verify session data exists
    if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_email']) || !isset($_SESSION['otp_expire'])) {
        $response['message'] = 'OTP session expired. Please request a new OTP.';
        echo json_encode($response);
        exit();
    }

    // Verify email matches
    if ($_SESSION['otp_email'] !== $email) {
        $response['message'] = 'Email does not match OTP recipient';
        echo json_encode($response);
        exit();
    }

    // Verify OTP hasn't expired
    if (time() > $_SESSION['otp_expire']) {
        $response['message'] = 'OTP has expired. Please request a new one.';
        echo json_encode($response);
        exit();
    }

    // Verify OTP code matches
    if ($_SESSION['otp'] != $userOtp) {
        $response['message'] = 'Invalid OTP code';
        echo json_encode($response);
        exit();
    }

    // Mark OTP as verified
    $_SESSION['otp_verified'] = true;
    $_SESSION['otp_verified_email'] = $email;

    $response['status'] = 'success';
    $response['message'] = 'OTP verified successfully';
    echo json_encode($response);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>