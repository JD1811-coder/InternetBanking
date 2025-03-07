<?php
include('conf/config.php');

if (isset($_GET['acc_name'])) {
    $acc_name = trim($_GET['acc_name']);

    $query = "SELECT acc_name FROM iB_bankAccounts WHERE acc_name = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $acc_name);
    $stmt->execute();
    $stmt->store_result();

    $response = ['exists' => $stmt->num_rows > 0];
    echo json_encode($response);
}
?>
