<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure client is logged in
if (!isset($_SESSION['client_id'])) {
    die("Error: Client ID is missing. Please log in again.");
}

$client_id = $_SESSION['client_id']; // Logged-in client's ID

// Fetch only approved loan applications
$query = "SELECT la.id,
                 la.loan_type_id,
                 lt.type_name AS loan_type,
                 lt.interest_rate,
                 la.loan_amount,
                 la.loan_duration_years,
                 la.loan_duration_months,
                 la.status
          FROM loan_applications la
          INNER JOIN loan_types lt ON la.loan_type_id = lt.id
          WHERE la.client_id = ? AND la.status = 'approved'";

$stmt = $mysqli->prepare($query);
if (!$stmt) {
    error_log("Query Preparation Failed: " . $mysqli->error);
    die("An unexpected error occurred. Please try again later.");
}

$stmt->bind_param('i', $client_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Loan EMI Management</title>
    <?php include("dist/_partials/head.php"); ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("dist/_partials/nav.php"); ?>
        <!-- Sidebar -->
        <?php include("dist/_partials/sidebar.php"); ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Loan EMI Management</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">EMI Management</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main Content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-purple">
                                <div class="card-header">
                                    <h3 class="card-title">Approved Loan Applications</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Loan Type</th>
                                                <th>Loan Amount (Rs.)</th>
                                                <th>Interest Rate (%)</th>
                                                <th>Loan Duration</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $count = 1;
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_object()) {
                                                    $loanDuration = "";
                                                    if (!empty($row->loan_duration_years) && $row->loan_duration_years > 0) {
                                                        $loanDuration .= $row->loan_duration_years . " Year" . ($row->loan_duration_years > 1 ? "s" : "");
                                                    }
                                                    if (!empty($row->loan_duration_months) && $row->loan_duration_months > 0) {
                                                        if (!empty($loanDuration)) {
                                                            $loanDuration .= " ";
                                                        }
                                                        $loanDuration .= $row->loan_duration_months . " Month" . ($row->loan_duration_months > 1 ? "s" : "");
                                                    }
                                                    $loanDuration = !empty($loanDuration) ? htmlspecialchars($loanDuration) : "N/A";

                                                    echo "<tr>";
                                                    echo "<td>" . $count . "</td>";
                                                    echo "<td>" . htmlspecialchars($row->loan_type) . "</td>";
                                                    echo "<td>" . number_format($row->loan_amount, 2) . "</td>";
                                                    echo "<td>" . number_format($row->interest_rate, 2) . "</td>";
                                                    echo "<td>" . $loanDuration . "</td>";
                                                    echo "<td><a href='installment_details.php?loan_id=" . $row->id . "' class='btn btn-primary btn-sm'>Installment</a></td>";

                                                    echo "</tr>";
                                                    $count++;
                                                }
                                            } else {
                                                echo "<tr><td colspan='6' class='text-center'>No approved loan applications found.</td></tr>";
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

        <!-- Footer -->
        <?php include("dist/_partials/footer.php"); ?>

    </div>

    <!-- Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
</body>

</html>
