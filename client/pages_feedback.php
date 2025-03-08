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

$errors = [];

if (isset($_POST['submit_feedback'])) {
    $subject = trim($_POST['subject']);
    $feedback_message = trim($_POST['feedback_message']);

    // Validation
    if (empty($subject)) {
        $errors['subject'] = "Subject is required.";
    }
    if (empty($feedback_message)) {
        $errors['feedback_message'] = "Feedback message cannot be empty.";
    }

    if (empty($errors)) {
        // Insert feedback into the database
        $query = "INSERT INTO client_feedback (client_id, subject, feedback_message) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('iss', $client_id, $subject, $feedback_message);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Thank you for your feedback!";
            header("Location: ".$_SERVER['PHP_SELF']); // Redirect to prevent resubmission
            exit();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include("dist/_partials/head.php"); ?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <?php include("dist/_partials/nav.php"); ?>
        <?php include("dist/_partials/sidebar.php"); ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Complain Form</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Complain Form</li>
                            </ol>
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
                                    <h3 class="card-title">We Value Your Complain</h3>
                                </div>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="card-body">

                                        <!-- SweetAlert for Success -->
                                        <?php if (isset($_SESSION['success'])) { ?>
                                            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                                            <script>
                                                document.addEventListener("DOMContentLoaded", function() {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Success!',
                                                        text: '<?php echo $_SESSION['success']; ?>',
                                                        confirmButtonColor: '#28a745'
                                                    }).then(() => {
                                                        // Remove success message after showing it
                                                        window.location.href = "<?php echo $_SERVER['PHP_SELF']; ?>";
                                                    });
                                                });
                                            </script>
                                            <?php unset($_SESSION['success']); ?>
                                        <?php } ?>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="client_name">Your Name</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($client_name); ?>" readonly>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="client_email">Your Email</label>
                                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($client_email); ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <label for="subject">Subject</label>
                                                <input type="text" name="subject" id="subject" class="form-control" value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>" required>
                                                <?php if (isset($errors['subject'])) { ?>
                                                    <small class="text-danger"><?php echo $errors['subject']; ?></small>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <label for="feedback_message">Your Complain</label>
                                                <textarea name="feedback_message" id="feedback_message" class="form-control" rows="5" required><?php echo isset($_POST['feedback_message']) ? htmlspecialchars($_POST['feedback_message']) : ''; ?></textarea>
                                                <?php if (isset($errors['feedback_message'])) { ?>
                                                    <small class="text-danger"><?php echo $errors['feedback_message']; ?></small>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="submit_feedback" class="btn btn-success">Submit Complain</button>
                                    </div>
                                </form>
                            </div>
                        </div>
            </section>
        </div>

        <?php include("dist/_partials/footer.php"); ?>
    </div>

    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
</body>

</html>
