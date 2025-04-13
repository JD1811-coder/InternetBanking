<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

// Handle notifications
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear message after displaying
} else {
    $message = '';
}

// Delete Loan Type
if (isset($_GET['deleteLoanType'])) {
    $id = intval($_GET['deleteLoanType']);
    $query = "DELETE FROM loan_types WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['message'] = "Loan Type Deleted Successfully";
    header("Location: pages_manage_loan_types.php");
    exit();
}

// Disable Loan Type
if (isset($_GET['disableLoanType'])) {
    $id = intval($_GET['disableLoanType']);
    $query = "UPDATE loan_types SET is_active = 0 WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $_SESSION['message'] = ($stmt->affected_rows > 0) ? "Loan Type Disabled Successfully" : "No changes made.";
    $stmt->close();
    
    header("Location: pages_manage_loan_types.php");
    exit();
}

// Enable Loan Type
if (isset($_GET['enableLoanType'])) {
    $id = intval($_GET['enableLoanType']);
    $query = "UPDATE loan_types SET is_active = 1 WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    $_SESSION['message'] = ($stmt->affected_rows > 0) ? "Loan Type Enabled Successfully" : "No changes made.";
    $stmt->close();
    
    header("Location: pages_manage_loan_types.php");
    exit();
}

// Fetch loan types
$loanTypesQuery = "SELECT id, type_name, description, interest_rate, max_amount, is_active FROM loan_types ORDER BY id DESC";
$loanTypesResult = $mysqli->query($loanTypesQuery);
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
                            <h1>Manage Loan Types</h1>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">List of Loan Types</h3>
                            </div>
                            <div class="card-body">
                                <!-- Success/Error Message Modal -->
                                <?php if (!empty($message)) : ?>
                                <script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: '<?php echo htmlspecialchars($message); ?>'
                                    });
                                });
                                </script>
                                <?php endif; ?>

                                <table class="table table-hover table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type Name</th>
                                            <th>Description</th>
                                            <th>Interest Rate</th>
                                            <!-- <th>Max Amount</th> -->
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cnt = 1;
                                        while ($row = $loanTypesResult->fetch_object()) {
                                        ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo htmlspecialchars($row->type_name); ?></td>
                                            <td><?php echo htmlspecialchars($row->description); ?></td>
                                            <td><?php echo htmlspecialchars($row->interest_rate); ?>%</td>
                                            <!-- <td><?php echo number_format($row->max_amount, 2); ?></td> -->
                                            <td>
    <div class="btn-group" role="group">
        <!-- Edit Button -->
        <a class="btn btn-success btn-sm" href="pages_edit_loan_type.php?id=<?php echo $row->id; ?>">
            <i class="fas fa-edit"></i> Edit
        </a>

        <!-- Enable/Disable Button -->
        <?php if ($row->is_active) : ?>
            <a class="btn btn-warning btn-sm" href="pages_manage_loan_types.php?disableLoanType=<?php echo $row->id; ?>">
                <i class="fas fa-ban"></i> Disable
            </a>
        <?php else : ?>
            <a class="btn btn-info btn-sm" href="pages_manage_loan_types.php?enableLoanType=<?php echo $row->id; ?>">
                <i class="fas fa-check"></i> Enable
            </a>
        <?php endif; ?>

        <!-- Delete Button with Confirmation -->
        <a class="btn btn-danger btn-sm delete-confirm" href="pages_manage_loan_types.php?deleteLoanType=<?php echo $row->id; ?>">
            <i class="fas fa-trash"></i> Delete
        </a>
    </div>
</td>
                                        </tr>
                                        <?php
                                            $cnt++;
                                        } ?>
                                    </tbody>
                                </table>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        $(document).on('click', '.delete-confirm', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>

</body>

</html>