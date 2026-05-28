<?php
session_start();
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_socialmedia';

// Database connection
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
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

$message = ""; // Initialize message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['userId'];
    $fullName = $_POST['fullName'];
    $contactNumber = $_POST['contactNumber'];
    $emailId = $_POST['emailId'];
    $birthDay = $_POST['birthDay'];

    // Check if the user ID exists
    $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Optional: handle image upload
        $imageUploaded = false;
        $imageName = '';

        if (isset($_FILES['adminImage']) && $_FILES['adminImage']['error'] === UPLOAD_ERR_OK) {
            $imageTmp = $_FILES['adminImage']['tmp_name'];
            $imageOriginalName = basename($_FILES['adminImage']['name']);
            $imageExtension = pathinfo($imageOriginalName, PATHINFO_EXTENSION);
            $imageName = time() . "_" . uniqid() . "." . $imageExtension;
            $uploadFolder = "upload/";

            if (move_uploaded_file($imageTmp, $uploadFolder . $imageName)) {
                $imageUploaded = true;
                $message .= "✅ Image uploaded successfully.<br>";
            } else {
                $message .= "❌ Failed to upload image.<br>";
            }
        }

        // Update statement depending on whether an image was uploaded
        if ($imageUploaded) {
            $updateStmt = $conn->prepare("UPDATE tbl_admin SET fullName = ?, contactNumber = ?, emailId = ?, birthDay = ?, image = ? WHERE id = ?");
            $updateStmt->bind_param("sssssi", $fullName, $contactNumber, $emailId, $birthDay, $imageName, $userId);
        } else {
            $updateStmt = $conn->prepare("UPDATE tbl_admin SET fullName = ?, contactNumber = ?, emailId = ?, birthDay = ? WHERE id = ?");
            $updateStmt->bind_param("ssssi", $fullName, $contactNumber, $emailId, $birthDay, $userId);
        }

        if ($updateStmt->execute()) {
            $message .= "✅ Profile updated successfully!<br>";
        } else {
            $message .= "❌ Error updating profile: " . $conn->error . "<br>";
        }

        $updateStmt->close();
    } else {
        $message .= "❌ User not found.<br>";
    }

    // Password Reset Section
    if (!empty($_POST['currentPassword']) && !empty($_POST['newPassword']) && !empty($_POST['confirmNewPassword'])) {
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmNewPassword = $_POST['confirmNewPassword'];

        // Fetch user details again
        $stmt = $conn->prepare("SELECT password FROM tbl_admin WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && $user['password'] === $currentPassword) {
            if ($newPassword === $confirmNewPassword) {
                $updatePasswordStmt = $conn->prepare("UPDATE tbl_admin SET password = ? WHERE id = ?");
                $updatePasswordStmt->bind_param("si", $newPassword, $userId);

                if ($updatePasswordStmt->execute()) {
                    $message .= "✅ Password updated successfully!<br>";
                } else {
                    $message .= "❌ Error updating password: " . $conn->error . "<br>";
                }
                $updatePasswordStmt->close();
            } else {
                $message .= "❌ New password and confirm password do not match!<br>";
            }
        } else {
            $message .= "❌ Current password is incorrect!<br>";
        }
    }

    $stmt->close();
    $conn->close();
}

echo $message;
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

    <!-- Date Range CSS -->
    <link rel="stylesheet" href="./socialite-admin/assets/vendor/daterange/daterange.css" />
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
              <span class="ms-2 text-truncate d-lg-block d-none">
			  <?php echo htmlspecialchars($adminName); ?></span>            </a>
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
              <li class="breadcrumb-item" aria-current="page">Settings</li>
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
              <div class="col-xxl-12">
                <div class="card mb-3">
                  <div class="card-body">
                    <div class="custom-tabs-container">
                      <ul class="nav nav-tabs" id="customTab2" role="tablist">
                        <li class="nav-item" role="presentation">
                          <a class="nav-link active" id="tab-oneA" data-bs-toggle="tab" href="#oneA" role="tab"
                            aria-controls="oneA" aria-selected="true">General</a>
                        </li>
                        <li class="nav-item" role="presentation">
                          <a class="nav-link" id="tab-twoA" data-bs-toggle="tab" href="#twoA" role="tab"
                            aria-controls="twoA" aria-selected="false">Settings</a>
                        </li>
                        <li class="nav-item" role="presentation">
                          <a class="nav-link" id="tab-threeA" data-bs-toggle="tab" href="#threeA" role="tab"
                            aria-controls="threeA" aria-selected="false">Credit Cards</a>
                        </li>
                      </ul>
                      <div class="tab-content">
                        <div class="tab-pane fade show active" id="oneA" role="tabpanel">
                          <!-- Row start -->
                          <div class="row gx-3 justify-content-between">
                            <div class="col-sm-8 col-12">
                              <div class="card mb-3">
                                <div class="card-header">
                                  <h5 class="card-title">Personal Details</h5>
                                </div>
                                <div class="card-body">
                                  <!-- Row start -->
								  <form method="POST" enctype="multipart/form-data">
                                 <div class="row gx-3">
									<div class="col-6">
										<!-- User ID Field -->
											<div class="mb-3">
												<label for="userId" class="form-label">User ID</label>
												<input type="text" class="form-control" id="userId" name="userId" placeholder="User ID" required />
											</div>

										<!-- Full Name Field -->
											<div class="mb-3">
												<label for="fullName" class="form-label">Full Name</label>
												<input type="text" class="form-control" id="fullName" name="fullName" placeholder="Full Name" >
											</div>

										<!-- Contact Number Field -->
											<div class="mb-3">
												<label for="contactNumber" class="form-label">Contact</label>
												<input type="text" class="form-control" id="contactNumber" name="contactNumber" placeholder="Contact" >
											</div>
									</div>
									<div class="col-6">
										<!-- Email Field -->
											<div class="mb-3">
												<label for="emailId" class="form-label">Email</label>
												<input type="email" class="form-control" id="emailId" name="emailId" placeholder="Email ID">
											</div>

										<!-- Date of Birth Field -->
											<div class="mb-3">
												<label for="birthDay" class="form-label">Birthday</label>
												<div class="input-group">
												<input type="date" class="form-control" id="birthDay" name="birthDay">
												<span class="input-group-text">
												<i class="bi bi-calendar4"></i>
												</span>
												</div>
											</div>
											<div class="mb-3">
												<label for="adminImage" class="form-label">Upload Admin Image</label>
												<input type="file" class="form-control" id="adminImage" name="adminImage" accept="image/*">
											</div>

									</div>
								</div>

                                  <!-- Row end -->
                                </div>
                              </div>
                            </div>
                            <div class="col-sm-4 col-12">
                              <div class="card mb-3">
                                <div class="card-header">
                                  <h5 class="card-title">Reset Password</h5>
                                </div>
                                <div class="card-body">
                                  <div class="row gx-3">
                                    <div class="col-12">
                                      <!-- Form Field Start -->
                                      <div class="mb-3">
                                        <label for="currentPassword" class="form-label">Current Password</label>
                                        <input type="text" class="form-control" id="currentPassword" name="currentPassword"
                                          placeholder="Enter Current Password" />
                                      </div>
                                      <!-- Form Field Start -->
                                      <div class="mb-3">
                                        <label for="newPassword" class="form-label">New Password</label>
                                        <input type="text" class="form-control" id="newPassword" name="newPassword"
                                          placeholder="Enter New Password" />
                                      </div>
                                      <!-- Form Field Start -->
                                      <div class="mb-3">
                                        <label for="confirmNewPassword" class="form-label">Confirm New Password</label>
                                        <input type="text" class="form-control" id="confirmNewPassword" name="confirmNewPassword"
                                          placeholder="Confirm New Password" />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- Row end -->
                          <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary">
                              Reset
                            </button>
                            <button type="submit" class="btn btn-success" name="update">
                              Update
                            </button>
                          </div>
						  </form>
                        </div>
                        <div class="tab-pane fade" id="twoA" role="tabpanel">
                          <!-- Row start -->
                          <div class="row gx-3">
                            <div class="col-sm-6 col-12">
                              <!-- Card start -->
                              <div class="card">
                                <div class="card-body">
                                  <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      Show desktop notifications
                                      <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="switchOne" />
                                      </div>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      Show email notifications
                                      <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="switchTwo"
                                          checked />
                                      </div>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      Show chat notifications
                                      <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                          id="switchThree" />
                                      </div>
                                    </li>
                                  </ul>
                                </div>
                              </div>
                              <!-- Card end -->
                            </div>
                            <div class="col-sm-6 col-12">
                              <!-- Card start -->
                              <div class="card">
                                <div class="card-body">
                                  <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      Show purchase history
                                      <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="switchFour" />
                                      </div>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      Show orders
                                      <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="switchFive" />
                                      </div>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                      Show alerts
                                      <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="switchSix" />
                                      </div>
                                    </li>
                                  </ul>
                                </div>
                              </div>
                              <!-- Card end -->
                            </div>
                          </div>
                          <!-- Row end -->
                          <div class="d-flex gap-2 mt-4 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary">
                              Cancel
                            </button>
                            <button type="button" class="btn btn-success" name="update">
                              Update
                            </button>
                          </div>
                        </div>
						
                        <div class="tab-pane fade" id="threeA" role="tabpanel">
                          <!-- Row start -->
                          <div class="row gx-3">
                            <div class="col-12">
                              <div class="table-responsive">
                                <table class="table align-middle table-bordered m-0">
                                  <thead>
                                    <tr>
                                      <th>Bank Name</th>
                                      <th>Card Number</th>
                                      <th>Card type</th>
                                      <th>Expiry Date</th>
                                      <th>Credit Balance</th>
                                      <th>Actions</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <tr>
                                      <td>Bank of America</td>
                                      <td>0000 0000 0000 0000</td>
                                      <td>Visa</td>
                                      <td>10/10/2025</td>
                                      <td>$100000</td>
                                      <td>
                                        <div class="form-check form-switch m-0">
                                          <input class="form-check-input" type="checkbox" role="switch"
                                            id="cardActive" />
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td>Citi Group</td>
                                      <td>0000 0000 0000 0000</td>
                                      <td>Master</td>
                                      <td>02/24/2028</td>
                                      <td>$150000</td>
                                      <td>
                                        <div class="form-check form-switch m-0">
                                          <input class="form-check-input" type="checkbox" role="switch" id="cardActive2"
                                            checked />
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td>Capital One</td>
                                      <td>0000 0000 0000 0000</td>
                                      <td>Visa</td>
                                      <td>05/05/2025</td>
                                      <td>$50000</td>
                                      <td>
                                        <div class="form-check form-switch m-0">
                                          <input class="form-check-input" type="checkbox" role="switch" id="cardActive3"
                                            checked />
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td>Axix</td>
                                      <td>0000 0000 0000 0000</td>
                                      <td>Visa</td>
                                      <td>08/20/2027</td>
                                      <td>$100000</td>
                                      <td>
                                        <div class="form-check form-switch m-0">
                                          <input class="form-check-input" type="checkbox" role="switch" id="cardActive4"
                                            checked />
                                        </div>
                                      </td>
                                    </tr>
                                    <tr>
                                      <td>HDFC</td>
                                      <td>0000 0000 0000 0000</td>
                                      <td>Visa</td>
                                      <td>05/08/2029</td>
                                      <td>$90000</td>
                                      <td>
                                        <div class="form-check form-switch m-0">
                                          <input class="form-check-input" type="checkbox" role="switch"
                                            id="cardActive5" />
                                        </div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </div>
                          <!-- Row end -->
                          <div class="d-flex gap-2 mt-4 justify-content-end">
                            <button type="button" class="btn btn-outline-secondary">
                              Cancel
                            </button>
                            <button type="button" class="btn btn-success">
                              Update
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Row end -->

          </div>
          <!-- App body ends -->

          <!-- App footer start -->
          <div class="app-footer">
            <span>© Bootstrap Gallery 2024</span>
          </div>
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
    <script src="./socialite-admin/assets/js/modernizr.js"></script>
    <script src="./socialite-admin/assets/js/moment.min.js"></script>
    <!-- *************
			************ Vendor Js Files *************
		************* -->

    <!-- Overlay Scroll JS -->
    <script src="./socialite-admin/assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js"></script>
    <script src="./socialite-admin/assets/vendor/overlay-scroll/custom-scrollbar.js"></script>

    <!-- Date Range JS -->
    <script src="./socialite-admin/assets/vendor/daterange/daterange.js"></script>
    <script src="./socialite-admin/assets/vendor/daterange/custom-daterange.js"></script>

    <!-- Custom JS files -->
    <script src="./socialite-admin/assets/js/custom.js"></script>
  </body>

</html>