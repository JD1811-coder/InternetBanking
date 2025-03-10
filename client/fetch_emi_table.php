<?php
session_start();
include('conf/config.php');

$client_id = $_SESSION['client_id'];
$loan_id = $_GET['loan_id'];

$query = "SELECT la.id, la.loan_type_id, lt.interest_rate, la.loan_amount, 
                 la.loan_duration_years, la.loan_duration_months, la.application_date 
          FROM loan_applications la
          INNER JOIN loan_types lt ON la.loan_type_id = lt.id 
          WHERE la.id = ? AND la.status = 'approved'";

$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $loan_id);
$stmt->execute();
$result = $stmt->get_result();

$count = 1;
$current_date = new DateTime();
while ($row = $result->fetch_object()) {
    $principal = $row->loan_amount;
    $years = $row->loan_duration_years;
    $months = ($years * 12) + $row->loan_duration_months;
    $rate = $row->interest_rate;
    $total_interest = ($principal * $rate * $years) / 100;
    $emi = round(($principal + $total_interest) / $months);

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

    $due_dates = generate_due_dates($row->application_date, $months);

    for ($i = 0; $i < $months; $i++) {
        $due_date = new DateTime($due_dates[$i]);
        $due_date_str = $due_date->format("Y-m-d");

        // **Check if EMI is Paid**
        $payment_check_query = "SELECT status FROM loan_payments 
        WHERE client_id = ? AND loan_id = ? AND emi_date = ? LIMIT 1";

$stmt = $mysqli->prepare($payment_check_query);
$stmt->bind_param('iis', $client_id, $row->id, $due_date_str);
$stmt->execute();
$payment_result = $stmt->get_result();
$payment_row = $payment_result->fetch_assoc();

$is_paid = $payment_row && $payment_row['status'] === 'paid';


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
                echo "<button class='btn btn-primary btn-sm pay-btn' 
                        data-loan-id='" . $row->id . "' 
                        data-emi-index='" . $i . "' 
                        data-emi-amount='" . $emi . "' 
                        data-emi-date='" . $due_date_str . "'>Pay Now</button>";
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
