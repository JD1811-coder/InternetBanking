<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

$staff_id = $_SESSION['staff_id']; // Ensure staff is logged in

if (isset($_POST['transaction'])) {
    if (isset($_GET['account_id']) && isset($_GET['account_number']) && isset($_GET['client_id'])) {
        $tr_code = $_POST['tr_code'];
        $account_id = $_GET['account_id']; // Client Account ID
        $account_number = $_GET['account_number'];
        $client_id = $_GET['client_id'];
        $transaction_amt = $_POST['transaction_amt'];

        // Start Database Transaction
        $mysqli->autocommit(FALSE);

        // Fetch Current Account Balance
        $query = "SELECT acc_amount FROM ib_bankaccounts WHERE account_id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('i', $account_id);
        $stmt->execute();
        $stmt->bind_result($acc_amount);
        $stmt->fetch();
        $stmt->close();

        // Ensure Deposit Amount is Valid
        if ($transaction_amt <= 0) {
            $err = "Invalid amount! Please enter a positive amount.";
        } else {
            // Update Account Balance (Increase Balance)
            $new_balance = $acc_amount + $transaction_amt;
            $update_balance_query = "UPDATE ib_bankaccounts SET acc_amount = ? WHERE account_id = ?";
            $stmt = $mysqli->prepare($update_balance_query);
            $stmt->bind_param('di', $new_balance, $account_id);
            $stmt->execute();
            $stmt->close();

            // Insert Transaction Record (Without processed_by)
            $insert_transaction = "INSERT INTO iB_Transactions (tr_code, account_id, tr_type, tr_status, client_id, transaction_amt) 
                                   VALUES (?, ?, 'Deposit', 'Success', ?, ?)";
            $stmt = $mysqli->prepare($insert_transaction);
            $stmt->bind_param('ssii', $tr_code, $account_id, $client_id, $transaction_amt);
            $stmt->execute();
            $stmt->close();

            // Insert Notification for Client
            $notification_details = "A deposit of Rs. $transaction_amt has been made into Bank Account $account_number";
            $notification_query = "INSERT INTO iB_notifications (notification_details) VALUES (?)";
            $stmt = $mysqli->prepare($notification_query);
            $stmt->bind_param('s', $notification_details);
            $stmt->execute();
            $stmt->close();

            // Commit Changes
            $mysqli->commit();
            $success = "Deposit of Rs. $transaction_amt was successful!";
        }

        // Enable Autocommit Again
        $mysqli->autocommit(TRUE);
    } else {
        $err = "Required parameters are missing.";
    }
}
?>


<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>
<?php if (isset($success)) { ?>
<script>
setTimeout(function() {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '<?php echo $success; ?>',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            setTimeout(function() {
                window.location.href = "pages_deposits.php";
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


<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <?php include("dist/_partials/nav.php"); ?>
        <?php include("dist/_partials/sidebar.php"); ?>
        <?php
        $account_id = $_GET['account_id'];
        $ret = "SELECT a.*, c.name AS client_name, c.phone AS client_phone FROM iB_bankAccounts a JOIN iB_clients c ON a.client_id = c.client_id WHERE a.account_id = ?";
        $stmt = $mysqli->prepare($ret);
        $stmt->bind_param('i', $account_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $cnt = 1;
        while ($row = $res->fetch_object()) {
        ?>
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Deposit Money</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="pages_transactions.php">Transactions</a></li>
                                <li class="breadcrumb-item active"><?php echo $row->acc_name; ?></li>
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
                                <form method="post" enctype="multipart/form-data" role="form">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="exampleInputEmail1">Client Name</label>
                                                <input type="text" readonly name="client_name"
                                                    value="<?php echo $row->client_name; ?>" required
                                                    class="form-control">
                                            </div>

                                            <div class="col-md-8 form-group">
                                                <label for="exampleInputEmail1">Client Phone Number</label>
                                                <input type="text" readonly name="client_phone"
                                                    value="<?php echo $row->client_phone; ?>" required
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="exampleInputEmail1">Account Name</label>
                                                <input type="text" readonly name="acc_name"
                                                    value="<?php echo $row->acc_name; ?>" required class="form-control">
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label for="exampleInputPassword1">Account Number</label>
                                                <input type="text" readonly value="<?php echo $row->account_number; ?>"
                                                    name="account_number" required class="form-control">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="exampleInputEmail1">Transaction Code</label>
                                                <?php
                                                    $length = 20;
                                                    $_transcode =  substr(str_shuffle('0123456789QWERgfdsazxcvbnTYUIOqwertyuioplkjhmPASDFGHJKLMNBVCXZ'), 1, $length);
                                                    ?>
                                                <input type="text" name="tr_code" readonly
                                                    value="<?php echo $_transcode; ?>" required class="form-control">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label for="exampleInputPassword1">Deposit Amount(Rs.)</label>
                                                <input type="number" min="1" name="transaction_amt" required
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <input type="hidden" name="tr_type" value="Deposit">
                                        <input type="hidden" name="tr_status" value="Success">
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="transaction" class="btn btn-success">Deposit
                                            Funds</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php } ?>
        <?php include("dist/_partials/footer.php"); ?>
        <aside class="control-sidebar control-sidebar-dark">
        </aside>
    </div>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    <script src="dist/js/demo.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        bsCustomFileInput.init();
    });

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelector("form").addEventListener("submit", function(event) {
            var transaction_amt = parseFloat(document.getElementById("transaction_amt").value);

            if (isNaN(transaction_amt) || transaction_amt <= 0) {
                alert("Please enter a valid positive number for the deposit amount.");
                event.preventDefault();
            }
        });
    });
    </script>
</body>

</html>