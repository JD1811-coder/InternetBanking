<?php
session_start();

// Unset all session variables
unset($_SESSION['client_id']);
session_destroy();

// Destroy cookies
if (isset($_COOKIE['client_id'])) {
    setcookie('client_id', '', time() - 3600, "/"); // Expire the cookie by setting past time
}

// Redirect to login or homepage
header("Location: pages_client_index.php");
exit;
?>
