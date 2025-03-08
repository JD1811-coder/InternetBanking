<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

$errors = ['name' => '', 'description' => '', 'rate' => '', 'min_balance' => ''];
$success_message = '';
$error_message = '';

$name = $description = $rate = $min_balance = '';
$code = "ACC-CAT-" . substr(str_shuffle('0123456789QWERTYUIOPLKJHGFDSAZXCVBNM'), 1, 5);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_acc_type'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $rate = trim($_POST['rate']);
    $min_balance = trim($_POST['min_balance']);

    $hasError = false;

    // **Check if Name already exists**
    $checkQuery = "SELECT acctype_id FROM iB_Acc_types WHERE name = ?";
$checkStmt = $mysqli->prepare($checkQuery);
$checkStmt->bind_param('s', $name);
$checkStmt->execute();
$checkStmt->store_result();

if ($checkStmt->num_rows > 0) {
    $errors['name'] = "Account Category Name already exists!";
    $hasError = true;
}
$checkStmt->close();


    // **Validation checks**
    if ($name === "" || ctype_space($name)) {
        $errors['name'] = "Category Name is required!";
        $hasError = true;
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $errors['name'] = "Only letters and spaces allowed!";
        $hasError = true;
    }

    if ($description === "" || ctype_space($description)) {
        $errors['description'] = "Description is required!";
        $hasError = true;
    }

    if (!is_numeric($rate) || $rate < 0.1 || $rate > 100) {
        $errors['rate'] = "Rate must be between 0.1 and 100!";
        $hasError = true;
    }

    if (!is_numeric($min_balance) || $min_balance < 1 || $min_balance > 100000) {
        $errors['min_balance'] = "Min balance must be between 1 and 100000!";
        $hasError = true;
    }

    // **Insert into database if no errors**
    if (!$hasError) {
        $query = "INSERT INTO iB_Acc_types (name, description, rate, min_balance, code) VALUES (?,?,?,?,?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssdss', $name, $description, $rate, $min_balance, $code);
        $stmt->execute();

        if ($stmt) {
            $_SESSION['success'] = "Account Category Created Successfully!";
            header("Location: pages_manage_accs.php");
            exit();
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

                                <form method="post">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-3 form-group">
                                                <label>Account Category Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="<?php echo htmlspecialchars($name); ?>" required>
                                                <span class="text-danger"><?php echo $errors['name']; ?></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>Account Category Rates % Per Year</label>
                                                <input type="number" name="rate" class="form-control" step="0.01"
                                                    min="0.1" max="100" value="<?php echo htmlspecialchars($rate); ?>"
                                                    required>
                                                <span class="text-danger"><?php echo $errors['rate']; ?></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>Minimum Balance</label>
                                                <input type="number" name="min_balance" class="form-control" min="1"
                                                    max="100000"
                                                    value="<?php echo htmlspecialchars($min_balance); ?>" required>
                                                <span class="text-danger"><?php echo $errors['min_balance']; ?></span>
                                            </div>
                                            <div class="col-md-3 form-group">
                                                <label>Account Category Code</label>
                                                <input type="text" readonly name="code" value="<?php echo $code; ?>"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <label>Account Category Description</label>
                                                <textarea name="description" class="form-control"
                                                    required><?php echo htmlspecialchars($description); ?></textarea>
                                                <span class="text-danger"><?php echo $errors['description']; ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="create_acc_type" class="btn btn-success">
                                            Add Account Type
                                        </button>
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
