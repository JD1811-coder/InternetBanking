<?php
include('conf/config.php');

if (isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $query = "SELECT name FROM iB_staff WHERE name = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $name);
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
