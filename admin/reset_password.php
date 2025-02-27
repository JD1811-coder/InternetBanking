<?php
session_start();
include('conf/config.php');

if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['email'])) {
    header("Location: forget_password.php");
    exit();
}

if (isset($_POST['reset_password'])) {
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = $_SESSION['email'];

    if ($new_password !== $confirm_password) {
        $err = "Passwords do not match!";
    } else {
        $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT); // Hash new password

        $query = "UPDATE iB_admin SET password=?, otp=NULL, otp_expiry=NULL WHERE email=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ss', $new_hashed_password, $email);
        $stmt->execute();
        $stmt->close();

        session_destroy(); // Clear session after password reset
        header("Location: pages_index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<?php include("dist/_partials/head.php"); ?>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <p>DigiBankX - Reset Password</p>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Enter your new password</p>

                <?php if (isset($err)) echo "<p style='color:red;'>$err</p>"; ?>

                <form method="POST">
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="New Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="reset_password" class="btn btn-danger btn-block">Reset</button>
                        </div>
                    </div>
                </form>

                <p class="mt-2">
                    <a href="pages_index.php">Back to Login</a>
                </p>
            </div>
        </div>
    </div>

    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
</body>
</html>
