<?php
include('conf/config.php');

$data = json_decode(file_get_contents("php://input"), true);
$type_name = trim($data['type_name']);

$stmt = $mysqli->prepare("SELECT type_name FROM loan_types WHERE type_name = ?");
$stmt->bind_param('s', $type_name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['duplicate' => true]);
} else {
    echo json_encode(['duplicate' => false]);
}
?>
