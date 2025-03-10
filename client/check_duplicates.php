<?php
session_start();
include('conf/config.php'); // Include your database configuration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fieldName = $_POST['fieldName'];
    $fieldValue = trim($_POST['fieldValue']);
    $client_id = $_POST['client_id'];

    // Map field names to database column names
    $columnMap = [
        'inputAadhar' => 'aadhar_number',
        'inputEmail' => 'email',
        'inputPhone' => 'phone'
    ];

    if (isset($columnMap[$fieldName])) {
        $columnName = $columnMap[$fieldName];

        // Prepare and execute the query
        $query = "SELECT COUNT(*) FROM iB_clients WHERE $columnName = ? AND client_id != ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ss', $fieldValue, $client_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo 'duplicate'; // Indicate a duplicate
        } else {
            echo 'unique'; // Indicate a unique value
        }
    } else {
        echo 'invalid_field'; // Handle invalid field names
    }
} else {
    echo 'invalid_request'; // Handle non-POST requests
}
?>