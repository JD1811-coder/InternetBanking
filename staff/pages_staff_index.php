<?php
session_start();
include('conf/config.php'); // Database connection

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare SQL statement to get stored hashed password
    $stmt = $mysqli->prepare("SELECT staff_id, password FROM iB_staff WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($staff_id, $stored_password);
    
    if ($stmt->fetch()) {
        // Verify the entered password against the stored hash
        if (password_verify($password, $stored_password)) {
            $_SESSION['staff_id'] = $staff_id;
            $_SESSION['success_msg'] = "Login Successful";
            header("location:pages_dashboard.php");
            exit();
        } else {
            $err = "Invalid email or password!";
        }
    } else {
        $err = "Invalid email or password!";
    }
    $stmt->close();
}

// Fetch system settings for branding
$ret = "SELECT * FROM `iB_SystemSettings`";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
$auth = $res->fetch_object();
?>

<!DOCTYPE html>
<html>
<?php include("dist/_partials/head.php"); ?>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <p><?php echo $auth->sys_name; ?></p>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <?php
                if (isset($_SESSION['success_msg'])) {
                    echo "<p style='color:green;'>".$_SESSION['success_msg']."</p>";
                    unset($_SESSION['success_msg']);
                }
                ?>
                <p class="login-box-msg">Log In To Start Staff Session</p>
                <form method="post">
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember">
                                <label for="remember">Remember Me</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <button type="submit" name="login" class="btn btn-success btn-block">Log In</button>
                        </div>
                    </div>
                </form>
                <p class="mb-1"><a href="staff_forget_password.php">I forgot my password</a></p>
                <?php
                if (isset($err)) {
                    echo "<p style='color:red;'>$err</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
</body>
</html>
