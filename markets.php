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
// DELETE Operation
if (isset($_GET['delete_id'])) {
    $deleteID = intval($_GET['delete_id']);
    $conn->query("DELETE FROM products WHERE id = $deleteID");
    header("Location: market.php");
    exit();
}

// UPDATE Operation
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
	$product_name = $conn->real_escape_string($_POST['product_name']);
	$product_description = $conn->real_escape_string($_POST['product_description']);
    $price = $conn->real_escape_string($_POST['price']);
	$num_purchases = $conn->real_escape_string($_POST['num_purchases']);

    $conn->query("UPDATE products 
                  SET product_name='$product_name',product_description='$product_description',price='$price',num_purchases='$num_purchases'
                  WHERE id=$id");
    header("Location: market.php");
    exit();
}

// Fetch Data
$result = $conn->query("SELECT id, username, product_name,  product_description, price, image, created_at, num_purchases  FROM products");
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

	<style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #4CAF50; color: white; }
        a { text-decoration: none; color: red; }
        button { background-color: #2196F3; color: #fff; border: none; padding: 5px 10px; cursor: pointer; }
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
          <a href="index.php" class="d-sm-block d-none">
            <img src="./socialite-admin/assets/images/logo.png" class="logo" alt="Bootstrap Gallery" />
          </a>
          <a href="index.php" class="d-sm-none d-block">
            <img src="./socialite-admin/assets/images/logo-mobile.png" class="logo" alt="Bootstrap Gallery" />
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
              <img src="./socialite-admin/assets/images/om.jpg" class="rounded-2 img-3x" alt="Bootstrap Gallery" />
              <span class="ms-2 text-truncate d-lg-block d-none"><?php echo htmlspecialchars($adminName); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow-lg">
              <div class="header-action-links mx-3 gap-2">
                <a class="dropdown-item" href="profile.php"><i class="bi bi-person text-primary"></i>Profile</a>
                <a class="dropdown-item" href="settings.php"><i class="bi bi-gear text-danger"></i>Settings</a>
                <a class="dropdown-item" href="widgets.php"><i class="bi bi-box text-success"></i>Widgets</a>
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
              <li>
                <a href="analytics.php">
                  <i class="bi bi-bar-chart-line"></i>
                  <span class="menu-text">Analytics</span>
                </a>
              </li>
      
              <li >
                <a href="userp.php">
                  <i class="bi bi-window-sidebar"></i>
                  <span class="menu-text">User Profiles</span>
                </a>
                
              </li>
              <li>
                <a href="tables.php">
                  <i class="bi bi-border-all"></i>
                  <span class="menu-text">Tables</span>
                </a>
              </li>
              <li>
                <a href="#.php">
                  <i class="bi bi-border-all"></i>
                  <span class="menu-text">Comments</span>
                </a>
              </li>
              <li>
                <a href="subscribers.php">
                  <i class="bi bi-check-circle"></i>
                  <span class="menu-text">Subscribers</span>
                </a>
              </li>
              <li>
                <a href="contacts.php">
                  <i class="bi bi-wallet2"></i>
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
                <a href="profile.php">
                  <i class="bi bi-person-square"></i>
                  <span class="menu-text">Profile</span>
                </a>
              </li>
              <li>
                <a href="faq.php">
                  <i class="bi bi-code-square"></i>
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
                    <a href="login.php">Login</a>
                  </li>
                  <li>
                    <a href="signup.php">Signup</a>
                  </li>
                  <li>
                    <a href="forgot-password.php">Forgot Password</a>
                  </li>
                </ul>
              </li>
              <li>
                <a href="page-not-found.php">
                  <i class="bi bi-exclamation-diamond"></i>
                  <span class="menu-text">Page Not Found</span>
                </a>
              </li>
              <li>
                <a href="maintenance.php">
                  <i class="bi bi-exclamation-octagon"></i>
                  <span class="menu-text">Maintenance</span>
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
			<h2>Manage Users</h2>
<table>
    <tr>
        <th>ID</th>
        <th>username</th>
		<th>Product Name</th>
		<th>Product Description</th>
        <th>Price</th>
        <th>Image</th>
        <th>Created At</th>
		<th>Number Of Purchases</th>
        
        <th>Action</th>
    </tr>
    <?php if ($result->num_rows > 0) : ?>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['id']; ?></td>
				<td><?= $row['username']; ?></td>
                <td><?= $row['product_name']; ?></td>
                <td><?= $row['product_description']; ?></td>
				<td><?= $row['price']; ?></td>
                <td><?= $row['image']; ?></td>
                <td><?= $row['created_at']; ?></td>
				<td><?= $row['num_purchases']; ?></td>
                <td>
                    <a href="market.php?delete_id=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a> |
                    <button onclick="editUser(
                        '<?= $row['id']; ?>',
						'<?= $row['username']; ?>',
                        '<?= $row['product_name']; ?>',
                        '<?= $row['product_description']; ?>', 
						'<?= $row['price']; ?>',
                        '<?= $row['image']; ?>', 
                        '<?= $row['created_at']; ?>',
						'<?= $row['num_purchases']; ?>',)">Edit</button>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else : ?>
        <tr>
            <td colspan="7">No users found.</td>
        </tr>
    <?php endif; ?>
</table>

<!-- Update Form (Hidden Initially) -->
<div id="editForm" style="display: none;">
    <h3>Edit User</h3>
    <form method="POST" action="market.php">
        <input type="hidden" id="edit_id" name="id">
		<label>Group Name:</label>
        <input type="text" id="edit_group_name" name="group_name" required><br>
		<label>Group Description:</label>
        <input type="group_description" id="edit_group_description" name="group_description" required><br>
        <label>Group Image:</label>
        <input type="image" id="edit_group_image" name="group_image" required><br>
        
        
        <button type="submit" name="update">Update</button>
        <button type="button" onclick="document.getElementById('editForm').style.display='none'">Cancel</button>
    </form>
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
    <script src="./socialite-admin/assets/vendor/toastify/toastify.js"></script>
    <script src="./socialite-admin/assets/vendor/toastify/custom.js"></script>

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
function editUser(id, username, email, bio, createdAt, gender, relationship) {
    // Show the form
    document.getElementById('editForm').style.display = 'block';

    // Fill the form with the selected user's data
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_group_name').value = group_name;
    document.getElementById('edit_group_description').value = group_description;
    document.getElementById('edit_group_image').value = group_image;
}
</script>


  </body>

</html>