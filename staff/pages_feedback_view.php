<?php
// Enable error reporting (for development only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

// Ensure the session variable exists
if (!isset($_SESSION['staff_id'])) {
    die("Staff ID is not set in session.");
}

$staff_id = $_SESSION['staff_id'];

// Ensure database connection
if (!isset($mysqli) || $mysqli->connect_error) {
    die("Database connection failed: " . (isset($mysqli->connect_error) ? $mysqli->connect_error : "mysqli object not set"));
}

// Query to fetch feedback with client details
$query = "SELECT cf.*, c.name as client_name, c.email as client_email 
          FROM client_feedback cf
          LEFT JOIN ib_clients c ON cf.client_id = c.client_id";

$feedbackResult = $mysqli->query($query);

if (!$feedbackResult) {
    die("SQL Error: " . $mysqli->error . "<br>Query: " . $query);
}

// Function to truncate text
function truncateText($text, $length = 50) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Client Feedback</title>
    <?php include("dist/_partials/head.php"); ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <!-- Navigation & Sidebar -->
        <?php include("dist/_partials/nav.php"); ?>
        <?php include("dist/_partials/sidebar.php"); ?>

        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Client Feedback</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Client Feedback</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Client Feedback History</h3>
                            </div>
                            <div class="card-body">
                                <table id="feedbackTable" class="table table-bordered table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Client Name</th>
                                            <th>Client Email</th>
                                            <th>Subject</th>
                                            <th>Feedback</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cnt = 1;
                                        if ($feedbackResult->num_rows > 0) {
                                            while ($row = $feedbackResult->fetch_object()) {
                                                // Truncate feedback message for table view
                                                $truncated_message = truncateText($row->feedback_message, 100);
                                                
                                                echo "<tr>";
                                                echo "<td>" . $cnt . "</td>";
                                                echo "<td>" . htmlspecialchars($row->client_name ?? 'N/A') . "</td>";
                                                echo "<td>" . htmlspecialchars($row->client_email ?? 'N/A') . "</td>";
                                                echo "<td>" . htmlspecialchars($row->subject ?? '') . "</td>";
                                                echo "<td>" . htmlspecialchars($truncated_message) . "</td>";
                                                
                                                echo "<td>
                                                        <button type='button' 
                                                                class='btn btn-info btn-sm' 
                                                                data-toggle='modal' 
                                                                data-target='#feedbackModal" . $row->id . "'>
                                                            <i class='fas fa-eye'></i> View
                                                        </button>
                                                    </td>";
                                                echo "</tr>";
                                                
                                                // Modal for full feedback view
                                                echo "<div class='modal fade' id='feedbackModal" . $row->id . "'>
                                                        <div class='modal-dialog modal-lg'> <!-- Changed to modal-lg for larger view -->
                                                            <div class='modal-content'>
                                                                <div class='modal-header'>
                                                                    <h4 class='modal-title'>Feedback Details</h4>
                                                                    <button type='button' class='close' data-dismiss='modal'>&times;</button>
                                                                </div>
                                                                <div class='modal-body'>
                                                                    <div class='row'>
                                                                        <div class='col-md-6'>
                                                                            <p><strong>Client:</strong> " . htmlspecialchars($row->client_name ?? 'N/A') . "</p>
                                                                        </div>
                                                                        <div class='col-md-6'>
                                                                            <p><strong>Client Email:</strong> " . htmlspecialchars($row->client_email ?? 'N/A') . "</p>
                                                                        </div>
                                                                    </div>
                                                                    <p><strong>Subject:</strong> " . htmlspecialchars($row->subject ?? '') . "</p>
                                                                    <hr>
                                                                    <p><strong>Message:</strong></p>
                                                                    <div class='border p-3 bg-light'>
                                                                        " . nl2br(htmlspecialchars($row->feedback_message)) . "
                                                                    </div>
                                                                </div>
                                                                <div class='modal-footer'>
                                                                    <button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>";
                                                $cnt++;
                                            }
                                        } else {
                                            echo "<tr><td colspan='6'>No feedback found.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include("dist/_partials/footer.php"); ?>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="plugins/datatables/jquery.dataTables.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#feedbackTable").DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
            });
        });
    </script>
</body>
</html>