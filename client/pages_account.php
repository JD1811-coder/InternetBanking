<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$client_id = $_SESSION['client_id'];

// Update logged-in user account
if (isset($_POST['update_client_account'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $aadhaar = $_POST['aadhar_number'];
    $pan = $_POST['pan_number'];

    // Validate inputs
    if (
        strlen($phone) === 10 && ctype_digit($phone) &&
        strlen($aadhaar) === 12 && ctype_digit($aadhaar) &&
        preg_match('/^[A-Z0-9]{10}$/', $pan)
    ) {
        // Process profile picture upload
        $profile_pic = $_FILES["profile_pic"]["name"];
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], "../admin/dist/img/" . $_FILES["profile_pic"]["name"]);

        // Update client information in the database
        $query = "UPDATE iB_clients SET name=?, phone=?, email=?, profile_pic=?, aadhar_number=?, pan_number=? WHERE client_id=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sssssss', $name, $phone, $email, $profile_pic, $aadhaar, $pan, $client_id);
        $stmt->execute();

        if ($stmt) {
            $success = "Client Account Updated";
        } else {
            $err = "Please Try Again Or Try Later";
        }
    } else {
        $err = "Invalid input. Ensure phone is 10 digits, Aadhaar is 12 digits, and PAN follows format ABCDE1234F.";
    }
}

$ret = "SELECT name, phone, email, profile_pic, aadhar_number, pan_number FROM iB_clients WHERE client_id = ?";
$stmt = $mysqli->prepare($ret);
$stmt->bind_param('s', $client_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_object();

?>

<!-- Log on to codeastro.com for more projects! -->
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
            $staff_id = $_SESSION['client_id'];
            $ret = "SELECT * FROM  iB_clients  WHERE client_id = ? ";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param('s', $client_id);
            $stmt->execute(); //ok
            $res = $stmt->get_result();
            while ($row = $res->fetch_object()) {
                //set automatically logged in user default image if they have not updated their pics
                if ($row->profile_pic == '') {
                    $profile_picture = "

                        <img class='img-fluid'
                        src='../admin/dist/img/user_icon.png'
                        alt='User profile picture'>

                        ";
                } else {
                    $profile_picture = "

                        <img class=' img-fluid'
                        src='../admin/dist/img/$row->profile_pic'
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
                                    <li class="breadcrumb-item"><a href="pages_manage.php">iBanking Clients</a></li>
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

                                        <p class="text-muted text-center">Client @iBanking </p>

                                        <ul class="list-group list-group-unbordered mb-3">
                                            <li class="list-group-item">
                                                <b>Email: </b> <a class="float-right"><?php echo $row->email; ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>Phone: </b> <a class="float-right"><?php echo $row->phone; ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <b>ClientNo: </b> <a
                                                    class="float-right"><?php echo $row->client_number; ?></a>
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
                                                            <input type="text" name="name" required class="form-control"
                                                                value="<?php echo htmlspecialchars($row->name, ENT_QUOTES, 'UTF-8'); ?>"
                                                                id="inputName" pattern="[A-Za-z\s]{2,50}"
                                                                title="Name should only contain letters and spaces (2-50 characters).">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="inputEmail"
                                                            class="col-sm-2 col-form-label">Email</label>
                                                        <div class="col-sm-10">
                                                            <input type="email" name="email" required
                                                                value="<?php echo $row->email; ?>" class="form-control"
                                                                id="inputEmail">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2"
                                                            class="col-sm-2 col-form-label">Contact</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control" required name="phone"
                                                                value="<?php echo $row->phone; ?>" id="inputName2">
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
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="aadhaar" class="col-sm-2 col-form-label">Aadhaar
                                                            Card</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="aadhar_number" required
                                                                class="form-control"
                                                                value="<?php echo isset($row->aadhar_number) ? htmlspecialchars($row->aadhar_number, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                                                id="aadhar_number" pattern="\d{12}"
                                                                title="Aadhaar number should be exactly 12 digits.">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="pan" class="col-sm-2 col-form-label">PAN Card</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="pan_number" required
                                                                class="form-control"
                                                                value="<?php echo isset($row->pan_number) ? htmlspecialchars($row->pan_number, ENT_QUOTES, 'UTF-8') : ''; ?>"
                                                                id="pan_number" pattern="[A-Z0-9]{10}"
                                                                title="PAN should contain exactly 10 characters (only uppercase letters and digits).">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <button name="update_client_account" type="submit"
                                                                class="btn btn-outline-success">Update Account</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>

                                            <!-- /Change Password -->
                                            <div class="tab-pane" id="Change_Password">
                                                <form method="post" class="form-horizontal">
                                                    <div class="form-group row">
                                                        <label for="inputName" class="col-sm-2 col-form-label">Old
                                                            Password</label>
                                                        <div class="col-sm-10">
                                                            <input type="password" class="form-control" required
                                                                id="inputName">
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
                                                            <button type="submit" name="change_$client_password"
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
</body>

</html>