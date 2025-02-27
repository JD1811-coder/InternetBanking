<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];
//update logged in user account
if (isset($_POST['update_staff_account'])) {
    //Register  Staff
    $name = $_POST['name'];
    $staff_number = $_GET['staff_number'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    //$password = sha1(md5($_POST['password']));
    $sex  = $_POST['sex'];

    $profile_pic  = $_FILES["profile_pic"]["name"];
    move_uploaded_file($_FILES["profile_pic"]["tmp_name"], "dist/img/" . $_FILES["profile_pic"]["name"]);

    //Insert Captured information to a database table
    $query = "UPDATE iB_staff SET name=?, phone=?, email=?, sex=?, profile_pic=? WHERE staff_number=?";
    $stmt = $mysqli->prepare($query);
    //bind parameters
    $rc = $stmt->bind_param('ssssss', $name, $phone, $email, $sex, $profile_pic, $staff_number);
    $stmt->execute();

    //declare a variable which will be passed to alert function
    if ($stmt) {
        $success = "Staff Account Updated";
    } else {
        $err = "Please Try Again Or Try Later";
    }
}
//change password
if (isset($_POST['change_staff_password'])) {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $staff_number = $_GET['staff_number'];
    
    // Validate new password and confirm password
    if ($new_password !== $confirm_password) {
        $err = "New password and confirm password do not match";
    } else {
        // Passwords match, proceed to update password
        $password = sha1(md5($new_password));
        //insert unto certain table in database
        $query = "UPDATE iB_staff SET password=? WHERE staff_number=?";
        $stmt = $mysqli->prepare($query);
        //bind parameters
        $rc = $stmt->bind_param('ss', $password, $staff_number);
        $stmt->execute();
        //declare a variable which will be passed to alert function
        if ($stmt) {
            $success = "Staff Password Updated";
        } else {
            $err = "Please Try Again Or Try Later";
        }
    }
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
                                                <b>StaffNo: </b> <a class="float-right"><?php echo $row->staff_number; ?></a>
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
                                            <li class="nav-item"><a class="nav-link active" href="#update_Profile" data-toggle="tab">Update Profile</a></li>
                                            <li class="nav-item"><a class="nav-link" href="#Change_Password" data-toggle="tab">Change Password</a></li>
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
                                                            <input type="text" name="name" required readonly class="form-control" value="<?php echo $row->name; ?>" id="inputName">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                                        <div class="col-sm-10">
                                                            <input type="email" name="email" required value="<?php echo $row->email; ?>" class="form-control" id="inputEmail">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2" class="col-sm-2 col-form-label">Contact</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control" required name="phone" pattern="[0-9]{10}" title="Please enter a valid 10-digit phone number" value="<?php echo $row->phone; ?>" id="inputName2">
                                                          
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2" class="col-sm-2 col-form-label">Profile Picture</label>
                                                        <div class="input-group col-sm-10">
                                                            <div class="custom-file">
                                                                <input type="file" name="profile_pic" class=" form-control custom-file-input" id="exampleInputFile">
                                                                <label class="custom-file-label  col-form-label" for="exampleInputFile">Choose file</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2" class="col-sm-2 col-form-label">Gender</label>
                                                        <div class="col-sm-10">
                                                            <select readonly class="form-control" name="sex">
                                                                <option>Male</option>
                                                                <option>Female</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button name="update_staff_account" type="submit" class="btn btn-outline-success">Update Account</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- /Change Password -->
                                            <div class="tab-pane" id="Change_Password">
                                                <form method="post" id="changePasswordForm" class="form-horizontal">
                                                    <div class="form-group row">
                                                        <label for="inputName" class="col-sm-2 col-form-label">Old Password</label>
                                                        <div class="col-sm-10">
                                                            <input type="password" class="form-control" required id="oldPassword">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputEmail" class="col-sm-2 col-form-label">New Password</label>
                                                        <div class="col-sm-10">
                                                            <input type="password" name="password" id="newPassword" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2" class="col-sm-2 col-form-label">Confirm New Password</label>
                                                        <div class="col-sm-10">
                                                            <input type="password" name="confirm_password" id="confirmPassword" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button type="submit" name="change_staff_password" class="btn btn-outline-success">Change Password</button>
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
    <script>
        document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
            var newPassword = document.getElementById('newPassword').value;
            var confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                event.preventDefault();
                alert("New password and confirm password do not match");
            }
        });
    </script>
</body>
</html>
