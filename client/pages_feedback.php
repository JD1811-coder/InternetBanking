<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$client_id = $_SESSION['client_id'];

// Fetch client details from ib_clients
$query = "SELECT name, email FROM ib_clients WHERE client_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $client_id);
$stmt->execute();
$stmt->bind_result($client_name, $client_email);
$stmt->fetch();
$stmt->close();


if (isset($_POST['submit_feedback'])) {
    $subject = $_POST['subject'];
    $feedback_message = $_POST['feedback_message'];

    // Insert feedback into the database
    $query = "INSERT INTO client_feedback (client_id, subject, feedback_message) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iss', $client_id, $subject, $feedback_message);
    if ($stmt->execute()) {
        $success = "Thank you for your feedback!";
    } else {
        $err = "Error submitting feedback. Please try again.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("dist/_partials/head.php"); ?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("dist/_partials/nav.php"); ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include("dist/_partials/sidebar.php"); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Feedback Form</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Feedback Form</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- left column -->
                        <div class="col-md-12">
                            <!-- general form elements -->
                            <div class="card card-purple">
                                <div class="card-header">
                                    <h3 class="card-title">We Value Your Feedback</h3>
                                </div>
                                <!-- form start -->
                                <form method="post" enctype="multipart/form-data" role="form">
                                    <div class="card-body">
                                        <?php if (isset($success)) { ?>
                                            <div class="alert alert-success"><?php echo $success; ?></div>
                                        <?php } ?>
                                        <?php if (isset($err)) { ?>
                                            <div class="alert alert-danger"><?php echo $err; ?></div>
                                        <?php } ?>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="client_name">Your Name</label>
                                                <input type="text" name="client_name" id="client_name" class="form-control" value="<?php echo $client_name; ?>" readonly>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="client_email">Your Email</label>
                                                <input type="email" name="client_email" id="client_email" class="form-control" value="<?php echo $client_email; ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <label for="subject">Subject</label>
                                                <input type="text" name="subject" id="subject" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <label for="feedback_message">Your Feedback</label>
                                                <textarea name="feedback_message" id="feedback_message" class="form-control" rows="5" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" name="submit_feedback" class="btn btn-success">Submit Feedback</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <?php include("dist/_partials/footer.php"); ?>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            // Optional: Add any custom JavaScript here
        });
    </script>
</body>

</html>
