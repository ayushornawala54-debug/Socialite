<?php
session_start();

// Database Connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_socialmedia';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Check if admin session is set
if (!isset($_SESSION['admin_email'])) {
    echo "❌ Admin session not found. Please log in again.";
    exit();
}

$admin_email = $_SESSION['admin_email'];

// Fetch Admin Details
$stmt = $conn->prepare("SELECT fullName FROM tbl_admin WHERE emailID = ?");
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    $adminName = $admin['fullName'];
} else {
    echo "❌ Admin details not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard Templates - Unify Admin Template</title>

    <!-- Meta -->
    <meta name="description" content="Marketplace for Bootstrap Admin Dashboards" />
    <meta name="author" content="Bootstrap Gallery" />
    <link rel="canonical" href="https://www.bootstrap.gallery/">
    <meta property="og:url" content="https://www.bootstrap.gallery/">
    <meta property="og:title" content="Admin Templates - Dashboard Templates | Bootstrap Gallery">
    <meta property="og:description" content="Marketplace for Bootstrap Admin Dashboards">
    <meta property="og:type" content="Website">
    <meta property="og:site_name" content="Bootstrap Gallery">
    <link rel="shortcut icon" href="assets/images/favicon.svg" />

    <!-- *************
			************ CSS Files *************
		************* -->
    <link rel="stylesheet" href="./socialite-admin/assets/fonts/bootstrap/bootstrap-icons.css" />
    <link rel="stylesheet" href="./socialite-admin/assets/css/main.min.css" />

    <!-- *************
			************ Vendor Css Files *************
		************ -->

    <!-- Scrollbar CSS -->
    <link rel="stylesheet" href="./socialite-admin/assets/vendor/overlay-scroll/OverlayScrollbars.min.css" />
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  </head>

  <body>

    <!-- Page wrapper start -->
    <div class="page-wrapper">

      <!-- App header starts -->
      <div class="app-header d-flex align-items-center">

        <!-- Toggle buttons start -->
        <div class="d-flex">
          <button class="toggle-sidebar" id="toggle-sidebar">
            <i class="bi bi-list lh-1"></i>
          </button>
          <button class="pin-sidebar" id="pin-sidebar">
            <i class="bi bi-list lh-1"></i>
          </button>
        </div>
        <!-- Toggle buttons end -->
 <!-- App brand starts -->
       <div class="app-brand py-2 ms-3">
          <a href="admin_feed.php" class="d-sm-block d-none">
            <img src="./socialite-admin/assets/images/logo.png" class="logo" alt="Bootstrap Gallery" />
          </a>
          <a href="admin_feed.php" class="d-sm-none d-block">
            <img src="assets/images/logo-mobile.png" class="logo" alt="Bootstrap Gallery" />
          </a>
        </div>
        <!-- App brand ends -->

        <!-- App header actions start -->
        <div class="header-actions col">
          <div class="d-lg-flex d-none">
            <div class="dropdown">
            </div>
            
                </div>

        <!-- App header actions start -->
		
       <div class="dropdown ms-2">
            <a id="userSettings" class="dropdown-toggle d-flex py-2 align-items-center text-decoration-none" href="#!"
              role="button" data-bs-toggle="dropdown" aria-expanded="false">
                         <span class="ms-2 text-truncate d-lg-block d-none"><?php echo htmlspecialchars($adminName); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow-lg">
              <div class="header-action-links mx-3 gap-2">
                <a class="dropdown-item" href="contacts.php"><i class="bi bi-person text-primary"></i>Contact</a>
                <a class="dropdown-item" href="settings.php"><i class="bi bi-gear text-danger"></i>Settings</a>
              </div>
              <div class="mx-3 mt-2 d-grid">
                <a href="form-login.php" class="btn btn-primary btn-sm">Logout</a>
              </div>
            </div>
          </div>
        </div>
        <!-- App header actions end -->

      </div>
      <!-- App header ends -->

      <!-- Main container start -->
      <div class="main-container">

        <!-- Sidebar wrapper start -->
        <nav id="sidebar" class="sidebar-wrapper">

          

 <!-- Sidebar menu starts -->
          <div class="sidebarMenuScroll">
            <ul class="sidebar-menu">
              <li class="active current-page">
                <a href="admin_feed.php">
                  <i class="bi bi-pie-chart"></i>
                  <span class="menu-text">Dashboard</span>
                </a>
              </li>
              <li class="treeview">
                <a href="#!">
                  <i class="bi bi-people"></i>
                  <span class="menu-text">Manage Users</span>
                </a>
                <ul class="treeview-menu">
                  <li>
                    <a href="analytics.php">User</a>
                  </li>
                  <li>
                    <a href="userp.php">User Profiles</a>
                  </li>
                </ul>
              </li>
			   <li class="treeview">
                <a href="#!">
                  <i class="bi bi-bar-chart-line"></i>
                  <span class="menu-text">Activity</span>
                </a>
                <ul class="treeview-menu">
                  <li>
                    <a href="tables.php">Post</a>
                  </li>
                  <li>
                    <a href="comment.php">Comment</a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="subscribers.php">
                  <i class="bi bi-check-circle"></i>
                  <span class="menu-text">Subscribers</span>
                </a>
              </li>
			  <li class="treeview">
                <a href="#!">
                  <i class="bi bi-shop"></i></i>
                  <span class="menu-text">Manage Market</span>
                </a>
                <ul class="treeview-menu">
                   <li>
                    <a href="product.php">Product</a>
                  </li>
                  <li>
                    <a href="add_ad_product.php">Add Product</a>
                  </li>
				  <li>
                    <a href="ad_purchase.php">Purchases</a>
                  </li>
                </ul>
              </li>
			  <li>
                <a href="groupmember.php">
                  <i class="bi bi-collection"></i>
                  <span class="menu-text">Groups</span>
                </a>
              </li>
			  <li>
                <a href="ad_video.php">
                  <i class="bi bi-play-btn"></i>
                  <span class="menu-text">Video</span>
                </a>
              </li>
			  <li>
                <a href="stories.php">
                  <i class="bi bi-hourglass"></i>
                  <span class="menu-text">Story</span>
                </a>
              </li>
			  <li class="treeview">
                <a href="#!">
                  <i class="bi bi-calendar2-event"></i>
                  <span class="menu-text">Manage Events</span>
                </a>
                <ul class="treeview-menu">
                  <li>
                    <a href="admin_add_event.php">Add Events</a>
                  </li>
                  <li>
                    <a href="ad_event.php">Events</a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="contacts.php">
                  <i class="bi bi-person-rolodex"></i>
                  <span class="menu-text">Contacts</span>
                </a>
              </li>
              <li>
                <a href="settings.php">
                  <i class="bi bi-gear"></i>
                  <span class="menu-text">Settings</span>
                </a>
              </li>
              <li>
                <a href="faq.php">
                  <i class="bi bi-patch-question"></i>
                  <span class="menu-text">User Queries</span>
                </a>
              </li>
              <li class="treeview">
                <a href="#!">
                  <i class="bi bi-upc-scan"></i>
                  <span class="menu-text">Login/Signup</span>
                </a>
                <ul class="treeview-menu">
                  <li>
                    <a href="form-login.php">Login</a>
                  </li>
                  <li>
                    <a href="form-register.php">Signup</a>
                  </li>
                </ul>
              </li>
			  <li>
                <a href="admin_register.php">
                  <i class="bi bi-bootstrap-reboot"></i>
                  <span class="menu-text">Admin Register</span>
                </a>
              </li>
              
                        </ul>
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
          <!-- Sidebar menu ends -->

        </nav>
        <!-- Sidebar wrapper end -->

        <!-- App container starts -->
        <div class="app-container">

          <!-- App hero header starts -->
          <div class="app-hero-header d-flex align-items-start">

            <!-- Breadcrumb start -->
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <i class="bi bi-house lh-1"></i>
                <a href="index.php" class="text-decoration-none">Home</a>
              </li>
              <li class="breadcrumb-item" aria-current="page">Contacts</li>
            </ol>
            <!-- Breadcrumb end -->

            <!-- Sales stats start -->
            
            <!-- Sales stats end -->

          </div>
          <!-- App Hero header ends -->

          <!-- App body starts -->
          <div class="app-body">

            <!-- Row start -->
            <div class="row gx-3">
  <?php
  $query = "SELECT * FROM tbl_admin";
  $result = mysqli_query($conn, $query);

  while ($row = mysqli_fetch_assoc($result)) {
  ?>
    <div class="col-xl-6 col-lg-6 col-md-12 col-12"> <!-- Half width on large screens -->
      <div class="card mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center flex-row">
            <img src="upload/<?= $row['image']; ?>" alt="Admin Image" class="rounded-circle img-3x" />
            <div class="ms-3">
              <h5><?= htmlspecialchars($row['fullName']); ?></h5>
              <p class="m-0 text-muted text-truncate"><?= htmlspecialchars($row['role']); ?></p>
            </div>
            <div class="ms-auto">
              <a href="#" class="contact-btn me-1 p-2 bg-secondary bg-opacity-10 rounded-circle" data-type="phone" data-id="<?= $row['id']; ?>">
                <i class="bi bi-telephone lh-1"></i>
              </a>
              <a href="#" class="contact-btn me-1 p-2 bg-secondary bg-opacity-10 rounded-circle" data-type="email" data-id="<?= $row['id']; ?>">
                <i class="bi bi-envelope-open lh-1"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="card-img">
          <img src="upload/<?= $row['image']; ?>" class="card-img-top img-fluid" alt="Admin Image" />
        </div>
        <div class="card-body text-center">
          <h3 class="mb-3"><?= htmlspecialchars($row['fullName']); ?></h3>
          <h5 class="mb-3 fw-light"><?= htmlspecialchars($row['role']); ?></h5>
          <p class="lh-base mb-4"><?= htmlspecialchars($row['bio']); ?></p>
          <ul class="list-unstyled">
            <li>
              <i class="bi bi-globe-americas fs-4 text-primary"></i>
              <p><?= htmlspecialchars($row['address']); ?></p>
            </li>
            <li>
              <i class="bi bi-telephone mt-4 fs-4 text-primary"></i>
              <p><a href="#" class="contact-btn" data-type="phone" data-id="<?= $row['id']; ?>"><?= htmlspecialchars($row['contactNumber']); ?></a></p>
            </li>
            <li>
              <i class="bi bi-envelope-open mt-4 fs-4 text-primary"></i>
              <p><a href="#" class="contact-btn" data-type="email" data-id="<?= $row['id']; ?>"><?= htmlspecialchars($row['emailId']); ?></a></p>
            </li>
          </ul>
        </div>
      </div>
    </div>
  <?php } ?>
</div>

          <!-- App body ends -->

          <!-- App footer start -->
          
          <!-- App footer end -->

        </div>
        <!-- App container ends -->

      </div>
      <!-- Main container end -->

    </div>
    <!-- Page wrapper end -->

    <!-- *************
			************ JavaScript Files *************
		************* -->
    <!-- Required jQuery first, then Bootstrap Bundle JS -->
    <script src="./socialite-admin/assets/js/jquery.min.js"></script>
    <script src="./socialite-admin/assets/js/bootstrap.bundle.min.js"></script>

    <!-- *************
			************ Vendor Js Files *************
		************* -->

    <!-- Overlay Scroll JS -->
    <script src="./socialite-admin/assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js"></script>
    <script src="./socialite-admin/assets/vendor/overlay-scroll/custom-scrollbar.js"></script>

    <!-- Custom JS files -->
    <script src="./socialite-admin/assets/js/custom.js"></script>
	<script>
  document.querySelectorAll('.contact-btn').forEach(button => {
  button.addEventListener('click', function(e) {
    e.preventDefault();
    let type = this.getAttribute('data-type');

    fetch('get_admin_contact.php?type=' + type)
      .then(response => response.text())
      .then(data => {
        document.getElementById('modalContent').innerHTML = data;
        new bootstrap.Modal(document.getElementById('contactModal')).show();
      });
  });
});

</script>
<script>
  document.querySelectorAll('.contact-btn').forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      let type = this.getAttribute('data-type');

      fetch('get_admin_contact.php?type=' + type)
        .then(response => response.text())
        .then(data => {
          Swal.fire({
            title: type === 'phone' ? '📞 Call Us' : '📧 Email Us',
            html: data,
            icon: 'info',
            confirmButtonText: 'Close',
            background: '#f0f8ff',
            customClass: {
              popup: 'rounded-4',
              title: 'fs-4',
              htmlContainer: 'fs-5'
            }
          });
        });
    });
  });
</script>
<script>
document.querySelectorAll('.contact-btn').forEach(button => {
  button.addEventListener('click', function(e) {
    e.preventDefault();
    const type = this.getAttribute('data-type');
    const id = this.getAttribute('data-id');

    fetch('get_admin_contact.php?type=' + type + '&id=' + id)
      .then(res => res.text())
      .then(data => {
        Swal.fire({
          title: type === 'phone' ? '📞 Phone' : '📧 Email',
          html: data,
          icon: 'info',
          confirmButtonText: 'Close'
        });
      });
  });
});

</script>
  </body>

</html>