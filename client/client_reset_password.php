<?php
session_start();
include('conf/config.php');

if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['email'])) {
    header("Location: client_forget_password.php");
    exit();
}

if (isset($_POST['reset_password'])) {
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $email = $_SESSION['email'];

    // Validate password strength
    if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $err = "Password must be at least 8 characters long, contain an uppercase letter, and a number!";
    } elseif ($new_password !== $confirm_password) {
        $err = "Passwords do not match!";
    } else {
        // Hash new password
        $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        if ($stmt) {
            echo "New Hashed Password: " . $new_hashed_password; // Debugging output
        }
        // Update password in the database
        $query = "UPDATE iB_clients SET password=? WHERE email=?";
        $stmt = $mysqli->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param('ss', $new_hashed_password, $email);
            if ($stmt->execute()) {
                // Unset session variables instead of destroying session
                unset($_SESSION['otp_verified'], $_SESSION['email']);

                header("Location: pages_client_index.php");
                exit();
            } else {
                $err = "Something went wrong. Please try again.";
            }
        } else {
            $err = "Database error: " . $mysqli->error;
        }
        
    }
}
?>

<!DOCTYPE html>
<html>
<?php include("dist/_partials/head.php"); ?>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <p>DigiBankX - Client Reset Password</p>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Enter a new password</p>
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
                            <button type="submit" name="reset_password" class="btn btn-primary btn-block">Reset Password</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
</body>
</html>
