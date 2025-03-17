<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();


//clear notifications and alert user that they are cleared
if (isset($_GET['Clear_Notifications'])) {
  $id = intval($_GET['Clear_Notifications']);
  $adn = "DELETE FROM  iB_notifications  WHERE notification_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->close();

  if ($stmt) {
    $info = "Notifications Cleared";
  } else {
    $err = "Try Again Later";
  }
}
// Query to get account types and their counts for all clients
$query = "SELECT t.name AS acc_type, COUNT(*) AS count
FROM ib_bankaccounts a
JOIN ib_acc_types t ON a.acc_type_id = t.acctype_id
GROUP BY t.name;
";
$stmt = $mysqli->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$accountData = [];
while ($row = $result->fetch_assoc()) {
  $accountData[] = [
    "y" => $row['count'],
    "name" => $row['acc_type'],
    "exploded" => true
  ];
}
//return total number of ibank clients
$result = "SELECT count(*) FROM iB_clients";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($iBClients);
$stmt->fetch();
$stmt->close();

//return total number of iBank Staffs
$result = "SELECT count(*) FROM iB_staff";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($iBStaffs);
$stmt->fetch();
$stmt->close();

//return total number of iBank Account Types
$result = "SELECT count(*) FROM iB_Acc_types";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($iB_AccsType);
$stmt->fetch();
$stmt->close();

//return total number of iBank Accounts
$result = "SELECT count(*) FROM iB_bankAccounts";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($iB_Accs);
$stmt->fetch();
$stmt->close();

//return total number of iBank Deposits
$result = "SELECT SUM(transaction_amt) FROM iB_Transactions WHERE  tr_type = 'Deposit' ";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($iB_deposits);
$stmt->fetch();
$stmt->close();

//return total number of iBank Withdrawals
$result = "SELECT SUM(transaction_amt) FROM iB_Transactions WHERE  tr_type = 'Withdrawal' ";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($iB_withdrawal);
$stmt->fetch();
$stmt->close();



//return total number of iBank Transfers
$result = "SELECT SUM(transaction_amt) FROM iB_Transactions WHERE  tr_type = 'Transfer' ";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($iB_Transfers);
$stmt->fetch();
$stmt->close();

//return total number of  iBank initial cash->balances
// $client_id = $_SESSION['client_id'];
$result = "SELECT SUM(acc_amount) FROM iB_bankAccounts";
$stmt = $mysqli->prepare($result);
// $stmt->bind_param('i', $client_id);
$stmt->execute();
$stmt->bind_result($TotalBalInAccount);
$stmt->fetch();
$stmt->close();

//ibank money in the wallet
$result = "SELECT SUM(transaction_amt) FROM iB_Transactions ";
$stmt = $mysqli->prepare($result);
$stmt->execute();
$stmt->bind_result($new_amt);
$stmt->fetch();
$stmt->close();
//Withdrawal Computations

?>
<!-- Log on to codeastro.com for more projects! -->
<!DOCTYPE html>
<html lang="en">
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
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0 text-dark">Staff Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Info boxes -->
          <div class="row">

            <!-- ./ iBank Clients -->
            <div class="col-12 col-sm-6 col-md-4">
              <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"> Clients</span>
                  <span class="info-box-number">
                    <?php echo $iBClients; ?>
                  </span>
                </div>
              </div>
            </div>
            <!-- ./ iBank Clients -->

            <!-- fix for small devices only -->
            <div class="clearfix hidden-md-up"></div>

            <!--Ibank Acc types-->
            <!-- <div class="col-12 col-sm-6 col-md-4">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-briefcase"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"> Account Types</span>
                  <span class="info-box-number"><?php echo $iB_AccsType; ?></span>
                </div>
              </div>
            </div> -->
            <!-- /.iBank Acc types -->

            <!--iBank Accounts-->
            <div class="col-12 col-sm-6 col-md-4">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"> Accounts</span>
                  <span class="info-box-number"><?php echo $iB_Accs; ?></span>
                </div>
              </div>
            </div>
            <!-- iBank Acc-->
          </div>

          <div class="row">
            <!--iBank Deposits -->
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-upload"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text"> Deposits</span>
                  <span class="info-box-number">
                    Rs. <?php echo $iB_deposits; ?>
                  </span>
                </div>
              </div>
            </div>
            <!----./ iBank Deposits-->

            <!--iBank Withdrwals-->
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-download"></i></span>

                <div class="info-box-content">
                  <span class="info-box-text">Withdrawals</span>
                  <span class="info-box-number">Rs. <?php echo $iB_withdrawal; ?> </span>
                </div>
              </div>
            </div>
            <!-- Withdrawals-->

            <!-- fix for small devices only -->
            <div class="clearfix hidden-md-up"></div>

            <!--Transfers-->
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-random"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Transfers</span>
                  <span class="info-box-number">Rs. <?php echo $iB_Transfers; ?></span>
                </div>
              </div>
            </div>
            <!-- /.Transfers-->

            <!--Balances-->
            <div class="col-12 col-sm-6 col-md-3">
              <div class="info-box mb-3">
                <span class="info-box-icon bg-purple elevation-1"><i class="fas fa-money-bill-alt"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Wallet Balance</span>
                  <span class="info-box-number">Rs. <?php echo $TotalBalInAccount; ?></span>
                </div>
              </div>
            </div>
            <!-- ./Balances-->
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <h5 class="card-title">Advanced Analytics</h5>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div><!-- Log on to codeastro.com for more projects! -->
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="chart">
                        <!-- Transaction Donought chart Canvas -->
                        <div id="PieChart" class="col-md-6" style="height: 400px; max-width: 500px; margin: 0px auto;">
                        </div>
                      </div>
                      <!-- /.chart-responsive -->
                    </div>
                    <hr>
                    <div class="col-md-6">
                      <div class="chart">
                        <div id="AccountsPerAccountCategories" class="col-md-6"
                          style="height: 400px; max-width: 500px; margin: 0px auto;"></div>
                      </div>
                      <!-- /.chart-responsive -->
                    </div>

                    <!-- /.col -->
                  </div>
                  <!-- /.row -->
                </div>
                <!-- ./card-body -->
                <div class="card-footer">
                  <div class="row">
                    <div class="col-sm-3 col-6">
                      <div class="description-block border-right">
                        <h5 class="description-header">Rs. <?php echo $iB_deposits; ?></h5>
                        <span class="description-text">TOTAL DEPOSITS</span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                      <div class="description-block border-right">
                        <h5 class="description-header">Rs. <?php echo $iB_withdrawal; ?></h5>
                        <span class="description-text">TOTAL WITHDRAWALS</span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                      <div class="description-block border-right">
                        <h5 class="description-header">Rs. <?php echo $iB_Transfers; ?> </h5>
                        <span class="description-text">TOTAL TRANSFERS</span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                    <!-- /.col -->
                    <div class="col-sm-3 col-6">
                      <div class="description-block">
                        <h5 class="description-header">Rs. <?php echo $TotalBalInAccount; ?> </h5>
                        <span class="description-text">TOTAL MONEY IN iBANK Account</span>
                      </div>
                      <!-- /.description-block -->
                    </div>
                  </div>
                  <!-- /.row -->
                </div>
                <!-- /.card-footer -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /.col -->
          </div>
          <!-- /.row -->

          <!-- Main row -->
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header border-transparent">
                  <h3 class="card-title">Latest Transactions</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                  <div class="table-responsive">
                    <table class="table table-hover table-bordered m-0">
                      <thead>
                        <tr>
                          <th>Transaction Code</th>
                          <th>Account No.</th>
                          <th>Type</th>
                          <th>Amount</th>
                          <th>Acc. Owner</th>
                          <th>Timestamp</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $limit = 10; // Records per page
                        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                        $offset = ($page - 1) * $limit;

                        // Get total records count
                        $countQuery = "SELECT COUNT(*) AS total FROM iB_Transactions";
                        $countStmt = $mysqli->prepare($countQuery);
                        $countStmt->execute();
                        $countResult = $countStmt->get_result();
                        $totalRows = $countResult->fetch_object()->total;
                        $totalPages = ceil($totalRows / $limit);

                        // Fetch paginated records
                        $query = "SELECT 
    t.*,  
    b.account_number, 
    COALESCE(bt.name, 'N/A') AS acc_type,  -- Fetching Account Type Name
    COALESCE(bt.rate, 0) AS acc_rates,     -- Fetching Account Rate
    COALESCE(b.acc_name, 'N/A') AS account_owner, 
    COALESCE(c.name, 'N/A') AS client_name
FROM iB_Transactions t
LEFT JOIN ib_bankaccounts b ON t.account_id = b.account_id
LEFT JOIN ib_clients c ON t.client_id = c.client_id
LEFT JOIN ib_acc_types bt ON b.acc_type_id = bt.acctype_id  -- Added Join for Account Type
ORDER BY t.created_at DESC
LIMIT ? OFFSET ?;
";

                        $stmt = $mysqli->prepare($query);
                        if ($stmt) {
                          $stmt->bind_param("ii", $limit, $offset);
                          $stmt->execute();
                          $res = $stmt->get_result();

                          while ($row = $res->fetch_object()) {
                            $transTstamp = $row->created_at ?? 'N/A';
                            $alertClass = "<span class='badge badge-warning'>Transfer</span>";

                            if (isset($row->tr_type)) {
                              if ($row->tr_type == 'Deposit') {
                                $alertClass = "<span class='badge badge-success'>$row->tr_type</span>";
                              } elseif ($row->tr_type == 'Withdrawal') {
                                $alertClass = "<span class='badge badge-danger'>$row->tr_type</span>";
                              }
                            }
                            ?>
                            <tr>
                              <td><?php echo htmlspecialchars($row->tr_code ?? 'N/A'); ?></td>
                              <td><?php echo htmlspecialchars($row->account_number ?? 'N/A'); ?></td>
                              <td><?php echo $alertClass; ?></td>
                              <td>Rs. <?php echo htmlspecialchars($row->transaction_amt ?? '0.00'); ?></td>
                              <td><?php echo isset($row->client_name) ? htmlspecialchars($row->client_name) : 'N/A'; ?></td>
                              <td>
                                <?php echo $transTstamp !== 'N/A' ? date("d-M-Y h:i:s A", strtotime($transTstamp)) : 'N/A'; ?>
                              </td>
                            </tr>
                          <?php }
                        } else {
                          echo "<tr><td colspan='6'>Error fetching transactions.</td></tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                  <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                  <a href="pages_transactions_engine.php" class="btn btn-sm btn-info float-left">View All</a>

                  <!-- Pagination -->
                  <ul class="pagination pagination-sm float-right">
                    <?php if ($page > 1): ?>
                      <li class="page-item"><a class="page-link" href="?page=<?php echo ($page - 1); ?>">« Prev</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                      <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                      </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                      <li class="page-item"><a class="page-link" href="?page=<?php echo ($page + 1); ?>">Next »</a></li>
                    <?php endif; ?>
                  </ul>
                </div>
                <!-- /.card-footer -->
              </div>
              <!-- /.card -->
            </div>
          </div>

        </div>
        <!-- /.row -->
    </div>
    <!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <?php include("dist/_partials/footer.php"); ?>

  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.js"></script>

  <!-- OPTIONAL SCRIPTS -->
  <script src="dist/js/demo.js"></script>

  <!-- PAGE PLUGINS -->
  <!-- jQuery Mapael -->
  <script src="plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
  <script src="plugins/raphael/raphael.min.js"></script>
  <script src="plugins/jquery-mapael/jquery.mapael.min.js"></script>
  <script src="plugins/jquery-mapael/maps/usa_states.min.js"></script>
  <!-- ChartJS -->
  <script src="plugins/chart.js/Chart.min.js"></script>

  <!-- PAGE SCRIPTS -->
  <script src="dist/js/pages/dashboard2.js"></script>

  <!--Load Canvas JS -->
  <script src="plugins/canvasjs.min.js"></script>
  <!--Load Few Charts-->
  <script>
    window.onload = function () {

      var Piechart = new CanvasJS.Chart("PieChart", {
        exportEnabled: false,
        animationEnabled: true,
        title: {
          text: "A/C Types "
        },
        legend: {
          cursor: "pointer",
          itemclick: explodePie
        },
        data: [{
          type: "pie",
          showInLegend: true,
          toolTipContent: "{name}: <strong>{y}</strong>",
          indexLabel: "{name} - {y}",
          dataPoints: <?php echo json_encode($accountData, JSON_NUMERIC_CHECK); ?>
        }]
      });
      var AccChart = new CanvasJS.Chart("AccountsPerAccountCategories", {
        exportEnabled: false,
        animationEnabled: true,
        title: {
          text: "Transactions"
        },
        legend: {
          cursor: "pointer",
          itemclick: explodePie
        },
        data: [{
          type: "pie",
          showInLegend: true,
          toolTipContent: "{name}: <strong>{y}%</strong>",
          indexLabel: "{name} - {y}%",
          dataPoints: [{
            y: <?php
            //return total number of transactions under  Withdrawals
            $result = "SELECT count(*) FROM iB_Transactions WHERE  tr_type ='Withdrawal' ";
            $stmt = $mysqli->prepare($result);
            $stmt->execute();
            $stmt->bind_result($Withdrawals);
            $stmt->fetch();
            $stmt->close();
            echo $Withdrawals;
            ?>,
            name: "Withdrawals",
            exploded: true
          },

          {
            y: <?php
            //return total number of transactions under  Deposits
            $result = "SELECT count(*) FROM iB_Transactions WHERE  tr_type ='Deposit' ";
            $stmt = $mysqli->prepare($result);
            $stmt->execute();
            $stmt->bind_result($Deposits);
            $stmt->fetch();
            $stmt->close();
            echo $Deposits;
            ?>,
            name: "Deposits",
            exploded: true
          },

          {
            y: <?php
            //return total number of transactions under  Deposits
            $result = "SELECT count(*) FROM iB_Transactions WHERE  tr_type ='Transfer' ";
            $stmt = $mysqli->prepare($result);
            $stmt->execute();
            $stmt->bind_result($Transfers);
            $stmt->fetch();
            $stmt->close();
            echo $Transfers;
            ?>,
            name: "Transfers",
            exploded: true
          }

          ]
        }]
      });
      Piechart.render();
      AccChart.render();
    }

    function explodePie(e) {
      if (typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
        e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
      } else {
        e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
      }
      e.chart.render();

    }
  </script>

</body>

</html>