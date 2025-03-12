<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

if (isset($_POST['transaction'])) {
    $tr_code = $_POST['tr_code'];
    $account_id = $_GET['account_id'];
    $tr_type = $_POST['tr_type'];
    $tr_status = $_POST['tr_status'];
    $client_id = $_GET['client_id'];
    $client_name = $_POST['client_name'];
    $transaction_amt = $_POST['transaction_amt'];
    $client_phone = $_POST['client_phone'];

    // Start database transaction
    $mysqli->autocommit(FALSE);

    // Fetch current account balance
    $query = "SELECT acc_amount FROM ib_bankaccounts WHERE account_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $account_id);
    $stmt->execute();
    $stmt->bind_result($acc_amount);
    $stmt->fetch();
    $stmt->close();

    if ($tr_type == 'Withdrawal') {
        // Check if there are sufficient funds before withdrawal
        if ($transaction_amt > $acc_amount) {
            $err = "Insufficient Balance! Your Current Balance is Rs. $acc_amount";
        } else {
            $new_balance = $acc_amount - $transaction_amt;
            $update_balance_query = "UPDATE ib_bankaccounts SET acc_amount = ? WHERE account_id = ?";
            $stmt = $mysqli->prepare($update_balance_query);
            $stmt->bind_param('di', $new_balance, $account_id);
            $stmt->execute();
            $stmt->close();

            $notification_details = "$client_name has withdrawn Rs. $transaction_amt from bank account $account_id";
        }
    } else { // Deposit
        $new_balance = $acc_amount + $transaction_amt;
        $update_balance_query = "UPDATE ib_bankaccounts SET acc_amount = ? WHERE account_id = ?";
        $stmt = $mysqli->prepare($update_balance_query);
        $stmt->bind_param('di', $new_balance, $account_id);
        $stmt->execute();
        $stmt->close();

        $notification_details = "$client_name has deposited Rs. $transaction_amt into bank account $account_id";
    }

    // If the transaction is valid, insert into transactions table
    if (!isset($err)) {
        $insert_transaction = "INSERT INTO iB_Transactions (tr_code, account_id, tr_type, tr_status, client_id, transaction_amt) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($insert_transaction);
        $stmt->bind_param('sssssi', $tr_code, $account_id, $tr_type, $tr_status, $client_id, $transaction_amt);
        $stmt->execute();
        $stmt->close();

        // Insert notification
        $notification_query = "INSERT INTO iB_notifications (notification_details) VALUES (?)";
        $stmt = $mysqli->prepare($notification_query);
        $stmt->bind_param('s', $notification_details);
        $stmt->execute();
        $stmt->close();

        // Commit transaction if everything is successful
        $mysqli->commit();
        $success = "$tr_type of Rs. $transaction_amt was successful!";
    } else {
        $mysqli->rollback(); // Revert changes in case of failure
    }

    $mysqli->autocommit(TRUE);
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
                                <h1>Transaction</h1>
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
                                                    <input type="text" readonly name="client_name" value="<?php echo $row->client_name; ?>" required class="form-control">
                                                </div>
                                                
                                                <div class="col-md-8 form-group">
                                                    <label for="exampleInputEmail1">Client Phone Number</label>
                                                    <input type="text" readonly name="client_phone" value="<?php echo $row->client_phone; ?>" required class="form-control">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label for="exampleInputEmail1">Account Name</label>
                                                    <input type="text" readonly name="acc_name" value="<?php echo $row->acc_name; ?>" required class="form-control">
                                                </div>
                                                <div class="col-md-4 form-group">
                                                    <label for="exampleInputPassword1">Account Number</label>
                                                    <input type="text" readonly value="<?php echo $row->account_number; ?>" name="account_number" required class="form-control">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="exampleInputEmail1">Transaction Code</label>
                                                    <?php
                                                    $length = 20;
                                                    $_transcode =  substr(str_shuffle('0123456789QWERgfdsazxcvbnTYUIOqwertyuioplkjhmPASDFGHJKLMNBVCXZ'), 1, $length);
                                                    ?>
                                                    <input type="text" name="tr_code" readonly value="<?php echo $_transcode; ?>" required class="form-control">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label for="exampleInputPassword1">Transaction Amount(Rs.)</label>
                                                    <input type="number" min="1" name="transaction_amt" required class="form-control">
                                                </div>
                                            </div>
                                            <input type="hidden" name="tr_type" value="Deposit">
                                            <input type="hidden" name="tr_status" value="Success">
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" name="transaction" class="btn btn-success">Deposit Funds</button>
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
        var acc_balance = <?php echo $acc_amount; ?>; // Fetch balance from PHP
        var tr_type = document.querySelector('input[name="tr_type"]').value;

        if (isNaN(transaction_amt) || transaction_amt <= 1) {
            alert("Please enter a valid positive number for transaction amount.");
            event.preventDefault();
        } else if (tr_type === 'Withdrawal' && transaction_amt > acc_balance) {
            alert("Insufficient Balance! Your Current Balance is Rs. " + acc_balance);
            event.preventDefault();
        }
    });
});

    </script>
</body>
</html>

