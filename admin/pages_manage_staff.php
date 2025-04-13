<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

// Enable/Disable Staff
if (isset($_GET['toggleStaff'])) {
    $id = intval($_GET['toggleStaff']);
    $currentStatus = intval($_GET['status']);
    $newStatus = $currentStatus === 1 ? 0 : 1;

    $query = "UPDATE iB_staff SET is_active = ? WHERE staff_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $newStatus, $id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $info = $newStatus ? "Staff account enabled" : "Staff account disabled";
    } else {
        $err = "Failed to update staff status. Please try again later.";
    }
}


if (isset($_GET['deleteStaff'])) {
    $id = intval($_GET['deleteStaff']);

    // Delete Query
    $query = "DELETE FROM iB_staff WHERE staff_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        // echo "<script>alert('Staff deleted successfully!'); window.location.href='pages_manage_staff.php';</script>";
    } else {
        // echo "<script>alert('Error deleting staff. Please try again later.');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>

<body class="hold-transition sidebar-mini">
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
                            <h1>iBanking Staffs</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="pages_manage_staff.php">iBank Staffs</a></li>
                                <li class="breadcrumb-item active">Manage Staffs</li>
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
                                <h3 class="card-title">Select on any action options to manage your staffs</h3>
                            </div>
                            <div class="card-body">
                                <table id="example1" class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Staff Number</th>
                                            <th>Contact</th>
                                            <th>Email</th>
                                            <th>Gender</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ret = "SELECT * FROM iB_staff ORDER BY RAND()";
                                        $stmt = $mysqli->prepare($ret);
                                        $stmt->execute();
                                        $res = $stmt->get_result();
                                        $cnt = 1;
                                        while ($row = $res->fetch_object()) {
                                            ?>
                                            <tr>
                                                <td><?php echo $cnt; ?></td>
                                                <td><?php echo $row->name; ?></td>
                                                <td><?php echo $row->staff_number; ?></td>
                                                <td><?php echo $row->phone; ?></td>
                                                <td><?php echo $row->email; ?></td>
                                                <td><?php echo $row->sex; ?></td>
                                                <td>
    <div class="btn-group">
        <a class="btn btn-success btn-sm mr-1"
            href="pages_view_staff.php?staff_number=<?php echo $row->staff_number; ?>">
            <i class="fas fa-cogs"></i> Manage
        </a>

        <button class="btn btn-<?php echo $row->is_active ? 'warning' : 'primary'; ?> btn-sm mr-1"
            onclick="toggleStaff(<?php echo $row->staff_id; ?>, <?php echo $row->is_active; ?>)">
            <i class="fas fa-<?php echo $row->is_active ? 'times' : 'check'; ?>"></i>
            <?php echo $row->is_active ? 'Disable' : 'Enable'; ?>
        </button>

        <button class="btn btn-danger btn-sm"
            onclick="deleteStaff(<?php echo $row->staff_id; ?>)">
            <i class="fas fa-trash"></i> Delete
        </button>
    </div>
</td>

                                            </tr>
                                            <?php $cnt++;
                                        } ?>
                                    </tbody>

                                    </tfoot>
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
    <!-- page script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleStaff(id, currentStatus) {
            let actionText = currentStatus ? "disable" : "enable";
            Swal.fire({
                title: "Are you sure?",
                text: `You are about to ${actionText} this staff account.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: `Yes, ${actionText} it!`
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `pages_manage_staff.php?toggleStaff=${id}&status=${currentStatus}`;
                }
            });
        }

        function deleteStaff(id) {
            Swal.fire({
                title: "Are you sure?",
                text: "This action is irreversible! The staff account will be permanently deleted.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `pages_manage_staff.php?deleteStaff=${id}`;
                }
            });
        }
    </script>

    <script>
        $(function () {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
            });
        });
    </script>
    
</body>

</html>