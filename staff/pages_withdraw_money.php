<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$staff_id = $_SESSION['staff_id'];

if (isset($_POST['withdrawal'])) {
    $tr_code = $_POST['tr_code'];
    $account_id = $_GET['account_id'];
    $acc_name = $_POST['acc_name'];
    $account_number = $_GET['account_number'];
    $acc_type = $_POST['acc_type'];
    $tr_type = $_POST['tr_type'];
    $tr_status = $_POST['tr_status'];
    $client_id = $_GET['client_id'];
    $client_name = $_POST['client_name'];
    $transaction_amt = $_POST['transaction_amt'];
    $client_phone = $_POST['client_phone'];

    // Fetch account balance and minimum balance requirement
    $query = "SELECT acc_amount, acc_type FROM ib_bankaccounts WHERE account_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $account_id);
    $stmt->execute();
    $stmt->bind_result($acc_amount, $acc_type);
    $stmt->fetch();
    $stmt->close();

    $acc_type = trim(strtolower($acc_type));

    $query = "SELECT min_balance FROM ib_acc_types WHERE LOWER(TRIM(name)) = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $acc_type);
    $stmt->execute();
    $stmt->bind_result($min_balance);
    $stmt->fetch();
    $stmt->close();
    
    // Ensure valid balance calculations
    $acc_amount = $acc_amount ?? 0;
    $min_balance = $min_balance ?? 0;
    $remaining_balance = $acc_amount - $transaction_amt;

    // Debugging: Output fetched values
    echo "<script>console.log('Account Type: $acc_type');</script>";
    echo "<script>console.log('Min Balance: $min_balance');</script>";
    echo "<script>console.log('Account Balance: $acc_amount');</script>";
    echo "<script>console.log('Transaction Amount: $transaction_amt');</script>";
    echo "<script>console.log('Remaining Balance: $remaining_balance');</script>";

    // Validation
    if ($transaction_amt <= 0) {
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Invalid Amount",
            text: "Withdrawal amount must be greater than zero.",
            confirmButtonText: "OK"
        });
        </script>';
    } elseif ($transaction_amt > $acc_amount) {
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Insufficient Balance",
            text: "Your Current Balance is Rs. ' . $acc_amount . '",
            confirmButtonText: "OK"
        });
        </script>';
    } elseif ($remaining_balance < $min_balance) {
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Minimum Balance Required",
            text: "You must maintain a minimum balance of Rs. ' . $min_balance . ' in your ' . $acc_type . ' account.",
            confirmButtonText: "OK"
        });
        </script>';
    } else {
        // Proceed with withdrawal
        $mysqli->autocommit(FALSE);

        // Deduct withdrawal amount from account balance
        $update_balance_query = "UPDATE ib_bankaccounts SET acc_amount = ? WHERE account_id = ?";
        $stmt = $mysqli->prepare($update_balance_query);
        $stmt->bind_param('di', $remaining_balance, $account_id);
        $stmt->execute();
        $stmt->close();

        // Insert transaction record
        $insert_transaction = "INSERT INTO ib_transactions (tr_code, account_id, tr_type, tr_status, client_id, transaction_amt) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insert_transaction);
        $stmt->bind_param('sssssi', $tr_code, $account_id, $tr_type, $tr_status, $client_id, $transaction_amt);
        $stmt->execute();
        $stmt->close();

        // Insert notification
        $notification_details = "$client_name Has Withdrawn Rs. $transaction_amt From Bank Account $account_number";
        $notification = "INSERT INTO ib_notifications (notification_details) VALUES (?)";
        $stmt = $mysqli->prepare($notification);
        $stmt->bind_param('s', $notification_details);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $mysqli->commit();

        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: "success",
                title: "Success!",
                text: "Funds Withdrawn Successfully!",
                confirmButtonText: "OK"
            }).then(() => {
                window.location.href = "pages_withdrawals.php";
            });
        });
        </script>';
    }

    // Enable autocommit again
    $mysqli->autocommit(TRUE);
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <?php include("dist/_partials/head.php"); ?>
    <?php if (isset($success)) { ?>
        <script>
            setTimeout(function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: '<?php echo $success; ?>',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        setTimeout(function () {
                            window.location.href = "pages_withdrawals.php";
                        }, 500); // Delay added for better visibility
                    }
                });
            }, 100);
        </script>
    <?php } ?>


    <?php if (isset($err)) { ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: '<?php echo $err; ?>',
                confirmButtonText: 'Try Again'
            });
        </script>
    <?php } ?>


</head>

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
        $ret = "SELECT a.*, c.name AS client_name, c.phone AS client_phone FROM iB_bankAccounts a JOIN iB_clients c ON a.client_id = c.client_id WHERE a.account_id = ? ";
        $stmt = $mysqli->prepare($ret);
        $stmt->bind_param('i', $account_id);
        $stmt->execute(); //ok
        $res = $stmt->get_result();
        $cnt = 1;
        while ($row = $res->fetch_object()) {
            ?>
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Withdraw Money</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="pages_deposits">iBank Finances</a></li>
                                    <li class="breadcrumb-item"><a href="pages_deposits">Withdrawal</a></li>
                                    <li class="breadcrumb-item active"><?php echo $row->acc_name; ?></li>
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

                                            <!-- Display error message -->
                                            <?php if (isset($err)) { ?>
                                                <div class="alert alert-danger" role="alert">
                                                    <?php echo $err; ?>
                                                </div>
                                            <?php } ?>

                                            <div class="row">
                                                <div class=" col-md-4 form-group">
                                                    <label for="exampleInputEmail1">Client Name</label>
                                                    <input type="text" readonly name="client_name"
                                                        value="<?php echo $row->client_name; ?>" required
                                                        class="form-control" id="exampleInputEmail1">
                                                </div>

                                                <div class=" col-md-8 form-group">
                                                    <label for="exampleInputEmail1">Client Phone Number</label>
                                                    <input type="text" readonly name="client_phone"
                                                        value="<?php echo $row->client_phone; ?>" required
                                                        class="form-control" id="exampleInputEmail1">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class=" col-md-4 form-group">
                                                    <label for="exampleInputEmail1">Account Name</label>
                                                    <input type="text" readonly name="acc_name"
                                                        value="<?php echo $row->acc_name; ?>" required class="form-control"
                                                        id="exampleInputEmail1">
                                                </div>
                                                <div class=" col-md-4 form-group">
                                                    <label for="exampleInputPassword1">Account Number</label>
                                                    <input type="text" readonly value="<?php echo $row->account_number; ?>"
                                                        name="account_number" required class="form-control"
                                                        id="exampleInputEmail1">
                                                </div>
                                                <div class=" col-md-4 form-group">
                                                    <label for="exampleInputEmail1">Account Type | Category</label>
                                                    <input type="text" readonly name="acc_type"
                                                        value="<?php echo $row->acc_type; ?>" required class="form-control"
                                                        id="exampleInputEmail1">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class=" col-md-6 form-group">
                                                    <label for="exampleInputEmail1">Transaction Code</label>
                                                    <?php
                                                    //PHP function to generate random account number
                                                    $length = 20;
                                                    $_transcode = substr(str_shuffle('0123456789QWERgfdsazxcvbnTYUIOqwertyuioplkjhmPASDFGHJKLMNBVCXZ'), 1, $length);
                                                    ?>
                                                    <input type="text" name="tr_code" readonly
                                                        value="<?php echo $_transcode; ?>" required class="form-control"
                                                        id="exampleInputEmail1">
                                                </div>

                                                <div class=" col-md-6 form-group">
                                                    <label for="exampleInputPassword1">Amount Withdraw(Rs.) </label>
                                                    <input type="text" name="transaction_amt" required class="form-control"
                                                        id="transaction_amt">
                                                </div>
                                                <div class=" col-md-4 form-group" style="display:none">
                                                    <label for="exampleInputPassword1">Transaction Type</label>
                                                    <input type="text" name="tr_type" value="Withdrawal" required
                                                        class="form-control" id="exampleInputEmail1">
                                                </div>
                                                <div class=" col-md-4 form-group" style="display:none">
                                                    <label for="exampleInputPassword1">Transaction Status</label>
                                                    <input type="text" name="tr_status" value="Success " required
                                                        class="form-control" id="exampleInputEmail1">
                                                </div>

                                            </div>

                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="withdrawal" class="btn btn-success">Withdraw
                                                Funds</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
        <?php } ?>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("form").addEventListener("submit", function (event) {
        var transaction_amt = parseFloat(document.getElementById("transaction_amt").value);
        var acc_amount = <?php echo json_encode((float)$acc_amount); ?>; 
        var min_balance = <?php echo json_encode((float)$min_balance); ?>;
        var remaining_balance = acc_amount - transaction_amt;

        if (isNaN(transaction_amt) || transaction_amt <= 0) {
            Swal.fire("Error", "Please enter a valid positive number greater than zero for withdrawal.", "error");
            event.preventDefault();
        } else if (transaction_amt > acc_amount) {
            Swal.fire("Error", "Insufficient Balance! Your Current Balance is Rs. " + acc_amount, "error");
            event.preventDefault();
        } else if (remaining_balance < min_balance) {
            Swal.fire("Warning", "Minimum balance of Rs. " + min_balance + " is required in your account. Your withdrawal exceeds this limit.", "warning");
            event.preventDefault();
        }
    });
});
</script>
</body>
   
</html>