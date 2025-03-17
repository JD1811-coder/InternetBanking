<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$staff_id = $_SESSION['staff_id'];
if (isset($_POST['update_staff_account'])) {
    $name = $_POST['name'];
    $staff_id = $_SESSION['staff_id'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $sex = $_POST['sex'];

    if (!empty($_FILES["profile_pic"]["name"])) {
        $allowedTypes = array('jpg', 'jpeg', 'png');
        $fileExtension = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));

        if (!in_array($fileExtension, $allowedTypes)) {
            $err = "Only JPG, JPEG, and PNG files are allowed.";
        } else {
            $profile_pic = $_FILES["profile_pic"]["name"];
            move_uploaded_file($_FILES["profile_pic"]["tmp_name"], "../admin/dist/img/" . $profile_pic);

            // Update the database
            $query = "UPDATE iB_staff SET name=?, phone=?, email=?, sex=?, profile_pic=? WHERE staff_id=?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('sssssi', $name, $phone, $email, $sex, $profile_pic, $staff_id);
            $stmt->execute();

            if ($stmt) {
                $success = "Staff Account Updated";
            } else {
                $err = "Please Try Again Or Try Later";
            }
        }
    } else {
        // If no file is uploaded, update other details without changing the profile picture
        $query = "UPDATE iB_staff SET name=?, phone=?, email=?, sex=? WHERE staff_id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssssi', $name, $phone, $email, $sex, $staff_id);
        $stmt->execute();

        if ($stmt) {
            $success = "Staff Account Updated";
        } else {
            $err = "Please Try Again Or Try Later";
        }
    }
}

//change password
if (isset($_POST['change_staff_password'])) {
    $password = sha1(md5($_POST['password']));
    $staff_id = $_SESSION['staff_id'];
    //insert into certain table in database
    $query = "UPDATE iB_staff  SET password=? WHERE  staff_id=?";
    $stmt = $mysqli->prepare($query);
    //bind parameters
    $rc = $stmt->bind_param('si', $password, $staff_id);
    $stmt->execute();
    //declare a variable which will be passed to alert function
    if ($stmt) {
        $success = "Staff Password Updated";
    } else {
        $err = "Please Try Again Or Try Later";
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
            $staff_id = $_SESSION['staff_id'];
            $ret = "SELECT * FROM  iB_staff  WHERE staff_id = ? ";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param('i', $staff_id);
            $stmt->execute(); //ok
            $res = $stmt->get_result();
                while ($row = $res->fetch_object()) {
                    // Set default image if profile picture is empty
                    if (empty($row->profile_pic)) {
                        $profile_picture = "
                            <img class='img-fluid rounded-circle'
                            src='../admin/dist/img/user_icon.png'
                            alt='User profile picture'
                            style='width: 100px; height: 100px; object-fit: cover;'>
                        ";
                    } else {
                        $profile_picture = "
                            <img class='img-fluid rounded-circle'
                            src='../admin/dist/img/" . $row->profile_pic . "'
                            alt='User profile picture'
                            style='width: 100px; height: 100px; object-fit: cover;'>
                        ";
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
                                    <li class="breadcrumb-item"><a href="pages_manage.php">iBanking Staffs</a></li>
                                    <li class="breadcrumb-item"><a href="pages_manage.php">Manage</a></li>
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
                                                <b aria-readonly="">Gender: </b> <a
                                                    class="float-right"><?php echo $row->sex; ?></a>
                                            </li>

                                        </ul>

                                    </div>
                                    <!-- /.card-body -->
                                </div>
                                <!-- /.card -->

                                <!-- About Me Box 
                        <div class="card card-purple">
                        <div class="card-header">
                            <h3 class="card-title">About Me</h3>
                        </div>
                        <div class="card-body">
                            <strong><i class="fas fa-book mr-1"></i> Education</strong>

                            <p class="text-muted">
                            B.S. in Computer Science from the University of Tennessee at Knoxville
                            </p>

                            <hr>

                            <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

                            <p class="text-muted">Malibu, California</p>

                            <hr>

                            <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

                            <p class="text-muted">
                            <span class="tag tag-danger">UI Design</span>
                            <span class="tag tag-success">Coding</span>
                            <span class="tag tag-info">Javascript</span>
                            <span class="tag tag-warning">PHP</span>
                            <span class="tag tag-primary">Node.js</span>
                            </p>

                            <hr>

                            <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>

                            <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
                        </div>
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
                                                <form method="post" enctype="multipart/form-data" class="form-horizontal"
                                                    id="updateProfileForm">
                                                    <div class="form-group row">
                                                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="name" readonly required class="form-control"
                                                                value="<?php echo $row->name; ?>" id="inputName" readonlypages_manage_acc_openings
                                                                pattern="^[a-zA-Z ]{2,50}$">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputEmail"
                                                            class="col-sm-2 col-form-label">Email</label>
                                                        <div class="col-sm-10">
                                                            <input type="email" name="email"  required
                                                                value="<?php echo $row->email; ?>" class="form-control"
                                                                id="inputEmail">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2" class="col-sm-2 col-form-label">Contact
                                                            Number</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control" required name="phone"
                                                                value="<?php echo $row->phone; ?>" id="inputPhone"
                                                                pattern="^\d{10}$">
                                                            <div id="phoneError" class="text-danger"></div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2" class="col-sm-2 col-form-label">Profile
                                                            Picture</label>
                                                        <div class="input-group col-sm-10">
                                                            <div class="custom-file">
                                                                <input type="file" name="profile_pic"
                                                                    class=" form-control custom-file-input"
                                                                    id="exampleInputFile">
                                                                <label class="custom-file-label  col-form-label"
                                                                    for="exampleInputFile">Choose file</label>
                                                            </div>
                                                            <div id="fileError" class="text-danger"></div>

                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2"
                                                            class="col-sm-2 col-form-label">Gender</label>
                                                        <div class="col-sm-10">
                                                            <select class="form-control" name="sex">
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
                                                <form method="post" class="form-horizontal" id="changePasswordForm">
                                                <div class="form-group row">
    <label for="oldPassword" class="col-sm-2 col-form-label">Old Password</label>
    <div class="col-sm-10">
        <input type="password" class="form-control" id="oldPassword"
            value="<?php echo htmlspecialchars($old_password); ?>" readonly>
    </div>
</div>
                                                    <div class="form-group row">
                                                        <label for="inputNewPassword" class="col-sm-2 col-form-label">New
                                                            Password</label>
                                                        <div class="col-sm-10">
                                                            <input type="password" name="new_password" class="form-control"
                                                                required id="inputNewPassword">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputConfirmPassword"
                                                            class="col-sm-2 col-form-label">Confirm New Password</label>
                                                        <div class="col-sm-10">
                                                            <input type="password" name="confirm_password"
                                                                class="form-control" required id="inputConfirmPassword">
                                                            <div id="passwordError" class="text-danger"></div>
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
    <!-- Contact Number Validation Script -->
    <script>
        document.getElementById('updateProfileForm').addEventListener('submit', function (event) {
            var phoneInput = document.getElementById('inputPhone');
            var phoneError = document.getElementById('phoneError');
            var phoneRegex = /^\d{10}$/;

            if (!phoneRegex.test(phoneInput.value)) {
                phoneError.textContent = "Phone number should be exactly 10 digits.";
                event.preventDefault();
            } else {
                phoneError.textContent = "";
            }
        });

        document.getElementById('changePasswordForm').addEventListener('submit', function (event) {
            var newPasswordInput = document.getElementById('inputNewPassword');
            var confirmPasswordInput = document.getElementById('inputConfirmPassword');
            var passwordError = document.getElementById('passwordError');

            if (newPasswordInput.value !== confirmPasswordInput.value) {
                passwordError.textContent = "New password and confirm password do not match.";
                event.preventDefault();
            } else {
                passwordError.textContent = "";
            }
        });
        document.getElementById('updateProfileForm').addEventListener('submit', function (event) {
            var phoneInput = document.getElementById('inputPhone');
            var phoneError = document.getElementById('phoneError');
            var phoneRegex = /^\d{10}$/;
            var profilePicInput = document.getElementById('exampleInputFile');
            var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
            var fileError = document.getElementById('fileError');

            if (!phoneRegex.test(phoneInput.value)) {
                phoneError.textContent = "Phone number should be exactly 10 digits.";
                event.preventDefault();
            } else {
                phoneError.textContent = "";
            }

            if (profilePicInput.files.length > 0) {
                var fileName = profilePicInput.files[0].name;
                if (!allowedExtensions.test(fileName)) {
                    fileError.textContent = "Only JPG, JPEG, and PNG files are allowed.";
                    event.preventDefault();
                } else {
                    fileError.textContent = "";
                }
            }
        });

    </script>
</body>

</html>