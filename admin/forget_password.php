<?php
session_start();
include('conf/config.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

if (isset($_POST['reset_password'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = "Invalid Email Format";
    } else {
        // Check if email exists
        $checkEmail = $mysqli->prepare("SELECT email FROM iB_admin WHERE email = ?");
        $checkEmail->bind_param('s', $email);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            // Generate 6-digit OTP
            $otp = rand(100000, 999999);
            
            // Store OTP in the database
            $query = "UPDATE iB_admin SET otp=?, otp_expiry=DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE email=?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('ss', $otp, $email);
            $stmt->execute();

            // Send OTP via Gmail using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'jenildhola1811@gmail.com'; // Update with actual email
                $mail->Password = 'xkfx oyox rokx hgku';   // Use App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
            
                $mail->setFrom('your-email@gmail.com', 'DigiBankX');
                $mail->addAddress($email);
            
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP';
                $mail->Body = "<p>Your OTP for password reset is: <strong>$otp</strong>. It is valid for 10 minutes.</p>";
            
                if ($mail->send()) {
                    $_SESSION['email'] = $email;  // Store email in session
                    $_SESSION['otp_verified'] = false;
                    header("Location: verify_otp.php"); 
                    exit();
                } else {
                    $err = "Email sending failed!";
                }
            } catch (Exception $e) {
                $err = "Email sending failed: " . $mail->ErrorInfo;
            }
        } else {
            $err = "Email Not Registered";
        }
        $checkEmail->close();
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
                <p class="login-box-msg">Enter your email to receive an OTP</p>

                <?php if (isset($err)) echo "<p style='color:red;'>$err</p>"; ?>
                <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>

                <form method="POST">
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                        <div class="col-12">
                            <button type="submit" name="reset_password" class="btn btn-danger btn-block">Request OTP</button>
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
