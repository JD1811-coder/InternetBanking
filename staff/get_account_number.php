<?php
include('conf/config.php'); // Ensure this is included

if (isset($_POST['account_name'])) {
    $account_name = trim($_POST['account_name']);

    $query = "SELECT account_number FROM ib_bankaccounts WHERE acc_name = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $account_name);
    $stmt->execute();
    $stmt->bind_result($account_number);
    $stmt->fetch();
    $stmt->close();

    if ($account_number) {
        echo json_encode(['success' => true, 'account_number' => $account_number]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
