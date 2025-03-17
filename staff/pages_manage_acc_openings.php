<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$staff_id = $_SESSION['staff_id'];
//fire staff
if (isset($_GET['deleteBankAcc'])) {
  $id = intval($_GET['deleteBankAcc']);
  $adn = "DELETE FROM  iB_bankAccounts  WHERE account_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->close();

  if ($stmt) {
    $info = "Account Closed";
  } else {
    $err = "Try Again Later";
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
                <h3 class="card-title">Select on any action options to manage your accounts</h3>
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
    <th>Balance</th> <!-- New column -->
    <th>Date Opened</th>
    <th>Action</th>
  </tr>
</thead>
<tbody>
  <?php
  // Fetch all iB_Accs with balance
  $ret = "SELECT 
  ba.*, 
  c.name AS client_name, 
  ba.acc_amount, 
  at.name AS acc_type,   -- Fetching Account Type Name
  at.rate AS acc_rates   -- Fetching Interest Rate
FROM iB_bankAccounts ba
JOIN ib_clients c ON ba.client_id = c.client_id
JOIN ib_acc_types at ON ba.acc_type_id = at.acctype_id  -- Fetch Account Type Name & Rate
ORDER BY RAND()"; 


  $stmt = $mysqli->prepare($ret);
  $stmt->execute();
  $res = $stmt->get_result();
  $cnt = 1;

  while ($row = $res->fetch_object()) {
    $dateOpened = $row->created_at;
  ?>

    <tr>
      <td><?php echo $cnt; ?></td>
      <td><?php echo $row->acc_name; ?></td>
      <td><?php echo $row->account_number; ?></td>
      <td><?php echo $row->acc_rates; ?>%</td>
      <td><?php echo $row->acc_type; ?></td>
      <!-- <td><?php echo $row->name; ?></td> -->
      <td><?php echo number_format($row->acc_amount, 2); ?> </td> <!-- Display balance -->
      <td><?php echo date("d-M-Y", strtotime($dateOpened)); ?></td>
      <td class="text-center">
    <div class="d-flex justify-content-center gap-2">
        <a class="btn btn-success btn-sm" href="pages_update_client_accounts.php?account_id=<?php echo $row->account_id; ?>">
            <i class="fas fa-cogs"></i> Manage
        </a>
        <a class="btn btn-danger btn-sm" href="pages_manage_acc_openings.php?deleteBankAcc=<?php echo $row->account_id; ?>">
            <i class="fas fa-times"></i> Close Account
        </a>
    </div>
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
  <!-- page script -->
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