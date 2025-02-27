<?php
session_start();
include('conf/config.php');
include('conf/checklogin.php');
check_login();
$staff_id = $_SESSION['staff_id'];
//register new account
if (isset($_POST['update_account'])) {
    // Get client and account details from form
    $account_id = $_GET['account_id'];

    $acc_name = $_POST['acc_name'];
    $account_number = $_POST['account_number'];
    $acc_type = $_POST['acc_type'];
    $acc_rates = $_POST['acc_rates'];
    $acc_status = $_POST['acc_status'];
    $acc_amount = $_POST['acc_amount'];

    // $client_national_id = $_POST['client_national_id'];
    $client_name = $_POST['client_name'];
    $client_phone = $_POST['client_phone'];
    $client_number = $_POST['client_number'];
    $client_email  = $_POST['client_email'];
    $client_adr  = $_POST['client_adr'];

    // 1️⃣ Fetch `client_id` from `ib_bankAccounts`
    $query = "SELECT client_id FROM iB_bankAccounts WHERE account_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $account_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if (!$row) {
        die("Error: No account found with account_id = $account_id");
    }

    $client_id = $row['client_id'];

    // 2️⃣ Update `iB_bankAccounts`
    $query1 = "UPDATE iB_bankAccounts SET acc_name=?, account_number=?, acc_type=?, acc_rates=?, acc_status=?, acc_amount=? WHERE account_id = ?";
    $stmt1 = $mysqli->prepare($query1);
    $stmt1->bind_param('ssssssi', $acc_name, $account_number, $acc_type, $acc_rates, $acc_status, $acc_amount, $account_id);
    $stmt1->execute();

    // 3️⃣ Update `ib_clients`
    $query2 = "UPDATE ib_clients SET name=?, phone=?, address=?, email=?, client_number=? WHERE client_id = ?";
    $stmt2 = $mysqli->prepare($query2);
    $stmt2->bind_param('sssssi', $client_name, $client_phone, $client_adr, $client_email, $client_number, $client_id);
    $stmt2->execute();

    // 4️⃣ Check for success
    if ($stmt1 && $stmt2) {
        $success = "Client and Account details updated successfully.";
    } else {
        $err = "Update failed. Please try again.";
    }
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
        <?php
        $account_id = $_GET['account_id'];
        $ret = "SELECT ba.*, c.client_id, c.name as client_name, c.phone as client_phone, c.address as client_adr, c.email as client_email, c.client_number 
                FROM iB_bankAccounts ba 
                JOIN ib_clients c ON ba.client_id = c.client_id 
                WHERE ba.account_id = ?";
        $stmt = $mysqli->prepare($ret);
        $stmt->bind_param('i', $account_id);
        $stmt->execute();
        $res = $stmt->get_result();
        
        if ($res->num_rows === 0) {
            die("Error: No matching data found.");
        }
        
        $row = $res->fetch_object();
{        

        ?>
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Update <?php echo $row->client_name; ?> iBanking Account</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="pages_dashboard.php">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="pages_open_acc.php">iBanking Accounts</a></li>
                                    <li class="breadcrumb-item"><a href="pages_open_acc.php">Manage </a></li>
                                    <li class="breadcrumb-item active"><?php echo $row->client_name; ?></li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <!-- left column -->
                            <div class="col-md-12">
                                <!-- general form elements -->
                                <div class="card card-purple">
                                    <div class="card-header">
                                        <h3 class="card-title">Fill All Fields</h3>
                                    </div>
                                    <!-- form start -->
                                    <form method="post" enctype="multipart/form-data" role="form">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="exampleInputEmail1">Client Name</label>
                                                    <input type="text" readonly name="client_name" value="<?php echo $row->client_name; ?>" required class="form-control" id="exampleInputEmail1">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label for="exampleInputPassword1">Client Number</label>
                                                    <input type="text" readonly name="client_number" value="<?php echo $row->client_number; ?>" class="form-control" id="exampleInputPassword1">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class=" col-md-6 form-group">
                                                    <label for="exampleInputEmail1">Client Phone Number</label>
                                                    <input type="text" readonly name="client_phone" value="<?php echo $row->client_phone; ?>" required class="form-control" id="exampleInputEmail1">
                                                </div>
                                                
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="exampleInputEmail1">Client Email</label>
                                                    <input type="email" readonly name="client_email" value="<?php echo $row->client_email; ?>" required class="form-control" id="exampleInputEmail1">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label for="exampleInputEmail1">Client Address</label>
                                                    <input type="text" name="client_adr" readonly value="<?php echo $row->client_adr; ?>" required class="form-control" id="exampleInputEmail1">
                                                </div>
                                            </div>
                                            <!-- ./End Personal Details -->

                                            <!--Bank Account Details-->

                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="exampleInputEmail1">Account Name</label>
                                                    <input type="text" name="acc_name" value="<?php echo $row->acc_name; ?>" required class="form-control" id="exampleInputEmail1">
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <label for="exampleInputEmail1">Account Number</label>
                                                    <input type="text" readonly name="account_number" value="<?php echo $row->account_number; ?>" required class="form-control" id="exampleInputEmail1">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="exampleInputEmail1">Account Type</label>
                                                    <select class="form-control" onChange="getiBankAccs(this.value);" name="acc_type">
                                                        <option>Select Any Account types</option>
                                                        <?php
                                                        //fetch all iB_Acc_types
                                                        $ret = "SELECT * FROM  iB_Acc_types ORDER BY RAND() ";
                                                        $stmt = $mysqli->prepare($ret);
                                                        $stmt->execute(); //ok
                                                        $res = $stmt->get_result();
                                                        $cnt = 1;
                                                        while ($row = $res->fetch_object()) {

                                                        ?>
                                                            <option value="<?php echo $row->name; ?> "> <?php echo $row->name; ?> </option>
                                                        <?php } ?>
                                                    </select>

                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label for="exampleInputEmail1">Account Type Rates (%)</label>
                                                    <input type="text" name="acc_rates" readonly required class="form-control" id="AccountRates">
                                                </div>

                                                <div class="col-md-6 form-group" style="display:none">
                                                    <label for="exampleInputEmail1">Account Status</label>
                                                    <input type="text" name="acc_status" value="Active" readonly required class="form-control">
                                                </div>

                                                <div class="col-md-6 form-group" style="display:none">
                                                    <label for="exampleInputEmail1">Account Amount</label>
                                                    <input type="text" name="acc_amount" value="0" readonly required class="form-control">
                                                </div>

                                            </div>
                                        </div>
                                        <!-- /.card-body -->
                                        <div class="card-footer">
                                            <button type="submit" name="update_account" class="btn btn-success">Update iBanking Account</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card -->
                            </div><!-- /.container-fluid -->
                </section>
                <!-- /.content -->
            </div>
        <?php } ?>
        <!-- /.content-wrapper -->
        <?php include("dist/_partials/footer.php"); ?>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- bs-custom-file-input -->
    <script src="plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            bsCustomFileInput.init();
        });
    </script>
</body>

</html>
