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
              <a class="dropdown-toggle d-flex px-3 py-4 position-relative" href="#!" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-grid fs-4 lh-1 text-secondary"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-end shadow-lg">
                <!-- Row start -->
                <div class="d-flex gap-2 m-2">
                  <a href="javascript:void(0)" class="g-col-4 p-2 border rounded-2">
                    <img src="./socialite-admin/assets/images/brand-behance.svg" class="img-3x" alt="Admin Themes" />
                  </a>
                  <a href="javascript:void(0)" class="g-col-4 p-2 border rounded-2">
                    <img src="./socialite-admin/assets/images/brand-gatsby.svg" class="img-3x" alt="Admin Themes" />
                  </a>
                  <a href="javascript:void(0)" class="g-col-4 p-2 border rounded-2">
                    <img src="./socialite-admin/assets/images/brand-google.svg" class="img-3x" alt="Admin Themes" />
                  </a>
                  <a href="javascript:void(0)" class="g-col-4 p-2 border rounded-2">
                    <img src="./socialite-admin/assets/images/brand-bitcoin.svg" class="img-3x" alt="Admin Themes" />
                  </a>
                  <a href="javascript:void(0)" class="g-col-4 p-2 border rounded-2">
                    <img src="./socialite-admin/assets/images/brand-dribbble.svg" class="img-3x" alt="Admin Themes" />
                  </a>
                </div>
                <!-- Row end -->
              </div>
            </div>
            <div class="dropdown border-start">
              <a class="dropdown-toggle d-flex px-3 py-4 position-relative" href="#!" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-exclamation-triangle fs-4 lh-1 text-secondary"></i>
                <span class="count-label warning"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-end shadow-lg">
                <h5 class="fw-semibold px-3 py-2 text-primary">
                  Notifications
                </h5>
                <div class="dropdown-item">
                  <div class="d-flex py-2 border-bottom">
                    <div class="icon-box md bg-success rounded-circle me-3">
                      <i class="bi bi-exclamation-triangle text-white fs-4"></i>
                    </div>
                    <div class="m-0">
                      <h6 class="mb-1 fw-semibold">Rosalie Deleon</h6>
                      <p class="mb-1">You have new order.</p>
                      <p class="small m-0 text-secondary">30 mins ago</p>
                    </div>
                  </div>
                </div>
                <div class="dropdown-item">
                  <div class="d-flex py-2 border-bottom">
                    <div class="icon-box md bg-danger rounded-circle me-3">
                      <i class="bi bi-exclamation-octagon text-white fs-4"></i>
                    </div>
                    <div class="m-0">
                      <h6 class="mb-1 fw-semibold">Donovan Stuart</h6>
                      <p class="mb-1">Membership has been expired.</p>
                      <p class="small m-0 text-secondary">2 days ago</p>
                    </div>
                  </div>
                </div>
                <div class="dropdown-item">
                  <div class="d-flex py-2">
                    <div class="icon-box md bg-warning rounded-circle me-3">
                      <i class="bi bi-exclamation-square text-white fs-4"></i>
                    </div>
                    <div class="m-0">
                      <h6 class="mb-1 fw-semibold">Roscoe Richards</h6>
                      <p class="mb-1">Payment pending. Pay now.</p>
                      <p class="small m-0 text-secondary">3 days ago</p>
                    </div>
                  </div>
                </div>
                <div class="d-grid mx-3 my-1">
                  <a href="javascript:void(0)" class="btn btn-primary">View all</a>
                </div>
              </div>
            </div>
            <div class="dropdown border-start">
              <a class="dropdown-toggle d-flex px-3 py-4 position-relative" href="#!" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell fs-4 lh-1 text-secondary"></i>
                <span class="count-label info"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-end shadow-lg">
                <h5 class="fw-semibold px-3 py-2 text-primary">Updates</h5>
                <div class="dropdown-item">
                  <div class="d-flex py-2 border-bottom">
                    <div class="icon-box md bg-success rounded-circle me-3">
                      <span class="fw-bold text-white">DS</span>
                    </div>
                    <div class="m-0">
                      <h6 class="mb-1 fw-semibold">Douglass Shaw</h6>
                      <p class="mb-1">
                        Membership has been ended.
                      </p>
                      <p class="small m-0 text-secondary">Today, 07:30pm</p>
                    </div>
                  </div>
                </div>
                <div class="dropdown-item">
                  <div class="d-flex py-2 border-bottom">
                    <div class="icon-box md bg-danger rounded-circle me-3">
                      <span class="fw-bold text-white">WG</span>
                    </div>
                    <div class="m-0">
                      <h6 class="mb-1 fw-semibold">Willie Garrison</h6>
                      <p class="mb-1">
                        Congratulate, James for new job.
                      </p>
                      <p class="small m-0 text-secondary">Today, 08:00pm</p>
                    </div>
                  </div>
                </div>
                <div class="dropdown-item">
                  <div class="d-flex py-2">
                    <div class="icon-box md bg-warning rounded-circle me-3">
                      <span class="fw-bold text-white">TJ</span>
                    </div>
                    <div class="m-0">
                      <h6 class="mb-1 fw-semibold">Terry Jenkins</h6>
                      <p class="mb-1">
                        Lewis added new schedule release.
                      </p>
                      <p class="small m-0 text-secondary">Today, 09:30pm</p>
                    </div>
                  </div>
                </div>
                <div class="d-grid mx-3 my-1">
                  <a href="javascript:void(0)" class="btn btn-primary">View all</a>
                </div>
              </div>
            </div>
            <div class="dropdown border-start">
              <a class="dropdown-toggle d-flex px-3 py-4 position-relative" href="#!" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-envelope-open fs-4 lh-1 text-secondary"></i>
                <span class="count-label"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-end shadow-lg">
                <h5 class="fw-semibold px-3 py-2 text-primary">Messages</h5>
                <div class="dropdown-item">
                  <div class="d-flex py-2 border-bottom">
                    <img src="./socialite-admin/assets/images/user3.png" class="img-3x me-3 rounded-5" alt="Admin Theme" />
                    <div class="m-0">
                      <h6 class="mb-1 fw-semibold">Angelia Payne</h6>
                      <p class="mb-1">
                        Membership has been ended.
                      </p>
                      <p class="small m-0 text-secondary">Today, 07:30pm</p>
                    </div>
                  </div>
                </div>
                <div class="dropdown-item">
                  <div class="d-flex py-2 border-bottom">
                    <img src="./socialite-admin/assets/images/user1.png" class="img-3x me-3 rounded-5" alt="Admin Theme" />
                    <div class="m-0">
                      <h6 class="mb-1 fw-semibold">Clyde Fowler</h6>
                      <p class="mb-1">
                        Congratulate, James for new job.
                      </p>
                      <p class="small m-0 text-secondary">Today, 08:00pm</p>
                    </div>
                  </div>
                </div>
                <div class="dropdown-item">
                  <div class="d-flex py-2">
                    <img src="./socialite-admin/assets/images/user4.png" class="img-3x me-3 rounded-5" alt="Admin Theme" />
                    <div class="m-0">
                      <h6 class="mb-1 fw-semibold">Sophie Michiels</h6>
                      <p class="mb-1">
                        Lewis added new schedule release.
                      </p>
                      <p class="small m-0 text-secondary">Today, 09:30pm</p>
                    </div>
                  </div>
                </div>
                <div class="d-grid mx-3 my-1">
                  <a href="javascript:void(0)" class="btn btn-primary">View all</a>
                </div>
              </div>
            </div>
          </div>
          <div class="dropdown ms-2">
            <a id="userSettings" class="dropdown-toggle d-flex py-2 align-items-center text-decoration-none" href="#!"
              role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="./socialite-admin/assets/images/om.jpg" class="rounded-2 img-3x" alt="Bootstrap Gallery" />
              <span class="ms-2 text-truncate d-lg-block d-none">Om Patel</span>
            </a>
            <div class="dropdown-menu dropdown-menu-end shadow-lg">
              <div class="header-action-links mx-3 gap-2">
                <a class="dropdown-item" href="profile.php"><i class="bi bi-person text-primary"></i>Profile</a>
                <a class="dropdown-item" href="settings.php"><i class="bi bi-gear text-danger"></i>Settings</a>
                <a class="dropdown-item" href="widgets.php"><i class="bi bi-box text-success"></i>Widgets</a>
              </div>
              <div class="mx-3 mt-2 d-grid">
                <a href="login.php" class="btn btn-primary btn-sm">Logout</a>
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

          <!-- Sidebar profile starts -->
          <div class="shop-profile">
            
          </div>
          <!-- Sidebar profile ends -->

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
                  <span class="menu-text">Post</span>
                </a>
              </li>
              <li>
                <a href="comment.php">
                  <i class="bi bi-border-all"></i>
                  <span class="menu-text">Comment</span>
                </a>
              </li>
              <li>
                <a href="subscribers.php">
                  <i class="bi bi-check-circle"></i>
                  <span class="menu-text">Subscribers</span>
                </a>
              </li>
			   <li>
                <a href="product.php">
                  <i class="bi bi-check-circle"></i>
                  <span class="menu-text">Products</span>
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
                    <a href="form-login.php">Login</a>
                  </li>
                  <li>
                    <a href="form-register.php">Signup</a>
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

        <!-- App container starts -->
        <div class="app-container">

          <!-- App hero header starts -->
          <div class="app-hero-header d-flex align-items-start">

            <!-- Breadcrumb start -->
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <i class="bi bi-house lh-1"></i>
                <a href="admin_feed.php" class="text-decoration-none">Home</a>
              </li>
              <li class="breadcrumb-item" aria-current="page">Profile</li>
            </ol>
            <!-- Breadcrumb end -->

            <!-- Sales stats start -->
            <div class="ms-auto d-lg-flex d-none flex-row">
              <div class="d-flex flex-row gap-2">
                <button class="btn btn-sm btn-primary">Today</button>
                <button class="btn btn-sm btn-white">7d</button>
                <button class="btn btn-sm btn-white">2w</button>
                <button class="btn btn-sm btn-white">1m</button>
                <button class="btn btn-sm btn-white">3m</button>
                <button class="btn btn-sm btn-white">6m</button>
                <button class="btn btn-sm btn-white">1y</button>
              </div>
            </div>
            <!-- Sales stats end -->

          </div>
          <!-- App Hero header ends -->

          <!-- App body starts -->
          <div class="app-body">

            <!-- Row start -->
            
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

    <!-- Apex Charts -->
    <script src="./socialite-admin/assets/vendor/apex/apexcharts.min.js"></script>
    <script src="./socialite-admin/assets/vendor/apex/custom/profile/sales.js"></script>

    <!-- Custom JS files -->
    <script src="./socialite-admin/assets/js/custom.js"></script>
  </body>

</html>