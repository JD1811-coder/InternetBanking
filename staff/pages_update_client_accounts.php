<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['staff_id'];
if (isset($_POST['update_account'])) {
    $account_id = $_GET['account_id'];
    $acc_type_id = $_POST['acc_type_id']; // Account type ID from dropdown

    // Update only acc_type_id in iB_bankAccounts
    $query = "UPDATE iB_bankAccounts SET acc_type_id=? WHERE account_id=?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $acc_type_id, $account_id);

    if ($stmt->execute()) {
        echo "<script>
            setTimeout(() => {
                Swal.fire({
                    title: 'Success!',
                    text: '✅ Account Type Updated Successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'pages_manage_acc_openings.php';
                });
            }, 500);
        </script>";
    } else {
        $err = "❌ Update Failed! Please Try Again.";
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
        <?php
        $account_id = $_GET['account_id'];
        $ret = "SELECT a.*, c.name AS client_name, c.phone AS client_phone, c.client_number, c.email AS client_email, c.address AS client_adr 
                FROM iB_bankAccounts a 
                JOIN iB_clients c ON a.client_id = c.client_id 
                WHERE a.account_id = ?";
        $stmt = $mysqli->prepare($ret);
        $stmt->bind_param('i', $account_id);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_object()) {
            // Default the account rate to '0' initially
            $accountRate = '0';

            // Get the account type rates based on the account type
            $accountType = $row->acc_type;
            $rateQuery = "SELECT rate FROM ib_acc_types WHERE name = ?"; // Use 'ib_acc_types'
            $rateStmt = $mysqli->prepare($rateQuery);
            $rateStmt->bind_param('s', $accountType);
            $rateStmt->execute();
            $rateResult = $rateStmt->get_result();

            // Check if we got a result and set accountRate accordingly
            if ($rateRow = $rateResult->fetch_object()) {
                $accountRate = $rateRow->rate; // Use the correct column 'rate'
            }
            ?>
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Update <?php echo htmlspecialchars($row->client_name); ?> iBanking Account</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="pages_open_acc.php">iBanking Accounts</a></li>
                                    <li class="breadcrumb-item"><a href="pages_open_acc.php">Manage</a></li>
                                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($row->client_name); ?>
                                    </li>
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
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="clientName">Client Name</label>
                                                    <input type="text" readonly name="client_name"
                                                        value="<?php echo htmlspecialchars($row->client_name); ?>" required
                                                        class="form-control" id="clientName">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label for="clientNumber">Client Number</label>
                                                    <input type="text" readonly name="client_number"
                                                        value="<?php echo htmlspecialchars($row->client_number); ?>"
                                                        class="form-control" id="clientNumber">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <label for="clientPhone">Client Phone Number</label>
                                                    <input type="text" readonly name="client_phone"
                                                        value="<?php echo htmlspecialchars($row->client_phone); ?>" required
                                                        class="form-control" id="clientPhone">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="clientEmail">Client Email</label>
                                                    <input type="email" readonly name="client_email"
                                                        value="<?php echo htmlspecialchars($row->client_email); ?>" required
                                                        class="form-control" id="clientEmail">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label for="clientAddress">Client Address</label>
                                                    <input type="text" name="client_adr" readonly
                                                        value="<?php echo htmlspecialchars($row->client_adr); ?>" required
                                                        class="form-control" id="clientAddress">
                                                </div>
                                            </div>

                                            <!-- Bank Account Details -->
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="accName">Account Name</label>
                                                    <input type="text" name="acc_name" readonly
                                                        value="<?php echo htmlspecialchars($row->acc_name); ?>" required
                                                        class="form-control" id="accName">
                                                    <small id="accNameError"></small> <!-- Error message appears here -->
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <label for="accountNumber">Account Number</label>
                                                    <input type="text" readonly name="account_number"
                                                        value="<?php echo htmlspecialchars($row->account_number); ?>"
                                                        required class="form-control" id="accountNumber">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="accType">Account Type</label>
                                                    <label for="accType">Select Account Type</label>
                                                    <select class="form-control" name="acc_type_id" id="accType" required>
                                                        <option value="">Select Any Account Type</option>
                                                        <?php
                                                        // Fetch all account types
                                                        $ret = "SELECT * FROM ib_acc_types";
                                                        $stmt = $mysqli->prepare($ret);
                                                        $stmt->execute();
                                                        $res = $stmt->get_result();

                                                        while ($typeRow = $res->fetch_object()) {
                                                            ?>
                                                            <option
                                                                value="<?php echo htmlspecialchars($typeRow->acctype_id); ?>"
                                                                data-rate="<?php echo htmlspecialchars($typeRow->rate); ?>"
                                                                <?php echo (isset($row->acc_type_id) && $row->acc_type_id == $typeRow->acctype_id) ? 'selected' : ''; ?>>
                                                                <?php echo htmlspecialchars($typeRow->name); ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                    </div>
                                                    <div class="col-md-6 form-group">
                                                    <label for="accRates">Account Type Rates (%)</label>
                                                    <input type="text" name="acc_rates"
                                                        value="<?php echo isset($accountRate) ? htmlspecialchars($accountRate) : ''; ?>"
                                                        readonly required class="form-control" id="accRates">

                                                    <script>
                                                        document.getElementById("accType").addEventListener("change", function () {
                                                            let selectedOption = this.options[this.selectedIndex];
                                                            let rate = selectedOption.getAttribute("data-rate") || "";
                                                            document.getElementById("accRates").value = rate;
                                                        });
                                                    </script>

                                                    <div class="col-md-6 form-group" style="display:none">
                                                        <label for="accStatus">Account Status</label>
                                                        <input type="text" name="acc_status" value="Active" readonly
                                                            required class="form-control" id="accStatus">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <button type="submit" name="update_account" class="btn btn-primary">Update
                                                    Account</button>
                                            </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
        <?php } ?>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="dist/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="dist/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelector("form").addEventListener("submit", function (event) {
            let accNameInput = document.getElementById("accName").value.trim();
            let errorDiv = document.getElementById("accNameError");

            // Regular expression to allow only alphabets and spaces
            let nameRegex = /^[a-zA-Z ]+$/;

            // Clear previous error message
            errorDiv.innerHTML = "";
            errorDiv.style.color = "red";

            if (accNameInput === "") {
                errorDiv.innerHTML = "❌ Account Name cannot be empty!";
                event.preventDefault(); // Stop form submission
                return;
            }

            if (!nameRegex.test(accNameInput)) {
                errorDiv.innerHTML = "❌ Account Name should contain only alphabets and spaces!";
                event.preventDefault();
                return;
            }

            if (accNameInput.replace(/\s/g, '') === "") {
                errorDiv.innerHTML = "❌ Account Name cannot be only spaces!";
                event.preventDefault();
                return;
            }

            // Check for duplicate name using AJAX
            fetch(`check_duplicate.php?acc_name=${encodeURIComponent(accNameInput)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        errorDiv.innerHTML = "❌ This Account Name is already in use!";
                        event.preventDefault();
                    }
                })
                .catch(error => console.error("Error checking duplicate:", error));
        });
    </script>
    <script>
    document.getElementById("accType").addEventListener("change", function () {
        let selectedOption = this.options[this.selectedIndex];
        let rate = selectedOption.getAttribute("data-rate") || "";
        document.getElementById("accRates").value = rate;
    });
</script>




</body>

</html>