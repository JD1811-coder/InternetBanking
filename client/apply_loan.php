<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$client_id = $_SESSION['client_id'];

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Fetch loan types from the database
$loan_types = array();
$type_query = "SELECT id, type_name, max_amount, interest_rate FROM loan_types WHERE is_active = 1"; // Only active loans
$type_stmt = $mysqli->prepare($type_query);
$type_stmt->execute();
$type_result = $type_stmt->get_result();
while ($type = $type_result->fetch_assoc()) {
    $loan_types[] = $type;
}

if (isset($_POST['apply_for_loan'])) {
    $applicant_name = $_POST['applicant_name'];
    $loan_amount = $_POST['loan_amount'];
    $loan_type_id = $_POST['loan_type_id'];
    $staff_remark = $_POST['staff_remark'];
    $income_salary = $_POST['income_salary']; 
    $client_id = $_SESSION['client_id'];

    // Check if loan amount exceeds maximum allowed
    foreach ($loan_types as $type) {
        if ($type['id'] == $loan_type_id && $loan_amount > $type['max_amount']) {
            $err = "Loan amount exceeds maximum allowed for the selected loan type.";
            break;
        }
    }

   if (!isset($err)) {
    // Ensure admin_remark has a default value if not provided
    $admin_remark = "Pending Review"; // Default value instead of NULL

    $query = "INSERT INTO loan_applications (applicant_name, loan_amount, staff_remark, loan_type_id, client_id, income_salary, admin_remark) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('sdsiids', $applicant_name, $loan_amount, $staff_remark, $loan_type_id, $client_id, $income_salary, $admin_remark);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $success = "Loan application submitted successfully!";
    } else {
        $err = "Error applying for the loan. Try again.";
    }
}

}



?>


<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
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
                            <h1>Apply for Loan</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Apply for Loan</li>
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
                                    <h3 class="card-title">Fill All Fields</h3>
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
    <label for="applicant_name">Applicant Name</label>
    <input type="text" name="applicant_name" id="applicant_name" class="form-control" 
           required pattern="^[a-zA-Z\s]{2,50}$" 
           title="Applicant name should only contain letters and spaces, and be 2 to 50 characters long.">
</div>


                                            <div class="col-md-6 form-group">
                                                <label for="loan_type_id">Loan Type</label>
                                                <select name="loan_type_id" id="loan_type_id" class="form-control"
                                                    required onchange="updateLoanDetails()">
                                                    <option value="">Select Loan Type</option>
                                                    <?php foreach ($loan_types as $type) { ?>
                                                    <option value="<?php echo $type['id']; ?>">
                                                        <?php echo htmlspecialchars($type['type_name']); ?>
                                                    </option>
                                                    <?php } ?>
                                                </select>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="interest_rate">Interest Rate (%)</label>
                                                <input type="text" name="interest_rate" id="interest_rate"
                                                    class="form-control" readonly>
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label for="loan_amount">Loan Amount</label>
                                                <input type="number" name="loan_amount" id="loan_amount"
                                                    class="form-control" required>
                                                <!-- <small id="max_amount_label" class="form-text text-muted">Max Amount:
                                                </small> -->
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="income_salary">Monthly Income/Salary</label>
                                                <input type="number" name="income_salary" id="income_salary"
                                                    class="form-control" required>
                                            </div>

                                        </div>


                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <label for="staff_remark">Remarks</label>
                                                <textarea name="staff_remark" id="staff_remark" class="form-control"
                                                    required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" name="apply_for_loan" class="btn btn-success">Submit
                                            Application</button>
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
    <!-- bs-custom-file-input -->
    <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>


    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const loanTypes = <?php echo json_encode($loan_types); ?>;

        function updateLoanDetails() {
            const selectedTypeId = document.getElementById('loan_type_id').value;
            console.log('Selected Type ID:', selectedTypeId);

            const loanType = loanTypes.find(type => type.id == selectedTypeId);
            console.log('Found Loan Type:', loanType);

            if (loanType) {
                document.getElementById('interest_rate').value = loanType.interest_rate + "%";
                document.getElementById('loan_amount').max = loanType.max_amount;
                document.getElementById('loan_amount').placeholder = "Enter up to " + loanType.max_amount;
            } else {
                document.getElementById('interest_rate').value = '';
                document.getElementById('loan_amount').placeholder = "Amount";
            }
        }

        document.getElementById('loan_type_id').addEventListener('change', updateLoanDetails);
    });
    </script>




    <script type="text/javascript">
    $(document).ready(function() {
        bsCustomFileInput.init();
    });
    </script>
</body>

</html>