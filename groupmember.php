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
    $conn->query("DELETE FROM tbl_groups WHERE id = $deleteID");
    header("Location: groupmember.php");
    exit();
}


// UPDATE Operation
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $group_name = $conn->real_escape_string($_POST['group_name']);
    $group_description = $conn->real_escape_string($_POST['group_description']);
    
    $conn->query("UPDATE tbl_groups 
                  SET  group_name='$group_name', group_description='$group_description' 
                  WHERE id=$id");
    header("Location: groupmember.php");
    exit();
}

// Search Operation
$searchQuery = '';
if (isset($_GET['search'])) {
    $searchQuery = $conn->real_escape_string($_GET['search']);
}

$query = "SELECT id, group_name, group_description, group_image, created_by, created_at FROM tbl_groups WHERE id LIKE '%$searchQuery%'";
$result = $conn->query($query);
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
	.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 1000; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto; /* 15% from the top and centered */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Could be more or less, depending on screen size */
    border-radius: 5px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.confirm-button {
    background-color: #f44336; /* Red */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
}

.cancel-button {
    background-color: #2196F3; /* Blue */
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    cursor: pointer;
}

.confirm-button:hover,
.cancel-button:hover {
    opacity: 0.8;
}
        .user-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    font-family: Arial, sans-serif;
}

.user-table thead {
    background-color: #4CAF50;
    color: white;
}

.user-table th, .user-table td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}

.user-table tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

.user-table tbody tr:hover {
    background-color: #e0e0e0;
}

.user-table .delete-button {
    color: #f44336;
    text-decoration: none;
    font-weight: bold;
}

.user-table .edit-button {
    background-color: #2196F3;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
}

.user-table .edit-button:hover {
    background-color: #1976D2;
}

.user-table .delete-button:hover {
    text-decoration: underline;
}
    
	#editForm {
    display: none; /* Initially hidden */
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    background-color: #f9f9f9;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

#editForm h3 {
    margin-bottom: 15px;
    color: #333;
}

#editForm label {
    font-weight: bold;
}

#editForm input[type="text"],
#editForm input[type="email"] {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    margin-bottom: 15px;
}

#editForm button {
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
}

#editForm button[type="submit"] {
    background-color: #4CAF50;
    color: white;
    border: none;
}

#editForm button[type="button"] {
    background-color: #f44336;
    color: white;
    border: none;
    margin-left: 10px;
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
			<h2>Manage Groups</h2>
			<div>
				<form method="GET" action="groupmember.php">
				<input type="text" name="search" placeholder="Search by username" required>
				<button type="submit">Search</button>
				</form>
			</div>
<table class="user-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Group Name</th>
            <th>Group Description</th>
            <th>Group Image</th>
            <th>Created By</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="userTableBody">
        <?php if ($result->num_rows > 0) : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['group_name']; ?></td>
                    <td><?= $row['group_description']; ?></td>
                    <td><img src="/sem6/uploads/groups/<?= htmlspecialchars($row['group_image']); ?>" alt="Product Image" width="100"></td>
                    <td><?= $row['created_by']; ?></td>
                    <td><?= $row['created_at']; ?></td>
                    <!-- Edit Button inside Table (Fixed) -->
<td>
    <a href="groupmember.php?delete_id=<?= $row['id']; ?>" onclick="return confirm('Are you sure?')" class="delete-button">Delete</a> |
    <button onclick="editUser(
        '<?= $row['id']; ?>',  
        '<?= $row['group_name']; ?>', 
        '<?= $row['group_description']; ?>')" class="edit-button">
        Edit
    </button>
</td>

                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="7">No users found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Update Form (Hidden Initially) -->
<div id="editForm" style="display: none; padding: 20px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
    <h3 style="margin-bottom: 15px; color: #333;">Edit User</h3>
    <form method="POST" action="groupmember.php">
        <input type="hidden" id="edit_id" name="id">
        
        <div style="margin-bottom: 15px;">
            <label for="edit_group_name" style="font-weight: bold;">Group Name:</label>
            <input type="text" id="edit_group_name" name="group_name" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        
        <div style="margin-bottom: 15px;">
            <label for="edit_group_description" style="font-weight: bold;">Group Description:</label>
            <input type="text" id="edit_group_description" name="group_description" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        
       <div>
            <button type="submit" name="update" style="background-color: #4CAF50; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer;">Update</button>
            <button type="button" onclick="document.getElementById('editForm').style.display='none'" style="background-color: #f44336; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; margin-left: 10px;">Cancel</button>
        </div>
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
        document.getElementById('edit_group_name').value = group_name;
        document.getElementById('edit_group_description').value = group_description;
    }
</script>

  </body>

</html>