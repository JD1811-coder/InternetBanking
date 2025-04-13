<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
if (isset($_POST['systemSettings'])) {
  $error = 0;
  
  // Validate System Name (only letters & spaces)
  if (isset($_POST['sys_name']) && !empty($_POST['sys_name'])) {
      $sys_name = trim($_POST['sys_name']); // Trim spaces
      if (!preg_match("/^[a-zA-Z ]+$/", $sys_name)) {
          $error = 1;
          $err = "System Name can only contain letters and spaces.";
      }
  } else {
      $error = 1;
      $err = "System Name Cannot Be Empty.";
  }

  // Validate System Logo File Type
  if (!empty($_FILES['sys_logo']['name'])) {
      $allowed_extensions = ['png', 'jpeg', 'jpg'];
      $file_extension = strtolower(pathinfo($_FILES['sys_logo']['name'], PATHINFO_EXTENSION));

      if (!in_array($file_extension, $allowed_extensions)) {
          $error = 1;
          $err = "Only PNG, JPEG, and JPG file formats are allowed for the logo.";
      }
  }
  if (isset($_POST['sys_tagline']) && !empty(trim($_POST['sys_tagline']))) {
    $sys_tagline = trim($_POST['sys_tagline']);
} else {
    $error = 1;
    $err = "System Tagline Cannot Be Empty or Just Spaces.";
}


  // If no errors, proceed with database update
  if (!$error) {
      $id = $_POST['id'];
      $sys_tagline = trim($_POST['sys_tagline']);

      // Handle Logo Upload
      if (!empty($_FILES['sys_logo']['name'])) {
          $sys_logo = $_FILES['sys_logo']['name'];
          move_uploaded_file($_FILES["sys_logo"]["tmp_name"], "dist/img/" . $sys_logo);
      } else {
          $sys_logo = ""; // Keep existing logo if no new upload
      }

      $query = "UPDATE iB_SystemSettings SET sys_name =?, sys_logo =?, sys_tagline=? WHERE id = ?";
      $stmt = $mysqli->prepare($query);
      $stmt->bind_param('ssss', $sys_name, $sys_logo, $sys_tagline, $id);
      $stmt->execute();

      if ($stmt) {
          $_SESSION['success'] = "Settings Updated Successfully!";
          header("refresh:1; url=pages_system_settings.php");
          exit();
      } else {
          $info = "Please Try Again Later.";
      }
  }
}

?>
<!-- Log on to codeastro.com for more projects! -->
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
              <h1>System Settings</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">System Settings</li>
              </ol>
            </div>
          </div>
        </div><!-- /.container-fluid -->
      </section><!-- Log on to codeastro.com for more projects! -->

      <!-- Main content -->
      <section class="content">
        <div class="row">
          <div class="col-12">
            <div class="card card-purple card-outline">
              <div class="card-header">
                <h3 class="card-title">Reconfigure This System Accordingly</h3>
              </div>
              <div class="card-body">
                <?php
                /* Persisit System Settings On Brand */
                $ret = "SELECT * FROM iB_SystemSettings ";
                $stmt = $mysqli->prepare($ret);
                $stmt->execute(); //ok
                $res = $stmt->get_result();
                while ($sys = $res->fetch_object()) {
                ?>
                  <form method="post" enctype="multipart/form-data" role="form">
                    <div class="card-body">
                      <div class="row">
                       <div class="form-group col-md-12">
    <label for="">Company Name</label>
    <input type="text" required name="sys_name" value="<?php echo $sys->sys_name; ?>" class="form-control">
    <input type="hidden" required name="id" value="<?php echo $sys->id ?>" class="form-control">
    <?php if (isset($err) && strpos($err, 'System Name') !== false) : ?>
        <small class="text-danger"><?php echo $err; ?></small>
    <?php endif; ?>
</div>

<div class="form-group col-md-12">
    <label for="">Company Tagline</label>
    <input type="text" required name="sys_tagline" value="<?php echo isset($sys->sys_tagline) ? trim($sys->sys_tagline) : ''; ?>" class="form-control">
    <?php if (isset($err) && strpos($err, 'System Tagline') !== false) : ?>
        <small class="text-danger"><?php echo $err; ?></small>
    <?php endif; ?>
</div>


<div class="form-group col-md-12">
    <label for="">System Logo</label>
    <div class="input-group">
        <div class="custom-file">
            <input required name="sys_logo" type="file" class="custom-file-input">
            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
        </div>
    </div>
    <?php if (isset($err) && strpos($err, 'Only PNG') !== false) : ?>
        <small class="text-danger"><?php echo $err; ?></small>
    <?php endif; ?>
</div>
                      </div>
                    </div>
                    <div class="text-right">
                      <button type="submit" name="systemSettings" class="btn btn-success">Submit</button>
                    </div>
                  </form>
                <?php
                } ?>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
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
    /* Custom File Uploads */
    $(document).ready(function() {
      bsCustomFileInput.init();
    });
  </script>
  <script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector("form").addEventListener("submit", function (e) {
        let sysNameInput = document.querySelector("input[name='sys_name']");
        let taglineInput = document.querySelector("input[name='sys_tagline']");
        let fileInput = document.querySelector("input[name='sys_logo']");
        let file = fileInput.files[0];
        let error = false;

        // Validate System Name (only letters and spaces)
        if (!/^[a-zA-Z ]+$/.test(sysNameInput.value.trim())) {
            sysNameInput.nextElementSibling.textContent = "System Name can only contain letters and spaces.";
            error = true;
        } else {
            sysNameInput.nextElementSibling.textContent = "";
        }

        // Validate System Tagline (should not be empty or spaces only)
        if (taglineInput.value.trim() === "") {
            taglineInput.nextElementSibling.textContent = "System Tagline Cannot Be Empty or Just Spaces.";
            error = true;
        } else {
            taglineInput.nextElementSibling.textContent = "";
        }

        // Validate Logo File Type
        if (file) {
            let allowedExtensions = ["png", "jpeg", "jpg"];
            let fileExtension = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(fileExtension)) {
                fileInput.nextElementSibling.textContent = "Only PNG, JPEG, and JPG formats are allowed.";
                error = true;
            } else {
                fileInput.nextElementSibling.textContent = "";
            }
        }

        if (error) {
            e.preventDefault();
        }
    });
});
</script>

</script>

</body>

</html>