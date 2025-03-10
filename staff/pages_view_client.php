<?php

use const Dom\NO_MODIFICATION_ALLOWED_ERR;
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

if (isset($_POST['update_client_account'])) {
    $name = trim($_POST['name']);
    $client_number = $_GET['client_number'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $aadhar_number = $_POST['aadhar_number'];
    $pan_number = $_POST['pan_number'];

    // Aadhaar Number Validation
    if (!preg_match('/^[1-9][0-9]{11}$/', $aadhar_number)) {
        $_SESSION['swal_message'] = ['error', 'Aadhaar number must be exactly 12 digits and cannot start with zero!'];
        header("Location: " . $_SERVER['PHP_SELF'] . "?client_number=$client_number");
        exit();
    }

    // Duplicate Aadhaar Check
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM iB_clients WHERE aadhar_number = ? AND client_number != ?");
    $stmt->bind_param('ss', $aadhar_number, $client_number);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $_SESSION['swal_message'] = ['error', 'Aadhaar number already exists!'];
        header("Location: " . $_SERVER['PHP_SELF'] . "?client_number=$client_number");
        exit();
    }

    // Duplicate Checks for PAN, Phone, and Email
    $fields = [
        'pan_number' => 'PAN number already exists!',
        'phone' => 'Contact number already exists!',
        'email' => 'Email already exists!'
    ];

    foreach ($fields as $field => $error_message) {
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM iB_clients WHERE $field = ? AND client_number != ?");
        $stmt->bind_param('ss', $$field, $client_number);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $_SESSION['swal_message'] = ['error', $error_message];
            header("Location: " . $_SERVER['PHP_SELF'] . "?client_number=$client_number");
            exit();
        }
    }

    // Profile Picture Validation
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $profile_pic = $_FILES["profile_pic"]["name"];
    $profile_pic_tmp = $_FILES["profile_pic"]["tmp_name"];
    $profile_pic_ext = strtolower(pathinfo($profile_pic, PATHINFO_EXTENSION));

    if (empty($name) || empty($phone) || empty($email) || empty($address)) {
        $_SESSION['swal_message'] = ['error', 'All fields are required!'];
    } elseif (empty($profile_pic)) {
        $_SESSION['swal_message'] = ['error', 'Profile picture is required!'];
    } elseif (!in_array($profile_pic_ext, $allowed_extensions)) {
        $_SESSION['swal_message'] = ['error', 'Only JPG, JPEG, and PNG files are allowed!'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['swal_message'] = ['error', 'Invalid email format!'];
    } elseif (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
        $_SESSION['swal_message'] = ['error', 'Phone number must start with 6-9 and be exactly 10 digits!'];
    } elseif (!preg_match('/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/', $pan_number)) {
        $_SESSION['swal_message'] = ['error', 'Invalid PAN number format!'];
    } else {
        move_uploaded_file($profile_pic_tmp, "dist/img/" . $profile_pic);

        $stmt = $mysqli->prepare("UPDATE iB_clients SET name=?, phone=?, email=?, address=?, aadhar_number=?, pan_number=?, profile_pic=? WHERE client_number = ?");
        $stmt->bind_param('ssssssss', $name, $phone, $email, $address, $aadhar_number, $pan_number, $profile_pic, $client_number);
        $stmt->execute();

       if (!$stmt->execute()) {
    $_SESSION['swal_message'] = ['error', 'Database update failed: ' . $stmt->error];
} else {
    $_SESSION['swal_message'] = ['success', 'Client details updated successfully!'];
}

    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?client_number=$client_number");
    exit();
}

// Change Password Section
if (isset($_POST['change_client_password'])) {
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($new_password) || empty($confirm_password)) {
        $_SESSION['swal_message'] = ['error', 'All fields are required!'];
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['swal_message'] = ['error', 'Passwords do not match!'];
    } elseif (strlen($new_password) < 6) {
        $_SESSION['swal_message'] = ['error', 'Password must be at least 6 characters!'];
    } else {
        $password = sha1(md5($new_password));
        $stmt = $mysqli->prepare("UPDATE iB_clients SET password=? WHERE client_number=?");
        $stmt->bind_param('ss', $password, $client_number);
        $stmt->execute();

        if ($stmt) {
            $_SESSION['swal_message'] = ['success', 'Password Updated!'];
        } else {
            $_SESSION['swal_message'] = ['error', 'Please try again later!'];
        }
        $stmt->close();
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "?client_number=$client_number");
    exit();
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
            $client_number = $_GET['client_number'];
            $ret = "SELECT * FROM  iB_clients  WHERE client_number = ? ";
            $stmt = $mysqli->prepare($ret);
            $stmt->bind_param('s', $client_number);
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
                                    <li class="breadcrumb-item"><a href="pages_manage_clients.php">iBanking Clients</a></li>
                                    <li class="breadcrumb-item"><a href="pages_manage_clients.php">Manage</a></li>
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
                                            <li class="list-group-item">
                                                <b>Address: </b> <a class="float-right"><?php echo $row->address; ?></a>
                                            </li>
                                        </ul>
                                    </div><!-- /.card-body -->
                                </div><!-- /.card -->
                            </div><!-- /.col -->
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
                                                                value="<?php echo $row->phone; ?>" id="inputName2"
                                                                pattern="[0-9]{10}"
                                                                title="Please enter a 10-digit phone number">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="inputName2"
                                                            class="col-sm-2 col-form-label">Address</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" class="form-control" required name="address"
                                                                value="<?php echo $row->address; ?>" id="inputName2">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">Aadhaar Number</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="aadhar_number" class="form-control"
                                                                value="<?php echo $row->aadhar_number; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-2 col-form-label">PAN Number</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="pan_number" class="form-control"
                                                                value="<?php echo $row->pan_number; ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputName2" class="col-sm-2 col-form-label">Profile
                                                            Picture</label>
                                                        <div class="input-group col-sm-10">
                                                            <div class="custom-file">
                                                                <input type="file" name="profile_pic"
                                                                    class="form-control custom-file-input"
                                                                    id="exampleInputFile">
                                                                <label class="custom-file-label col-form-label"
                                                                    for="exampleInputFile" ><?php echo !empty($row->profile_pic) ? basename($row->profile_pic) : "Choose file"; ?></label>
                                                            </div>
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
                                                            <button type="submit" name="change_client_password"
                                                                class="btn btn-outline-success">Change Password</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <!-- /.tab-pane -->
                                        </div>
                                        <!-- /.tab-content -->
                                    </div><!-- /.card-body -->
                                </div><!-- /.card -->
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.container-fluid -->
                </section><!-- /.content -->
            <?php } ?>
            <?php
          if (isset($_SESSION['swal_message'])) {
              list($type, $message) = $_SESSION['swal_message'];
              echo "<script>
                  document.addEventListener('DOMContentLoaded', function() {
                      Swal.fire({
                          icon: '$type',
                          title: '$message',
                          showConfirmButton: false,
                          timer: 2000
                      });
                  });
              </script>";
              unset($_SESSION['swal_message']);
          }
          ?>
        </div><!-- /.content-wrapper -->
        <?php include("dist/_partials/footer.php"); ?>
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside><!-- /.control-sidebar -->
    </div><!-- ./wrapper -->
    <!-- jQuery -->
     
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
            document.querySelector("form").addEventListener("submit", function (e) {
                let aadhar = document.querySelector("input[name='aadhar_number']").value;
                if (/^0{12}$/.test(aadhar)) {
                    Swal.fire("Error", "Aadhaar number cannot be all zeros!", "error");
                    e.preventDefault();
                }
            });
        });
</script>

</body>

</html>