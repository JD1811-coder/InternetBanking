<?php
session_start();
include('conf/config.php');

if (isset($_POST['reset_password'])) {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_SESSION['phone'];

    $stmt = $mysqli->prepare("UPDATE ib_clients SET password = ? WHERE phone = ?");
    $stmt->bind_param("ss", $new_password, $phone);
    $stmt->execute();

    echo "Password updated successfully! <a href='login.php'>Login</a>";

    session_destroy(); // Clear session after reset
}
?>
