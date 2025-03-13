<?php
include("admin/conf/config.php");

/* Fetch System Settings */
$ret = "SELECT * FROM `iB_SystemSettings` ";
$stmt = $mysqli->prepare($ret);
$stmt->execute();
$res = $stmt->get_result();
while ($sys = $res->fetch_object()) {
?>
    <!DOCTYPE html>
    <html lang="en">
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Secure Internet Banking">
        <meta name="author" content="DigiBankX">
        <title><?php echo $sys->sys_name; ?> - <?php echo $sys->sys_tagline; ?></title>
        <link href="dist/css/robust.css" rel="stylesheet">
    </head>

    <body>

        <!-- Navbar -->
        <nav class="navbar navbar-lg navbar-expand-lg navbar-transparant navbar-dark navbar-absolute w-100">
            <div class="container">
                <a class="navbar-brand" href="index.php"><?php echo $sys->sys_name; ?></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item active">
                            <a class="nav-link" target="_blank" href="admin/pages_index.php">Admin Portal</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" target="_blank" href="staff/pages_staff_index.php">Staff Portal</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" target="_blank" href="client/pages_client_index.php">Client Portal</a>
                        </li>
                    </ul>
                    <a class="btn btn-danger" href="client/pages_client_signup.php" target="_blank">Join Us</a>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="intro py-5 py-lg-9 position-relative text-white">
            <div class="bg-overlay-gray">
                <img src="dist/bg.webp" class="img-fluid img-cover"/>
            </div>
            <div class="intro-content py-6 text-center">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-12 col-sm-10 col-md-8 col-lg-6 mx-auto text-center">
                            <h1 class="my-3 display-4 d-none d-lg-inline-block"><?php echo $sys->sys_name; ?></h1>
                            <p class="lead mb-3">
                                <?php echo $sys->sys_tagline; ?>
                            </p>
                            <br>
                            <a class="btn btn-success btn-lg mr-lg-2 my-1" target="_blank" href="client/pages_client_signup.php" role="button">Get started</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitor Page - About Us -->
        <section class="py-5 bg-light text-center">
            <div class="container">
                <h2 class="mb-4">Welcome to <?php echo $sys->sys_name; ?></h2>
                <p class="lead">
                    Experience secure and seamless banking with DigiBankX. We provide modern digital banking solutions, ensuring quick transactions, easy account management, and hassle-free loan applications.
                </p>
                <a href="client/pages_client_signup.php" target="_blank" class="btn btn-primary">Open an Account</a>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-5">
            <div class="container">
                <h2 class="text-center mb-4">Why Choose DigiBankX?</h2>
                <div class="row">
                    <!-- Feature 1 -->
                    <div class="col-md-4 text-center mb-4">
                        <div class="card p-3">
                            <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                            <h5>Secure Transactions</h5>
                            <p>Bank with confidence using our highly secure transaction system.</p>
                        </div>
                    </div>
                    <!-- Feature 2 -->
                    <div class="col-md-4 text-center mb-4">
                        <div class="card p-3">
                            <i class="fas fa-hand-holding-usd fa-3x text-primary mb-3"></i>
                            <h5>Loan Management</h5>
                            <p>Apply for loans and manage EMI payments directly from your account.</p>
                        </div>
                    </div>
                    <!-- Feature 3 -->
                    <div class="col-md-4 text-center mb-4">
                        <div class="card p-3">
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h5>24/7 Banking</h5>
                            <p>Access your account anytime, anywhere, with our online banking services.</p>
                        </div>
                    </div>
                    <!-- Feature 4 -->
                    <div class="col-md-4 text-center mb-4">
                        <div class="card p-3">
                            <i class="fas fa-receipt fa-3x text-danger mb-3"></i>
                            <h5>Easy Bill Payments</h5>
                            <p>Pay bills online without the hassle of long queues.</p>
                        </div>
                    </div>
                    <!-- Feature 5 -->
                    <div class="col-md-4 text-center mb-4">
                        <div class="card p-3">
                            <i class="fas fa-calculator fa-3x text-info mb-3"></i>
                            <h5>EMI Management</h5>
                            <p>Track and pay your monthly EMI installments with ease.</p>
                        </div>
                    </div>
                    <!-- Feature 6 -->
                    <div class="col-md-4 text-center mb-4">
                        <div class="card p-3">
                            <i class="fas fa-mobile-alt fa-3x text-secondary mb-3"></i>
                            <h5>User-Friendly Interface</h5>
                            <p>Simple and intuitive design for a seamless banking experience.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-4 bg-dark text-white text-center">
            <p>&copy; <?php echo date("Y"); ?> <?php echo $sys->sys_name; ?>. All rights reserved.</p>
        </footer>

        <script src="dist/js/bundle.js"></script>
    </body>

    </html>
<?php
} ?>
