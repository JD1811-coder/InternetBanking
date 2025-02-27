<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('conf/config.php'); //get configuration file
if (isset($_POST['login'])) {
  $email = trim($_POST['email']);
  $password = trim($_POST['password']); // Do NOT hash here!

  // Get hashed password from database
  $stmt = $mysqli->prepare("SELECT admin_id, password FROM iB_admin WHERE email=?");
  if ($stmt === false) {
      die('Error: ' . htmlspecialchars($mysqli->error));
  }
  
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $stmt->bind_result($admin_id, $db_password); // Fetch hashed password
  $stmt->fetch();
  $stmt->close();

  // Verify password
  if ($db_password && password_verify($password, $db_password)) {
      $_SESSION['admin_id'] = $admin_id;
      header("location: pages_dashboard.php");
      exit();
  } else {
      $err = "Access Denied. Please Check Your Credentials";
  }
}


/* Persist System Settings On Brand */
$ret = "SELECT * FROM `iB_SystemSettings` ";
$stmt = $mysqli->prepare($ret);
$stmt->execute(); //ok
$res = $stmt->get_result();
while ($auth = $res->fetch_object()) {
?>
<!-- Log on to codeastro.com for more projects! -->
  <!DOCTYPE html>
  <html>
  <meta http-equiv="content-type" content="text/html;charset=utf-8" />
  <?php include("dist/_partials/head.php"); ?>

  <body class="hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        <p><?php echo $auth->sys_name; ?></p>
      </div>
      <!-- /.login-logo -->
      <div class="card">
        <div class="card-body login-card-body">
          <p class="login-box-msg">Log In To Start Administrator Session</p>

          <form method="post">
            <div class="input-group mb-3">
              <input type="email" name="email" class="form-control" placeholder="Email">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" name="password" class="form-control" placeholder="Password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-4">
                <div class="icheck-primary">
                  <!-- <input type="checkbox" id="remember">
                  <label for="remember">
                    Remember Me
                  </label> -->
                </div>
              </div>
              <!-- /.col -->
              <div class="col-8">
                <button type="submit" name="login" class="btn btn-danger btn-block">Log In as Admin</button>
              </div>
              <!-- /.col -->
            </div>
            <p class="mb-1">
                    <a href="forget_password.php">I forgot my password</a>
                </p>
          </form>

        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>

  </body>

  </html>
<?php
} ?> 
