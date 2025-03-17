<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];
// Fire staff
if (isset($_GET['deleteBankAcc'])) {
    $id = intval($_GET['deleteBankAcc']);
    $adn = "DELETE FROM iB_bankAccounts WHERE account_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $_SESSION['success'] = "iBanking Account Closed Successfully!";
    } else {
        $_SESSION['error'] = "Error! Try Again Later.";
    }

    header("Location: pages_manage_acc_openings.php"); // Redirect to avoid resubmission
    exit();
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
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Manage iBanking Accounts</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="pages_manage_acc_openings.php">iBank Accounts</a></li>
                                <li class="breadcrumb-item active">Manage Accounts</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Select any action options to manage your accounts</h3>
                            </div>
                            <div class="card-body">
                                <table id="example1" class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Acc Number</th>
                                            <th>Rate</th>
                                            <th>Acc Type</th>
                                            <!-- <th>Acc Owner</th> -->
                                            <th>Balance</th>
                                            <th>Date Opened</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch all iB_Accs
                                        $ret = "SELECT 
    b.*, 
    c.name AS client_name, 
    at.name AS acc_type,  -- Get actual account type name
    at.rate AS acc_rates  -- Get interest rate
FROM iB_bankAccounts b
LEFT JOIN ib_clients c ON b.client_id = c.client_id
LEFT JOIN ib_acc_types at ON b.acc_type_id = at.acctype_id  -- Join to fetch account type name & rate
ORDER BY RAND();
";
                            

                                        $stmt = $mysqli->prepare($ret);
                                        $stmt->execute();
                                        $res = $stmt->get_result();
                                        $cnt = 1;
                                        while ($row = $res->fetch_object()) {
                                            // Trim Timestamp to DD-MM-YYYY : H-M-S
                                            $dateOpened = $row->created_at ?? 'N/A';
                                        ?>
                                            <tr>
                                                <td><?php echo $cnt; ?></td>
                                                <td><?php echo htmlspecialchars($row->acc_name ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($row->account_number ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($row->acc_rates ?? '0.00'); ?>%</td>
                                                <td><?php echo htmlspecialchars($row->acc_type ?? 'N/A'); ?></td>
                                                <td>₹<?php echo htmlspecialchars(  number_format($row->acc_amount ?? 0, 2)); ?></td>

                                                <!-- <td><?php echo isset($row->client_name) ? htmlspecialchars($row->client_name) : 'N/A'; ?></td> -->
                                                <td><?php echo $dateOpened !== 'N/A' ? date("d-M-Y", strtotime($dateOpened)) : 'N/A'; ?></td>
                                                <td>
                                                    <a class="btn btn-success btn-sm" href="pages_update_client_accounts.php?account_id=<?php echo $row->account_id; ?>">
                                                        <i class="fas fa-cogs"></i> Manage
                                                    </a>
                                                    <a class="btn btn-danger btn-sm" href="pages_manage_acc_openings.php?deleteBankAcc=<?php echo $row->account_id; ?>">
                                                        <i class="fas fa-times"></i> Close Account
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php $cnt++; } ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </section>
            <!-- /.content -->
        </div>
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
    <!-- DataTables -->
    <script src="plugins/datatables/jquery.dataTables.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- Page script -->
    <script>
        $(function() {
            $("#example1").DataTable();
        });
    </script>
    <!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['success'])) { ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonColor: '#3085d6'
            });
            <?php unset($_SESSION['success']); // Clear session message ?>
        <?php } ?>

        <?php if (isset($_SESSION['error'])) { ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonColor: '#d33'
            });
            <?php unset($_SESSION['error']); ?>
        <?php } ?>
    });
</script>

</body>

</html>
        