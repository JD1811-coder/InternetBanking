<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Notifications Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <?php
                // Count notifications
                $result = "SELECT COUNT(*) FROM iB_notifications";
                $stmt = $mysqli->prepare($result);
                $stmt->execute();
                $stmt->bind_result($ntf);
                $stmt->fetch();
                $stmt->close();
                ?>
                <span class="badge badge-danger navbar-badge"><?php echo $ntf; ?></span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-3" style="width: 350px; max-height: 400px; overflow-y: auto;">
                <span class="dropdown-header"><?php echo $ntf; ?> Notifications</span>
                <div class="dropdown-divider"></div>

                <?php
                // Fetch notifications
                $ret = "SELECT * FROM iB_notifications ORDER BY created_at DESC LIMIT 10";
                $stmt = $mysqli->prepare($ret);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_object()) {
                        $notification_time = date("d-M-Y H:i", strtotime($row->created_at));
                ?>
                        <div class="dropdown-item d-flex align-items-start">
                            <div class="media-body">
                                <h3 class="dropdown-item-title">
                                    <i class="fas fa-bell text-warning"></i> Notification
                                </h3>
                                <p class="text-sm mb-1"><?php echo $row->notification_details; ?></p>
                                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> <?php echo $notification_time; ?></p>
                            </div>
                            <a href="javascript:void(0);" class="ml-3 text-danger clear-notification" data-id="<?php echo $row->notification_id; ?>" title="Clear Notification">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                        <div class="dropdown-divider"></div>
                <?php 
                    }
                } else {
                    echo '<p class="text-center text-muted">No new notifications</p>';
                } 
                ?>
            </div>
        </li>
    </ul>
</nav>

<!-- Include SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JavaScript to Handle Notification Clear with SweetAlert -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".clear-notification").forEach(function(element) {
        element.addEventListener("click", function() {
            var notificationId = this.getAttribute("data-id");

            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to recover this notification!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "pages_dashboard.php?Clear_Notifications=" + notificationId;
                }
            });
        });
    });

    // Check if URL contains Clear_Notifications and reload page without query string
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has("Clear_Notifications")) {
        Swal.fire({
            title: "Deleted!",
            text: "Notification has been cleared.",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
        }).then(() => {
            window.location.href = "pages_dashboard.php"; // Reload without query string
        });
    }
});
</script>
