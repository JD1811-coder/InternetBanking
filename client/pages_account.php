<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$client_id = $_SESSION['client_id'];

$errors = []; // Store validation errors

if (isset($_POST['update_client_account'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $aadhaar_number = trim($_POST['aadhar_number']);
    $pan_number = trim($_POST['pan_number']);

    // Profile Picture Validation
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $profile_pic = $_FILES["profile_pic"]["name"];
    $profile_pic_tmp = $_FILES["profile_pic"]["tmp_name"];
    $profile_ext = strtolower(pathinfo($profile_pic, PATHINFO_EXTENSION));

    // Validate Inputs
    if (!preg_match('/^[A-Za-z\s]{2,50}$/', $name)) {
        $errors['name'] = "Name should contain only letters and spaces (2-50 characters).";
    }
    if (!preg_match('/^[6789]\d{9}$/', $phone)) {
        $errors['phone'] = "Phone number must start with 6, 7, 8, or 9 and be exactly 10 digits.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    }
    $aadhar_number = trim($_POST['aadhar_number']);
if (!preg_match('/^(?!0{12}$)\d{12}$/', $aadhar_number)) {
    $err = "Invalid Aadhaar Number. It must be 12 digits and cannot be all zeros.";
}

    
    if (!preg_match('/^[A-Z0-9]{10}$/', $pan_number)) {
        $errors['pan_number'] = "PAN should contain exactly 10 characters (only uppercase letters and digits).";
    }
    if (!empty($profile_pic) && !in_array($profile_ext, $allowed_extensions)) {
        $errors['profile_pic'] = "Profile picture must be in JPG, JPEG, or PNG format.";
    }

    if (empty($errors)) {
        // Check for Duplicates
       // Check for duplicate entries
$check_query = "SELECT * FROM iB_staff WHERE email=? OR phone=? OR aadhar_number=? OR pan_number=?";
$stmt = $mysqli->prepare($check_query);
$stmt->bind_param('ssss', $email, $phone, $aadhar_number, $pan_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $err = "Email, Phone, aadhaar_number, or PAN Number already exists.";
} else {
    // Proceed with the update/insert logic
}
 {
            // Process Profile Picture Upload
            if (!empty($profile_pic)) {
                $profile_pic_new = time() . "_" . basename($profile_pic);
                move_uploaded_file($profile_pic_tmp, "../admin/dist/img/" . $profile_pic_new);
            } else {
                $profile_pic_new = ""; // Keep existing pic if not updated
            }

            // Update Client Information
            $query = "UPDATE iB_clients SET name=?, phone=?, email=?, profile_pic=?, aadhar_number=?, pan_number=? WHERE client_id=?";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('sssssss', $name, $phone, $email, $profile_pic_new, $aadhaar_number, $pan_number, $client_id);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Client Account Updated Successfully."; 
                header("Location: pages_account.php"); 
                exit();
                
            } else {
                $errors['database'] = "Error updating account. Please try again.";
            }
        }
    }
}

// Fetch Client Data
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
                                                                value="<?php echo htmlspecialchars($row->name ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                id="inputName">
                                                            <small
                                                                class="text-danger"><?php echo $errors['name'] ?? ''; ?></small>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="inputEmail"
                                                            class="col-sm-2 col-form-label">Email</label>
                                                        <div class="col-sm-10">
                                                            <input type="email" name="email" required class="form-control"
                                                                value="<?php echo htmlspecialchars($row->email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                id="inputEmail">
                                                            <small
                                                                class="text-danger"><?php echo $errors['email'] ?? ''; ?></small>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="inputPhone"
                                                            class="col-sm-2 col-form-label">Phone</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="phone" required class="form-control"
                                                                value="<?php echo htmlspecialchars($row->phone ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                id="inputPhone">
                                                            <small
                                                                class="text-danger"><?php echo $errors['phone'] ?? ''; ?></small>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
    <label for="inputAadhar" class="col-sm-2 col-form-label">Aadhaar Number</label>
    <div class="col-sm-10">
        <input type="text" name="aadhar" required class="form-control" id="inputAadhar" pattern="^(?!0{12}$)\d{12}$">
        <div id="aadharError" class="text-danger"></div>
    </div>
</div>


                                                    <div class="form-group row">
                                                        <label for="pan_number" class="col-sm-2 col-form-label">PAN
                                                            Number</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="pan_number" required
                                                                class="form-control"
                                                                value="<?php echo htmlspecialchars($row->pan_number ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                id="pan_number">
                                                            <small
                                                                class="text-danger"><?php echo $errors['pan_number'] ?? ''; ?></small>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="profile_pic" class="col-sm-2 col-form-label">Profile
                                                            Picture</label>
                                                        <div class="input-group col-sm-10">
                                                            <div class="custom-file">
                                                                <input type="file" name="profile_pic"
                                                                    class="form-control custom-file-input" id="profile_pic">
                                                                <label class="custom-file-label" for="profile_pic">Choose
                                                                    file</label>
                                                            </div>
                                                        </div>
                                                        <small
                                                            class="text-danger"><?php echo $errors['profile_pic'] ?? ''; ?></small>
                                                    </div>

                                                    <!-- General error message -->
                                                    <?php if (!empty($errors['general'])): ?>
                                                        <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                                                    <?php endif; ?>

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
    <!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    <?php if (isset($_SESSION['success'])) { ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: "<?php echo $_SESSION['success']; ?>",
            showConfirmButton: false,
            timer: 3000
        });
        <?php unset($_SESSION['success']); ?> // Clear the session message after showing the alert
    <?php } ?>
</script>
<script>
    $(document).ready(function() {
        $('#inputAadhar, #inputEmail, #inputPhone').on('blur', function() {
            var fieldName = $(this).attr('name');
            var fieldValue = $(this).val().trim();

            if (fieldValue) {  // Check if field is not empty before AJAX call
                $.ajax({
                    url: 'check_duplicates.php',
                    method: 'POST',
                    data: { fieldName: fieldName, fieldValue: fieldValue },
                    success: function(response) {
                        if (response === 'duplicate') {
                            $('#' + fieldName + 'Error').text(fieldName.charAt(0).toUpperCase() + fieldName.slice(1) + " already exists.");
                        } else {
                            $('#' + fieldName + 'Error').text('');
                        }
                    }
                });
            } else {
                $('#' + fieldName + 'Error').text('');
            }
        });
    });
    </script>
    <script>
    if ('<?php echo $success ?? ''; ?>') {
    Swal.fire({
        icon: 'success',
        title: 'Success',
        text: '<?php echo $success; ?>'
    });
}

if ('<?php echo $err ?? ''; ?>') {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo $err; ?>'
    });
}
</script>
</body>

</html>
