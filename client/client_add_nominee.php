<?php
session_start();
include('conf/config.php');

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_nominee'])) {
    $client_id = $_SESSION['client_id'];
    $nominee_name = trim($_POST['nominee_name']);
    $relation = trim($_POST['relation']);
    $nominee_email = trim($_POST['nominee_email']);
    $nominee_phone = trim($_POST['nominee_phone']);
    $nominee_address = trim($_POST['nominee_address']);

    // Validation checks
    if (!preg_match("/^[a-zA-Z ]+$/", $nominee_name)) {
        $err = "Nominee name should contain only letters and spaces.";
    } elseif (!preg_match("/^[a-zA-Z ]+$/", $relation)) {
        $err = "Relation should only contain letters and spaces.";
    } elseif (!filter_var($nominee_email, FILTER_VALIDATE_EMAIL)) {
        $err = "Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10}$/", $nominee_phone)) {
        $err = "Phone number must be exactly 10 digits.";
    } else {
        // Insert into database
        $query = "INSERT INTO iB_nominees (client_id, nominee_name, relation, nominee_email, nominee_phone, nominee_address)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('isssss', $client_id, $nominee_name, $relation, $nominee_email, $nominee_phone, $nominee_address);

        if ($stmt->execute()) {
            $success = "Nominee added successfully!";
        } else {
            $err = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<?php include("dist/_partials/head.php"); ?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include("dist/_partials/nav.php"); ?>
        <!-- Sidebar -->
        <?php include("dist/_partials/sidebar.php"); ?>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Add Nominee</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="client_nominees.php">Nominees</a></li>
                                <li class="breadcrumb-item active">Add Nominee</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-purple">
                                <div class="card-header">
                                    <h3 class="card-title">Fill Nominee Details</h3>
                                </div>
                                <form method="post" onsubmit="return validateForm()">
                                    <div class="card-body">
                                        <?php if (isset($err)) echo "<div class='alert alert-danger'>$err</div>"; ?>
                                        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Nominee Name</label>
                                                <input type="text" name="nominee_name" id="nominee_name" class="form-control" required>
                                                <small class="text-danger" id="nameError"></small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Relation</label>
                                                <input type="text" name="relation" id="relation" class="form-control" required>
                                                <small class="text-danger" id="relationError"></small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Nominee Email</label>
                                                <input type="email" name="nominee_email" id="nominee_email" class="form-control">
                                                <small class="text-danger" id="emailError"></small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Nominee Phone</label>
                                                <input type="text" name="nominee_phone" id="nominee_phone" class="form-control" pattern="[0-9]{10}" required>
                                                <small class="text-danger" id="phoneError"></small>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Nominee Address</label>
                                            <textarea name="nominee_address" class="form-control" required></textarea>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="add_nominee" class="btn btn-success">Add Nominee</button>
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

    <!-- Scripts -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>

    <!-- Client-side validation -->
    <script>
        function validateForm() {
            let isValid = true;

            // Name validation
            let name = document.getElementById("nominee_name").value;
            let nameRegex = /^[a-zA-Z ]+$/;
            if (!nameRegex.test(name)) {
                document.getElementById("nameError").innerText = "Nominee name should contain only letters and spaces.";
                isValid = false;
            } else {
                document.getElementById("nameError").innerText = "";
            }

            // Relation validation
            let relation = document.getElementById("relation").value;
            if (!nameRegex.test(relation)) {
                document.getElementById("relationError").innerText = "Relation should only contain letters and spaces.";
                isValid = false;
            } else {
                document.getElementById("relationError").innerText = "";
            }

            // Email validation
            let email = document.getElementById("nominee_email").value;
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email !== "" && !emailRegex.test(email)) {
                document.getElementById("emailError").innerText = "Invalid email format.";
                isValid = false;
            } else {
                document.getElementById("emailError").innerText = "";
            }

            // Phone number validation
            let phone = document.getElementById("nominee_phone").value;
            let phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(phone)) {
                document.getElementById("phoneError").innerText = "Phone number must be exactly 10 digits.";
                isValid = false;
            } else {
                document.getElementById("phoneError").innerText = "";
            }

            return isValid;
        }
    </script>
</body>
</html>
