<?php
include('conf/config.php');

function check_login()
{
    global $mysqli;

    // If session is not set, redirect to login
    if (!isset($_SESSION['staff_id'])) {
        header("Location: pages_staff_index.php");
        exit();
    }

    $staff_id = $_SESSION['staff_id'];

    // Check staff's active status
    $query = "SELECT is_active FROM iB_staff WHERE staff_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $staff_id);
    $stmt->execute();
    $stmt->bind_result($is_active);
    $stmt->fetch();
    $stmt->close();

    // If staff is disabled, log them out and redirect
    if ($is_active != 1) {
        session_destroy();
        header("Location: pages_staff_index.php");
        exit();
    }
}
?>
