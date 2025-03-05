<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

$staff_id = $_SESSION['staff_id'];

// Determine the loan ID from GET or POST
if(isset($_GET['id']) || isset($_POST['id'])) {
    if(isset($_GET['id'])) {
        $id = intval($_GET['id']);
    } else {
        $id = intval($_POST['id']);
    }
} else {
    die("No loan ID provided.");
}

// Fetch loan details
$query = "SELECT la.*, lt.type_name, lt.max_amount, lt.interest_rate, la.income_salary, 
                 la.loan_duration_years, la.loan_duration_months 
          FROM loan_applications la
          LEFT JOIN loan_types lt ON la.loan_type_id = lt.id
          WHERE la.id = ?";


$stmt = $mysqli->prepare($query);
if(!$stmt){
    error_log("Query Preparation Failed: " . $mysqli->error);
    die("An error occurred. Please try again later.");
}
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$loan = $result->fetch_object();

// Process review submission
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $decision = $_POST['decision'];
    $staff_remark = $_POST['staff_remark'];
    
if($_SESSION['admin_level'] == 'admin') {
    $status = ($decision == 'approved') ? 'approved' : 'rejected';
} else {
    $status = ($decision == 'recommend') ? 'recommended' : 'rejected';
}

    $update = "UPDATE loan_applications SET 
               status = ?, 
               staff_remark = ?, 
               reviewed_by = ?, 
               review_date = NOW() 
               WHERE id = ?";
    $stmt = $mysqli->prepare($update);
    if(!$stmt){
        error_log("Update Query Preparation Failed: " . $mysqli->error);
        die("An error occurred. Please try again later.");
    }
    $stmt->bind_param('ssii', $status, $staff_remark, $staff_id, $id);
    $stmt->execute();
    header("Location: pages_loans.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<?php include("dist/_partials/head.php"); ?>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include("dist/_partials/nav.php"); ?>
        <?php include("dist/_partials/sidebar.php"); ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Review Loan Application</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Application Details</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Applicant Name:</dt>
                                <dd class="col-sm-9"><?php echo htmlspecialchars($loan->applicant_name); ?></dd>

                                <dt class="col-sm-3">Loan Type:</dt>
                                <dd class="col-sm-9">
                                    <?php echo htmlspecialchars($loan->type_name); ?>
                                    (Max: Rs. <?php echo number_format($loan->max_amount); ?>)
                                </dd>

                                <dt class="col-sm-3">Requested Amount:</dt>
                                <dd class="col-sm-9">Rs. <?php echo number_format($loan->loan_amount, 2); ?></dd>

                                <dt class="col-sm-3">Application Date:</dt>
                                <dd class="col-sm-9">
                                    <?php echo date('d/m/Y H:i', strtotime($loan->application_date)); ?>
                                </dd>
                                <dt class="col-sm-3">Income/Salary:</dt>
                                <dd class="col-sm-9">Rs. <?php echo number_format($loan->income_salary, 2); ?></dd>
                                <dt class="col-sm-3">Loan Duration:</dt>
<dd class="col-sm-9">
    <?php echo htmlspecialchars($loan->loan_duration_years ?? 0); ?> Years
    <?php echo htmlspecialchars($loan->loan_duration_months ?? 0); ?> Months
</dd>

                            </dl>


                            <form method="post">
                                <!-- Pass the loan ID with a hidden input field -->
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($loan->id); ?>">

                                <div class="form-group">
                                    <label>Review Decision:</label>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="approved" name="decision"
                                            value="<?php echo ($_SESSION['admin_level'] == 'admin') ? 'approved' : 'recommend'; ?>"
                                            required>
                                        <label class="custom-control-label" for="approved">
                                            <?php echo ($_SESSION['admin_level'] == 'admin') ? 'approved' : 'Recommend for Approval'; ?>
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" id="reject" name="decision"
                                            value="reject">
                                        <label class="custom-control-label" for="reject">Reject</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Remark:</label>
                                    <textarea class="form-control" name="staff_remark" rows="3" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include("dist/_partials/footer.php"); ?>
    </div>
</body>

</html>