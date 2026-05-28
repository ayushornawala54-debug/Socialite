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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $image = $_POST['image'];
    $date_time = $_POST['date_time'];
    $location = $_POST['location'];
    $category = $_POST['category'];

    $conn = new mysqli("localhost", "root", "", "db_socialmedia");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $sql = "INSERT INTO events (title, description, image, date_time, location, category) 
            VALUES ('$title', '$description', '$image', '$date_time', '$location', '$category')";

    if ($conn->query($sql) === TRUE) {
        echo "Event added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
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
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>


</script>
  <style>
        body {
            background: #f4f7fe;
            font-family: 'Poppins', sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            background: white;
        }
        .form-control {
            border-radius: 8px;
            height: 45px;
        }
        .btn-primary {
            background: #4CAF50;
            border: none;
            border-radius: 10px;
            padding: 12px;
            transition: 0.3s;
            width: 100%;
        }
        .btn-primary:hover {
            background: #45a049;
        }
        label {
            font-weight: 600;
            color: #333;
        }
        .form-header {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
        }
        .upload-btn {
            background: #ddd;
            border-radius: 8px;
            padding: 10px;
            cursor: pointer;
            text-align: center;
        }
        .upload-btn:hover {
            background: #ccc;
        }
    </style>	

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
          <!-- App body starts -->
	

<!-- Update Form (Hidden Initially) -->

    <div class="container">
    <div class="card p-4">
        <div class="form-header"><i class="fas fa-calendar-plus"></i> Add New Event</div>
        <form method="POST">
            <div class="mb-3">
                <label for="title">Event Title</label>
                <input type="text" class="form-control" name="title" required placeholder="Enter event title">
            </div>

            <div class="mb-3">
                <label for="description">Description</label>
                <textarea class="form-control" name="description" rows="3" placeholder="Enter event description"></textarea>
            </div>

            <div class="mb-3">
                <label for="image">Upload Image</label>
                <input type="file" class="form-control" name="image">
            </div>

            <div class="mb-3">
                <label for="date_time">Event Date & Time</label>
                <input type="datetime-local" class="form-control" name="date_time" required>
            </div>

            <div class="mb-3">
                <label for="location">Event Location</label>
                <input type="text" class="form-control" name="location" required placeholder="Enter location">
            </div>

            <div class="mb-3">
                <label for="category">Event Category</label>
                <select class="form-control" name="category">
                    <option value="red">Red</option>
                    <option value="blue">Blue</option>
                    <option value="teal">Teal</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Add Event</button>
        </form>
    </div>
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

    <!-- *************
			************ Vendor Js Files *************
		************* -->

    <!-- Overlay Scroll JS -->
    <script src="./socialite-admin/assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js"></script>
    <script src="./socialite-admin/assets/vendor/overlay-scroll/custom-scrollbar.js"></script>

    <!-- Toastify JS -->
    <!-- Apex Charts -->
    <script src="./socialite-admin/assets/vendor/apex/apexcharts.min.js"></script>
    <script src="./socialite-admin/assets/vendor/apex/custom/dash2/sparkline.js"></script>
    <script src="./socialite-admin/assets/vendor/apex/custom/dash2/traffic.js"></script>
    <script src="./socialite-admin/assets/vendor/apex/custom/dash2/active-users.js"></script>
    <script src="./socialite-admin/assets/vendor/apex/custom/dash2/statistics.js"></script>

    <!-- Newsticker JS -->
    <script src="./socialite-admin/assets/vendor/newsticker/newsTicker.min.js"></script>
    <script src="./socialite-admin/assets/vendor/newsticker/custom-newsTicker.js"></script>

    <!-- Custom JS files -->
    <script src="./socialite-admin/assets/js/custom.js"></script>
<script>
    function editUser(id, first, last, email, username) {
        document.getElementById('editForm').style.display = 'block';
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_first').value = first;
        document.getElementById('edit_last').value = last;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_username').value = username;
    }
</script>

  </body>

</html>