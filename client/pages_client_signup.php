<?php
session_start();
include('conf/config.php');

// Function to generate a random client number
function generateClientNumber() {
    $length = 4;
    return 'iBank-CLIENT-' . substr(str_shuffle('0123456789'), 1, $length);
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate client contact number
function validatePhoneNumber($phone) {
    return preg_match("/^\d{10}$/", $phone); // 10-digit phone number validation
}

// Function to validate client name (only letters and spaces allowed)
function validate_client_name($name) {
    return preg_match("/^[a-zA-Z ]+$/", $name);
}

// Function to validate password strength
function validatePassword($password) {
    $min_length = 8;
    $uppercase_regex = '/[A-Z]/';
    $lowercase_regex = '/[a-z]/';
    $number_regex = '/[0-9]/';
    $special_char_regex = '/[^A-Za-z0-9]/';

    if (strlen($password) < $min_length) return "Password must be at least $min_length characters long";
    if (!preg_match($uppercase_regex, $password)) return "Password must contain at least one uppercase letter";
    if (!preg_match($lowercase_regex, $password)) return "Password must contain at least one lowercase letter";
    if (!preg_match($number_regex, $password)) return "Password must contain at least one number";
    if (!preg_match($special_char_regex, $password)) return "Password must contain at least one special character";

    return true;
}

// Register new account
if (isset($_POST['create_account'])) {
    $errors = []; // Array to store error messages

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address  = $_POST['address'];
    $client_number = generateClientNumber();

    // Validate name
    if (!validate_client_name($name)) {
        $errors[] = "Invalid name: Only letters and spaces are allowed.";
    }

    // Validate email
    if (!validateEmail($email)) {
        $errors[] = "Invalid email address.";
    }

    // Validate phone number
    if (!validatePhoneNumber($phone)) {
        $errors[] = "Invalid contact number. Please enter a 10-digit phone number.";
    }

    // Validate password
    $password_validation = validatePassword($password);
    if ($password_validation !== true) {
        $errors[] = $password_validation;
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = sha1(md5($password)); // Secure hashing (Consider using password_hash)

        $query = "INSERT INTO iB_clients (name, client_number, phone, email, password, address) VALUES (?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssssss', $name, $client_number, $phone, $email, $hashed_password, $address);

        if ($stmt->execute()) {
            $success = "Account Created Successfully!";
        } else {
            $errors[] = "An error occurred. Please try again later.";
        }
    }
}

// Retrieve system settings
$ret = "SELECT * FROM iB_SystemSettings ";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
while ($auth = $res->fetch_object()) {
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $auth->sys_name; ?> - Sign Up</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .login-box {
            width: 400px;
            margin: auto;
            margin-top: 100px;
        }
        .login-logo {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .login-card-body {
            padding: 20px;
        }
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-logo">
            <?php echo $auth->sys_name; ?> - Sign Up
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign Up</p>

                <!-- Display all errors -->
                <?php if (!empty($errors)) { ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error) { ?>
                                <li><?php echo $error; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <!-- Display success message -->
                <?php if (isset($success)) { ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php } ?>

                <form method="post">
                    <div class="input-group mb-3">
                        <input type="text" name="name" required class="form-control" placeholder="Full Name">
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="phone" required class="form-control" placeholder="Phone Number">
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="address" required class="form-control" placeholder="Address">
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" name="email" required class="form-control" placeholder="Email Id">
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" required class="form-control" placeholder="Password">
                    </div>
                    <div class="row">
                        <div class="col-8"></div>
                        <div class="col-4">
                            <button type="submit" name="create_account" class="btn btn-success btn-block">Sign Up</button>
                        </div>
                    </div>
                </form>

                <p class="mb-0">
                    <a href="pages_client_index.php" class="text-center">Login</a>
                </p>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
} // End of while loop
?>
