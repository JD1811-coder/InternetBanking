<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$client_id = $_SESSION['client_id'];

if (isset($_POST['deposit'])) {
    if (isset($_GET['account_id']) && isset($_GET['client_id'])) {
        $tr_code = $_POST['tr_code'];
        $account_id = $_GET['account_id'];  // Sender's Account ID
        $client_id = $_GET['client_id'];
        $transaction_amt = $_POST['transaction_amt'];
        $receiving_acc_no = $_POST['receiving_acc_no'];

        // Validate input
        if (!is_numeric($transaction_amt) || $transaction_amt <= 0) {
            $err = "Invalid amount. Please enter an amount greater than zero.";
        } else {
            // Start Database Transaction
            $mysqli->autocommit(FALSE);

            // Fetch Sender's Current Balance
            $query = "SELECT acc_amount FROM ib_bankaccounts WHERE account_id = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('i', $account_id);
            $stmt->execute();
            $stmt->bind_result($sender_balance);
            $stmt->fetch();
            $stmt->close();

            // Fetch Receiver's Account ID & Current Balance
            $query = "SELECT account_id, acc_amount FROM ib_bankaccounts WHERE account_number = ?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('s', $receiving_acc_no);
            $stmt->execute();
            $stmt->bind_result($receiver_account_id, $receiver_balance);
            $stmt->fetch();
            $stmt->close();

            // Check if sender has sufficient balance
            if ($transaction_amt > $sender_balance) {
                $err = "Insufficient Balance! Your Current Balance is Rs. $sender_balance";
            } else {
                // Deduct amount from sender
                $new_sender_balance = $sender_balance - $transaction_amt;
                $update_sender_query = "UPDATE ib_bankaccounts SET acc_amount = ? WHERE account_id = ?";
                $stmt = $mysqli->prepare($update_sender_query);
                $stmt->bind_param('di', $new_sender_balance, $account_id);
                $stmt->execute();
                $stmt->close();

                // Add amount to receiver
                $new_receiver_balance = $receiver_balance + $transaction_amt;
                $update_receiver_query = "UPDATE ib_bankaccounts SET acc_amount = ? WHERE account_id = ?";
                $stmt = $mysqli->prepare($update_receiver_query);
                $stmt->bind_param('di', $new_receiver_balance, $receiver_account_id);
                $stmt->execute();
                $stmt->close();

                // Insert Transaction Record
                $insert_transaction = "INSERT INTO ib_transactions (tr_code, account_id, tr_type, tr_status, client_id, transaction_amt, receiving_acc_no, created_at, is_active) 
                                       VALUES (?, ?, 'Transfer', 'Success', ?, ?, ?, NOW(), 1)";
                $stmt = $mysqli->prepare($insert_transaction);
                $stmt->bind_param('siisi', $tr_code, $account_id, $client_id, $transaction_amt, $receiving_acc_no);
                $stmt->execute();
                $stmt->close();

                // Commit Changes
                $mysqli->commit();
                $success = "Money Transferred Successfully!";
            }

            // Enable Autocommit Again
            $mysqli->autocommit(TRUE);
        }
    } else {
        $err = "Required parameters not set.";
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
        $ret = "SELECT a.*, c.name AS name, c.phone AS phone 
                FROM iB_bankAccounts a 
                JOIN iB_clients c ON a.client_id = c.client_id 
                WHERE a.account_id = ? ";
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
                                <h1>Transfer Money</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="pages_transfer_money.php">Finances</a></li>
                                    <li class="breadcrumb-item"><a href="pages_transfer_money.php">Transfer</a></li>
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

                                            <div class="row">
                                                <div class=" col-md-4 form-group">
                                                    <label for="exampleInputEmail1">Client Name</label>
                                                    <input type="text" readonly name="client_name"
                                                        value="<?php echo $row->name; ?>" required class="form-control"
                                                        id="exampleInputEmail1">
                                                </div>

                                                <div class=" col-md-8 form-group">
                                                    <label for="exampleInputEmail1">Client Phone Number</label>
                                                    <input type="text" readonly name="client_phone"
                                                        value="<?php echo $row->phone; ?>" required class="form-control"
                                                        id="exampleInputEmail1">
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
                                                    <label for="exampleInputPassword1">Amount Transfered(Rs.)</label>
                                                    <input type="text" name="transaction_amt" required class="form-control"
                                                        id="exampleInputEmail1">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 form-group">
                                                    <label for="receiving_acc_no">Receiving Account Number</label>
                                                    <select name="receiving_acc_no" id="receiving_acc_no" required
                                                        class="form-control">
                                                        <option value="">Select Receiving Account</option>
                                                        <?php
                                                        // Fetch all bank accounts except the sender's own account
                                                        $ret = "SELECT account_number, acc_name FROM ib_bankaccounts WHERE account_id != ?";
                                                        $stmt = $mysqli->prepare($ret);
                                                        $stmt->bind_param('i', $account_id);
                                                        $stmt->execute();
                                                        $res = $stmt->get_result();
                                                        while ($row = $res->fetch_object()) {
                                                            echo "<option value='{$row->account_number}'>{$row->account_number}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-4 form-group">
                                                    <label for="receiving_acc_name">Receiving Account Name</label>
                                                    <input type="text" name="receiving_acc_name" id="receiving_acc_name"
                                                        readonly required class="form-control">
                                                </div>



                                                <!-- <div class=" col-md-4 form-group">
                                                    <label for="exampleInputPassword1">Receiving Account Holder</label>
                                                    <input type="text" name="receiving_acc_holder" required class="form-control" id="AccountHolder">
                                                </div> -->

                                                <div class=" col-md-4 form-group" style="display:none">
                                                    <label for="exampleInputPassword1">Transaction Type</label>
                                                    <input type="text" name="tr_type" value="Transfer" required
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
                                            <button type="submit" name="deposit" class="btn btn-success">Transfer
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
    <script type="text/javascript">
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector("form").addEventListener("submit", function (event) {
                var senderAccount = "<?php echo $account_number; ?>"; // Fetch sender's account number
                var receivingAccount = document.getElementById("receiving_acc_no").value;

                if (receivingAccount === senderAccount) {
                    alert("You cannot transfer money to your own account.");
                    event.preventDefault();
                }
            });
        });



    </script>

    <script>
        $(document).ready(function () {
            $("#receiving_acc_no").change(function () {
                var accountNumber = $(this).val();

                if (accountNumber) {
                    $.ajax({
                        type: "POST",
                        url: "get_account_name.php",
                        data: { account_number: accountNumber },
                        dataType: "json",
                        success: function (response) {
                            $("#receiving_acc_name").val(response.acc_name);
                        },
                        error: function () {
                            alert("Failed to fetch account details.");
                        }
                    });
                }
                // else {
                //     $("#receiving_acc_name").val(""); // Clear the field if no account is selected
                // }
            });
        });
    </script>
</body>

</html>