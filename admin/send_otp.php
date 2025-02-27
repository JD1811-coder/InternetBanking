<?php
require __DIR__ . '/vendor/autoload.php'; // Load Twilio SDK
use Twilio\Rest\Client;
session_start();
include('conf/config.php'); // Database connection

if (isset($_POST['send_otp'])) {
    $phone = $_POST['phone'];
    $otp = rand(100000, 999999); // Generate OTP

    $_SESSION['phone'] = $phone; // Store phone in session

    // Save OTP to database
    $stmt = $mysqli->prepare("INSERT INTO otp_verification (phone, otp) VALUES (?, ?) ON DUPLICATE KEY UPDATE otp=?");
    $stmt->bind_param("sss", $phone, $otp, $otp);
    $stmt->execute();
    
    // Twilio Credentials
    $sid = "your_account_sid";
    $token = "your_auth_token";
    $twilio = new Client($sid, $token);
    $twilio_number = "+your_twilio_number"; 

    try {
        $message = $twilio->messages->create(
            $phone,
            ["from" => $twilio_number, "body" => "Your OTP is: " . $otp]
        );
        header("Location: verify_otp.php"); // Redirect to OTP verification
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
