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

// Fetch loan applications
$query = "SELECT la.id,
                 la.loan_type_id,
                 lt.type_name AS loan_type,
                 la.loan_amount,
                 la.income_salary,
                 la.staff_remark,
                 la.application_date,
                 la.status
          FROM loan_applications la
          INNER JOIN loan_types lt ON la.loan_type_id = lt.id
          WHERE la.client_id = ?";

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
    <title>My Loan Applications</title>
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
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>My Loan Applications</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Loan Applications</li>
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
                                    <h3 class="card-title">Loan Applications</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Loan Type</th>
                                                <th>Loan Amount (Rs.)</th>
                                                <th>Income/Salary (Rs.)</th>
                                                <th>Staff Remark</th>
                                                <th>Application Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $count = 1;
                                            if ($result && $result->num_rows > 0) {
                                                while ($row = $result->fetch_object()) {
                                                    echo "<tr>";
                                                    echo "<td>" . $count . "</td>";
                                                    echo "<td>" . htmlspecialchars($row->loan_type) . "</td>";
                                                    echo "<td>" . number_format($row->loan_amount, 2) . "</td>";
                                                    echo "<td>" . number_format($row->income_salary, 2) . "</td>"; // New income column
                                                    echo "<td>" . htmlspecialchars($row->staff_remark) . "</td>";
                                                    echo "<td>" . date('d M Y', strtotime($row->application_date)) . "</td>";
                                                    echo "<td>" . htmlspecialchars($row->status) . "</td>";
                                                    echo "</tr>";
                                                    $count++;
                                                }
                                            } else {
                                                echo "<tr><td colspan='7' class='text-center'>No loan applications found.</td></tr>";
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

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>

    <!-- Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
</body>

</html>