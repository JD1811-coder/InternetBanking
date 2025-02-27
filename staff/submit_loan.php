<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

if (isset($_POST['submitLoan'])) {
    // Retrieve form data
    $applicant_name = $_POST['applicant_name'];
    $loan_amount = $_POST['loan_amount'];
    $staff_remark = isset($_POST['staff_remark']) ? $_POST['staff_remark'] : null;
    $staff_id = $_SESSION['staff_id']; // Staff ID for tracking

    // Validate inputs
    if (empty($applicant_name) || empty($loan_amount)) {
        $_SESSION['error'] = "Please fill in all required fields.";
        header("Location: loan_application.php");
        exit();
    }

    // Insert loan application into the database
    $query = "INSERT INTO loan_applications (applicant_name, loan_amount, staff_remark) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param('sds', $applicant_name, $loan_amount, $staff_remark);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $_SESSION['success'] = "Loan application submitted successfully!";
        } else {
            $_SESSION['error'] = "Failed to submit the loan application. Please try again.";
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: Unable to prepare the query.";
    }

    // Redirect back to the loan application page
    header("Location: pages_loans.php");
    exit();
}
?>