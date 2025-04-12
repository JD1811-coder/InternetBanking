<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

if (!isset($_SESSION['client_id'])) {
    die("Error: Client ID is missing. Please log in again.");
}

$client_id = $_SESSION['client_id'];

$account_query = "SELECT acc_amount FROM ib_bankaccounts WHERE client_id = ? AND is_active = 1";
$stmt = $mysqli->prepare($account_query);
$stmt->bind_param('i', $client_id);
$stmt->execute();
$account_result = $stmt->get_result();
$account_row = $account_result->fetch_assoc();
$client_balance = $account_row ? $account_row['acc_amount'] : 0;



// Ensure loan_id is provided
if (!isset($_GET['loan_id']) || !is_numeric($_GET['loan_id'])) {
    die("Invalid loan ID!");
}

$loan_id = $_GET['loan_id']; // Get loan_id from URL

// Fetch the correct loan details
$query = "SELECT la.id, la.loan_type_id, lt.interest_rate, la.loan_amount, 
                 la.loan_duration_years, la.loan_duration_months, la.application_date 
          FROM loan_applications la
          INNER JOIN loan_types lt ON la.loan_type_id = lt.id 
          WHERE la.id = ? AND la.status = 'approved'";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $loan_id);
$stmt->execute();
$result = $stmt->get_result();

// if ($result->num_rows > 0) {
//     echo ('loan id found');
// } else {
//     die("Loan not found!");
// }



// Generate EMI due dates function
function generate_due_dates($start_date, $months)
{
    $due_dates = [];
    $current_date = new DateTime($start_date);
    $current_date->modify('+1 month');
    for ($i = 0; $i < $months; $i++) {
        $random_day = rand(1, 28);
        $due_date = $current_date->format("Y-m-") . str_pad($random_day, 2, '0', STR_PAD_LEFT);
        $due_dates[] = $due_date;
        $current_date->modify('+1 month');
    }
    return $due_dates;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_emi'])) {
    header('Content-Type: application/json');

    // Retrieve values from the POST request
    $loan_id = $_POST['loan_id'];
    $emi_amount = $_POST['emi_amount'];
    $emi_date = $_POST['emi_date'];

    // Check if the client has enough balance
    if ($client_balance >= $emi_amount) {
        // Deduct the EMI amount from the client's account
        $update_balance_query = "UPDATE ib_bankaccounts SET acc_amount = acc_amount - ? WHERE client_id = ? AND is_active = 1";
        $stmt = $mysqli->prepare($update_balance_query);
        $stmt->bind_param('di', $emi_amount, $client_id);

        if (!$stmt->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update balance!']);
            exit;
        }

        // Insert the EMI payment record into loan_payments
        $payment_query = "INSERT INTO loan_payments (client_id, loan_id, emi_date, amount, status) 
                  VALUES (?, ?, ?, ?, 'paid') 
                  ON DUPLICATE KEY UPDATE status = 'paid', amount = VALUES(amount)";

        $stmt = $mysqli->prepare($payment_query);
        $stmt->bind_param('iisd', $client_id, $loan_id, $emi_date, $emi_amount);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => "Payment successful! ₹$emi_amount has been deducted."]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Payment processing failed!', 'sql_error' => $stmt->error]);
        }
        exit;


        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => "Payment successful! ₹$emi_amount has been deducted."]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Payment processing failed!', 'sql_error' => $stmt->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Insufficient balance! Please deposit money.']);
    }
    exit;
}





?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>EMI Schedule</title>
    <?php include("dist/_partials/head.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Swal Alert -->
</head>

<body>
    <div class="wrapper">
        <?php include("dist/_partials/nav.php"); ?>
        <?php include("dist/_partials/sidebar.php"); ?>
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>EMI Schedule</h1>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-purple">
                                <div class="card-header">
                                    <h3 class="card-title">EMI Breakdown (Month-wise)</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Month</th>
                                                <th>Due Date</th>
                                                <th>EMI Amount (Rs.)</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $count = 1;
                                            $current_date = new DateTime();
                                            while ($row = $result->fetch_object()) {
                                                $principal = $row->loan_amount;
                                                $years = $row->loan_duration_years;
                                                $months = ($years * 12) + $row->loan_duration_months;
                                                $rate = $row->interest_rate;
                                                $total_interest = ($principal * $rate * $years) / 100;
                                                $emi = round(($principal + $total_interest) / $months);

                                                $due_dates = generate_due_dates($row->application_date, $months);

                                                for ($i = 0; $i < $months; $i++) {
                                                    $due_date = new DateTime($due_dates[$i]);
                                                    $due_date_str = $due_date->format("Y-m-d");

                                                    // Check if payment is already made
                                                    $payment_check_query = "SELECT COUNT(*) AS paid FROM loan_payments 
                        WHERE client_id = ? AND loan_id = ? AND emi_date = ? AND status = 'paid'";

                                                    $stmt = $mysqli->prepare($payment_check_query);
                                                    $stmt->bind_param('iis', $client_id, $row->id, $due_date_str);
                                                    $stmt->execute();
                                                    $payment_result = $stmt->get_result();
                                                    $payment_row = $payment_result->fetch_assoc();
                                                    $is_paid = $payment_row['paid'] > 0;


                                                    echo "<tr>";
                                                    echo "<td>" . $count . "</td>";
                                                    echo "<td>Month " . ($i + 1) . "</td>";
                                                    echo "<td>" . $due_date_str . "</td>";
                                                    echo "<td>" . number_format($emi, 2) . "</td>";
                                                    echo "<td>";

                                                    if ($is_paid) {
                                                        echo "<button class='btn btn-success btn-sm' disabled>Paid</button>";
                                                    } else {
                                                        if ($due_date->format("Y-m") == $current_date->format("Y-m")) {
                                                            echo "<form id='paymentForm-" . $row->id . "-" . $i . "' method='POST' action=''>
                                                                    <input type='hidden' name='loan_id' value='" . $row->id . "'>
                                                                    <input type='hidden' name='emi_date' value='" . $due_date_str . "'>
                                                                    <input type='hidden' name='emi_amount' value='" . $emi . "'>
                                                                  </form>";

                                                                  echo "<button class='btn btn-primary btn-sm' 
                                                                  data-loan-id='" . $row->id . "' 
                                                                  data-emi-index='" . $i . "' 
                                                                  onclick='confirmPayment(" . $row->id . ", " . $i . ", " . $emi . ", \"" . $due_date_str . "\")'>
                                                                  Pay Now
                                                                  </button>";
                                                              
                                                        } else {
                                                            echo "<button class='btn btn-secondary btn-sm' disabled>Upcoming</button>";
                                                        }
                                                    }



                                                    echo "</td>";

                                                    echo "</tr>";
                                                    $count++;
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include("dist/_partials/footer.php"); ?>
    </div>

    <!-- Load jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Your script -->
    <script>
        function confirmPayment(loanId, emiIndex, emiAmount, emiDate) {
    Swal.fire({
        title: "Confirm Payment",
        text: "Are you sure you want to pay ₹" + emiAmount + "?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Pay Now"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "",
                type: "POST",
                data: {
                    pay_emi: '1',
                    loan_id: loanId,
                    emi_date: emiDate,
                    emi_amount: emiAmount
                },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        Swal.fire("Success!", response.message, "success").then(() => {
                            // Update the button to "Paid" without refreshing
                            let button = $("button[data-loan-id='" + loanId + "'][data-emi-index='" + emiIndex + "']");
                            button.replaceWith("<button class='btn btn-success btn-sm' disabled>Paid</button>");
                        });
                    } else {
                        Swal.fire("Error!", response.message, "error");
                    }
                },
                error: function (xhr) {
                    Swal.fire("Error!", "Something went wrong! Try again.", "error");
                }
            });
        }
    });
}

    </script>



</body>

</html>