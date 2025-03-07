<?php
include('conf/config.php');

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);

    $query = "SELECT email FROM iB_staff WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "exists";
    } else {
        echo "available";
    }

    $stmt->close();
}
?>
