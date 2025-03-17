    <?php
    session_start();
    include('conf/config.php');
    include('conf/checklogin.php');
    check_login();
    $admin_id = $_SESSION['admin_id'];
    //update logged in user account
    if (isset($_POST['update_account'])) {
        $name = trim($_POST['name']); // Trim spaces
        $email = $_POST['email'];
        $admin_id = $_SESSION['admin_id'];

        // Validate name: must not be empty and should only contain letters and spaces
        if (empty($name)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error!',
                    text: 'Name cannot be empty.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            </script>";
        } elseif (!preg_match("/^[A-Za-z ]+$/", $name)) {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error!',
                    text: 'Name should only contain letters and spaces.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
            </script>";
        } else {
            // Proceed with updating the account
            $query = "UPDATE iB_admin SET name=?, email=? WHERE admin_id=?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('ssi', $name, $email, $admin_id);
            if ($stmt->execute()) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Account Updated Successfully',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'pages_account.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Please Try Again Later',
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    });
                </script>";
            }
        }
    }


    //change password
    if (isset($_POST['change_password'])) {
        $new_password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        if ($new_password != $confirm_password) {
            $err = "New password and confirm password do not match.";
        } else {
            $password = sha1(md5($new_password));
            $admin_id = $_SESSION['admin_id'];
            //insert unto certain table in database
            $query = "UPDATE iB_admin  SET password=? WHERE  admin_id=?";
            $stmt = $mysqli->prepare($query);
            //bind parameters
            $rc = $stmt->bind_param('si', $password, $admin_id);
            $stmt->execute();
            //declare a variable which will be passed to alert function
            if ($stmt) {
                $success = "Password Updated";
            } else {
                $err = "Please Try Again Or Try Later";
            }
        }
    }

    $old_password = "";


    if (isset($_SESSION['admin_id'])) {
        $admin_id = $_SESSION['admin_id'];
    
        // Fetch old password from database
        $stmt = $mysqli->prepare("SELECT password FROM iB_admin WHERE admin_id = ?");
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
                $admin_id = $_SESSION['admin_id'];
                $ret = "SELECT * FROM  iB_admin  WHERE admin_id = ? ";
                $stmt = $mysqli->prepare($ret);
                $stmt->bind_param('i', $admin_id);
                $stmt->execute(); //ok
                $res = $stmt->get_result();
                while ($row = $res->fetch_object()) {
                    //set automatically logged in user default image if they have not updated their pics
                    if ($row->profile_pic == '') {
                        $profile_picture = "
                            <img class='img-fluid'
                            src='dist/img/user_icon.png'
                            alt='User profile picture'>
                            ";
                    } else {
                        $profile_picture = "
                            <img class=' img-fluid'
                            src='dist/img/$row->profile_pic'
                            alt='User profile picture'>
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
                                        <li class="breadcrumb-item"><a href="pages_account.php">Profile</a></li>
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
                                            <p class="text-muted text-center">@Admin iBanking </p>
                                            <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item">
    <b>Email: </b> 
    <a class="float-right">
        <div style="word-break: break-all; overflow-wrap: break-word; max-width: 100%;">
            <?php echo htmlspecialchars($row->email); ?>
        </div>
    </a>
</li>


                                                <li class="list-group-item">
                                                    <b>Number: </b> <a class="float-right"><?php echo $row->number; ?></a>
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
                                        </div>
                                        <!-- /.card-header -->
                                        <div class="card-body">
                                            <div class="tab-content">
                                                <!-- / Update Profile -->
                                                <div class="tab-pane active" id="update_Profile">
                                                    <form method="post" class="form-horizontal">
                                                        <div class="form-group row">
                                                            <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                                            <div class="col-sm-10">
                                                                <input type="text" name="name" required class="form-control"
                                                                    value="<?php echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>"
                                                                    id="inputName" pattern="^[A-Za-z\s]{2,50}$"
                                                                    title="Name should contain only letters and spaces (2-50 characters)"
                                                                    oninput="this.setCustomValidity('')"
                                                                    oninvalid="this.setCustomValidity('Please enter a valid name (only letters and spaces, 2-50 characters)')">
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="inputEmail"
                                                                class="col-sm-2 col-form-label">Email</label>
                                                            <div class="col-sm-10">
                                                                <input type="email" name="email" required
                                                                    value="<?php echo $row->email; ?>" class="form-control"
                                                                    id="inputEmail" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputName2"
                                                                class="col-sm-2 col-form-label">Number</label>
                                                            <div class="col-sm-10">
                                                                <input type="text" class="form-control" required readonly
                                                                    name="number" value="<?php echo $row->number; ?>"
                                                                    id="inputName2">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="offset-sm-2 col-sm-10">
                                                                <button name="update_account" type="submit"
                                                                    class="btn btn-outline-success">Update Account</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>

                                                <!-- /Change Password -->
                                                <div class="tab-pane" id="Change_Password">
                                                    <form method="post" class="form-horizontal">
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
                                                                <input type="password" name="password" class="form-control"
                                                                    required id="inputEmail">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="inputName2" class="col-sm-2 col-form-label">Confirm New
                                                                Password</label>
                                                            <div class="col-sm-10">
                                                                <input type="password" name="confirm_password"
                                                                    class="form-control" required id="inputName2">
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <div class="offset-sm-2 col-sm-10">
                                                                <button type="submit" name="change_password"
                                                                    class="btn btn-outline-success">Change Password</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- /.tab-pane -->
                                            </div>
                                            <!-- /.tab-content -->
                                        </div>
                                        <!-- /.card-body -->
                                    </div>
                                    <!-- /.card -->
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.container-fluid -->
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
        <script></script>
    </body>

    </html>

