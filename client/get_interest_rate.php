<?php
include('conf/config.php');

if (isset($_GET['loan_type_id'])) {
    $loan_type_id = intval($_GET['loan_type_id']);

    $query = "SELECT interest_rate FROM loan_types WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $loan_type_id);
    $stmt->execute();
    $stmt->bind_result($interest_rate);
    $stmt->fetch();
    
    echo json_encode(["interest_rate" => $interest_rate]);
    $stmt->close();
}
?>
