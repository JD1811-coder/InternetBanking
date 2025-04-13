<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

if (isset($_GET['deleteBankAccType'])) {
    $id = intval($_GET['deleteBankAccType']);
    $adn = "DELETE FROM iB_Acc_types WHERE acctype_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $_SESSION['success'] = "iBanking Account Type Removed Successfully!";
    } else {
        $_SESSION['error'] = "Failed to delete. Try Again Later!";
    }
    header("Location: pages_manage_accs.php");
    exit();
}
if (isset($_GET['toggleBankAccType'])) {
    $id = intval($_GET['toggleBankAccType']);
    $currentStatus = intval($_GET['status']);
    $newStatus = $currentStatus === 1 ? 0 : 1;

    $query = "UPDATE iB_Acc_types SET is_active = ? WHERE acctype_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $newStatus, $id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $_SESSION['success'] = $newStatus ? "Account Type Enabled Successfully!" : "Account Type Disabled Successfully!";
    } else {
        $_SESSION['error'] = "Failed to update status. Try Again!";
    }
    header("Location: pages_manage_accs.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<style>.btn-group .btn {
    margin-right: 5px;  /* Add spacing between buttons */
}
.btn-group .btn:last-child {
    margin-right: 0;  /* Remove right margin for the last button */
}
</style>
<?php include("dist/_partials/head.php"); ?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("dist/_partials/nav.php"); ?>

        <!-- Main Sidebar Container -->
        <?php include("dist/_partials/sidebar.php"); ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>iBanking Account Types</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="pages_manage_accs.php">iBank Account Types</a></li>
                                <li class="breadcrumb-item active">Manage Clients</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Manage Your Account Types</h3>
                            </div>
                            <div class="card-body">
                                <table id="example1" class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Rate</th>
                                            <th>Min Balance</th> <!-- New Column -->
                                            <th>Code</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ret = "SELECT * FROM iB_Acc_types ORDER BY RAND()";
                                        $stmt = $mysqli->prepare($ret);
                                        $stmt->execute();
                                        $res = $stmt->get_result();
                                        $cnt = 1;
                                        while ($row = $res->fetch_object()) {
                                            ?>
                                            <tr>
                                                <td><?php echo $cnt; ?></td>
                                                <td><?php echo $row->name; ?></td>
                                                <td><?php echo $row->rate; ?>%</td>
                                                <td><?php echo number_format($row->min_balance, 2); ?></td>
                                                <!-- Display Min Balance -->
                                                <td><?php echo $row->code; ?></td>
                                                <td>
    <div class="btn-group" role="group">
        <a class="btn btn-success btn-sm" href="pages_update_accs.php?code=<?php echo $row->code; ?>">
            <i class="fas fa-cogs"></i> Manage
        </a>

        <button class="btn btn-<?php echo $row->is_active ? 'warning' : 'primary'; ?> btn-sm"
            onclick="toggleBankAccType(<?php echo $row->acctype_id; ?>, <?php echo $row->is_active; ?>)">
            <i class="fas fa-<?php echo $row->is_active ? 'times' : 'check'; ?>"></i>
            <?php echo $row->is_active ? 'Disable' : 'Enable'; ?>
        </button>

        <a class="btn btn-danger btn-sm delete-confirm" href="pages_manage_accs.php?deleteBankAccType=<?php echo $row->acctype_id; ?>">
            <i class="fas fa-trash"></i> Delete
        </a>
    </div>
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
            </section>
        </div>

        <?php include("dist/_partials/footer.php"); ?>
    </div>

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="plugins/datatables/jquery.dataTables.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function () {
            $("#example1").DataTable();
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

        // Confirmation before delete
        $(document).on('click', '.delete-confirm', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            }); 
        });
        function toggleBankAccType(id, currentStatus) {
            let actionText = currentStatus ? "disable" : "enable";
            Swal.fire({
                title: "Are you sure?",
                text: `You are about to ${actionText} this account type.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: `Yes, ${actionText} it!`
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `pages_manage_accs.php?toggleBankAccType=${id}&status=${currentStatus}`;
                }
            });
        }

    </script>
</body>

</html>