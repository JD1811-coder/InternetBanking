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

// Function to validate phone number (10-digit format)
function validatePhoneNumber($phone) {
    return preg_match("/^\d{10}$/", $phone);
}

// Function to validate client name (only letters and spaces allowed)
function validate_client_name($name) {
    return preg_match("/^[a-zA-Z ]+$/", $name);
}

// Function to validate password strength
function validatePassword($password) {
    $min_length = 8;
    if (strlen($password) < $min_length) return "Password must be at least $min_length characters long";
    if (!preg_match('/[A-Z]/', $password)) return "Password must contain at least one uppercase letter";
    if (!preg_match('/[a-z]/', $password)) return "Password must contain at least one lowercase letter";
    if (!preg_match('/[0-9]/', $password)) return "Password must contain at least one number";
    if (!preg_match('/[^A-Za-z0-9]/', $password)) return "Password must contain at least one special character";
    return true;
}

// Function to validate Aadhar number (exactly 12 digits)
function validateAadhar($aadhar) {
    return preg_match("/^\d{12}$/", $aadhar);
}

// Function to validate PAN number (exactly 10 characters, no special characters)
function validatePan($pan) {
    return preg_match("/^[A-Z]{5}[0-9]{4}[A-Z]$/", $pan);
}

// Register new account
if (isset($_POST['create_account'])) {
    $errors = [];

    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $address = trim($_POST['address']);
    $aadhar_number = trim($_POST['aadhar_number']);
    $pan_number = trim($_POST['pan_number']);
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

    // Validate Aadhar number
    if (!validateAadhar($aadhar_number)) {
        $errors[] = "Aadhar number must be exactly 12 digits.";
    }

    // Validate PAN number
    if (!validatePan($pan_number)) {
        $errors[] = "Invalid PAN number format. Example: ABCDE1234F";
    }

    // Validate password
    $password_validation = validatePassword($password);
    if ($password_validation !== true) {
        $errors[] = $password_validation;
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashed_password = sha1(md5($password)); // Secure hashing (Consider using password_hash)

        $query = "INSERT INTO iB_clients (name, client_number, phone, email, password, address, aadhar_number, pan_number) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('ssssssss', $name, $client_number, $phone, $email, $hashed_password, $address, $aadhar_number, $pan_number);

        if ($stmt->execute()) {
            $success = "Account Created Successfully!";
        } else {
            $errors[] = "An error occurred. Please try again later.";
        }
    }
}

// Retrieve system settings
$ret = "SELECT * FROM iB_SystemSettings";
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
</head>
<body>
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-body">
                <h3 class="text-center"><?php echo $auth->sys_name; ?> - Sign Up</h3>

                <!-- Display Errors -->
                <?php if (!empty($errors)) { ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error) { ?>
                                <li><?php echo $error; ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <!-- Display Success Message -->
                <?php if (isset($success)) { ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php } ?>

                <form method="post">
                    <div class="form-group">
                        <input type="text" name="name" required class="form-control" placeholder="Full Name">
                    </div>
                    <div class="form-group">
                        <input type="text" name="phone" required class="form-control" placeholder="Phone Number">
                    </div>
                    <div class="form-group">
                        <input type="text" name="address" required class="form-control" placeholder="Address">
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" required class="form-control" placeholder="Email Id">
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" required class="form-control" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <input type="text" name="aadhar_number" required class="form-control" placeholder="Aadhar Card Number (12 Digits)">
                    </div>
                    <div class="form-group">
                        <input type="text" name="pan_number" required class="form-control" placeholder="PAN Card Number (e.g. ABCDE1234F)">
                    </div>
                    <button type="submit" name="create_account" class="btn btn-success btn-block">Sign Up</button>
                </form>

                <p class="text-center mt-3">
                    <a href="pages_client_index.php">Login</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
<?php
} // End of while loop
?>
