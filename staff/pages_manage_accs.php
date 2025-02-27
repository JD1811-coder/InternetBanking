<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();

$staff_id = $_SESSION['staff_id'];

// Toggle Enable/Disable Status
if (isset($_GET['toggleStatus'])) {
  $id = intval($_GET['toggleStatus']);
  $currentStatus = intval($_GET['currentStatus']);
  $newStatus = ($currentStatus === 1) ? 0 : 1;

  $adn = "UPDATE iB_Acc_types SET is_active = ? WHERE acctype_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('ii', $newStatus, $id);
  $stmt->execute();
  $stmt->close();

  if ($stmt) {
      $info = "Account Type " . ($newStatus === 1 ? "Enabled" : "Disabled") . " Successfully";
  } else {
      $err = "Failed to Update Status. Try Again.";
  }
}


// Delete Account Type
if (isset($_GET['deleteBankAccType'])) {
    $id = intval($_GET['deleteBankAccType']);

    $adn = "DELETE FROM iB_Acc_types WHERE acctype_id = ?";
    $stmt = $mysqli->prepare($adn);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    if ($stmt) {
        $info = "Account Type Removed";
    } else {
        $err = "Failed to delete account type. Try again later.";
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
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
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
        </div><!-- /.container-fluid -->
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Select on any action options to manage your account types</h3>
              </div>
              <div class="card-body">
                <table id="example1" class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Rate</th>
                      <th>Code</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    //fetch all iB_Acc_types
                    $ret = "SELECT * FROM  iB_Acc_types ORDER BY RAND() ";
                    $stmt = $mysqli->prepare($ret);
                    $stmt->execute(); //ok
                    $res = $stmt->get_result();
                    $cnt = 1;
                    while ($row = $res->fetch_object()) {

                    ?>

                      <tr>
                        <td><?php echo $cnt; ?></td>
                        <td><?php echo $row->name; ?></td>
                        <td><?php echo $row->rate; ?>%</td>
                        <td><?php echo $row->code; ?></td>

                        <td>
    <!-- Manage Button -->
    <a class="btn btn-success btn-sm" href="pages_update_accs.php?code=<?php echo $row->code; ?>">
        <i class="fas fa-cogs"></i> Manage
    </a>

    <!-- Enable/Disable Button -->
    <a class="btn btn-<?php echo ($row->is_active === 1) ? 'warning' : 'primary'; ?> btn-sm" 
       href="pages_manage_accs.php?toggleStatus=<?php echo $row->acctype_id; ?>&currentStatus=<?php echo $row->is_active; ?>">
        <i class="fas fa-toggle-<?php echo ($row->is_active === 1) ? 'on' : 'off'; ?>"></i>
        <?php echo ($row->is_active === 1) ? 'Disable' : 'Enable'; ?>
    </a>

    <!-- Delete Button -->
    <a class="btn btn-danger btn-sm" 
       href="pages_manage_accs.php?deleteBankAccType=<?php echo $row->acctype_id; ?>" 
       onclick="return confirm('Are you sure you want to delete this account type?');">
        <i class="fas fa-trash"></i> Delete
    </a>
</td>



                      </tr>
                    <?php $cnt = $cnt + 1;
                    } ?>
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
  <script>
    $(function() {
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