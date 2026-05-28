<?php
// Database Connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_socialmedia';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Fetch Total Users
$userQuery = $conn->query("SELECT COUNT(*) as total_users FROM tbl_register");
$userData = $userQuery->fetch_assoc();
$totalUsers = $userData['total_users'];

// Fetch Total Subscriptions
$subscriptionQuery = $conn->query("SELECT COUNT(*) as total_subscriptions FROM tbl_subscription");
$subscriptionData = $subscriptionQuery->fetch_assoc();
$totalSubscriptions = $subscriptionData['total_subscriptions'];

// Fetch Total Posts
$postQuery = $conn->query("SELECT COUNT(*) as total_posts FROM tbl_post");
$postData = $postQuery->fetch_assoc();
$totalPosts = $postData['total_posts'];

// Fetch Total FAQs (assuming they are stored in tbl_contact)
$faqQuery = $conn->query("SELECT COUNT(*) as total_faqs FROM tbl_contact");
$faqData = $faqQuery->fetch_assoc();
$totalFaqs = $faqData['total_faqs'];
?>
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
    <title>Socialite</title>

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

    <!-- Toastify CSS -->
    <link rel="stylesheet" href="./socialite-admin/assets/vendor/toastify/toastify.css" />

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
                <a href="admin-feed">
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
          <div class="app-hero-header d-flex align-items-center">
            <!-- Breadcrumb start -->
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <i class="bi bi-house lh-1 pe-3 me-3 border-end border-dark"></i>
                <a href="index.php" class="text-decoration-none">Home</a>
              </li>
              <li class="breadcrumb-item text-secondary" aria-current="page">
                Dashboard
              </li>
            </ol>
            <!-- Breadcrumb end -->

            

          </div>
          <!-- App Hero header ends -->

          <!-- App body starts -->
          <div class="app-body">

            <!-- Row start -->
            <div class="row gx-3">
              <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="mb-2">
                      <i class="bi bi-bar-chart fs-1 text-primary lh-1"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                      <h5 class="m-0 text-secondary fw-normal">Total Users</h5>
                      <h3 class="m-0 text-primary"><?php echo $totalUsers; ?></h3>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="mb-2">
                      <i class="bi bi-bag-check fs-1 text-primary lh-1"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                      <h5 class="m-0 text-secondary fw-normal">Total Subscriptions</h5>
                      <h3 class="m-0 text-primary"><?php echo $totalSubscriptions; ?></h3>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="mb-2">
                      <i class="bi bi-box-seam fs-1 text-primary lh-1"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                      <h5 class="m-0 text-secondary fw-normal">Total Posts</h5>
                     <h3 class="m-0 text-primary"><?php echo $totalPosts; ?></h3>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-3 col-sm-6 col-12">
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="mb-2">
                      <i class="bi bi-bell fs-1 text-primary lh-1"></i>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                      <h5 class="m-0 text-secondary fw-normal">Total FAQs</h5>
                       <h3 class="m-0 text-primary"><?php echo $totalFaqs; ?></h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Row end -->

            <!-- Row start -->
           <div class="row gx-3">
  <div class="col-xxl-12">
    <div class="card mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title">Overview</h5>                    
      </div>
      <div class="card-body">
        <!-- Row start -->
        <div class="row gx-3">
          <div class="col-lg-5 col-sm-12 col-12">
            <h6 class="text-center mb-3">Users</h6>
            <div id="visitors">
              <h2 class="text-center text-primary" id="userCount">Loading...</h2>
            </div>
            <div class="my-3 text-center">
              <div id="userGrowth" class="badge bg-danger bg-opacity-10 text-danger">...</div>
            </div>
          </div>
          <div class="col-lg-2 col-sm-12 col-12">
            <div class="border px-2 py-4 rounded-5 h-100 text-center">
              <h6 class="mt-3 mb-5">Monthly Average</h6>
              <div class="mb-5">
                <h2 class="text-primary" id="userAvg">0</h2>
                <h6 class="text-secondary fw-light">Users</h6>
              </div>
              <div class="mb-4">
                <h2 class="text-danger" id="subscriptionAvg">0</h2>
                <h6 class="text-secondary fw-light">Subscriptions</h6>
              </div>
            </div>
          </div>
          <div class="col-lg-5 col-sm-12 col-12">
            <h6 class="text-center mb-3">Subscriptions</h6>
            <div id="sales">
              <h2 class="text-center text-primary" id="subCount">Loading...</h2>
            </div>
            <div class="my-3 text-center">
              <div id="subGrowth" class="badge bg-primary bg-opacity-10 text-primary">...</div>
            </div>
          </div>
        </div>
        <!-- Row ends -->
      </div>
    </div>
  </div>
</div>

<script>
fetch("fetch_dashboard_stats.php")
  .then(res => res.json())
  .then(data => {
    document.getElementById("userCount").innerText = data.users.current;
    document.getElementById("subCount").innerText = data.subscriptions.current;

    document.getElementById("userAvg").innerText = data.users.current;
    document.getElementById("subscriptionAvg").innerText = `$${data.subscriptions.current * 50}`; // assuming $50 per subscription

    document.getElementById("userGrowth").innerText = `${Math.abs(data.users.percentage)}% ${data.users.percentage >= 0 ? 'higher' : 'lower'} than last month`;
    document.getElementById("userGrowth").classList.toggle("text-danger", data.users.percentage < 0);
    document.getElementById("userGrowth").classList.toggle("text-success", data.users.percentage >= 0);

    document.getElementById("subGrowth").innerText = `${Math.abs(data.subscriptions.percentage)}% ${data.subscriptions.percentage >= 0 ? 'higher' : 'lower'} than last month`;
    document.getElementById("subGrowth").classList.toggle("text-primary", data.subscriptions.percentage >= 0);
    document.getElementById("subGrowth").classList.toggle("text-danger", data.subscriptions.percentage < 0);
  })
  .catch(error => {
    console.error("Error fetching stats:", error);
  });
</script>

            <!-- Row ends -->

            <!-- Row start -->
            
             
            <!-- Row end -->

            <!-- Row start -->
    
            </div>
            <!-- Row end -->

          </div>
          <!-- App body ends -->

         

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
    <script src="./socialite-admin/assets/js/moment.min.js"></script>

    <!-- *************
			************ Vendor Js Files *************
		************* -->

    <!-- Overlay Scroll JS -->
    <script src="./socialite-admin/assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js"></script>
    <script src="./socialite-admin/assets/vendor/overlay-scroll/custom-scrollbar.js"></script>

    <!-- Toastify JS -->


    <!-- Apex Charts -->
    <script src="./socialite-admin/assets/vendor/apex/apexcharts.min.js"></script>
    <script src="./socialite-admin/assets/vendor/apex/custom/dash1/visitors.js"></script>
    <script src="./socialite-admin/assets/vendor/apex/custom/dash1/sales.js"></script>
    <script src="./socialite-admin/assets/vendor/apex/custom/dash1/sparkline.js"></script>
    <script src="./socialite-admin/assets/vendor/apex/custom/dash1/tasks.js"></script>
    <script src="./socialite-admin/assets/vendor/apex/custom/dash1/income.js"></script>

    <!-- Custom JS files -->
    <script src="./socialite-admin/assets/js/custom.js"></script>
    <script src="./socialite-admin/assets/js/todays-date.js"></script>
  </body>

</html>