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
        $_SESSION['nominee_limit_error'] = "You can only add up to 2 nominees.";
        // header("Location: client_nominees.php");
        // exit();
    } else {
        // Trim inputs
        $nominee_name = trim($_POST['nominee_name']);
        $relation = trim($_POST['relation']);
        $nominee_email = trim($_POST['nominee_email']);
        $nominee_phone = trim($_POST['nominee_phone']);
        $nominee_address = trim($_POST['nominee_address']);
        $aadhar_number = trim($_POST['aadhar_number']);
        $pan_number = trim($_POST['pan_number']);
        
        $errors = [];
        // Validation
        if (!preg_match("/^[a-zA-Z ]+$/", $nominee_name)) {
            $errors['nominee_name'] = "Only letters and spaces allowed.";
        }
        if (!preg_match("/^[a-zA-Z ]+$/", $relation)) {
            $errors['relation'] = "Only letters and spaces allowed.";
        }
        if (!filter_var($nominee_email, FILTER_VALIDATE_EMAIL)) {
            $errors['nominee_email'] = "Invalid email format.";
        }
        if (!preg_match("/^[0-9]{12}$/", $aadhar_number)) {
            $errors['aadhar_number'] = "Must be exactly 12 digits.";
        }
        if (!preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/", $pan_number)) {
            $errors['pan_number'] = "Invalid PAN number format.";
        }
        if (!preg_match("/^[6789]\d{9}$/", $nominee_phone)) {
            $errors['nominee_phone'] = "Phone number must start with 6, 7, 8, or 9 and be exactly 10 digits.";
        }

        // Check for duplicates
        $duplicateQuery = "SELECT COUNT(*) FROM iB_nominees WHERE (nominee_name = ? OR nominee_email = ? OR nominee_phone = ? OR aadhar_number = ? OR pan_number = ?) AND client_id = ?";
        $stmt = $mysqli->prepare($duplicateQuery);
        $stmt->bind_param('sssssi', $nominee_name, $nominee_email, $nominee_phone, $aadhar_number, $pan_number, $client_id);
        $stmt->execute();
        $stmt->bind_result($duplicateCount);
        $stmt->fetch();
        $stmt->close();

        if ($duplicateCount > 0) {
            $errors['duplicate'] = "Nominee with the same details already exists.";
        }

        // If no errors, insert nominee
        if (empty($errors)) {
            $query = "INSERT INTO iB_nominees (client_id, nominee_name, relation, nominee_email, nominee_phone, nominee_address, aadhar_number, pan_number) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param('isssssss', $client_id, $nominee_name, $relation, $nominee_email, $nominee_phone, $nominee_address, $aadhar_number, $pan_number);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Nominee added successfully!";
                header("Location: client_nominees.php");
                exit();
            } else {
                $_SESSION['error'] = "Something went wrong. Please try again.";
                header("Location: client_nominees.php");
                exit();
            }
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
                                <form method="post">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Nominee Name</label>
                                                <input type="text" name="nominee_name" class="form-control" required>
                                                <small class="text-danger"><?php echo $errors['nominee_name'] ?? ''; ?></small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Relation</label>
                                                <input type="text" name="relation" class="form-control" required>
                                                <small class="text-danger"><?php echo $errors['relation'] ?? ''; ?></small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Nominee Email</label>
                                                <input type="email" name="nominee_email" class="form-control">
                                                <small class="text-danger"><?php echo $errors['nominee_email'] ?? ''; ?></small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>Nominee Phone</label>
                                                <input type="text" name="nominee_phone" class="form-control">
                                                <small class="text-danger"><?php echo $errors['nominee_phone'] ?? ''; ?></small>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Nominee Address</label>
                                            <textarea name="nominee_address" class="form-control" required></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>Aadhar Card Number</label>
                                                <input type="text" name="aadhar_number" class="form-control">
                                                <small class="text-danger"><?php echo $errors['aadhar_number'] ?? ''; ?></small>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>PAN Card Number</label>
                                                <input type="text" name="pan_number" class="form-control">
                                                <small class="text-danger"><?php echo $errors['pan_number'] ?? ''; ?></small>
                                            </div>
                                        </div>

                                        <small class="text-danger"><?php echo $errors['duplicate'] ?? ''; ?></small>

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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php if (isset($_SESSION['nominee_limit_error'])) { ?>
        <script>
            Swal.fire({ icon: 'error', title: 'Limit Exceeded', text: '<?php echo $_SESSION['nominee_limit_error']; ?>' });
        </script>
    <?php unset($_SESSION['nominee_limit_error']); } ?>

</body>
</html>
