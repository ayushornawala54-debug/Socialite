<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "db_socialmedia");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: form-login.php");
    exit();
}

$email = $_SESSION['user_email'];

// Fetch user details from user_profiles
$stmt = $conn->prepare("SELECT username, bio, email FROM user_profiles WHERE email = ?");
if (!$stmt) {
    die("Error in query preparation: " . $conn->error);
}
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User profile not found.";
    exit();
}
// Fetch username and check subscription status
$stmt = $conn->prepare("SELECT username, email FROM user_profiles WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = $user['username'] ?? '';
$email = $user['email'] ?? '';

// Check if the user is subscribed
$is_subscribed = false;
if ($username) {
    $sub_stmt = $conn->prepare("SELECT id FROM tbl_subscription WHERE username = ?");
    $sub_stmt->bind_param("s", $username);
    $sub_stmt->execute();
    $sub_result = $sub_stmt->get_result();
    $is_subscribed = $sub_result->num_rows > 0;
    $sub_stmt->close();
}
?>
<?php
include 'conn.php'; // Include database connection

$query = "SELECT * FROM products ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);


?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link href="assets/images/favicon.png" rel="icon" type="image/png">

    <!-- title and description-->
    <title>Socialite</title>
    
   
    <!-- css files -->
    <link rel="stylesheet" href="./socialminds/assets/css/tailwind.css">
    <link rel="stylesheet" href="./socialminds/assets/css/style.css">  
	<style>
    .custom-modal {
    backdrop-filter: blur(10px); /* Apply blur effect */
    -webkit-backdrop-filter: blur(10px); /* For Safari */
}
.custom-modal {
    z-index: 9999 !important;
}

	/* Apply a blurred background effect */
body {
    background: url('images/background.jpg') no-repeat center center fixed;
    background-size: cover;
    position: relative;
}
img {
    z-index: 1;
}

body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.1); /* Light overlay */
    backdrop-filter: blur(10px); /* Blur effect */
    z-index: -1;
}

/* Make sections like headers and menus semi-transparent with blur */
.headerWrap-1, .menu-nav, .page-content, .footer-content {
    background: rgba(255, 255, 255, 0.2); /* Semi-transparent white */
    backdrop-filter: blur(15px); /* Adjust blur intensity */
    border-radius: 10px;
    padding: 10px;
}

/* Blur effect for mobile menu */
.nav-mobile {
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(10px);
}

/* Adjust input fields and buttons */
.reservation-fields, button {
    background: rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(5px);
    border: none;
    padding: 10px;
    border-radius: 5px;
    color: #fff;
}

</style>



    <!-- google font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@alpinejs/core@3.x.x/dist/cdn.min.js" defer></script>
	<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
		<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleButton = document.getElementById("darkModeToggle");
        const htmlElement = document.documentElement; // The <html> tag

        // Load saved mode from localStorage
        if (localStorage.getItem("theme") === "dark") {
            htmlElement.classList.add("dark");
        }

        toggleButton.addEventListener("click", function () {
            if (htmlElement.classList.contains("dark")) {
                htmlElement.classList.remove("dark");
                localStorage.setItem("theme", "light"); // Save theme
            } else {
                htmlElement.classList.add("dark");
                localStorage.setItem("theme", "dark"); // Save theme
            }
        });
    });
</script>

</head>
<body class="p-6" x-data="{ showModal: false }">
 
    <div id="wrapper">

        <!-- header -->
        <header class="z-[100] h-[--m-top] fixed top-0 left-0 w-full flex items-center bg-white/80 sky-50 backdrop-blur-xl border-b border-slate-200 dark:bg-dark2 dark:border-slate-800">

            <div class="flex items-center w-full xl:px-6 px-2 max-lg:gap-10">

                <div class="2xl:w-[--w-side] lg:w-[--w-side-sm]">

                    <!-- left -->
                    <div class="flex items-center gap-1"> 

                        <!-- icon menu -->
                        <button uk-toggle="target: #site__sidebar ; cls :!-translate-x-0"
                                class="flex items-center justify-center w-8 h-8 text-xl rounded-full hover:bg-gray-100 xl:hidden dark:hover:bg-slate-600 group"> 
                                <ion-icon name="menu-outline" class="text-2xl group-aria-expanded:hidden"></ion-icon>
                                <ion-icon name="close-outline" class="hidden text-2xl group-aria-expanded:block"></ion-icon>
                        </button>
                        <div id="logo">
                            <a href="feed.php"> 
                                <img src="./socialminds/assets/images/logo.png" alt="" class="w-28 md:block hidden dark:!hidden">
                                <img src="./socialminds/assets/images/logo-light.png" alt="" class="dark:md:block hidden">
                                <img src="./socialminds/assets/images/logo-mobile.png" class="hidden max-md:block w-20 dark:!hidden" alt="">
                                <img src="./socialminds/assets/images/logo-mobile-light.png" class="hidden dark:max-md:block w-20" alt="">
                            </a>
                        </div>
                        
                    </div>

                </div>
                <div class="flex-1 relative">

                    <div class="max-w-[1220px] mx-auto flex items-center">
                        
                        <!-- search -->
                        <div id="search--box" class="xl:w-[680px] sm:w-96 sm:relative rounded-xl overflow-hidden z-20 bg-secondery max-md:hidden w-screen left-0 max-sm:fixed max-sm:top-2 dark:!bg-white/5">
                            <ion-icon name="search" class="absolute left-4 top-1/2 -translate-y-1/2"></ion-icon>
                            <input type="text" placeholder="Search Friends, videos .." class="w-full !pl-10 !font-normal !bg-transparent h-12 !text-sm">
                        </div>  
                        <!-- search dropdown-->
                        <div class="hidden uk- open z-10"
                                uk-drop="pos: bottom-center ; animation: uk-animation-slide-bottom-small;mode:click ">
                            
                                <div class="xl:w-[694px] sm:w-96 bg-white dark:bg-dark3 w-screen p-2 rounded-lg shadow-lg -mt-14 pt-14">
                                    <div class="flex justify-between px-2 py-2.5 text-sm font-medium"> 
                                        <div class=" text-black dark:text-white">Recent</div>
                                        <button type="button" class="text-blue-500">Clear</button>
                                    </div>
                                    <nav class="text-sm font-medium text-black dark:text-white">
                                        <a href="#" class=" relative px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <img src="./socialminds/assets/images/avatars/avatar-2.jpg" class="w-9 h-9 rounded-full"> <div>   <div> Jesse Steeve </div>  <div class="text-xs text-blue-500 font-medium mt-0.5">  Friend </div>   </div> <ion-icon name="close" class="text-base absolute right-3 top-1/2 -translate-y-1/2 "></ion-icon>  </a>  
                                        <a href="#" class=" relative px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <img src="./socialminds/assets/images/avatars/avatar-2.jpg" class="w-9 h-9 rounded-full"> <div>   <div>  Martin Gray </div>  <div class="text-xs text-blue-500 font-medium mt-0.5">  Friend </div>   </div> <ion-icon name="close" class="text-base absolute right-3 top-1/2 -translate-y-1/2 "></ion-icon>  </a>  
                                        <a href="#" class=" relative px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <img src="./socialminds/assets/images/group/group-2.jpg" class="w-9 h-9 rounded-full"> <div>   <div>  Delicious Foods  </div>  <div class="text-xs text-rose-500 font-medium mt-0.5">  Group </div>   </div> <ion-icon name="close" class="text-base absolute right-3 top-1/2 -translate-y-1/2 "></ion-icon>  </a>  
                                        <a href="#" class=" relative px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <img src="./socialminds/assets/images/group/group-1.jpg" class="w-9 h-9 rounded-full"> <div>   <div> Delicious Foods  </div>  <div class="text-xs text-yellow-500 font-medium mt-0.5">  Page </div>   </div> <ion-icon name="close" class="text-base absolute right-3 top-1/2 -translate-y-1/2 "></ion-icon>  </a>  
                                        <a href="#" class=" relative px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <img src="./socialminds/assets/images/avatars/avatar-6.jpg" class="w-9 h-9 rounded-full"> <div>   <div>  John Welim </div>  <div class="text-xs text-blue-500 font-medium mt-0.5">  Friend </div>   </div> <ion-icon name="close" class="text-base absolute right-3 top-1/2 -translate-y-1/2 "></ion-icon>  </a>  
                                        <a href="#" class="hidden relative  px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <ion-icon class="text-2xl" name="search-outline"></ion-icon>  Creative ideas about Business  </a>  
                                        <a href="#" class="hidden relative  px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <ion-icon class="text-2xl" name="search-outline"></ion-icon>  8 Facts About Writting  </a>  
                                    </nav>
                                    <hr class="-mx-2 mt-2 hidden">
                                    <div class="flex justify-end pr-2 text-sm font-medium text-red-500 hidden"> 
                                        <a href="#" class="flex hover:bg-red-50 dark:hover:bg-slate-700 p-1.5 rounded"> <ion-icon name="trash" class="mr-2 text-lg"></ion-icon> Clear your history</a> 
                                    </div>
                                </div>
                                
                        </div>

                        <!-- header icons -->
                        <div class="flex items-center sm:gap-4 gap-2 absolute right-5 top-1/2 -translate-y-1/2 text-black">
										<a href="up.php" class="button">
											<button type="button" class="flex items-center gap-2">
												<svg class="w-6 h-6" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" stroke-linejoin="round" stroke-linecap="round"></path>
												</svg>
												<span class="text">
													Upload
												</span>
											</button>
										</a>

                            <!-- create -->
                            <button type="button" class="sm:p-2 p-1 rounded-full relative sm:bg-secondery dark:text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 max-sm:hidden">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path>
                                  </svg>
                                <ion-icon name="add-circle-outline" class="sm:hidden text-2xl "></ion-icon>
                            </button>
                           
                            <!-- notification -->
                            <button type="button" class="sm:p-2 p-1 rounded-full relative sm:bg-secondery dark:text-white" uk-tooltip="title: Notification; pos: bottom; offset:6">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 max-sm:hidden">
                                    <path d="M5.85 3.5a.75.75 0 00-1.117-1 9.719 9.719 0 00-2.348 4.876.75.75 0 001.479.248A8.219 8.219 0 015.85 3.5zM19.267 2.5a.75.75 0 10-1.118 1 8.22 8.22 0 011.987 4.124.75.75 0 001.48-.248A9.72 9.72 0 0019.266 2.5z" />
                                    <path fill-rule="evenodd" d="M12 2.25A6.75 6.75 0 005.25 9v.75a8.217 8.217 0 01-2.119 5.52.75.75 0 00.298 1.206c1.544.57 3.16.99 4.831 1.243a3.75 3.75 0 107.48 0 24.583 24.583 0 004.83-1.244.75.75 0 00.298-1.205 8.217 8.217 0 01-2.118-5.52V9A6.75 6.75 0 0012 2.25zM9.75 18c0-.034 0-.067.002-.1a25.05 25.05 0 004.496 0l.002.1a2.25 2.25 0 11-4.5 0z" clip-rule="evenodd" />
                                </svg>
                                <div class="absolute top-0 right-0 -m-1 bg-red-600 text-white text-xs px-1 rounded-full">6</div>
                                <ion-icon name="notifications-outline" class="sm:hidden text-2xl"></ion-icon>
                            </button> 
                            <div  class="hidden bg-white pr-1.5 rounded-lg drop-shadow-xl dark:bg-slate-700 md:w-[365px] w-screen border2"
                                uk-drop="offset:6;pos: bottom-right; mode: click; animate-out: true; animation: uk-animation-scale-up uk-transform-origin-top-right ">
                            
                                <!-- heading -->
                                <div class="flex items-center justify-between gap-2 p-4 pb-2">
                                    <h3 class="font-bold text-xl"> Notifications </h3>

                                    <div class="flex gap-2.5"> 
                                        <button type="button" class="p-1 flex rounded-full focus:bg-secondery dark:text-white"> <ion-icon class="text-xl" name="ellipsis-horizontal"></ion-icon> </button>
                                        <div  class="w-[280px] group" uk-dropdown="pos: bottom-right; animation: uk-animation-scale-up uk-transform-origin-top-right; animate-out: true; mode: click; offset:5"> 
                                            <nav class="text-sm"> 
                                                <a href="#"> <ion-icon class="text-xl shrink-0" name="checkmark-circle-outline"></ion-icon>  Mark all as read</a>  
                                                <a href="#"> <ion-icon class="text-xl shrink-0" name="settings-outline"></ion-icon> Notification setting</a>  
                                                <a href="#"> <ion-icon class="text-xl shrink-0" name="notifications-off-outline"></ion-icon> Mute Notification </a>  
                                            </nav>
                                        </div> 
                                    </div>
                                </div>
    
                             

                                <!-- footer -->
                                <a href="#">
                                    <div class="text-center py-4 border-t border-slate-100 text-sm font-medium text-blue-600 dark:text-white dark:border-gray-600">  View Notifications </div>
                                </a>
        
                                <div class="w-3 h-3 absolute -top-1.5 right-3 bg-white border-l border-t rotate-45 max-md:hidden dark:bg-dark3 dark:border-transparent"></div>
                            </div>

                            <!-- messages -->
                            <button type="button" class="sm:p-2 p-1 rounded-full relative sm:bg-secondery dark:text-white" uk-tooltip="title: Messages; pos: bottom; offset:6">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 max-sm:hidden">
                                    <path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0112 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 01-3.476.383.39.39 0 00-.297.17l-2.755 4.133a.75.75 0 01-1.248 0l-2.755-4.133a.39.39 0 00-.297-.17 48.9 48.9 0 01-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97zM6.75 8.25a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H7.5z" clip-rule="evenodd"></path>
                                </svg>
                                <ion-icon name="chatbox-ellipses-outline" class="sm:hidden text-2xl"></ion-icon>
                            </button>
                            <div  class="hidden bg-white pr-1.5 rounded-lg drop-shadow-xl dark:bg-slate-700 md:w-[360px] w-screen border2"
                                uk-drop="offset:6;pos: bottom-right; mode: click; animate-out: true; animation: uk-animation-scale-up uk-transform-origin-top-right ">
                            
                                <!-- heading -->
                                <div class="flex items-center justify-between gap-2 p-4 pb-1">
                                    <h3 class="font-bold text-xl"> Chats </h3>

                                    <div class="flex gap-2.5 text-lg text-slate-900 dark:text-white">
                                        <ion-icon name="expand-outline"></ion-icon>
                                        <ion-icon name="create-outline"></ion-icon>
                                    </div>
                                </div>

                                <div class="relative w-full p-2 px-3 ">
                                    <input type="text" class="w-full !pl-10 !rounded-lg dark:!bg-white/10" placeholder="Search">
                                    <ion-icon name="search-outline" class="dark:text-white absolute left-7 -translate-y-1/2 top-1/2"></ion-icon>
                                </div>
                                
                                
                                </div> 
    

                                <!-- footer -->
                                <a href="#">
                                    <div class="text-center py-4 border-t border-slate-100 text-sm font-medium text-blue-600 dark:text-white dark:border-gray-600">   See all Messages  </div>
                                </a>
        
                                <div class="w-3 h-3 absolute -top-1.5 right-3 bg-white border-l border-t rotate-45 max-md:hidden dark:bg-dark3 dark:border-transparent"></div>
                            </div>
        
                            <!-- profile -->
                            <div  class="rounded-full relative bg-secondery cursor-pointer shrink-0">
                                <img src="./socialminds/assets/images/favicon.png" class="logo">
                            </div>
                            <div  class="hidden bg-white rounded-lg drop-shadow-xl dark:bg-slate-700 w-64 border2"
                                uk-drop="offset:6;pos: bottom-right;animate-out: true; animation: uk-animation-scale-up uk-transform-origin-top-right ">
                                
                                <a href="timeline.php">
    <div class="p-4 py-5 flex items-center gap-4">
        <div class="flex-1">
            <h3 class="md:text-xl text-base font-semibold text-black dark:text-white">
                <?php echo htmlspecialchars($username); ?>
                <?php if ($is_subscribed): ?>
                    <span title="Verified" style="color: #1DA1F2;">✔️</span> <!-- Blue tick emoji -->
                <?php endif; ?>
            </h3>
            <p class="text-sm text-blue-600 mt-1 font-normal">
                <?php echo htmlspecialchars($user['email'] ?? 'Guest'); ?>
            </p>
        </div>
    </div>
</a>


                                <hr class="dark:border-gray-600/60">

                                <nav class="p-2 text-sm text-black font-normal dark:text-white">
                                    <a href="upgrade.php">
                                        <div class="flex items-center gap-2.5 hover:bg-secondery p-2 px-2.5 rounded-md dark:hover:bg-white/10 text-blue-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                            </svg>
                                            Upgrade To Premium 
                                        </div>
                                    </a>  
                                    <a href="setting.php">
                                        <div class="flex items-center gap-2.5 hover:bg-secondery p-2 px-2.5 rounded-md dark:hover:bg-white/10"> 
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                            </svg>
                                            My Billing 
                                        </div>
                                    </a>
                                    <a href="setting.php">
                                        <div class="flex items-center gap-2.5 hover:bg-secondery p-2 px-2.5 rounded-md dark:hover:bg-white/10"> 
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46" />
                                            </svg>
                                            Advatacing
                                        </div>
                                    </a>
                                    <a href="setting.php">
                                        <div class="flex items-center gap-2.5 hover:bg-secondery p-2 px-2.5 rounded-md dark:hover:bg-white/10"> 
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            My Account
                                        </div>
                                    </a>
   
                                    <hr class="-mx-2 my-2 dark:border-gray-600/60">
                                    <a href="form-login.php">
                                        <div class="flex items-center gap-2.5 hover:bg-secondery p-2 px-2.5 rounded-md dark:hover:bg-white/10"> 
                                            <svg class="w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            Log Out 
                                        </div>
                                    </a>
    
                                </nav>

                            </div> 

                            <div class="flex items-center gap-2 hidden">
        
                                <img src="./socialminds/assets/images/avatars/avatar-2.jpg" alt="" class="w-9 h-9 rounded-full shadow">
        
                                <div class="w-20 font-semibold text-gray-600"> Hamse </div>
        
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg> 
        
                            </div> 
        
                        </div>

                    </div> 

                </div>

            </div>

        </header>
    
        <!-- sidebar -->
        <div id="site__sidebar" class="fixed top-0 left-0 z-[99] pt-[--m-top] overflow-hidden transition-transform xl:duration-500 max-xl:w-full max-xl:-translate-x-full">

            <!-- sidebar inner -->
            <div class="p-2 max-xl:bg-white shadow-sm 2xl:w-72 sm:w-64 w-[80%] h-[calc(100vh-64px)] relative z-30 max-lg:border-r dark:max-xl:!bg-slate-700 dark:border-slate-700">
        
                <div class="pr-4" data-simplebar>

                    <nav id="side">
                    
                        <ul>
                            <li class="active">
                                <a href="feed.php">
                                    <img src="./socialminds/assets/images/icons/home.png" alt="feeds" class="w-6">
                                    <span> Feed </span> 
                                </a>
                            </li>
                            <li>
                                <a href="messages.php">
                                    <img src="./socialminds/assets/images/icons/message.png" alt="messages" class="w-5">
                                    <span> messages </span> 
                                </a>
                            </li> 
                            <li>
                                <a href="video.php">
                                    <img src="./socialminds/assets/images/icons/video.png" alt="messages" class="w-6">
                                    <span> video </span> 
                                </a>
                            </li>
                            <li>
                                <a href="event-2.php">
                                    <img src="./socialminds/assets/images/icons/event.png" alt="messages" class="w-6">
                                    <span> event </span> 
                                </a>
                            </li>
                            <li>
                                <a href="groups.php">
                                    <img src="./socialminds/assets/images/icons/group.png" alt="groups" class="w-6">
                                    <span> Groups </span> 
                                </a>
                            </li>
                            <li>
                                <a href="market.php">
                                    <img src="./socialminds/assets/images/icons/market.png" alt="market" class="w-7 -ml-1">
                                    <span> market </span> 
                                </a>
                            </li> 
                         
                           
                    </ul>
                        
                        
        
                    </nav>

                   
        
                    <nav id="side" class="font-medium text-sm text-black border-t pt-3 mt-2 dark:text-white dark:border-slate-800">
                        <div class="px-3 pb-2 text-sm font-medium"> 
                            <div class="text-black dark:text-white">Pages</div> 
                        </div>
        
                        <ul class="mt-2 -space-y-2" 
                            uk-nav="multiple: true">
                
                            <li>
                                <a href="setting.php"> 
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span> Setting </span>                  
                                </a>
                            </li>
                            
                        
                        </ul>
        
                    </nav>
                

                    <div class="text-xs font-medium flex flex-wrap gap-2 gap-y-0.5 p-2 mt-2">
                        <a href="#" class="hover:underline">About</a>
                        <a href="#" class="hover:underline">Blog </a>
                        <a href="#" class="hover:underline">Careers</a>
                        <a href="#" class="hover:underline">Support</a>
                        <a href="#" class="hover:underline">Contact Us </a>
                        <a href="#" class="hover:underline">Developer</a>
                    </div>

                </div>

            </div>

            <!-- sidebar overly -->
            <div id="site__sidebar__overly" 
                class="absolute top-0 left-0 z-20 w-screen h-screen xl:hidden backdrop-blur-sm"
                uk-toggle="target: #site__sidebar ; cls :!-translate-x-0"> 
            </div>
        </div>

        <!-- main contents -->
        <main id="site__main" class="2xl:ml-[--w-side]  xl:ml-[--w-side-sm] p-2.5 h-[calc(100vh-var(--m-top))] mt-[--m-top]">

            <div class="lg:flex 2xl:gap-12 gap-10 2xl:max-w-[1220px] max-w-[1065px] mx-auto" id="js-oversized">

                <div class="flex-1">
                    
                    <div class="lg:max-w-[680px] w-full">

                        <div class="page-heading">
                            
                            <h1 class="page-title test"> Products </h1>
            
                            <nav class="nav__underline">
            
                                <ul class="group" uk-switcher="connect: #group-tabs ; animation: uk-animation-slide-right-medium, uk-animation-slide-left-medium"> 
                               
                                    <li> <a href="#"> Suggestions  </a> </li>
                                    <li> <a href="#"> My products </a> </li>
        
                                </ul> 
            
                            </nav>
            
                            <button type="button" class="absolute right-3 bottom-5"> <div class="w-7 h-7 bg-blue-600 rounded-full shadow grid place-items-center">  <ion-icon name="add" class="text-xl text-white"></ion-icon> </div> </button>
                            
                        </div>
        
                        <!-- feature one slider -->
<div class="max-w-5xl mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4">Market Products</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php while ($row = mysqli_fetch_assoc($result)) { 
                $product_id = $row['id'];

                // Fetch purchase count
                $purchase_query = "SELECT COUNT(*) AS purchase_count FROM purchases WHERE product_id = '$product_id'";
                $purchase_result = mysqli_query($conn, $purchase_query);
                $purchase_count = mysqli_fetch_assoc($purchase_result)['purchase_count'] ?? 0;

                // Fetch average rating
                $rating_query = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE product_id = '$product_id'";
                $rating_result = mysqli_query($conn, $rating_query);
                $avg_rating = round($rating_result->fetch_assoc()['avg_rating'] ?? 0, 1);
            ?>
            
            <div class="card border rounded-lg shadow-md p-4 bg-white" x-data="{ showModal: false }">
                <a href="product-details.php?id=<?php echo $row['id']; ?>">
                    <div class="card-media sm:aspect-[2/1.7] h-36 overflow-hidden">
                        <img src="uploads/<?php echo $row['image']; ?>" class="w-full h-full object-cover rounded-lg" alt="<?php echo $row['product_name']; ?>">
                    </div>
                </a>
                <div class="card-body relative mt-2">
                    <a href="#"><span class="text-teal-600 font-semibold text-xs"> Seller: <?php echo $row['username']; ?></span></a>
                    <p class="card-text block text-black mt-0.5 font-medium"> <?php echo $row['product_name']; ?> </p>
                    <p class="text-sm text-gray-600 mt-1"> <?php echo $row['product_description']; ?> </p>
                    <p class="text-sm text-yellow-500 mt-1"> ⭐ <?php echo $avg_rating > 0 ? $avg_rating . " / 5" : "No ratings yet"; ?></p>
					<div class="absolute top-2 right-2 bg-blue-100 font-medium px-2 py-0.5 rounded-full text-blue-500 text-sm">
                        ₹<?php echo $row['price']; ?>
                    </div>
                </div>

                <!-- Buy Button -->
                <a href="product-details.php?id=<?php echo $product_id; ?>&name=<?php echo urlencode($row['product_name']); ?>&price=<?php echo $row['price']; ?>&img=<?php echo urlencode($row['image']); ?>" class="block mt-3 bg-blue-500 hover:bg-blue-600 text-white text-center py-2 rounded-lg">
                    Buy Now
                </a>

                <!-- Review & Rating Section -->

                <div x-data="{ showCustomModal: false }">
    <button @click="showCustomModal = true" class="mt-3 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg w-full">
        Leave a Review
    </button>

    <!-- Custom Modal -->
    <div x-show="showCustomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 custom-modal" style="display: none;">
        <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
            <button class="absolute top-2 right-2 text-gray-500" @click="showCustomModal = false">&times;</button>
            <h2 class="text-lg font-semibold mb-3">Rate and Review</h2>
            
            <form id="reviewForm-<?php echo $product_id; ?>">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <input type="hidden" name="username" value="<?php echo $_SESSION['username'] ?? 'Guest'; ?>">
                
                <label class="block text-gray-700">Rating:</label>
                <select name="rating" class="w-full p-2 border rounded-lg mt-1">
                    <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                    <option value="4">⭐⭐⭐⭐ - Good</option>
                    <option value="3">⭐⭐⭐ - Average</option>
                    <option value="2">⭐⭐ - Poor</option>
                    <option value="1">⭐ - Terrible</option>
                </select>

                <label class="block mt-3 text-gray-700">Review:</label>
                <textarea name="review_text" class="w-full p-2 border rounded-lg mt-1" rows="3"></textarea>

                <button type="button" onclick="submitReview(<?php echo $product_id; ?>)" class="mt-3 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg w-full">
                    Submit Review
                </button>
            </form>
        </div>
    </div>
</div>


            </div>
            
            <?php } ?>
        </div>
    </div>

    <script>
    async function submitReview(productId) {
    let form = document.getElementById(`reviewForm-${productId}`);
    let formData = new FormData(form);
    let submitButton = form.querySelector("button[type='button']");

    // Disable button to prevent multiple submissions
    submitButton.disabled = true;
    submitButton.textContent = "Submitting...";

    try {
        let response = await fetch('submit_review.php', {
            method: 'POST',
            body: formData
        });

        let data = await response.text();

        // Log response for debugging
        console.log("Server Response:", data);

        // Show success/error message
        alert(data);

        // Close modal using Alpine.js dispatch event
        if (data.toLowerCase().includes("success")) {
            form.closest("[x-data]").dispatchEvent(new CustomEvent('close-modal'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert("An error occurred. Please try again.");
    }

    // Re-enable the submit button
    submitButton.disabled = false;
    submitButton.textContent = "Submit Review";
}

</script>

         
                        
        
                        <!-- categories -->
                        <div class="sm:mt-6 mt-3 flex items-center justify-between border-t pt-3 dark:border-slate-800">
                            <div>
                                <h2 class="text-xl font-semibold text-black"> Categories
                                <p class="font-normal text-sm text-gray-500 leading-6"> Find a group by browsing top categories. </p>
                            </div> 
                        </div>
                        
                        <!-- categories slider-->
                        <div class="relative" tabindex="-1" uk-slider="auto play: true;finite: true">
        
                            <div class="py-5 uk-slider-container">
                               
                                <ul class="uk-slider-items w-[calc(100%+12px)]" uk-scrollspy="target: > li; cls: uk-animation-scale-up; delay: 20;repeat:true">
        
                                    <li class="pr-3 md:w-1/3 w-auto" uk-scrollspy-class="uk-animation-fade">
                                        <div class="p-4 flex gap-3 justify-between bg-sky-600 rounded-md">
                                            <div>
                                                <h4 class="font-medium !text-white whitespace-nowrap"> All Categories </h4>
                                                <p  class="font-medium !text-white/80 text-sm mt-1 whitespace-nowrap"> 14 product </p>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-white shrink-0">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z" />
                                              </svg>
                                              
                                        </div>
                                    </li>
                                    <li class="pr-3 md:w-1/3 w-auto" uk-scrollspy-class="uk-animation-fade">
                                        <div class="p-4 flex gap-3 item-center justify-between bg-rose-500 rounded-md">
                                            <div>
                                                <h4 class="font-medium !text-white">  Building  </h4>
                                                <p  class="font-medium !text-white/80 text-sm mt-1"> 23 product </p>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-white shrink-0">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                                              </svg>
                                              
                                        </div>
                                    </li>
                                    <li class="pr-3 md:w-1/3 w-auto" uk-scrollspy-class="uk-animation-fade">
                                        <div class="p-4 flex gap-3 item-center justify-between bg-teal-600 rounded-md">
                                            <div>
                                                <h4 class="font-medium !text-white">  Life style </h4>
                                                <p  class="font-medium !text-white/80 text-sm mt-1"> 32 product </p>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-white">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 011.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 00-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 010 9.424m-4.138-5.976a3.736 3.736 0 00-.88-1.388 3.737 3.737 0 00-1.388-.88m2.268 2.268a3.765 3.765 0 010 2.528m-2.268-4.796a3.765 3.765 0 00-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 01-1.388.88m2.268-2.268l4.138 3.448m0 0a9.027 9.027 0 01-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0l-3.448-4.138m3.448 4.138a9.014 9.014 0 01-9.424 0m5.976-4.138a3.765 3.765 0 01-2.528 0m0 0a3.736 3.736 0 01-1.388-.88 3.737 3.737 0 01-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 01-1.652-1.306 9.027 9.027 0 01-1.306-1.652m0 0l4.138-3.448M4.33 16.712a9.014 9.014 0 010-9.424m4.138 5.976a3.765 3.765 0 010-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 011.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 00-1.652 1.306A9.025 9.025 0 004.33 7.288" />
                                            </svg>
                                        </div>
                                    </li>
                                    <li class="pr-3 md:w-1/3 w-auto" uk-scrollspy-class="uk-animation-fade">
                                        <div class="p-4 flex gap-3 item-center justify-between bg-sky-600 rounded-md">
                                            <div>
                                                <h4 class="font-medium !text-white">  Electronic </h4>
                                                <p  class="font-medium !text-white/80 text-sm mt-1"> 14 product </p>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-white shrink-0">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.098 19.902a3.75 3.75 0 005.304 0l6.401-6.402M6.75 21A3.75 3.75 0 013 17.25V4.125C3 3.504 3.504 3 4.125 3h5.25c.621 0 1.125.504 1.125 1.125v4.072M6.75 21a3.75 3.75 0 003.75-3.75V8.197M6.75 21h13.125c.621 0 1.125-.504 1.125-1.125v-5.25c0-.621-.504-1.125-1.125-1.125h-4.072M10.5 8.197l2.88-2.88c.438-.439 1.15-.439 1.59 0l3.712 3.713c.44.44.44 1.152 0 1.59l-2.879 2.88M6.75 17.25h.008v.008H6.75v-.008z" />
                                            </svg>
                                        </div>
                                    </li>
                                    <li class="pr-3 md:w-1/3 w-auto" uk-scrollspy-class="uk-animation-fade">
                                        <div class="p-4 flex gap-3 item-center justify-between bg-pink-600 rounded-md">
                                            <div>
                                                <h4 class="font-medium !text-white"> Building </h4>
                                                <p  class="font-medium !text-white/80 text-sm mt-1"> 16 product </p>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-white shrink-0">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
                                              </svg>
                                              
                                        </div>
                                    </li>
                                </ul>
         
                                <!-- slide nav -->
                                <div class="max-md:hidden">
        
                                    <button type="button" class="absolute -translate-y-1/2 bg-white shadow rounded-full top-1/2 -left-3.5 grid w-8 h-8 place-items-center dark:bg-dark3" uk-slider-item="previous"> <ion-icon name="chevron-back" class="text-2xl"></ion-icon></button>
                                    <button type="button" class="absolute -right-2 -translate-y-1/2 bg-white shadow rounded-full top-1/2 grid w-8 h-8 place-items-center dark:bg-dark3" uk-slider-item="next"> <ion-icon name="chevron-forward" class="text-2xl"></ion-icon> </button>
        
                                </div>
                                
                                <!-- slide nav dots-->
                                <div class="flex justify-center">
                                    <ul class="inline-flex flex-wrap justify-center my-5 gap-2 uk-dotnav uk-slider-nav"> </ul>
                                </div>
        
                            </div>
                            
                        </div>
        
                        <!-- Products title -->
                        <div class="sm:my-6 my-3 flex items-center justify-between border-t pt-3 dark:border-slate-800">
                            <div>
                                <h2 class="text-xl font-semibold text-black"> Products </h2>
                                <p class="font-normal text-sm text-gray-500 leading-6 hidden"> Find a group by browsing top categories. </p>
                            </div>
                            <a href="#" class="text-blue-500 sm:block hidden text-sm"> See all </a>
                        </div>
        
                        <!-- list products -->
                        <div class="grid sm:grid-cols-3 grid-cols-2 gap-3" uk-scrollspy="target: > div; cls: uk-animation-scale-up; delay: 100 ;repeat: true">
                            
                           <?php
$conn = new mysqli("localhost", "root", "", "db_socialmedia");
$result = $conn->query("SELECT * FROM ad_product");
?>

<?php while ($row = $result->fetch_assoc()): ?>
<div class="card uk-transition-toggle">
    <a href="product-view.php?id=<?= $row['id'] ?>">
        <div class="card-media sm:aspect-[2/1.7] h-36">
            <img src="<?= $row['image'] ?>" alt="">
            <div class="card-overly"></div> 
        </div> 
    </a>
    <div class="card-body flex justify-between">
        <div class="flex-1">
            <p class="card-text text-black font-medium line-clamp-1"><?= htmlspecialchars($row['name']) ?></p>
            <div class="text-xs line-clamp-1 mt-1"><?= htmlspecialchars($row['description']) ?></div>
        </div>
        <h4 class="card-title"> ₹<?= $row['price'] ?> </h4>
    </div>  
    <div class="absolute w-full bottom-0 bg-white/20 backdrop-blur-sm uk-transition-slide-bottom-small max-xl:h-full z-[2] flex flex-col justify-center">
        <div class="flex gap-3 py-4 px-3">
            <a href="product-view.php?id=<?= $row['id'] ?>" class="button bg-primary text-white flex-1 text-center">Buy</a>
			
            <a href="product-view.php?id=<?= $row['id'] ?>" class="button border bg-white !w-auto text-center">View</a>
        </div>
    </div> 
</div>
<?php endwhile; ?>

        
                        </div>
    
                        <div class="flex justify-center my-6">
                            <button type="button" class="bg-white py-2 px-5 rounded-full shadow-md font-semibold text-sm dark:bg-dark2">Load more...</button>
                        </div>

                    </div>

     
                </div>
    
                <!-- sidebar -->
                <div class="2xl:w-[380px] lg:w-[330px] w-full">
    
                    <div  class="lg:space-y-6 space-y-4 lg:pb-8 max-lg:grid sm:grid-cols-2 max-lg:gap-6" 
                          uk-sticky="media: 1024; end: #js-oversized; offset: 80">

                    <!-- peaple you might know -->
              

                    <!-- latest marketplace items -->
                    <div class="box p-5 px-6 border1 dark:bg-dark2">
                        
                        <div class="flex justify-between text-black dark:text-white">
                            <h3 class="font-bold text-base"> Premium Products </h3>
                            <button type="button"> <ion-icon name="sync-outline" class="text-xl"></ion-icon> </button>
                        </div>

                        <div class="relative capitalize font-medium text-sm text-center mt-4 mb-2" tabindex="-1" uk-slider="autoplay: true;finite: true">

                            <div class="overflow-hidden uk-slider-container">
    <ul class="-ml-2 uk-slider-items w-[calc(100%+0.5rem)]">

        <?php
        // Database connection
        $conn = new mysqli("localhost", "root", "", "db_socialmedia");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch products from the 'products' table
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <li class="w-1/2 pr-2">
                    <a href="product-details.php?id=<?= $row['id'] ?>">
                        <div class="relative overflow-hidden rounded-lg">
                            <div class="relative w-full h-40">
                                <img src="uploads/<?php echo $row['image']; ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="object-cover w-full h-full inset-0">
                            </div>
                            <div class="absolute right-0 top-0 m-2 bg-white/60 rounded-full py-0.5 px-2 text-sm font-semibold dark:bg-slate-800/60">
                                ₹<?= $row['price'] ?>
                            </div>
                        </div>
                        <div class="mt-3 w-full"><?= htmlspecialchars($row['product_name']) ?></div>
                    </a>
                </li>
                <?php
            }
        } else {
            echo "<li>No products available.</li>";
        }

        $conn->close();
        ?>

    </ul>

    <button type="button" class="absolute bg-white rounded-full top-16 -left-4 grid w-9 h-9 place-items-center shadow dark:bg-dark3" uk-slider-item="previous">
        <ion-icon name="chevron-back" class="text-2xl"></ion-icon>
    </button>
    <button type="button" class="absolute -right-4 bg-white rounded-full top-16 grid w-9 h-9 place-items-center shadow dark:bg-dark3" uk-slider-item="next">
        <ion-icon name="chevron-forward" class="text-2xl"></ion-icon>
    </button>
</div>

                        
                        </div>


                    </div>
                    
                   

                </div>
                </div> 
                
            </div>
            
        </main>

    </div>

   
        </div>
        
    </div>

    <!-- Javascript  -->
    <script src="./socialminds/assets/js/uikit.min.js"></script>
    <script src="./socialminds/assets/js/simplebar.js"></script>
 
 
    <!-- Ion icon -->
	<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>


</body>

</html>