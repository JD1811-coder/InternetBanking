<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

if (isset($_POST['create_acc_type'])) {
    // Sanitize and validate inputs
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $rate = trim($_POST['rate']);
    $min_balance = trim($_POST['min_balance']);
    $code = trim($_POST['code']);

    // Validation
    if (empty($name) || empty($description) || empty($rate) || empty($min_balance) || empty($code)) {
        $_SESSION['error'] = "All fields are required!";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $_SESSION['error'] = "Category Name should contain only alphabets!";
    } elseif (!is_numeric($rate) || $rate < 0.1 || $rate > 100) {
        $_SESSION['error'] = "Rate must be a number between 0.1 and 100!";
    } elseif (!is_numeric($min_balance) || $min_balance < 1 || $min_balance > 100000) {
        $_SESSION['error'] = "Minimum balance must be a number between 1 and 100000!";
    } else {
        // Insert into database
        $query = "INSERT INTO iB_Acc_types (name, description, rate, min_balance, code) VALUES (?,?,?,?,?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sssss', $name, $description, $rate, $min_balance, $code);
        $stmt->execute();

        if ($stmt) {
            $_SESSION['success'] = "Account Category Created Successfully!";
        } else {
            $_SESSION['error'] = "Error! Please try again.";
        }
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
                            <h1>Create Account Categories</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Add Account Category</li>
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

                                <form method="post" id="accForm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <label>Account Category Name</label>
                                                <input type="text" name="name" required class="form-control" id="categoryName">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>Account Category Rates % Per Year</label>
                                                <input type="number" name="rate" required class="form-control" id="rate" step="0.01" min="0.1" max="100">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>Minimum Balance</label>
                                                <input type="number" name="min_balance" required class="form-control" id="min_balance" min="1" max="100000">
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>Account Category Code</label>
                                                <?php
                                                $length = 5;
                                                $_Number = substr(str_shuffle('0123456789QWERTYUIOPLKJHGFDSAZXCVBNM'), 1, $length);
                                                ?>
                                                <input type="text" readonly name="code" value="ACC-CAT-<?php echo $_Number; ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <label>Account Category Description</label>
                                                <textarea name="description" required class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="create_acc_type" class="btn btn-success">Add Account Type</button>
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

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            $("#min_balance").on("input", function () {
                let value = $(this).val().replace(/[^0-9]/g, "");
                if (value < 1) value = "1";
                if (value > 100000) value = "100000";
                $(this).val(value);
            });

            <?php if (isset($_SESSION['success'])) { ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?php echo $_SESSION["success"]; ?>',
                    showConfirmButton: false,
                    timer: 2000
                });
                <?php unset($_SESSION['success']); ?>
            <?php } ?>

            <?php if (isset($_SESSION['error'])) { ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: '<?php echo $_SESSION["error"]; ?>',
                    showConfirmButton: false,
                    timer: 2000
                });
                <?php unset($_SESSION['error']); ?>
            <?php } ?>
        });
    </script>
</body>
</html>
