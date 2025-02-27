<?php
session_start();
include('conf/config.php'); // Database connection

if (isset($_POST['verify_otp'])) {
    $user_otp = $_POST['otp'];
    $phone = $_SESSION['phone'];

    $stmt = $mysqli->prepare("SELECT * FROM otp_verification WHERE phone = ? AND otp = ?");
    $stmt->bind_param("ss", $phone, $user_otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['verified'] = true;
        header("Location: reset_password.php");
    } else {
        echo "Invalid OTP. Try again.";
    }
}
?>
