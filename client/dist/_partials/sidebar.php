  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo and Logged-in User -->
    <?php
    $client_id = $_SESSION['client_id'];
    $ret = "SELECT * FROM iB_clients WHERE client_id = ?";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('i', $client_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_object()) {
      $profile_picture = $row->profile_pic 
          ? "<img src='../admin/dist/img/$row->profile_pic' class='img-fluid rounded-circle elevation-2' alt='User Image' style='width: 40px; height: 40px; object-fit: cover;'>"
          : "<img src='../admin/dist/img/user_icon.png' class='img-fluid rounded-circle elevation-2' alt='User Image' style='width: 40px; height: 40px; object-fit: cover;'>";
    

      $ret = "SELECT * FROM `iB_SystemSettings`";
      $stmt = $mysqli->prepare($ret);
      $stmt->execute();
      $res = $stmt->get_result();
      while ($sys = $res->fetch_object()) {
    ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    
  </style>
    <a href="pages_dashboard.php" class="brand-link">
      <img src="../admin/dist/img/<?php echo $sys->sys_logo; ?>" alt="iBanking Logo"
        class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><?php echo $sys->sys_name; ?></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- User Panel -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <?php echo $profile_picture; ?>
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $row->name; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          
          <!-- Dashboard -->
          <li class="nav-item">
            <a href="pages_dashboard.php" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>

          <!-- Account -->
          <li class="nav-item">
            <a href="pages_account.php" class="nav-link">
              <i class="nav-icon fas fa-user-tie"></i>
              <p>Account</p>
            </a>
          </li>

          <!-- iBank Accounts -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-briefcase"></i>
              <p>iBank Accounts <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="pages_open_acc.php" class="nav-link">
                  <i class="fas fa-lock-open nav-icon"></i>
                  <p>Open iBank Acc</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages_manage_acc_openings.php" class="nav-link">
                  <i class="fas fa-cog nav-icon"></i>
                  <p>My iBank Accounts</p>
                </a>
              </li>
            </ul>
          </li>

          <!-- Finances -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-rupee-sign"></i>
              <p>Finances <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <!-- <li class="nav-item">
                <a href="pages_deposits.php" class="nav-link">
                  <i class="fas fa-upload nav-icon"></i>
                  <p>Deposits</p>
                </a>
              </li> -->
              <li class="nav-item">
                <a href="pages_withdrawals.php" class="nav-link">
                  <i class="fas fa-download nav-icon"></i>
                  <p>Withdrawals</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages_transfers.php" class="nav-link">
                  <i class="fas fa-random nav-icon"></i>
                  <p>Transfers</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="apply_loan.php" class="nav-link">
                  <i class="fas fa-cart-arrow-down nav-icon"></i>
                  <p>Loan Applications</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="loan_status.php" class="nav-link">
                  <i class="fas fa-file nav-icon"></i>
                  <p>My Loan Status</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="pages_view_client_bank_acc.php" class="nav-link">
                  <i class="fas fa-money-bill-alt nav-icon"></i>
                  <p>Balance Enquiries</p>
                </a>
              </li>
            </ul>
          </li>
   <!-- Nominee Section -->
<li class="nav-item has-treeview">
    <a href="#" class="nav-link">
        <i class="fa-solid fa-id-card nav-icon"></i>
        <p>Nominee <i class="fas fa-angle-left right"></i></p>
    </a>
    <ul class="nav nav-treeview pl-4"> <!-- Added padding-left to align submenu -->
        <li class="nav-item">
            <a href="client_add_nominee.php" class="nav-link">
                <i class="fa-solid fa-user-plus nav-icon"></i>
                <p>Add Nominee</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="client_nominees.php" class="nav-link">
                <i class="fa-solid fa-users nav-icon"></i>
                <p>Show Nominee</p>
            </a>
        </li>
    </ul>
</li>

          <!-- EMI Section -->
          <li class="nav-item has-treeview">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-credit-card"></i>
              <p>EMI <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="pages_emi.php" class="nav-link">
                  <i class="fas fa-calendar-alt nav-icon"></i>
                  <p>EMI Installments</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-header">Advanced Modules</li>
          <!-- Transactions History -->
          <li class="nav-item">
            <a href="pages_transactions_engine.php" class="nav-link">
              <i class="nav-icon fas fa-exchange-alt"></i>
              <p>Transactions History</p>
            </a>
          </li>

          <!-- Complaints -->
          <li class="nav-item">
            <a href="pages_feedback.php" class="nav-link">
              <i class="nav-icon fas fa-comments"></i>
              <p>Complaints</p>
            </a>
          </li>

          <!-- Logout -->
          <li class="nav-item">
            <a href="pages_logout.php" class="nav-link">
              <i class="nav-icon fas fa-power-off"></i>
              <p>Log Out</p>
            </a>
          </li>

        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  <?php
      }
    }
  ?>
