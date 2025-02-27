<?php
include('conf/config.php'); // Database connection

if (isset($_POST['account_number'])) {
    $account_number = $_POST['account_number'];

    $query = "SELECT acc_name FROM ib_bankaccounts WHERE account_number = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $account_number);
    $stmt->execute();
    $stmt->bind_result($acc_name);
    $stmt->fetch();
    $stmt->close();

    echo json_encode(['acc_name' => $acc_name]);
}
?>
