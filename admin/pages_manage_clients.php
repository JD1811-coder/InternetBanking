<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

// Enable/Disable Client
if (isset($_GET['toggleClient'])) {
  $id = intval($_GET['toggleClient']);
  $currentStatus = intval($_GET['status']);
  $newStatus = $currentStatus === 1 ? 0 : 1;

  $query = "UPDATE iB_clients SET is_active = ? WHERE client_id = ?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param('ii', $newStatus, $id);
  $stmt->execute();
  $stmt->close();

  if ($stmt) {
    $info = $newStatus ? "Client account enabled" : "Client account disabled";
  } else {
    $err = "Failed to update client status. Please try again.";
  }
}

// Delete Client
if (isset($_GET['deleteClient'])) {
  $id = intval($_GET['deleteClient']);
  $query = "DELETE FROM iB_clients WHERE client_id = ?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->close();

  if ($stmt) {
    $info = "Client account deleted.";
  } else {
    $err = "Failed to delete client account. Please try again.";
  }
}
?>

<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <?php include("dist/_partials/nav.php"); ?>
    <?php include("dist/_partials/sidebar.php"); ?>

    <div class="content-wrapper">
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Manage Clients</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Manage Clients</li>
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
                <h3 class="card-title">Manage Client Accounts</h3>
              </div>
              <div class="card-body">
                <table id="example1" class="table table-hover table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Name</th>
                      <th>Client Number</th>
                      <th>Contact</th>
                      <th>Email</th>
                      <th>Address</th>
                      <th>Nominee Name</th> <!-- Added Nominee Column -->
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    // Fetch Clients with Nominee Data
                    $query = "SELECT c.client_id, c.name, c.client_number, c.phone, c.email, c.address, c.is_active, 
                                     n.nominee_name 
                              FROM iB_clients c
                              LEFT JOIN iB_nominees n ON c.client_id = n.client_id
                              ORDER BY c.name ASC;";

                    $stmt = $mysqli->prepare($query);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $cnt = 1;
                    while ($row = $res->fetch_object()) {
                      ?>
                      <tr>
                        <td><?php echo $cnt; ?></td>
                        <td><?php echo htmlspecialchars($row->name); ?></td>
                        <td><?php echo htmlspecialchars($row->client_number); ?></td>
                        <td><?php echo htmlspecialchars($row->phone); ?></td>
                        <td><?php echo htmlspecialchars($row->email); ?></td>
                        <td><?php echo htmlspecialchars($row->address); ?></td>
                        <td><?php echo htmlspecialchars($row->nominee_name ?? 'N/A'); ?></td> <!-- Display Nominee -->
                        <td>
                          <div class="btn-group" role="group">
                            <a class="btn btn-success btn-sm"
                              href="pages_view_client.php?client_number=<?php echo $row->client_number; ?>">
                              <i class="fas fa-cogs"></i> Manage
                            </a>
                            <a class="btn btn-<?php echo $row->is_active ? 'warning' : 'primary'; ?> btn-sm" href="#"
                              onclick="toggleClient(<?php echo $row->client_id; ?>, <?php echo $row->is_active; ?>)">
                              <i class="fas fa-<?php echo $row->is_active ? 'times' : 'check'; ?>"></i>
                              <?php echo $row->is_active ? 'Disable' : 'Enable'; ?>
                            </a>

                            <a class="btn btn-danger btn-sm" href="#"
                              onclick="deleteClient(<?php echo $row->client_id; ?>)">
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
  <script>
  function toggleClient(clientId, currentStatus) {
    let action = currentStatus ? "disable" : "enable";
    Swal.fire({
      title: `Are you sure?`,
      text: `You are about to ${action} this client.`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: `Yes, ${action} it!`
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `pages_manage_clients.php?toggleClient=${clientId}&status=${currentStatus}`;
      }
    });
  }

  function deleteClient(clientId) {
    Swal.fire({
      title: "Are you sure?",
      text: "This action cannot be undone!",
      icon: "error",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Yes, delete it!"
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = `pages_manage_clients.php?deleteClient=${clientId}`;
      }
    });
  }
</script>

</body>

</html>