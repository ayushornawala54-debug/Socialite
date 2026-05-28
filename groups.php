<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "db_socialmedia");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: form-login.php");
    exit();
}

$email = $_SESSION['user_email'];

// Fetch user details
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
    echo "❌ User profile not found.";
    exit();
}

// Fetch all groups with follower count
$suggestionsQuery = "SELECT g.id, g.group_name, g.group_description, g.group_image, 
                (SELECT COUNT(*) FROM tbl_group_followers WHERE group_id = g.id) AS follower_count
                FROM tbl_groups g
                ORDER BY g.created_at DESC";

$suggestionsResult = $conn->query($suggestionsQuery);


$myGroupsQuery = "SELECT g.id, g.group_name, g.group_description, g.group_image,
                (SELECT COUNT(*) FROM tbl_group_followers WHERE group_id = g.id) AS follower_count
                FROM tbl_groups g
                WHERE g.created_by = (SELECT id FROM tbl_register WHERE email = ?)
                ORDER BY g.created_at DESC";

$stmt = $conn->prepare($myGroupsQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$myGroupsResult = $stmt->get_result();

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

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link href="./socialminds/assets/images/favicon.png" rel="icon" type="image/png">

    <!-- title and description-->
    <title>Socialite</title>
    
   
    <!-- css files -->
    <link rel="stylesheet" href="./socialminds/assets/css/tailwind.css">
    <link rel="stylesheet" href="./socialminds/assets/css/style.css">   
    
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
 	 <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
        .card { border: 1px solid #ddd; border-radius: 10px; overflow: hidden; background: #fff; }
        .card img { width: 100%; height: 150px; object-fit: cover; }
        .card-body { padding: 15px; }
        .button { display: inline-block; padding: 8px 12px; border: none; cursor: pointer; text-align: center; border-radius: 5px; }
        .join { background-color: #007bff; color: #fff; }
        .view { background-color: #28a745; color: #fff; }
    </style>
	<style>
	.hidden {
    display: none;
}

.fixed {
    position: fixed;
}

.inset-0 {
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}
.button5 {background-color: #f44336;} /* Red */
	</style>
</head>
<body>
 
    <div id="wrapper">

        <!-- header -->
        <header class="z-[100] h-[--m-top] fixed top-0 left-0 w-full flex items-center bg-white/80 sky-50 backdrop-blur-xl border-b border-slate-200 dark:bg-dark2 dark:border-slate-800">

            <div class="flex items-center w-full xl:px-6 px-2 max-lg:gap-10">

                <div class="2xl:w-[--w-side] lg:w-64">

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
                            <!-- create -->
                            <button type="button" onclick="window.location.href='add_group.php'" class="sm:p-2 p-1 rounded-full relative sm:bg-secondery dark:text-white">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 max-sm:hidden">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
    </svg>
    <ion-icon name="add-circle-outline" class="sm:hidden text-2xl"></ion-icon>
</button>

                        
    
                           

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
                                <a href="#">
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
        <main id="site__main" class="2xl:ml-[--w-side]  xl:ml-[--w-side-sm] py-10 p-2.5 h-[calc(100vh-var(--m-top))] mt-[--m-top]">

            
            <div class="2xl:max-w-[1220px] max-w-[1065px] mx-auto">

          
                <div class="page-heading">
                    
                    <h1 class="page-title"> Groups </h1>
    
                    <nav class="nav__underline">
    
                        <ul class="group" uk-switcher="connect: #group-tabs ; animation: uk-animation-slide-right-medium, uk-animation-slide-left-medium"> 
                       
                            <li> <a href="#" > Suggestions  </a> </li>
                            <li> <a href="#"> My groups </a> </li>
                            
                        </ul> 
    
                    </nav>
    
                </div>

                <!-- group list tabs -->
                <div class="uk-switcher" id="group-tabs">
                    
                    <!-- card layout 1 -->
					<div class="grid">

    <?php while ($group = $suggestionsResult->fetch_assoc()) { ?>
        <div class="card">
            <a href="#">
                <div class="card-media h-24">
                    <img src="<?php echo !empty($group['group_image']) ? htmlspecialchars($group['group_image']) : "assets/default-group.jpg"; ?>" alt="Group Image">


                    <div class="card-overly"></div>
                </div>
            </a> 
            <div class="card-body">
                <a href="#">
                    <h4 class="card-title"><?php echo htmlspecialchars($group['group_name']); ?></h4>
                </a> 
                <div class="card-text"> 
                    <div class="card-list-info font-normal mt-1">
                        <div><?php echo $group['follower_count']; ?> members</div>
                    </div> 
                </div>
                <div class="flex gap-2 mt-3">
                    <button type="button" class="button bg-primary text-white flex-1" onclick="joinGroup(<?php echo $group['id']; ?>)">Join</button>
                    <a href="#" class="button bg-secondary !w-auto">View</a> 
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<!-- My Groups Section -->
<div class="grid mt-6">

    <?php while ($group = $myGroupsResult->fetch_assoc()) { ?>
        <div class="card">
            <a href="timeline-group.php?id=<?php echo $group['id']; ?>">
                <div class="card-media h-24">
                    <img src="<?php echo !empty($group['group_image']) ? htmlspecialchars($group['group_image']) : "assets/default-group.jpg"; ?>" alt="Group Image">


                    <div class="card-overly"></div>
                </div>
            </a> 
            <div class="card-body">
                <a href="timeline-group.php?id=<?php echo $group['id']; ?>">
                    <h4 class="card-title"><?php echo htmlspecialchars($group['group_name']); ?></h4>
                </a> 
                <div class="card-text"> 
                    <div class="card-list-info font-normal mt-1">
                        <div><?php echo $group['follower_count']; ?> members</div>
                    </div> 
                </div>
                <div class="flex gap-2 mt-3">
                    <a href="edit_group.php?id=<?php echo $group['id']; ?>" class="button bg-primary text-white flex-1">Edit</a>
                    <button type="button" class="button button5" onclick="deleteGroup(<?php echo $group['id']; ?>)">Delete</button>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

                    
         
                </div>
                
                <!-- category title -->
                <div class="sm:my-6 my-3 flex items-center justify-between">
                    <div>
                        <h2 class="md:text-lg text-base font-semibold text-black"> Categories </h2>
                        <p class="font-normal text-sm text-gray-500 leading-6"> Find a group by browsing top categories. </p>
                    </div>
                    <a href="#" class="text-blue-500 sm:block hidden text-sm"> See all </a>
                </div>
                   
                <!-- group  slider -->
                <div tabindex="-1" uk-slider="finite:true">
    
                    <div class="uk-slider-container pb-1">
                       
                        <ul class="uk-slider-items grid-small">
                            
                            <li class="md:w-1/5 sm:w-1/3 w-1/2">
                                <a href="#">  
                                    <div class="relative rounded-lg overflow-hidden">
                                        <img src="./socialminds/assets/images/events/listing-5.jpg" alt="" class="h-36 w-full object-cover">
                                        <div class="w-full bottom-0 absolute left-0 bg-gradient-to-t from-black/60 pt-10">
                                            <div class="text-white p-5 text-lg leading-3"> Shopping </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="md:w-1/5 sm:w-1/3 w-1/2">
                                <a href="#">  
                                    <div class="relative rounded-lg overflow-hidden">
                                        <img src="./socialminds/assets/images/category/health.jpg" alt="" class="h-36 w-full object-cover">
                                        <div class="w-full bottom-0 absolute left-0 bg-gradient-to-t from-black/60 pt-10">
                                            <div class="text-white p-5 text-lg leading-3"> health </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="md:w-1/5 sm:w-1/3 w-1/2">
                                <a href="#"> 
                                    <div class="relative rounded-lg overflow-hidden">
                                        <img src="./socialminds/assets/images/category/science-and-tech.jpg" alt="" class="h-36 w-full object-cover">
                                        <div class="w-full bottom-0 absolute left-0 bg-gradient-to-t from-black/60 pt-10">
                                            <div class="text-white p-5 text-lg leading-3"> science </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="md:w-1/5 sm:w-1/3 w-1/2">
                                <a href="#"> 
                                    <div class="relative rounded-lg overflow-hidden">
                                        <img src="./socialminds/assets/images/category/travel.jpg" alt="" class="h-36 w-full object-cover">
                                        <div class="w-full bottom-0 absolute left-0 bg-gradient-to-t from-black/60 pt-10">
                                            <div class="text-white p-5 text-lg leading-3"> Travel </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="md:w-1/5 sm:w-1/3 w-1/2">
                                <a href="#">  
                                    <div class="relative rounded-lg overflow-hidden">
                                        <img src="./socialminds/assets/images/category/business.jpg" alt="" class="h-36 w-full object-cover">
                                        <div class="w-full bottom-0 absolute left-0 bg-gradient-to-t from-black/60 pt-10">
                                            <div class="text-white p-5 text-lg leading-3"> business </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            
                        </ul>
                
                    </div>
                   
                    <!-- slide nav icons -->
                    <a class="nav-prev" href="#" uk-slider-item="previous"> <ion-icon name="chevron-back" class="text-2xl"></ion-icon> </a>
                    <a class="nav-next" href="#" uk-slider-item="next"> <ion-icon name="chevron-forward" class="text-2xl"></ion-icon></a>
                    
                </div>
                
                <!-- suggest title -->
               

                <div class="flex justify-center my-6">
                    <button type="button" class="bg-white py-2 px-5 rounded-full shadow-md font-semibold text-sm dark:bg-dark2">Load more...</button>
                </div>
     
                
            </div>

          
        </main>

    </div>


    <!-- open chat box -->
    

    <!-- Javascript  -->
    <script src="./socialminds/assets/js/uikit.min.js"></script>
    <script src="./socialminds/assets/js/simplebar.js"></script>
    <script src="./socialminds/assets/js/script.js"></script>
 
 
    <!-- Ion icon -->
    <script type="module" src="../../unpkg.com/ionicons%405.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="../../unpkg.com/ionicons%405.5.2/dist/ionicons/ionicons.js"></script>
	<script>
    function joinGroup(groupId) {
        fetch("join_group.php", {
            method: "POST",
            body: JSON.stringify({ group_id: groupId }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("✅ Successfully followed the group!");
                location.reload();
            } else {
                alert("❌ " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    }

    // Function to open the modal and set group details
   function openModal(name, description, image) {
    document.getElementById("modalTitle").innerText = name;
    document.getElementById("modalDescription").innerText = description;
    
    // Ensure image path is correct
    document.getElementById("modalImage").src = image.startsWith("uploads/") ? image : "uploads/" + image;

    document.getElementById("groupModal").classList.remove("hidden");
}

</script>
<script>
function deleteGroup(groupId) {
    if (confirm("Are you sure you want to delete this group? This action cannot be undone.")) {
        fetch("delete_group.php", {
            method: "POST",
            body: JSON.stringify({ group_id: groupId }),
            headers: { "Content-Type": "application/json" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("✅ Group deleted successfully!");
                location.reload();
            } else {
                alert("❌ " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    }
}

</script>

</body>

</html>