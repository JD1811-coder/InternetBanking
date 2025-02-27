<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

if (isset($_POST['add_loan_type'])) {
    $type_name = $_POST['type_name'];
    $description = $_POST['description'];
    $interest_rate = $_POST['interest_rate'];
    $max_amount = $_POST['max_amount'];

    $query = "INSERT INTO loan_types (type_name, description, interest_rate, max_amount, created_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ssdd', $type_name, $description, $interest_rate, $max_amount);
    $stmt->execute();

    if ($stmt) {
        $success = "Loan Type Added Successfully";
    } else {
        $err = "Please Try Again Later";
    }
}
?>
<!DOCTYPE html>
<html>
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
                            <h1>Add Loan Type</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Add Loan Type</li>
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
                                    <h3 class="card-title">Fill All Fields</h3>
                                </div>
                                <form method="post">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Loan Type Name</label>
                                            <input type="text" name="type_name" required class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" required class="form-control"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Interest Rate (%)</label>
                                            <input type="number" step="0.01" name="interest_rate" required class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>Maximum Loan Amount</label>
                                            <input type="number" step="0.01" name="max_amount" required class="form-control">
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="add_loan_type" class="btn btn-success">Add Loan Type</button>
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
</body>
</html>
