<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$client_id = $_SESSION['client_id'];

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Fetch loan types
$loan_types = array();
$type_query = "SELECT id, type_name FROM loan_types WHERE is_active = 1";
$type_stmt = $mysqli->prepare($type_query);
$type_stmt->execute();
$type_result = $type_stmt->get_result();
while ($type = $type_result->fetch_assoc()) {
    $loan_types[] = $type;
}

$errors = []; 
$success = "";

// Form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_for_loan'])) {
    $applicant_name = trim($_POST['applicant_name']);
    $loan_amount = trim($_POST['loan_amount']);
    $loan_type_id = $_POST['loan_type_id'];
    $staff_remark = trim($_POST['staff_remark']);
    $income_salary = trim($_POST['income_salary']);
    $loan_duration_years = $_POST['loan_duration_years'];
    $loan_duration_months = $_POST['loan_duration_months'];

    // Validation
    if (empty($applicant_name)) {
        $errors['applicant_name'] = "Applicant name is required.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $applicant_name)) {
        $errors['applicant_name'] = "Only letters and spaces allowed.";
    }

    if (!preg_match("/^\d{1,6}$/", $loan_amount) || $loan_amount <= 0) {
        $errors['loan_amount'] = "Enter a valid amount.";
    }

    if (!preg_match("/^\d{1,6}$/", $income_salary)) {
        $errors['income_salary'] = "Enter a valid salary.";
    }

    if (!preg_match("/^\d{1,2}$/", $loan_duration_years) || $loan_duration_years < 0 || $loan_duration_years > 20) {
        $errors['loan_duration_years'] = "Enter valid years (0-20).";
    }

    if (!preg_match("/^\d{1,2}$/", $loan_duration_months) || $loan_duration_months < 0 || $loan_duration_months > 12) {
        $errors['loan_duration_months'] = "Enter valid months (0-12).";
    }

    if (empty($errors)) {
        $admin_remark = "Pending Review";

        $query = "INSERT INTO loan_applications (applicant_name, loan_amount, staff_remark, loan_type_id, client_id, 
                  income_salary, admin_remark, loan_duration_years, loan_duration_months) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sdsiidsii', $applicant_name, $loan_amount, $staff_remark, $loan_type_id, $client_id, 
                          $income_salary, $admin_remark, $loan_duration_years, $loan_duration_months);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $success = "Loan application submitted successfully!";
            header("Location: loan_status.php");
    exit;
        } else {
            $errors['general'] = "Error applying for loan.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<?php include("dist/_partials/head.php"); ?>
<body>
    <div class="wrapper">
        <?php include("dist/_partials/nav.php"); ?>
        <?php include("dist/_partials/sidebar.php"); ?>

        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-purple">
                                <div class="card-header">
                                    <h3 class="card-title">Apply for Loan</h3>
                                </div>
                                <form method="post">
                                    <div class="card-body">
                                        <?php if (!empty($success)) { ?>
                                            <script>
                                                document.addEventListener("DOMContentLoaded", function() {
                                                    Swal.fire({
                                                        title: "Success!",
                                                        text: "<?php echo $success; ?>",
                                                        icon: "success",
                                                        confirmButtonText: "OK"
                                                    });
                                                });
                                            </script>
                                        <?php } ?>

                                        <div class="form-group">
                                            <label>Applicant Name</label>
                                            <input type="text" name="applicant_name" class="form-control" 
                                                   value="<?php echo $_POST['applicant_name'] ?? ''; ?>">
                                            <small class="text-danger"><?php echo $errors['applicant_name'] ?? ''; ?></small>
                                        </div>

                                        <div class="form-group">
                                            <label>Loan Type</label>
                                            <select name="loan_type_id" class="form-control">
                                                <option value="">Select Loan Type</option>
                                                <?php foreach ($loan_types as $type) { ?>
                                                    <option value="<?php echo $type['id']; ?>" 
                                                        <?php echo (isset($_POST['loan_type_id']) && $_POST['loan_type_id'] == $type['id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($type['type_name']); ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Loan Amount</label>
                                            <input type="number" name="loan_amount" class="form-control" 
                                                   value="<?php echo $_POST['loan_amount'] ?? ''; ?>">
                                            <small class="text-danger"><?php echo $errors['loan_amount'] ?? ''; ?></small>
                                        </div>

                                        <div class="form-group">
                                            <label>Monthly Income/Salary</label>
                                            <input type="number" name="income_salary" class="form-control" 
                                                   value="<?php echo $_POST['income_salary'] ?? ''; ?>">
                                            <small class="text-danger"><?php echo $errors['income_salary'] ?? ''; ?></small>
                                        </div>

                                        <div class="form-group">
                                            <label>Loan Duration (Years)</label>
                                            <input type="number" name="loan_duration_years" class="form-control" 
                                                   value="<?php echo $_POST['loan_duration_years'] ?? ''; ?>">
                                            <small class="text-danger"><?php echo $errors['loan_duration_years'] ?? ''; ?></small>
                                        </div>

                                        <div class="form-group">
                                            <label>Loan Duration (Months)</label>
                                            <input type="number" name="loan_duration_months" class="form-control" 
                                                   value="<?php echo $_POST['loan_duration_months'] ?? ''; ?>">
                                            <small class="text-danger"><?php echo $errors['loan_duration_months'] ?? ''; ?></small>
                                        </div>

                                        <div class="form-group">
                                            <label>Remarks</label>
                                            <textarea name="staff_remark" class="form-control"><?php echo $_POST['staff_remark'] ?? ''; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="apply_for_loan" class="btn btn-success">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include("dist/_partials/footer.php"); ?>
    </div>

    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>
</html>
