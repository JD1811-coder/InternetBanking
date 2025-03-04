<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

if (isset($_POST['update_acc_type'])) {
    // Capture form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $rate = trim($_POST['rate']);
    $min_balance = trim($_POST['min_balance']);
    $code = $_GET['code'];

    // Validation
    if (empty($name) || empty($description) || empty($rate) || empty($min_balance)) {
        $err = "All fields are required!";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
        $err = "Category Name should contain only alphabets!";
    } elseif (!is_numeric($rate) || $rate < 1 || $rate > 100) {
        $err = "Rate must be a number between 1 and 100!";
    } elseif (!is_numeric($min_balance) || $min_balance < 1 || $min_balance > 100000) {
        $err = "Minimum balance must be a number between 1 and 100000!";
    } else {
        // Update database
        $query = "UPDATE iB_Acc_types SET name=?, description=?, rate=?, min_balance=? WHERE code=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sssss', $name, $description, $rate, $min_balance, $code);
        $stmt->execute();

        if ($stmt) {
            $success = "iBank Account Category Updated Successfully!";
        } else {
            $err = "Please Try Again Later!";
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
        <?php include("dist/_partials/nav.php"); ?>
        <?php include("dist/_partials/sidebar.php"); ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Update Account Category</h1>
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

                                <?php
                                // Fetch account type data
                                $code = $_GET['code'];
                                $ret = "SELECT * FROM iB_Acc_types WHERE code = ?";
                                $stmt = $mysqli->prepare($ret);
                                $stmt->bind_param('s', $code);
                                $stmt->execute();
                                $res = $stmt->get_result();
                                $row = $res->fetch_object();
                                ?>

                                <form method="post" role="form" onsubmit="return validateForm()">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label>Account Category Name</label>
                                                <input type="text" name="name" value="<?php echo $row->name; ?>"
                                                    required class="form-control" id="name">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Account Category Rates % Per Year</label>
                                                <input type="text" name="rate" value="<?php echo $row->rate; ?>"
                                                    required class="form-control" id="rate">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Minimum Balance</label>
                                                <input type="text" name="min_balance"
                                                    value="<?php echo $row->min_balance; ?>" required
                                                    class="form-control" id="min_balance">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>Account Category Code</label>
                                                <input type="text" readonly name="code"
                                                    value="<?php echo $row->code; ?>" class="form-control">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12 form-group">
                                                <label>Account Category Description</label>
                                                <textarea name="description" required class="form-control"
                                                    id="desc"><?php echo $row->description; ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="update_acc_type" class="btn btn-success">Update
                                            Account</button>
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

    <!-- Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//cdn.ckeditor.com/4.6.2/basic/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('desc');

        function validateForm() {
            let name = document.getElementById('name').value.trim();
            let rate = document.getElementById('rate').value.trim();
            let desc = document.getElementById('desc').value.trim();
            let nameRegex = /^[a-zA-Z ]+$/;

            if (name === '' || rate === '' || desc === '') {
                Swal.fire("Error!", "All fields are required!", "error");
                return false;
            }

            if (!nameRegex.test(name)) {
                Swal.fire("Error!", "Category Name should contain only alphabets!", "error");
                return false;
            }

            if (isNaN(rate) || rate < 1 || rate > 100) {
                Swal.fire("Error!", "Rate must be a number between 1 and 100!", "error");
                return false;
            }

            return true;
        }

        <?php if (isset($success)) { ?>
            Swal.fire("Success!", "<?php echo $success; ?>", "success");
        <?php } ?>

        <?php if (isset($err)) { ?>
            Swal.fire("Error!", "<?php echo $err; ?>", "error");
        <?php } ?>
        function validateForm() {
            let name = document.getElementById('name').value.trim();
            let rate = document.getElementById('rate').value.trim();
            let desc = document.getElementById('desc').value.trim();
            let minBalance = document.getElementById('min_balance').value.trim();
            let nameRegex = /^[a-zA-Z ]+$/;

            if (name === '' || rate === '' || desc === '' || minBalance === '') {
                Swal.fire("Error!", "All fields are required!", "error");
                return false;
            }

            if (!nameRegex.test(name)) {
                Swal.fire("Error!", "Category Name should contain only alphabets!", "error");
                return false;
            }

            if (isNaN(rate) || rate < 1 || rate > 100) {
                Swal.fire("Error!", "Rate must be a number between 1 and 100!", "error");
                return false;
            }

            if (isNaN(minBalance) || minBalance < 1 || minBalance > 100000) {
                Swal.fire("Error!", "Minimum balance must be a number between 1 and 100000!", "error");
                return false;
            }

            return true;
        }

    </script>
</body>

</html>