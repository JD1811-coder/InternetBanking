<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
  
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
