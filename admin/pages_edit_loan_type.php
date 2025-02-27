<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

// Initialize variables
$loanType = null;
$message = '';

// Fetch loan type details for editing
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM loan_types WHERE id = ?";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $loanType = $result->fetch_object();
        $stmt->close();
    } else {
        $_SESSION['message'] = "Failed to fetch loan type details.";
        header("Location: pages_manage_loan_types.php");
        exit();
    }
}

// Handle form submission for updating loan type
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_loan_type'])) {
    $type_name = trim($_POST['type_name']);
    $description = trim($_POST['description']);
    $interest_rate = floatval($_POST['interest_rate']);
    $max_amount = floatval($_POST['max_amount']);

    // Update loan type details
    $updateQuery = "UPDATE loan_types SET type_name = ?, description = ?, interest_rate = ?, max_amount = ? WHERE id = ?";
    $updateStmt = $mysqli->prepare($updateQuery);

    if ($updateStmt) {
        $updateStmt->bind_param('ssdii', $type_name, $description, $interest_rate, $max_amount, $id);
        $updateStmt->execute();

        if ($updateStmt->affected_rows > 0) {
            $_SESSION['message'] = "Loan Type Updated Successfully";
            $updateStmt->close();
            header("Location: pages_manage_loan_types.php");
            exit();
        } else {
            $_SESSION['message'] = "No changes made. Please check the details and try again.";
            header("Location: pages_edit_loan_type.php?id=$id");
            exit();
        }
    } else {
        $_SESSION['message'] = "Failed to update loan type.";
        header("Location: pages_edit_loan_type.php?id=$id");
        exit();
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
                            <h1>Edit Loan Type</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Edit Loan Type Details</h3>
                            </div>
                            <div class="card-body">
                                <!-- Success/Error Message Modal -->
                                <?php if (isset($_SESSION['message'])) : ?>
                                <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    Swal.fire({
                                        icon: '<?php echo ($_SESSION['message'] === "Loan Type Updated Successfully") ? "success" : "error"; ?>',
                                        title: '<?php echo ($_SESSION['message'] === "Loan Type Updated Successfully") ? "Success" : "Error"; ?>',
                                        text: '<?php echo htmlspecialchars($_SESSION['message']); ?>'
                                    });
                                });
                                </script>
                                <?php unset($_SESSION['message']); ?>
                                <?php endif; ?>

                                <?php if ($loanType): ?>
                                <form method="POST" action="">
                                    <div class="form-group">
                                        <label for="type_name">Type Name</label>
                                        <input type="text" class="form-control" id="type_name" name="type_name"
                                            value="<?php echo htmlspecialchars($loanType->type_name); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"
                                            required><?php echo htmlspecialchars($loanType->description); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="interest_rate">Interest Rate (%)</label>
                                        <input type="number" class="form-control" id="interest_rate"
                                            name="interest_rate"
                                            value="<?php echo htmlspecialchars($loanType->interest_rate); ?>"
                                            step="0.01" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="max_amount">Max Amount</label>
                                        <input type="number" class="form-control" id="max_amount" name="max_amount"
                                            value="<?php echo htmlspecialchars($loanType->max_amount); ?>" required>
                                    </div>
                                    <button type="submit" name="update_loan_type" class="btn btn-primary">Update Loan
                                        Type</button>
                                    <a href="pages_manage_loan_types.php" class="btn btn-secondary">Cancel</a>
                                </form>
                                <?php else: ?>
                                <div class="alert alert-danger">Loan Type not found.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include("dist/_partials/footer.php"); ?>
    </div>

    <!-- Required JS Files -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</body>

</html>