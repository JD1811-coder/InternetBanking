<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$staff_id = $_SESSION['staff_id'];

// Rollback transaction
if (isset($_GET['RollBack_Transaction'])) {
    $id = intval($_GET['RollBack_Transaction']);
    $adn = "DELETE FROM iB_Transactions WHERE tr_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $info = "Transaction Rolled Back";
    } else {
        $err = "Try Again Later";
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
                            <h1>Transaction History</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Transactions</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Manage Transactions</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-hover table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Transaction Code</th>
                                                <th>Account No.</th>
                                                <th>Account Type</th>
                                                <th>Transaction Type</th>
                                                <th>Amount</th>
                                                <th>Account Owner</th>
                                                <!-- <th>Client Name</th> -->
                                                <th>Timestamp</th>
                                                <th>Action</th> 
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                           $ret = "SELECT 
                                           t.tr_id, 
                                           t.tr_code, 
                                           b.account_number, 
                                           bt.name AS acc_type,  -- Fetching Account Type Name
                                           bt.rate AS acc_rates,  -- Fetching Account Interest Rate
                                           t.tr_type, 
                                           t.transaction_amt, 
                                           b.acc_name AS account_owner, 
                                           c.name AS client_name, 
                                           t.created_at
                                       FROM iB_Transactions t
                                       JOIN ib_bankaccounts b ON t.account_id = b.account_id
                                       JOIN ib_clients c ON t.client_id = c.client_id
                                       JOIN ib_acc_types bt ON b.acc_type_id = bt.acctype_id  -- Added Join for Account Type
                                       ORDER BY t.created_at DESC";
                               
                                            $stmt = $mysqli->prepare($ret);
                                            $stmt->execute();
                                            $res = $stmt->get_result();
                                            $cnt = 1;
                                            while ($row = $res->fetch_object()) {
                                                $alertClass = $row->tr_type == 'Deposit' ? "<span class='badge badge-success'>$row->tr_type</span>" :
                                                    ($row->tr_type == 'Withdrawal' ? "<span class='badge badge-danger'>$row->tr_type</span>" :
                                                        "<span class='badge badge-warning'>$row->tr_type</span>");
                                                ?>
                                                <tr>
                                                    <td><?php echo $cnt; ?></td>
                                                    <td><?php echo $row->tr_code; ?></td>
                                                    <td><?php echo $row->account_number; ?></td>
                                                    <td><?php echo $row->acc_type; ?></td>
                                                    <td><?php echo $alertClass; ?></td>
                                                    <td>Rs. <?php echo $row->transaction_amt; ?></td>
                                                    <td><?php echo $row->account_owner; ?></td>
                                                    <!-- <td><?php echo $row->client_name; ?></td> -->
                                                    <td><?php echo date("d-M-Y h:i:s A", strtotime($row->created_at)); ?>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-danger btn-sm"
                                                            href="pages_transactions_engine.php?RollBack_Transaction=<?php echo $row->tr_id; ?>">
                                                            <i class="fas fa-power-off"></i> Roll Back
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php $cnt++;
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
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
    <script src="plugins/datatables/jquery.dataTables.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    <script>
        $(function () {
            $("#example1").DataTable();
        });
    </script>
</body>

</html>