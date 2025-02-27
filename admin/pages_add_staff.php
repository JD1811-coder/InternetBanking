<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

$success = $err = "";

// Register new staff
if (isset($_POST['create_staff_account'])) {
    $name = trim($_POST['name']);
    $staff_number = $_POST['staff_number'];
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = sha1(md5(trim($_POST['password'])));
    $sex = $_POST['sex'];

    // Handle profile picture upload
    $profile_pic = $_FILES["profile_pic"]["name"];
    $target_dir = "dist/img/";
    $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);
    move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file);

    // Check if the staff name already exists
    $check_query = "SELECT name FROM iB_staff WHERE name = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param('s', $name);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $err = "Staff name already exists! Please use a different name.";
    } else {
        // Proceed with insertion
        $query = "INSERT INTO iB_staff (name, staff_number, phone, email, password, sex, profile_pic) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sssssss', $name, $staff_number, $phone, $email, $password, $sex, $profile_pic);
        $stmt->execute();

        if ($stmt) {
            $success = "Staff Account Created Successfully!";
        } else {
            $err = "Error! Please Try Again Later.";
        }
        $stmt->close();
    }
    $check_stmt->close();

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
                                                <input type="text" name="name" required class="form-control" id="name">
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
                                                    class="form-control" id="phone">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Gender</label>
                                                <select class="form-control" name="sex" id="gender">
                                                    <option value="">Select Gender</option>
                                                    <option>Female</option>
                                                    <option>Male</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Email</label>
                                                <input type="email" name="email" required class="form-control"
                                                    id="email">
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Password</label>
                                                <input type="password" name="password" required class="form-control"
                                                    id="password">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Profile Picture</label>
                                            <input type="file" name="profile_pic" class="form-control-file"
                                                id="profile_pic">
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="create_staff_account" class="btn btn-success"
                                            onclick="return validateForm()">Add Staff</button>
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

    <!-- SweetAlert Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        function validateForm() {
            var name = document.getElementById("name").value.trim();
            var phone = document.getElementById("phone").value.trim();
            var email = document.getElementById("email").value.trim();
            var password = document.getElementById("password").value.trim();
            var gender = document.getElementById("gender").value;
            var profilePic = document.getElementById("profile_pic").value;

            var namePattern = /^[A-Za-z ]+$/;
            var phonePattern = /^[0-9]{10}$/;
            var emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

            if (!name.match(namePattern)) {
                Swal.fire("Error", "Name must contain only alphabets!", "error");
                return false;
            }
            if (!phone.match(phonePattern)) {
                Swal.fire("Error", "Phone number must be 10 digits!", "error");
                return false;
            }
            if (!email.match(emailPattern)) {
                Swal.fire("Error", "Enter a valid email!", "error");
                return false;
            }
            if (password.length < 6) {
                Swal.fire("Error", "Password must be at least 6 characters long!", "error");
                return false;
            }
            if (gender == "") {
                Swal.fire("Error", "Please select a gender!", "error");
                return false;
            }
            if (profilePic == "") {
                Swal.fire("Error", "Please upload a profile picture!", "error");
                return false;
            }
            return true;
        }

        <?php if (!empty($success)) { ?>
            Swal.fire("Success", "<?php echo $success; ?>", "success").then(() => {
                window.location.href = 'pages_manage_staff.php';
            });
        <?php } elseif (!empty($err)) { ?>
            Swal.fire("Error", "<?php echo $err; ?>", "error");
        <?php } ?>
        $(document).ready(function () {
            $("#name").keyup(function () {
                var name = $(this).val().trim();
                if (name.length > 0) {
                    $.ajax({
                        url: "check_staff_name.php",
                        method: "POST",
                        data: { name: name },
                        success: function (response) {
                            if (response.trim() === "exists") {
                                Swal.fire("Error", "Staff name already exists!", "error");
                                $("#name").addClass("is-invalid");
                            } else {
                                $("#name").removeClass("is-invalid");
                            }
                        }
                    });
                }
            });
        });

    </script>

</body>

</html>