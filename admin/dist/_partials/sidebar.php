<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Load logged-in admin details -->
    <?php
    $admin_id = $_SESSION['admin_id'];
    $query = "SELECT * FROM iB_admin WHERE admin_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $admin_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_object()) {
        // Set default profile picture if none is uploaded
        $profile_picture = empty($row->profile_pic)
            ? "<img src='dist/img/user_icon.png' class='img-circle elevation-2' alt='User Image'>"
            : "<img src='dist/img/$row->profile_pic' class='img-circle elevation-2' alt='User Image'>";

        // Fetch system settings
        $sys_query = "SELECT * FROM iB_SystemSettings";
        $stmt = $mysqli->prepare($sys_query);
        $stmt->execute();
        $sys_res = $stmt->get_result();
        while ($sys = $sys_res->fetch_object()) {
            ?>

            <!-- Brand Logo -->
            <a href="pages_dashboard.php" class="brand-link text-light">
                <img src="dist/img/<?php echo $sys->sys_logo; ?>" alt="Logo" class="brand-image img-circle elevation-3">
                <span class="brand-text font-weight-light"><?php echo $sys->sys_name; ?></span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- User Panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image"><?php echo $profile_picture; ?></div>
                    <div class="info">
                        <a href="#" class="d-block"><?php echo $row->name; ?></a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="pages_dashboard.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="pages_account.php" class="nav-link">
                                <i class="nav-icon fas fa-user-secret"></i>
                                <p>Account</p>
                            </a>
                        </li>

                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-user-tie"></i>
                                <p>Staff <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="pages_add_staff.php" class="nav-link"><i
                                            class="fas fa-user-plus nav-icon"></i> Add Staff</a></li>
                                <li class="nav-item"><a href="pages_manage_staff.php" class="nav-link"><i
                                            class="fas fa-user-cog nav-icon"></i> Manage Staff</a></li>
                            </ul>
                        </li>

                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Clients <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="pages_manage_clients.php" class="nav-link"><i
                                            class="fas fa-user-cog nav-icon"></i> Manage Clients</a></li>
                            </ul>
                        </li>

                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-briefcase"></i>
                                <p>Accounts <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="pages_add_acc_type.php" class="nav-link"><i
                                            class="fas fa-plus nav-icon"></i> Add Acc Type</a></li>
                                <li class="nav-item"><a href="pages_manage_accs.php" class="nav-link"><i
                                            class="fas fa-cogs nav-icon"></i> Manage Acc Types</a></li>
                                <li class="nav-item"><a href="pages_manage_acc_openings.php" class="nav-link"><i
                                            class="fas fa-cog nav-icon"></i> Manage Acc Openings</a></li>
                            </ul>
                        </li>

                        <!-- Improved Loan Section -->
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-hand-holding-usd"></i>
                                <p>
                                    Loans
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview pl-4">
                                <li class="nav-item">
                                    <a href="pages_review_loan_list.php" class="nav-link">
                                        <i class="fas fa-clipboard-list nav-icon"></i>
                                        <p>Review Application</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="pages_add_loan_types.php" class="nav-link">
                                        <i class="fas fa-plus nav-icon"></i>
                                        <p>Add Loan Types</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="pages_manage_loan_types.php" class="nav-link">
                                        <i class="fas fa-cogs nav-icon"></i>
                                        <p>Manage Loan Types</p>
                                    </a>
                                </li>
                            </ul>
                        </li>


                        <li class="nav-item">
                            <a href="pages_transactions_engine.php" class="nav-link">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>Transactions History</p>
                            </a>
                        </li>

                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-rupee-sign"></i>
                                <p>Financial Reports <i class="fas fa-angle-left right"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item"><a href="pages_financial_reporting_deposits.php" class="nav-link"><i
                                            class="fas fa-file-upload nav-icon"></i> Deposits</a></li>
                                <li class="nav-item"><a href="pages_financial_reporting_withdrawals.php" class="nav-link"><i
                                            class="fas fa-cart-arrow-down nav-icon"></i> Withdrawals</a></li>
                                <li class="nav-item"><a href="pages_financial_reporting_transfers.php" class="nav-link"><i
                                            class="fas fa-random nav-icon"></i> Transfers</a></li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="pages_system_settings.php" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>System Settings</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="pages_logout.php" class="nav-link">
                                <i class="nav-icon fas fa-power-off"></i>
                                <p>Log Out</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

    <?php }
    } ?>