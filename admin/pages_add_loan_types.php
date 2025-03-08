<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$admin_id = $_SESSION['admin_id'];

if (isset($_POST['add_loan_type'])) {
    $type_name = trim($_POST['type_name']);
    $description = trim($_POST['description']);
    $interest_rate = floatval($_POST['interest_rate']);
    $max_amount = floatval($_POST['max_amount']);

    // Check for duplicate Loan Type Name
    $stmt = $mysqli->prepare("SELECT type_name FROM loan_types WHERE type_name = ?");
    $stmt->bind_param('s', $type_name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $err = "Loan Type Name already exists!";
    } elseif (empty($type_name) || empty($description)) {
        $err = "All fields are required!";
    } elseif ($interest_rate < 1 || $interest_rate > 100) {
        $err = "Interest Rate must be between 1 and 100!";
    } elseif ($max_amount <= 0) {
        $err = "Maximum Loan Amount must be greater than zero!";
    } else {
        $query = "INSERT INTO loan_types (type_name, description, interest_rate, max_amount, created_at) 
                  VALUES (?, ?, ?, ?, NOW())";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssdd', $type_name, $description, $interest_rate, $max_amount);

        if ($stmt->execute()) {
            $success = "Loan Type Added Successfully";
        } else {
            $err = "Please Try Again Later";
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
                            <h1>Add Loan Type</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Add Loan Type</li>
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
                                <form id="loanForm" method="post">
    <div class="card-body">
        <div class="form-group">
            <label>Loan Type Name</label>
            <input type="text" name="type_name" id="type_name" class="form-control">
            <span class="error text-danger" id="error_type_name"></span>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" id="description" class="form-control"></textarea>
            <span class="error text-danger" id="error_description"></span>
        </div>
        <div class="form-group">
    <label>Interest Rate (%)</label>
    <input type="number" step="0.01" name="interest_rate" id="interest_rate" required class="form-control" min="0">
    <span class="error text-danger" id="error_interest_rate"></span>
</div>

<div class="form-group">
    <label>Maximum Loan Amount</label>
    <input type="number" step="0.01" name="max_amount" id="max_amount" required class="form-control" min="0">
    <span class="error text-danger" id="error_max_amount"></span>
</div>

    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-success">Add Loan Type</button>
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
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="dist/js/adminlte.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("loanForm").addEventListener("submit", function (e) {
        e.preventDefault(); // Prevent normal form submission

        // Get form values & trim spaces
        let typeName = document.getElementById("type_name").value.trim();
        let description = document.getElementById("description").value.trim();
        let interestRate = document.getElementById("interest_rate").value.trim();
        let maxAmount = document.getElementById("max_amount").value.trim();

        // Validation regex
        let nameRegex = /^[a-zA-Z\s_]+$/; 

        // Clear previous errors
        document.querySelectorAll(".error").forEach(el => el.innerText = "");

        let hasError = false; 

        // Loan Type Name validation
        if (typeName === "") {
            document.getElementById("error_type_name").innerText = "Loan Type Name is required!";
            hasError = true;
        } else if (!nameRegex.test(typeName)) {
            document.getElementById("error_type_name").innerText = "Only letters, spaces, and underscores allowed!";
            hasError = true;
        }

        // Description validation
        if (description === "") {
            document.getElementById("error_description").innerText = "Description is required!";
            hasError = true;
        }

        // Interest Rate validation
        if (interestRate === "" || isNaN(interestRate)) {
            document.getElementById("error_interest_rate").innerText = "Valid Interest Rate is required!";
            hasError = true;
        } else if (parseFloat(interestRate) < 1 || parseFloat(interestRate) > 100) {
            document.getElementById("error_interest_rate").innerText = "Interest Rate must be between 1 and 100!";
            hasError = true;
        }

        // Maximum Loan Amount validation
        if (maxAmount === "" || isNaN(maxAmount) || parseFloat(maxAmount) <= 0) {
            document.getElementById("error_max_amount").innerText = "Valid Maximum Loan Amount is required!";
            hasError = true;
        }

        // AJAX Duplicate Check for Loan Type Name
        if (!hasError) {
            fetch("ajax_check_duplicate.php", {
                method: "POST",
                body: JSON.stringify({ type_name: typeName }),
                headers: { "Content-Type": "application/json" }
            })
            .then(response => response.json())
            .then(data => {
                if (data.duplicate) {
                    document.getElementById("error_type_name").innerText = "Loan Type Name already exists!";
                } else {
                    // Submit the form if no duplicates
                    document.getElementById("loanForm").submit();
                }
            });
        }
    });
});

</script>

</body>
</html>
