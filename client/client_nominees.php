<?php
session_start();
include('conf/config.php');

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}

$client_id = $_SESSION['client_id'];

// Enable/Disable nominee
if (isset($_GET['toggleNominee'])) {
    $id = intval($_GET['toggleNominee']);
    $currentStatus = intval($_GET['status']);
    $newStatus = $currentStatus === 1 ? 0 : 1;

    $query = "UPDATE iB_nominees SET is_active = ? WHERE nominee_id = ? AND client_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('iii', $newStatus, $id, $client_id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $info = $newStatus ? "Nominee enabled" : "Nominee disabled";
    } else {
        $err = "Failed to update nominee status. Please try again.";
    }
}

// Delete nominee
if (isset($_GET['deleteNominee'])) {
    $id = intval($_GET['deleteNominee']);

    $query = "DELETE FROM iB_nominees WHERE nominee_id = ? AND client_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ii', $id, $client_id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $info = "Nominee deleted successfully.";
    } else {
        $err = "Failed to delete nominee. Please try again.";
    }
}

// Fetch nominees
$query = "SELECT * FROM iB_nominees WHERE client_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('i', $client_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
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
                            <h1>Show Nominees</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="client_nominees.php"> Nominees</a></li>
                                <li class="breadcrumb-item active">Show Nominees</li>
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
                                <h3 class="card-title">Show nominees</h3>
                            </div>
                            <div class="card-body">
                                <table id="example1" class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Relation</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <!-- <th>Actions</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cnt = 1;
                                        while ($row = $result->fetch_object()) { ?>
                                            <tr>
                                                <td><?php echo $cnt; ?></td>
                                                <td><?php echo $row->nominee_name; ?></td>
                                                <td><?php echo $row->relation; ?></td>
                                                <td><?php echo $row->nominee_email; ?></td>
                                                <td><?php echo $row->nominee_phone; ?></td>
                                                <td><?php echo $row->nominee_address; ?></td>
                                                <!-- <td>
                                                    <a class="btn btn-success btn-sm" href="pages_view_nominee.php?nominee_id=<?php echo $row->nominee_id; ?>">
                                                        <i class="fas fa-cogs"></i> Manage
                                                    </a>
                                                    <a class="btn btn-<?php echo $row->is_active ? 'warning' : 'primary'; ?> btn-sm" 
                                                        href="client_nominees.php?toggleNominee=<?php echo $row->nominee_id; ?>&status=<?php echo $row->is_active; ?>">
                                                        <i class="fas fa-<?php echo $row->is_active ? 'times' : 'check'; ?>"></i> 
                                                        <?php echo $row->is_active ? 'Disable' : 'Enable'; ?>
                                                    </a>
                                                    <a class="btn btn-danger btn-sm" href="client_nominees.php?deleteNominee=<?php echo $row->nominee_id; ?>" onclick="return confirm('Are you sure you want to delete this nominee?');">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </td> -->
                                            </tr>
                                        <?php $cnt++; } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- Footer -->
        <?php include("dist/_partials/footer.php"); ?>

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
    <!-- DataTable Initialization -->
    <script>
        $(function() {
            $("#example1").DataTable();
        });
    </script>
</body>
</html>
