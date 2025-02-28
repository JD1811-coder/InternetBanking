<?php
session_start();
include('conf/config.php');

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_nominee'])) {
    $client_id = $_SESSION['client_id'];

    // Check if client already has 2 nominees
    $countQuery = "SELECT COUNT(*) AS nominee_count FROM iB_nominees WHERE client_id = ?";
    $stmt = $mysqli->prepare($countQuery);
    $stmt->bind_param('i', $client_id);
    $stmt->execute();
    $stmt->bind_result($nominee_count);
    $stmt->fetch();
    $stmt->close();

    if ($nominee_count >= 2) {
        $err = "You can only add up to 2 nominees.";
    } else {
        // Proceed with inserting new nominee
        $nominee_name = trim($_POST['nominee_name']);
        $relation = trim($_POST['relation']);
        $nominee_email = trim($_POST['nominee_email']);
        $nominee_phone = trim($_POST['nominee_phone']);
        $nominee_address = trim($_POST['nominee_address']);
        $aadhar_number = trim($_POST['aadhar_number']);
        $pan_number = trim($_POST['pan_number']);

        // Validation checks (Server-side)
        if (!preg_match("/^[a-zA-Z ]+$/", $nominee_name)) {
            $err = "Nominee name should contain only letters and spaces.";
        } elseif (!preg_match("/^[a-zA-Z ]+$/", $relation)) {
            $err = "Relation should only contain letters and spaces.";
        } elseif (!filter_var($nominee_email, FILTER_VALIDATE_EMAIL)) {
            $err = "Invalid email format.";
        } elseif (!preg_match("/^[0-9]{10}$/", $nominee_phone)) {
            $err = "Phone number must be exactly 10 digits.";
        } elseif (!preg_match("/^[0-9]{12}$/", $aadhar_number)) {
            $err = "Aadhar number must be exactly 12 digits.";
        } elseif (!preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/", $pan_number)) {
            $err = "Invalid PAN number format.";
        } else {
            // Insert into database
            $query = "INSERT INTO iB_nominees (client_id, nominee_name, relation, nominee_email, nominee_phone, nominee_address, aadhar_number, pan_number) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('isssssss', $client_id, $nominee_name, $relation, $nominee_email, $nominee_phone, $nominee_address, $aadhar_number, $pan_number);

            if ($stmt->execute()) {
                $success = "Nominee added successfully!";
            } else {
                $err = "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html>
<?php include("dist/_partials/head.php"); ?>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
    <div class="wrapper">
        <?php include("dist/_partials/nav.php"); ?>
        <?php include("dist/_partials/sidebar.php"); ?>

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
                                        <?php if (isset($err))
                                            echo "<div class='alert alert-danger'>$err</div>"; ?>
                                        <?php if (isset($success))
                                            echo "<div class='alert alert-success'>$success</div>"; ?>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Nominee Name</label>
                                                <input type="text" name="nominee_name" id="nominee_name"
                                                    class="form-control" required>
                                                <small class="text-danger" id="nameError"></small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Relation</label>
                                                <input type="text" name="relation" id="relation" class="form-control"
                                                    required>
                                                <small class="text-danger" id="relationError"></small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Nominee Email</label>
                                                <input type="email" name="nominee_email" id="nominee_email"
                                                    class="form-control">
                                                <small class="text-danger" id="emailError"></small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Nominee Phone</label>
                                                <input type="text" name="nominee_phone" id="nominee_phone"
                                                    class="form-control">
                                                <small class="text-danger" id="phoneError"></small>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Nominee Address</label>
                                            <textarea name="nominee_address" class="form-control" required></textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Aadhar Card Number</label>
                                                <input type="text" name="aadhar_number" id="aadhar_number"
                                                    class="form-control">
                                                <small class="text-danger" id="aadharError"></small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>PAN Card Number</label>
                                                <input type="text" name="pan_number" id="pan_number"
                                                    class="form-control">
                                                <small class="text-danger" id="panError"></small>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="add_nominee" class="btn btn-success">Add
                                            Nominee</button>
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
    <script>
        function validateForm() {
            let isValid = true;

            function validateField(id, regex, errorMsg) {
                let field = document.getElementById(id).value;
                if (!regex.test(field)) {
                    document.getElementById(id + "Error").innerText = errorMsg;
                    isValid = false;
                } else {
                    document.getElementById(id + "Error").innerText = "";
                }
            }

            validateField("nominee_name", /^[a-zA-Z ]+$/, "Nominee name should contain only letters and spaces.");
            validateField("relation", /^[a-zA-Z ]+$/, "Relation should only contain letters and spaces.");
            validateField("nominee_phone", /^[0-9]{10}$/, "Phone number must be exactly 10 digits.");
            validateField("aadhar_number", /^[0-9]{12}$/, "Aadhar number must be exactly 12 digits.");
            validateField("pan_number", /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/, "Invalid PAN number format.");

            return isValid;
        }
        document.addEventListener("DOMContentLoaded", function () {
            fetch("check_nominee_limit.php")
                .then(response => response.json())
                .then(data => {
                    if (data.nominee_count >= 2) {
                        document.querySelector("form").style.display = "none";
                        let message = document.createElement("div");
                        message.className = "alert alert-warning";
                        message.innerText = "You can only add up to 2 nominees.";
                        document.querySelector(".content").prepend(message);
                    }
                });
        });


    </script>
</body>

</html>