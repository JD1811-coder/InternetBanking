<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

$errors = [];
$success = false;

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
        $errors['profile_pic'] = "Only JPG, JPEG, and PNG files are allowed.";
    }

    // Validate strong password
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $errors['password'] = "Password must be at least 8 characters long, contain an uppercase letter, a lowercase letter, a number, and a special character.";
    }
    if (!preg_match('/^[6789]\d{9}$/', $phone)) {
        $errors['phone'] = "Phone number must start with 6, 7, 8, or 9 and be exactly 10 digits.";
    }
    // Validate email format strictly
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format. Please enter a valid email address.";
    }


    if (empty($errors)) {
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
                $errors['name'] = "Staff name already exists!";
            }
            if ($existing_phone == $phone) {
                $errors['phone'] = "Phone number already exists!";
            }
            if ($existing_email == $email) {
                $errors['email'] = "Email already exists!";
            }
            if (empty(trim($name))) {
                $errors['name'] = "Staff name cannot be empty or contain only spaces.";
            }
            if (empty(trim($phone))) {
                $errors['phone'] = "Phone number cannot be empty or contain only spaces.";
            }
            if (empty(trim($email))) {
                $errors['email'] = "Email cannot be empty or contain only spaces.";
            }
            if (empty(trim($password))) {
                $errors['password'] = "Password cannot be empty or contain only spaces.";
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
                $success = true;
            } else {
                $errors['general'] = "Error! Please try again later.";
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
                                        <?php if (isset($errors['general'])): ?>
                                            <div class="alert alert-danger"><?php echo $errors['general']; ?></div>
                                        <?php endif; ?>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Staff Name</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="<?php echo htmlspecialchars($name ?? ''); ?>">
                                                <small class="text-danger"><?php echo $errors['name'] ?? ''; ?></small>
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
                                                <input type="text" name="phone" class="form-control"
                                                    value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                                                <small class="text-danger"><?php echo $errors['phone'] ?? ''; ?></small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Gender</label>
                                                <select class="form-control" name="sex">
                                                    <option value="">Select Gender</option>
                                                    <option value="Female" <?php echo isset($sex) && $sex === 'Female' ? 'selected' : ''; ?>>Female</option>
                                                    <option value="Male" <?php echo isset($sex) && $sex === 'Male' ? 'selected' : ''; ?>>Male</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control">
                                                <small class="text-danger"><?php echo $errors['email'] ?? ''; ?></small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Password</label>
                                                <input type="password" name="password" class="form-control">
                                                <small
                                                    class="text-danger"><?php echo $errors['password'] ?? ''; ?></small>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Profile Picture (JPG, JPEG, PNG)</label>
                                            <input type="file" name="profile_pic" class="form-control-file">
                                            <small
                                                class="text-danger"><?php echo $errors['profile_pic'] ?? ''; ?></small>
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

    <?php if ($success): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Staff Account Created Successfully!',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>

</body>

</html>