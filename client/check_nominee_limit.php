<?php
session_start();
include('conf/config.php');

$client_id = $_SESSION['client_id'];

$countQuery = "SELECT COUNT(*) AS nominee_count FROM iB_nominees WHERE client_id = ?";
$stmt = $mysqli->prepare($countQuery);
$stmt->bind_param('i', $client_id);
$stmt->execute();
$stmt->bind_result($nominee_count);
$stmt->fetch();
$stmt->close();

echo json_encode(['nominee_count' => $nominee_count]);
?>
