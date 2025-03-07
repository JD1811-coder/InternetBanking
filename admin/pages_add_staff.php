<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

if (isset($_POST['create_staff_account'])) {
    $name = trim($_POST['name']);
    $staff_number = $_POST['staff_number'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $sex = $_POST['sex'];
    $profile_pic = $_FILES["profile_pic"]["name"];
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $file_extension = strtolower(pathinfo($profile_pic, PATHINFO_EXTENSION));

    // Validate profile picture format
    if (!in_array($file_extension, $allowed_extensions)) {
        $swal_type = "error";
        $swal_message = "Only JPG, JPEG, and PNG files are allowed for the profile picture.";
    } 
    // Validate strong password
    elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $swal_type = "error";
        $swal_message = "Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.";
    } else {
        // Encrypt password
        $hashed_password = sha1(md5($password));

        // Check for duplicate name, phone, or email
        $check_query = "SELECT name, phone, email FROM iB_staff WHERE name = ? OR phone = ? OR email = ?";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param('sss', $name, $phone, $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $check_stmt->bind_result($existing_name, $existing_phone, $existing_email);
            $check_stmt->fetch();

            if ($existing_name == $name) {
                $swal_type = "error";
                $swal_message = "Staff name already exists! Please use a different name.";
            } elseif ($existing_phone == $phone) {
                $swal_type = "error";
                $swal_message = "Phone number already exists! Please use a different number.";
            } elseif ($existing_email == $email) {
                $swal_type = "error";
                $swal_message = "Email already exists! Please use a different email.";
            }
        } else {
            // Upload profile picture
            $target_dir = "dist/img/";
            $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
            move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);

            // Insert into database
            $query = "INSERT INTO iB_staff (name, staff_number, phone, email, password, sex, profile_pic) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('sssssss', $name, $staff_number, $phone, $email, $hashed_password, $sex, $profile_pic);

            if ($stmt->execute()) {
                $swal_type = "success";
                $swal_message = "Staff Account Created Successfully!";
            } else {
                $swal_type = "error";
                $swal_message = "Error! Please Try Again Later.";
            }

            $stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<?php include("dist/_partials/head.php"); ?>

<body>
    <div class="wrapper">
        <?php include("dist/_partials/nav.php"); ?>
        <?php include("dist/_partials/sidebar.php"); ?>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Create Staff Account</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Add Staff</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-purple">
                                <div class="card-header">
                                    <h3 class="card-title">Fill All Fields</h3>
                                </div>

                                <form method="post" enctype="multipart/form-data" role="form" id="staffForm">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Staff Name</label>
                                                <input type="text" name="name" required class="form-control">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Staff Number</label>
                                                <input type="text" readonly name="staff_number"
                                                    value="iBank-STAFF-<?php echo substr(str_shuffle('0123456789'), 1, 4); ?>"
                                                    class="form-control">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Phone Number</label>
                                                <input type="text" name="phone" required pattern="[0-9]{10}"
                                                    class="form-control">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Gender</label>
                                                <select class="form-control" name="sex">
                                                    <option value="">Select Gender</option>
                                                    <option>Female</option>
                                                    <option>Male</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" required class="form-control">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Password</label>
                                                <input type="password" name="password" required class="form-control"
                                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@$!%*?&]).{8,}"
                                                    title="Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Profile Picture (JPG, JPEG, PNG)</label>
                                            <input type="file" name="profile_pic" required class="form-control-file"
                                                accept=".jpg,.jpeg,.png">
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="create_staff_account" class="btn btn-success">Add
                                            Staff</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include("dist/_partials/footer.php"); ?>
    </div>

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Show SweetAlert -->
    <?php if (isset($swal_type) && isset($swal_message)) : ?>
    <script>
        Swal.fire({
            icon: '<?php echo $swal_type; ?>',
            title: 'Message',
            text: '<?php echo $swal_message; ?>',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    </script>
    <?php endif; ?>

</body>
</html>
