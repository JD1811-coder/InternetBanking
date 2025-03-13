<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

if (isset($_POST['update_staff_account'])) {
    $name = $_POST['name'];
    $staff_number = $_GET['staff_number'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $sex = $_POST['sex'];

    // Check for duplicate email or phone (excluding the current staff_number)
    $checkQuery = "SELECT * FROM iB_staff WHERE (email = ? OR phone = ?) AND staff_number != ?";
    $stmt = $mysqli->prepare($checkQuery);
    $stmt->bind_param('sss', $email, $phone, $staff_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email or phone number already exists. Please use a different one.";
    } else {
        // Profile Picture Validation
        if ($_FILES["profile_pic"]["name"] != '') {
            $profile_pic = $_FILES["profile_pic"]["name"];
            $allowed_extensions = ['jpg', 'jpeg', 'png'];
            $file_extension = pathinfo($profile_pic, PATHINFO_EXTENSION);

            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                $_SESSION['error'] = "Invalid file format. Only JPG, JPEG, and PNG allowed.";
            } else {
                // Move the uploaded file to the correct location
                $profile_pic_path = "dist/img/" . $profile_pic;
                move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profile_pic_path);

                // Update the database
                $query = "UPDATE iB_staff SET name=?, phone=?, email=?, sex=?, profile_pic=? WHERE staff_number=?";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param('ssssss', $name, $phone, $email, $sex, $profile_pic, $staff_number);
                $stmt->execute();

                if ($stmt) {
                    $_SESSION['success'] = "Staff Account Updated Successfully!";
                    header("Location: " . $_SERVER['PHP_SELF'] . "?staff_number=" . $staff_number); // Avoid resubmission
                    exit();
                } else {
                    $_SESSION['error'] = "Please Try Again Or Try Later.";
                }
            }
        } else {
            // If no new picture is uploaded, keep the old one
            $query = "UPDATE iB_staff SET name=?, phone=?, email=?, sex=? WHERE staff_number=?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('sssss', $name, $phone, $email, $sex, $staff_number);
            $stmt->execute();

            if ($stmt) {
                $_SESSION['success'] = "Staff Account Updated Successfully!";
                header("Location: " . $_SERVER['PHP_SELF'] . "?staff_number=" . $staff_number); // Avoid resubmission
                exit();
            } else {
                $_SESSION['error'] = "Please Try Again Or Try Later.";
            }
        }
    }

}
    $old_password = "";

    if (isset($_SESSION['staff_id'])) {
        $admin_id = $_SESSION['staff_id'];

        // Fetch old password from database
        $stmt = $mysqli->prepare("SELECT password FROM iB_staff WHERE staff_id = ?");
        $stmt->bind_param('i', $admin_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        // Mask the password for security purposes
        $old_password = str_repeat("*", 8); // Shows 8 asterisks instead of actual password
    } else {
        $old_password = "********"; // Default masked value if no session exists
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
            <!-- Content Header with logged in user details (Page header) -->
            <?php
            $staff_number = $_GET['staff_number'];
            $ret = "SELECT * FROM  iB_staff  WHERE staff_number = ? ";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param('s', $staff_number);
            $stmt->execute(); //ok
            $res = $stmt->get_result();
            while ($row = $res->fetch_object()) {
                //set automatically logged in user default image if they have not updated their pics
                if ($row->profile_pic == '') {
                    $profile_picture = "<img class='img-fluid' src='dist/img/user_icon.png' alt='User profile picture'>";
                } else {
                    $profile_picture = "<img class=' img-fluid' src='dist/img/$row->profile_pic' alt='User profile picture'>";
                }
                ?>
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1><?php echo $row->name; ?> Profile</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="pages_manage_staff.php">iBanking Staffs</a></li>
                                    <li class="breadcrumb-item"><a href="pages_manage_staff.php">Manage</a></li>
                                    <li class="breadcrumb-item active"><?php echo $row->name; ?></li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-3">
                                <!-- Profile Image -->
                                <div class="card card-purple card-outline">
                                    <div class="card-body box-profile">
                                        <div class="text-center">
                                            <?php echo $profile_picture; ?>
                                        </div>

                                        <h3 class="profile-username text-center"><?php echo $row->name; ?></h3>
                                        <p class="text-muted text-center">Staff @iBanking </p>
                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item">
                                                <b>Email: </b> <a class="float-right"><?php echo $row->email; ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Phone: </b> <a class="float-right"><?php echo $row->phone; ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>StaffNo: </b> <a
                                                    class="float-right"><?php echo $row->staff_number; ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Gender: </b> <a class="float-right"><?php echo $row->sex; ?></a>
                                            </li>
                                        </ul>
                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->
                            </div>
                            <!-- /.col -->
                            <div class="col-md-9">
                                <div class="card">
                                    <div class="card-header p-2">
                                        <ul class="nav nav-pills">
                                            <li class="nav-item"><a class="nav-link active" href="#update_Profile"
                                                    data-toggle="tab">Update Profile</a></li>
                                            <li class="nav-item"><a class="nav-link" href="#Change_Password"
                                                    data-toggle="tab">Change Password</a></li>
                                        </ul>
                                    </div><!-- /.card-header -->
                                    <div class="card-body">
                                        <div class="tab-content">
                                            <!-- / Update Profile -->
                                            <div class="tab-pane active" id="update_Profile">
                                                <form method="post" enctype="multipart/form-data" class="form-horizontal">
                                                    <div class="form-group row">
                                                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="name" required readonly
                                                                class="form-control" value="<?php echo $row->name; ?>"
                                                                id="inputName">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputEmail"
                                                            class="col-sm-2 col-form-label">Email</label>
                                                        <div class="col-sm-10">
                                                            <input type="email" name="email" required class="form-control"
                                                                id="inputEmail" value="<?php echo $row->email; ?>">
                                                            <span id="emailError" class="text-danger"></span>
                                                            <!-- Error message for email -->
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="inputPhone"
                                                            class="col-sm-2 col-form-label">Contact</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control" required name="phone"
                                                                id="inputPhone" value="<?php echo $row->phone; ?>">
                                                            <span id="phoneError" class="text-danger"></span>
                                                            <!-- Error message for phone -->
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="profilePic" class="col-sm-2 col-form-label">Profile
                                                            Picture</label>
                                                        <div class="input-group col-sm-10">
                                                            <div class="custom-file">
                                                                <input type="file" name="profile_pic" id="profilePic"
                                                                    class="form-control custom-file-input"
                                                                    onchange="validateFile()">
                                                                <label class="custom-file-label col-form-label"
                                                                    for="profilePic">Choose file</label>
                                                            </div>
                                                        </div>
                                                        <span id="profilePicError" class="text-danger"></span>
                                                        <!-- Error message for profile picture -->
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="inputName2"
                                                            class="col-sm-2 col-form-label">Gender</label>
                                                        <div class="col-sm-10">
                                                            <select readonly class="form-control" name="sex">
                                                                <option>Male</option>
                                                                <option>Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button name="update_staff_account" type="submit"
                                                                class="btn btn-outline-success">Update Account</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- /Change Password -->
                                            <div class="tab-pane" id="Change_Password">
                                                <form method="post" id="changePasswordForm" class="form-horizontal">
                                                <div class="form-group row">
    <label for="oldPassword" class="col-sm-2 col-form-label">Old Password</label>
    <div class="col-sm-10">
        <input type="password" class="form-control" id="oldPassword"
            value="<?php echo htmlspecialchars($old_password); ?>" readonly>
    </div>
</div>
                                                    <div class="form-group row">
                                                        <label for="inputEmail" class="col-sm-2 col-form-label">New
                                                            Password</label>
                                                        <div class="col-sm-10">
                                                            <input type="password" name="password" id="newPassword"
                                                                class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2" class="col-sm-2 col-form-label">Confirm New
                                                            Password</label>
                                                        <div class="col-sm-10">
                                                            <input type="password" name="confirm_password"
                                                                id="confirmPassword" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button type="submit" name="change_staff_password"
                                                                class="btn btn-outline-success">Change Password</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- /.tab-pane -->
                                        </div>
                                        <!-- /.tab-content -->
                                    </div><!-- /.card-body -->
                                </div>
                                <!-- /.nav-tabs-custom -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            <?php } ?>
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
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- Custom script for password validation -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('changePasswordForm').addEventListener('submit', function (event) {
            var newPassword = document.getElementById('newPassword').value;
            var confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                event.preventDefault();
                alert("New password and confirm password do not match");
            }
        });

    </script>
    <script>
        document.getElementById('changePasswordForm').addEventListener('submit', function (event) {
            var newPassword = document.getElementById('newPassword').value;
            var confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword !== confirmPassword) {
                event.preventDefault();
                Swal.fire("Error!", "New password and confirm password do not match", "error");
            }
        });

        document.querySelector("form").addEventListener("submit", function (event) {
            var email = document.getElementById("inputEmail").value;
            var phone = document.getElementById("inputPhone").value;
            var emailError = document.getElementById("emailError");
            var phoneError = document.getElementById("phoneError");
            var phoneRegex = /^[6789]\d{9}$/;
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            emailError.innerText = "";
            phoneError.innerText = "";

            if (!emailRegex.test(email)) {
                emailError.innerText = "Invalid email format.";
                event.preventDefault();
            }
            if (!phoneRegex.test(phone)) {
                phoneError.innerText = "Phone number must start with 6, 7, 8, or 9 and be 10 digits.";
                event.preventDefault();
            }
        });

        function validateFile() {
            var fileInput = document.getElementById('profilePic');
            var errorSpan = document.getElementById('profilePicError');
            var filePath = fileInput.value;
            var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

            if (!allowedExtensions.exec(filePath)) {
                errorSpan.innerText = "Only JPG, JPEG, and PNG formats are allowed.";
                fileInput.value = ''; // Clear the input
                return false;
            } else {
                errorSpan.innerText = ""; // Clear error if valid
            }
        }

        // Show success message
        <?php if (isset($_SESSION['success'])) { ?>
            Swal.fire({
                title: 'Success!',
                text: '<?php echo $_SESSION['success']; ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['success']); ?>
        <?php } ?>

        // Show error message
        <?php if (isset($_SESSION['error'])) { ?>
            Swal.fire({
                title: 'Error!',
                text: '<?php echo $_SESSION['error']; ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['error']); ?>
        <?php } ?>

    </script>

</body>

</html>