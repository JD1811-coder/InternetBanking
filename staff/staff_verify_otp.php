<?php
session_start();
include('conf/config.php');

if (!isset($_SESSION['email'])) {
    header("Location: staff_forget_password.php");
    exit();
}

if (isset($_POST['verify_otp'])) {
    $otp = trim($_POST['otp']);
    $email = $_SESSION['email'];

    // Check OTP and expiry
    $query = "SELECT * FROM iB_staff WHERE email=? AND otp=? AND otp_expiry > NOW()";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('ss', $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['otp_verified'] = true;
        header("Location: staff_reset_password.php");
        exit();
    } else {
        $err = "Invalid or Expired OTP";
    }
}
?>

<!DOCTYPE html>
<html>
<?php include("dist/_partials/head.php"); ?>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <p>DigiBankX - Verify OTP</p>
        </div>

        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Enter the OTP sent to your email</p>

                <?php if (isset($err)) echo "<p style='color:red;'>$err</p>"; ?>

                <form method="POST">
                    <div class="input-group mb-3">
                        <input type="text" name="otp" class="form-control" placeholder="Enter OTP" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-key"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" name="verify_otp" class="btn btn-primary btn-block">Verify OTP</button>
                        </div>
                    </div>
                </form>

                <p class="mt-2">
                    <a href="staff_forget_password.php">Resend OTP</a>
                </p>
            </div>
        </div>
    </div>

    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
</body>
</html>
