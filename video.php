<?php
session_start();
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "db_socialmedia";

$conn = new mysqli($host, $username, $password, $database);

$email = $_SESSION['user_email'];
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
// Handle video deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_video_id'])) {
    $video_id = intval($_POST['delete_video_id']);
    $uploader_name = $_SESSION['username'] ?? 'Anonymous';
    
    // Ensure only the uploader can delete
    $check_query = "SELECT uploader_name FROM videos WHERE id = $video_id";
    $check_result = mysqli_query($conn, $check_query);
    $row = mysqli_fetch_assoc($check_result);
    
    if ($row && $row['uploader_name'] === $uploader_name) {
        $delete_query = "DELETE FROM videos WHERE id = $video_id";
        mysqli_query($conn, $delete_query);
    }
}

// Handle video upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['video_url'], $_POST['video_title'])) {
    $video_url = mysqli_real_escape_string($conn, $_POST['video_url']);
    $title = mysqli_real_escape_string($conn, $_POST['video_title']);
    $uploader_name = $_SESSION['username'] ?? 'Anonymous';
    
    // Extract YouTube video ID
    preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|.*[?&]v=))([^"&?\/\s]{11})/', $video_url, $matches);
    $video_id = $matches[1] ?? '';
    $thumbnail = "https://img.youtube.com/vi/$video_id/0.jpg";

    if ($video_id) {
        $insert = "INSERT INTO videos (title, youtube_url, thumbnail, uploader_name) VALUES ('$title', '$video_url', '$thumbnail', '$uploader_name')";
        mysqli_query($conn, $insert);
    }
}

// Fetch videos from the database
$query = "SELECT id, title, youtube_url, thumbnail, uploader_name, upload_date, views FROM videos ORDER BY upload_date DESC LIMIT 10";
$result = mysqli_query($conn, $query);

// Fetch logged-in user's videos
$my_videos = [];
if (isset($_SESSION['username'])) {
    $user = $_SESSION['username'];
    $my_videos_query = "SELECT id, title, youtube_url, thumbnail, uploader_name, upload_date, views FROM videos WHERE uploader_name='$user' ORDER BY upload_date DESC";
    $my_videos_result = mysqli_query($conn, $my_videos_query);
    $my_videos = mysqli_fetch_all($my_videos_result, MYSQLI_ASSOC);
}
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
    
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
 
</head>
<body>
 
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
                                        <a href="#" class=" relative px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <img src="assets/images/avatars/avatar-2.jpg" class="w-9 h-9 rounded-full"> <div>   <div> Jesse Steeve </div>  <div class="text-xs text-blue-500 font-medium mt-0.5">  Friend </div>   </div> <ion-icon name="close" class="text-base absolute right-3 top-1/2 -translate-y-1/2 "></ion-icon>  </a>  
                                        <a href="#" class=" relative px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <img src="assets/images/avatars/avatar-2.jpg" class="w-9 h-9 rounded-full"> <div>   <div>  Martin Gray </div>  <div class="text-xs text-blue-500 font-medium mt-0.5">  Friend </div>   </div> <ion-icon name="close" class="text-base absolute right-3 top-1/2 -translate-y-1/2 "></ion-icon>  </a>  
                                        <a href="#" class=" relative px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <img src="assets/images/group/group-2.jpg" class="w-9 h-9 rounded-full"> <div>   <div>  Delicious Foods  </div>  <div class="text-xs text-rose-500 font-medium mt-0.5">  Group </div>   </div> <ion-icon name="close" class="text-base absolute right-3 top-1/2 -translate-y-1/2 "></ion-icon>  </a>  
                                        <a href="#" class=" relative px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <img src="assets/images/group/group-1.jpg" class="w-9 h-9 rounded-full"> <div>   <div> Delicious Foods  </div>  <div class="text-xs text-yellow-500 font-medium mt-0.5">  Page </div>   </div> <ion-icon name="close" class="text-base absolute right-3 top-1/2 -translate-y-1/2 "></ion-icon>  </a>  
                                        <a href="#" class=" relative px-3 py-1.5 flex items-center gap-4 hover:bg-secondery rounded-lg dark:hover:bg-white/10"> <img src="assets/images/avatars/avatar-6.jpg" class="w-9 h-9 rounded-full"> <div>   <div>  John Welim </div>  <div class="text-xs text-blue-500 font-medium mt-0.5">  Friend </div>   </div> <ion-icon name="close" class="text-base absolute right-3 top-1/2 -translate-y-1/2 "></ion-icon>  </a>  
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
                            <!-- search -->
                                                       
                            <div    class="hidden bg-white p-4 rounded-lg overflow-hidden drop-shadow-xl dark:bg-slate-700 md:w-[324px] w-screen border2"
                                    uk-drop="offset:6;pos: bottom-right; mode: click; animate-out: true; animation: uk-animation-scale-up uk-transform-origin-top-right ">
                                
                                    <h3 class="font-bold text-md"> Create  </h3>

                                  


                            </div>
    
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
                                    <button type="button" class="w-full">
                                        <div class="flex items-center gap-2.5 hover:bg-secondery p-2 px-2.5 rounded-md dark:hover:bg-white/10"> 
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                            </svg>
                                            Night mode
                                            <span class="bg-slate-200/40 ml-auto p-0.5 rounded-full w-9 dark:hover:bg-white/20">
                                                <span class="bg-white block h-4 relative rounded-full shadow-md w-2 w-4 dark:bg-blue-600"></span>
                                            </span>
                                        </div>
                                    </button>   
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

            <div class="2xl:max-w-[1220px] max-w-[1065px] mx-auto">

                <div class="page-heading">
                        
                    <h1 class="page-title"> Videos </h1>
    
                    <nav class="nav__underline">
    
                        <ul uk-tab class="group" uk-switcher="connect: #ttabs ; animation: uk-animation-slide-right-medium, uk-animation-slide-left-medium"> 
                       
                            <li> <a href="#"> Suggestions  </a> </li>
                            
                        </ul> 
    
                    </nav>
    
                </div>
    
                <div class="mt-10">
    
                    <!-- page feautred -->
					<form action="video.php" method="POST" class="mb-4 p-4 border rounded">
    <input type="text" name="video_title" placeholder="Enter Video Title" required class="border p-2 w-full mb-2">
    <input type="url" name="video_url" placeholder="Enter YouTube Video URL" required class="border p-2 w-full mb-2">
    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Upload Video</button>
</form>
                    <div class="relative" tabindex="-1" uk-slider="finite:true">
        
                        <div class="uk-slider-container pb-1">
                        
                            <ul class="uk-slider-items grid-small">
            <?php while ($row = mysqli_fetch_assoc($result)) {
                $video_id = $row['id'];
                $title = htmlspecialchars($row['title']);
                $youtube_url = htmlspecialchars($row['youtube_url']);
                $thumbnail = htmlspecialchars($row['thumbnail']);
                $uploader = htmlspecialchars($row['uploader_name']);
                $upload_date = htmlspecialchars($row['upload_date']);
                $views = number_format($row['views']);
                $is_owner = isset($_SESSION['username']) && $_SESSION['username'] === $uploader;
            ?>
            <li class="lg:w-1/4 sm:w-1/3 w-1/2 relative">
                <div class="card">
                    <a href="<?= $youtube_url ?>" target="_blank">
                        <div class="card-media sm:aspect-[2/1.1] h-28">
                            <img src="<?= $thumbnail ?>" alt="">
                            <div class="card-overly"></div>
                            <img src="./socialminds/assets/images/icon-play.svg" class="!w-12 !h-12 absolute !top-1/2 !left-1/2 -translate-x-1/2 -translate-y-1/2" alt="">
                        </div>
                    </a>
                    <div class="card-body">
                        <a href="<?= $youtube_url ?>" target="_blank">
                            <h4 class="card-title text-sm line-clamp-2"> <?= $title ?> </h4>
                        </a>
                        <p class="card-text mt-1.5"> <a href="timeline.php"> <?= $uploader ?> </a> </p>
                        <div class="card-list-info text-xs mt-1 flex-wrap">
                            <div> <?= $upload_date ?> </div>
                            <div class="md:block hidden">·</div>
                            <div> <?= $views ?> views</div>
                        </div>
                    </div>
                    <?php if ($is_owner) { ?>
                    <div class="absolute top-2 right-2">
                        <button onclick="document.getElementById('delete-form-<?= $video_id ?>').submit();" class="text-gray-600 hover:text-red-600">⋮</button>
                        <form id="delete-form-<?= $video_id ?>" action="video.php" method="POST" class="hidden">
                            <input type="hidden" name="delete_video_id" value="<?= $video_id ?>">
                        </form>
                    </div>
                    <?php } ?>
                </div>
            </li>
            <?php } ?>
                                
                    
                        <!-- slide nav icons -->
                        <a class="nav-prev !top-20" href="#" uk-slider-item="previous"> <ion-icon name="chevron-back" class="text-2xl"></ion-icon> </a>
                        <a class="nav-next !top-20" href="#" uk-slider-item="next"> <ion-icon name="chevron-forward" class="text-2xl"></ion-icon></a>
                        
                    </div>
    
                    
     
    
                    <!-- second title -->
                    <div class="flex items-center justify-between text-black dark:text-white py-3 mt-10">
                        <h3 class="text-xl font-semibold"> Your videos </h3>
                        <a href="#" class="text-sm text-blue-500">See all</a>
                    </div>
    
                    <!-- card list  -->
					<h2 class="text-xl font-bold mt-6">My Videos</h2>
<div class="grid grid-cols-1 gap-4">
    <?php foreach ($my_videos as $video) { ?>
    <div class="card-list">
        <a href="<?= $video['youtube_url'] ?>" target="_blank">
            <div class="card-list-media md:w-[320px] md:h-[180px] sm:aspect-[3/1.2] aspect-[3/1.5]">
                <img src="<?= $video['thumbnail'] ?>" alt="">
                <img src="./socialminds/assets/images/icon-play.svg" class="!w-12 !h-12 absolute !top-1/2 !left-1/2 -translate-x-1/2 -translate-y-1/2" alt="">
            </div>
        </a>
        <div class="card-list-body relative">
            <a href="<?= $video['youtube_url'] ?>" target="_blank">
                <h3 class="card-list-title lg:mt-2 lg:line-clamp-1"> <?= htmlspecialchars($video['title']) ?> </h3>
            </a>
            <p class="card-list-text"> Uploaded by: <?= htmlspecialchars($video['uploader_name']) ?> </p>
            <div class="card-list-info">
                <div> <?= htmlspecialchars($video['upload_date']) ?> </div>
                <div class="md:block hidden">·</div>
                <div> <?= number_format($video['views']) ?> views</div>
            </div>
            <div class="absolute top-0 right-0 -m-1">
                <button type="button" class="hover:bg-secondery w-10 h-10 rounded-full grid place-items-center"> <ion-icon class="text-2xl" name="ellipsis-horizontal"></ion-icon> </button>
                <div class="w-[245px]" uk-dropdown="pos: bottom-right; animation: uk-animation-scale-up uk-transform-origin-top-right; animate-out: true; mode: click"> 
                    <nav>
                        <a href="#"> <ion-icon class="text-xl" name="bookmark-outline"></ion-icon> Add to favorites </a>  
                        <a href="#"> <ion-icon class="text-xl" name="albums-outline"></ion-icon> Add to collections </a>  
                        <a href="#"> <ion-icon class="text-xl" name="flag-outline"></ion-icon> Report </a>  
                        <a href="#"> <ion-icon class="text-xl" name="share-outline"></ion-icon> Share </a>  
                        <hr>
                        <form action="video.php" method="POST">
                            <input type="hidden" name="delete_video_id" value="<?= $video['id'] ?>">
                            <button type="submit" class="text-red-400 hover:!bg-red-50 dark:hover:!bg-red-500/50"> <ion-icon class="text-xl" name="trash-outline"></ion-icon> Delete </button>  
                        </form>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

                    
    
    
                    <!-- load more -->
                    <div class="flex justify-center my-6">
                        <button type="button" class="bg-white py-2 px-5 rounded-full shadow-md font-semibold text-sm dark:bg-dark2">Load more...</button>
                    </div>
    
    
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
 

</body>


</html>