<?php
session_start();
include('conf/config.php');

header('Content-Type: application/json');

$response = ["success" => false];

// Get values & trim spaces
$type_name = trim($_POST['type_name']);
$description = trim($_POST['description']);
$interest_rate = trim($_POST['interest_rate']);
$max_amount = trim($_POST['max_amount']);

// Validation
if ($type_name === "" || $description === "" || $interest_rate === "" || $max_amount === "") {
    $response["error"] = "All fields are required!";
} elseif (!preg_match("/^[a-zA-Z\s_]+$/", $type_name)) {
    $response["error"] = "Loan Type Name can only contain letters, spaces, and underscores!";
} elseif (!is_numeric($interest_rate) || $interest_rate < 0) {
    $response["error"] = "Interest Rate must be a valid non-negative number!";
} elseif (!is_numeric($max_amount) || $max_amount < 0) {
    $response["error"] = "Maximum Loan Amount must be a valid non-negative number!";
} else {
    // Check if loan type already exists
    $check_query = "SELECT type_name FROM loan_types WHERE type_name = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("s", $type_name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $response["error"] = "Loan Type Name already exists!";
    } else {
        // Insert new loan type
        $query = "INSERT INTO loan_types (type_name, description, interest_rate, max_amount, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssdd', $type_name, $description, $interest_rate, $max_amount);
        
        if ($stmt->execute()) {
            $response["success"] = true;
        } else {
            $response["error"] = "Database error. Please try again!";
        }
    }
}

echo json_encode($response);
?>
