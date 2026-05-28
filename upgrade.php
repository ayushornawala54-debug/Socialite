<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_email'])) {
    header("Location: form-login.php");
    exit();
}

$email = $_SESSION['user_email'];
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $card_number = str_replace(' ', '', $_POST['card_number']); // remove spaces
    $expiry_date = substr($_POST['expiry_date'], 0, 7); // "YYYY-MM"
    $cvv = $_POST['cvv'];
    $username = $_SESSION['username']; // assuming the user is logged in
    $selected_plan = $_POST['selected_plan'];
    
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime("+30 days")); // for 1 month plan

    $sql = "INSERT INTO tbl_subscription 
        (name, card_number, expiry_date, cvv, username, start_date, selected_plan, end_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $name, $card_number, $expiry_date, $cvv, $username, $start_date, $selected_plan, $end_date);

    if ($stmt->execute()) {
        echo "✅ Subscription saved!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

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

$conn->close();
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
    <!-- Styles -->
<style>
/* Background Blur Effect */
.popup-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 999;
    opacity: 0;
    transition: opacity 0.3s ease-in-out;
}

/* Popup Box */
.popup-content {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(15px);
    padding: 20px;
    width: 360px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.3s ease-in-out;
    color: white;
}

/* Text Colors */
h2 {
    font-size: 24px;
    margin-bottom: 5px;
}

.subtext {
    font-size: 14px;
    color: #ddd;
    margin-bottom: 15px;
}

.selected-plan {
    font-size: 16px;
    font-weight: bold;
    padding: 10px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.2);
    margin-bottom: 15px;
}

/* Form Styles */
.input-group {
    text-align: left;
    margin-bottom: 10px;
}

.input-group label {
    display: block;
    font-weight: bold;
    font-size: 14px;
    margin-bottom: 3px;
}

.input-group input {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    transition: 0.3s;
    background: rgba(255, 255, 255, 0.3);
    color: white;
    outline: none;
}

.input-group input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

/* Input Row for Expiry & CVV */
.input-row {
    display: flex;
    gap: 10px;
}

/* Buttons */
.popup-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
}

.btn-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
    color: white;
    padding: 12px 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    width: 48%;
    transition: 0.3s;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #0056b3, #0043a5);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    padding: 12px 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    width: 48%;
    transition: 0.3s;
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Fade In Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
	 <style>
        /* Basic button styling */
        .btn {
            padding: 10px 20px;
            border: 1px solid #ccc;
            background-color: white;
            color: black;
            cursor: pointer;
            margin: 5px;
            transition: all 0.3s ease-in-out;
            border-radius: 5px;
        }

        /* Hover effect */
        .btn:hover {
            background-color: lightgray;
        }

        /* Active effect (stays after click) */
        .btn.active {
            background-color: blue;
            color: white;
            border: 1px solid blue;
            box-shadow: 0 0 10px rgba(0, 0, 255, 0.5);
        }
.button {
  --width: 100px;
  --height: 35px;
  --tooltip-height: 35px;
  --tooltip-width: 90px;
  --gap-between-tooltip-to-button: 18px;
  --button-color: #222;
  --tooltip-color: #fff;
  width: var(--width);
  height: var(--height);
  background: var(--button-color);
  position: relative;
  text-align: center;
  border-radius: 0.45em;
  font-family: "Arial";
  transition: background 0.3s;
}

.button::before {
  position: absolute;
  content: attr(data-tooltip);
  width: var(--tooltip-width);
  height: var(--tooltip-height);
  background-color: #555;
  font-size: 0.9rem;
  color: #fff;
  border-radius: .25em;
  line-height: var(--tooltip-height);
  bottom: calc(var(--height) + var(--gap-between-tooltip-to-button) + 10px);
  left: calc(50% - var(--tooltip-width) / 2);
}

.button::after {
  position: absolute;
  content: '';
  width: 0;
  height: 0;
  border: 10px solid transparent;
  border-top-color: #555;
  left: calc(50% - 10px);
  bottom: calc(100% + var(--gap-between-tooltip-to-button) - 10px);
}

.button::after,.button::before {
  opacity: 0;
  visibility: hidden;
  transition: all 0.5s;
}

.text {
  display: flex;
  align-items: center;
  justify-content: center;
}

.button-wrapper,.text,.icon {
  overflow: hidden;
  position: absolute;
  width: 100%;
  height: 100%;
  left: 0;
  color: #fff;
}

.text {
  top: 0
}

.text,.icon {
  transition: top 0.5s;
}

.icon {
  color: #fff;
  top: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon svg {
  width: 24px;
  height: 24px;
}

.button:hover {
  background: #222;
}

.button:hover .text {
  top: -100%;
}

.button:hover .icon {
  top: 0;
}

.button:hover:before,.button:hover:after {
  opacity: 1;
  visibility: visible;
}

.button:hover:after {
  bottom: calc(var(--height) + var(--gap-between-tooltip-to-button) - 20px);
}

.button:hover:before {
  bottom: calc(var(--height) + var(--gap-between-tooltip-to-button));
}
.button {
  margin: 0;
  position: absolute;
  
  left: 60%;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
}
#popupOverlay {
    display: none; 
    position: fixed; 
    top: 0; 
    left: 0; 
    width: 100%; 
    height: 100%; 
    background-color: rgba(0, 0, 0, 0.7); 
    align-items: center; 
    justify-content: center;
    z-index: 9999; /* Ensure it's above other content */
}



    </style>
	
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
                           

                            <!-- messages -->
                            <button type="button" class="sm:p-2 p-1 rounded-full relative sm:bg-secondery dark:text-white" uk-tooltip="title: Messages; pos: bottom; offset:6">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 max-sm:hidden">
                                    <path fill-rule="evenodd" d="M4.848 2.771A49.144 49.144 0 0112 2.25c2.43 0 4.817.178 7.152.52 1.978.292 3.348 2.024 3.348 3.97v6.02c0 1.946-1.37 3.678-3.348 3.97a48.901 48.901 0 01-3.476.383.39.39 0 00-.297.17l-2.755 4.133a.75.75 0 01-1.248 0l-2.755-4.133a.39.39 0 00-.297-.17 48.9 48.9 0 01-3.476-.384c-1.978-.29-3.348-2.024-3.348-3.97V6.741c0-1.946 1.37-3.68 3.348-3.97zM6.75 8.25a.75.75 0 01.75-.75h9a.75.75 0 010 1.5h-9a.75.75 0 01-.75-.75zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H7.5z" clip-rule="evenodd"></path>
                                </svg>
                                <ion-icon name="chatbox-ellipses-outline" class="sm:hidden text-2xl"></ion-icon>
                            </button>
						
        
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

            <div class="max-w-4xl mx-auto max-lg:px-4">

      
                <div class="lg:py-20 py-12">
                    <div class="text-center">
                        <ion-icon name="sparkles-sharp" class="text-5xl mb-6 text-sky-500 opacity-70 rotate-12"></ion-icon> 
                        <h1 class="lg:text-5xl lg:font-bold md:text-3xl text-xl font-semibold bg-gradient-to-tr from-indigo-500 to-sky-400 bg-clip-text !text-transparent leading-relaxed">  With Socialite Premium</h1>
                        <p class="text-sm text-gray-500 mt-2 dark:text-white/80"> Exclusive features and benefits on Socialite are accessible to you. </p>
                    </div>
                      
    
                    <!-- pricing lebel with slider -->
                    <div class="relative lg:mt-12 mt-8 max-w-2xl mx-auto" tabindex="-1" uk-slider="finite: true">
    
                        <div class="overflow-hidden uk-slider-container py-2">
                           
                            <ul class="-ml-2 uk-slider-items w-[calc(100%+10px)]" >
                           
                                <li class="lg:w-1/3 w-1/2 pr-[10px]">
    <label for="weekly">
        <input type="radio" name="radio-membership" id="weekly" class="peer appearance-none hidden" />
        <div class="relative p-4 bg-white shadow-sm rounded-xl cursor-pointer dark:bg-dark3" id="weeklyCard">
            <button type="button" class="w-full">
                <div class="mb-4 text-sm"> Weekly </div>
                <h2 class="text-3xl font-bold text-black relative px-2 dark:text-white">
                    <span class="text-sm absolute top-1.5 -left-1 font-normal text-gray-700">₹</span> 120
                </h2>
            </button>
        </div>
    </label>
</li>

<li class="lg:w-1/3 w-1/2 pr-[10px]">
    <label for="monthly">
        <input type="radio" name="radio-membership" id="monthly" class="peer appearance-none hidden" />
        <div class="relative p-4 bg-white shadow-sm rounded-xl cursor-pointer dark:bg-dark3" id="monthlyCard">
            <button type="button" class="w-full">
                <div class="mb-4 text-sm"> Monthly </div>
                <h2 class="text-3xl font-bold text-black relative px-2 dark:text-white">
                    <span class="text-sm absolute top-1.5 -left-1 font-normal text-gray-700">₹</span> 999
                </h2>
            </button>
        </div>
    </label>
</li>

<li class="lg:w-1/3 w-1/2 pr-[10px]">
    <label for="yearly">
        <input type="radio" name="radio-membership" id="yearly" class="peer appearance-none hidden" />
        <div class="relative p-4 bg-white shadow-sm rounded-xl cursor-pointer dark:bg-dark3" id="yearlyCard">
            <button type="button" class="w-full">
                <div class="mb-4 text-sm"> Yearly </div>
                <h2 class="text-3xl font-bold text-black relative px-2 dark:text-white">
                    <span class="text-sm absolute top-1.5 -left-1 font-normal text-gray-700">₹</span> 3,999
                </h2>
            </button>
        </div>
    </label>
</li>

<li class="lg:w-1/3 w-1/2 pr-[10px]">
    <label for="lifetime">
        <input type="radio" name="radio-membership" id="lifetime" class="peer appearance-none hidden" />
        <div class="relative p-4 bg-white shadow-sm rounded-xl cursor-pointer dark:bg-dark3" id="lifetimeCard">
            <button type="button" class="w-full">
                <div class="mb-4 text-sm"> Lifetime </div>
                <h2 class="text-3xl font-bold text-black relative px-2 dark:text-white">
                    <span class="text-sm absolute top-1.5 -left-1 font-normal text-gray-700">₹</span> 7,999
                </h2>
            </button>
        </div>
    </label>
</li>

 
								
								
							

<script>
let selectedPlan = "";

// Select all membership cards
const membershipCards = document.querySelectorAll('[id$="Card"]');

membershipCards.forEach(card => {
    card.addEventListener('click', function () {
        
        // Remove active ring from all cards
        membershipCards.forEach(c => c.classList.remove('ring', 'ring-blue-600', 'ring-offset-2', 'dark:ring-offset-slate-900'));

        // Add active ring to clicked card
        card.classList.add('ring', 'ring-blue-600', 'ring-offset-2', 'dark:ring-offset-slate-900');

        // Extract and store the selected plan details
        const planType = card.querySelector('.mb-4').innerText.trim();
        const planPrice = card.querySelector('h2').innerText.trim();
        selectedPlan = `${planType} - ${planPrice}`;

        // Check the associated radio input
        const inputId = card.id.replace('Card', ''); 
        document.getElementById(inputId).checked = true;
    });
});


</script> 
                            </ul>
                    
                        </div>
                       
                        <!-- slide nav -->
                        <ul class="flex flex-wrap justify-center my-7 uk-dotnav uk-slider-nav gap-2.5"></ul>
    
    
                        <a class="hidden absolute -translate-y-1/2 bg-white rounded-full top-1/2 -left-4 flex w-8 h-8 p-2.5 place-items-center" href="#" uk-slidenav-previous uk-slider-item="previous"></a>
                        <a class="hidden absolute -right-4 -translate-y-1/2 bg-white rounded-full top-1/2 flex w-8 h-8 p-2.5 place-items-center" href="#" uk-slidenav-next uk-slider-item="next"></a>
                        
                    </div>
    
    
                    <!-- pricing lebel with out slider ( remove the hidden class)-->
                   
    
    
                    <div class="md:p-8 p-5 bg-white shadow-sm rounded-xl mt-16 dark:bg-dark3">
                        
                        <h1 class="text-base font-medium text-black dark:text-white">Why Choose Premium Membership </h1>
                   
                        <div class=" text-sm text-gray-500 grid md:grid-cols-2 grid-cols-3 gap-10 mt-8 dark:text-white/80">
    
                            <div class="flex gap-5 max-md:items-center max-md:flex-col">
                                <ion-icon name="camera" class="flex shrink-0 p-2 text-2xl rounded-full bg-sky-100 text-sky-500 dark:bg-sky-500/20"></ion-icon>
                                <div> 
                                    <h5 class="text-black text-base font-medium dark:text-white"> Stories </h5>
                                    <p class="mt-1 max-md:hidden"> Post moments your everyday life that disappear after 24 hours </p>
                                </div>
                            </div>
                            <div class="flex gap-5 max-md:items-center max-md:flex-col">
                                <ion-icon name="image" class="flex shrink-0 p-2 text-2xl rounded-full bg-teal-100 text-teal-500 dark:bg-teal-500/20"></ion-icon>
                                <div> 
                                    <h5 class="text-black text-base font-medium dark:text-white"> Images </h5>
                                    <p class="mt-1 max-md:hidden"> You can upload Unlimited photes and share with your friends </p>
                                </div>
                            </div>
                            <div class="flex gap-5 max-md:items-center max-md:flex-col">
                                <ion-icon name="chatbox" class="flex shrink-0 p-2 text-2xl rounded-full bg-orange-100 text-orange-500 dark:bg-orange-500/20"></ion-icon>
                                <div> 
                                    <h5 class="text-black text-base font-medium dark:text-white"> Messages </h5>
                                    <p class="mt-1 max-md:hidden"> Send photos, videos, and messages privately to your friends or groups </p>
                                </div>
                            </div>
    
                            <div class="flex gap-5 max-md:items-center max-md:flex-col">
                                <ion-icon name="videocam" class="flex shrink-0 p-2 text-2xl rounded-full bg-pink-100 text-pink-500 dark:bg-pink-500/20"></ion-icon>
                                <div> 
                                    <h5 class="text-black text-base font-medium dark:text-white"> Shorts </h5>
                                    <p class="mt-1 max-md:hidden"> Create and share short, entertaining videos with music, filters, and effects </p>
                                </div>
                            </div>
    
                            <div class="flex gap-5 max-md:items-center max-md:flex-col">
                                <ion-icon name="compass" class="flex shrink-0 p-2 text-2xl rounded-full bg-purple-100 text-purple-500 dark:bg-purple-500/20"></ion-icon>
                                <div> 
                                    <h5 class="text-black text-base font-medium dark:text-white"> Explore </h5>
                                    <p class="mt-1 max-md:hidden">  Discover content and creators based on their interests </p>
                                </div>
                            </div>
                            <div class="flex gap-5 max-md:items-center max-md:flex-col">
                                <ion-icon name="bookmark" class="flex shrink-0 p-2 text-2xl rounded-full bg-green-100 text-green-500 dark:bg-green-500/20"></ion-icon>
                                <div> 
                                    <h5 class="text-black text-base font-medium dark:text-white"> Bookmark </h5>
                                    <p class="mt-1 max-md:hidden"> Create collections of saved posts based on themes, topics, or categories. </p>
                                </div>
                            </div>
                            <div class="flex gap-5 max-md:items-center max-md:flex-col">
                                <ion-icon name="shield" class="flex shrink-0 p-2 text-2xl rounded-full bg-red-100 text-red-500 dark:bg-red-500/20"></ion-icon>
                                <div> 
                                    <h5 class="text-black text-base font-medium dark:text-white"> Privacy  </h5>
                                    <p class="mt-1 max-md:hidden"> Make your account visible only to people who follow you </p>
                                </div>
                            </div>
                            <div class="flex gap-5 max-md:items-center max-md:flex-col">
                                <ion-icon name="cart" class="flex shrink-0 p-2 text-2xl rounded-full bg-sky-100 text-sky-500 dark:bg-sky-500/20"></ion-icon>
                                <div> 
                                    <h5 class="text-black text-base font-medium dark:text-white"> Shopping </h5>
                                    <p class="mt-1 max-md:hidden"> Browse and buy products from your favorite brands and creators</p>
                                </div>
                            </div>
    
    
                        </div> 
    
                    </div> 
                    
                   
				   
                </div>
                     
                    <div class="py-10 flex justify-between">
    
                        <p class="max-w-xl mx-auto text-center text-sm text-gray-500dark:text-white/80"> Socialite Premium is the ultimate way to enhance your Socialite experience and connect with your passions. Try it free for 30 days and cancel anytime. </p>
    
                    </div>
                     
<!-- Buy Now Button -->
<div class="button" id="openPopup">
    <div class="button-wrapper">
        <div class="text">Buy Now</div>
        <span class="icon">
            <svg viewBox="0 0 16 16" class="bi bi-cart2" fill="currentColor" height="16" width="16">
                <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .485.379L2.89 4H14.5a.5.5 0 0 1 .485.621l-1.5 6A.5.5 0 0 1 13 11H4a.5.5 0 0 1-.485-.379L1.61 3H.5a.5.5 0 0 1-.5-.5zM3.14 5l1.25 5h8.22l1.25-5H3.14zM5 13a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm9-1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm-2 1a2 2 0 1 1 4 0 2 2 0 0 1-4 0z"/>
            </svg>
        </span>
    </div>
</div>


<!-- Popup Overlay -->
<div id="popupOverlay" class="popup-overlay">
    <div class="popup-content">
        <h2>💳 Checkout</h2>
        <p class="subtext">Complete your payment securely</p>

        <div id="selectedPlan" class="selected-plan">No plan selected</div>

        <form method="POST" id="checkoutForm" action="upgrade.php">
            <div class="input-group">
                <label>👤 Name</label>
                <input type="text" name="name" id="name" placeholder="John Doe" required>
            </div>

            <div class="input-group">
                <label>💳 Card Number</label>
                <input type="text" name="card_number" id="card_number" maxlength="19" placeholder="1234 5678 9012 3456" required>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>📅 Expiry Date</label>
                    <input type="date" name="expiry_date" id="expiry_date" placeholder="MM/YY" required>
                </div>
                <div class="input-group">
                    <label>🔒 CVV</label>
                    <input type="number" name="cvv" id="cvv" maxlength="3" placeholder="123" required>
                </div>
            </div>

            <input type="hidden" name="selected_plan" id="selected_plan">

            <div class="popup-buttons">
                <button type="submit" name="submit" class="btn-primary">Pay Now 💰</button>
                <button type="button" id="closePopup" class="btn-secondary">Cancel ❌</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const openButton = document.getElementById("openPopup");
    const popup = document.getElementById("popupOverlay");
    const closeButton = document.getElementById("closePopup");
    const cardNumberInput = document.getElementById("card_number");
    const checkoutForm = document.getElementById("checkoutForm");
    const selectedPlanInput = document.getElementById("selected_plan");
    const selectedPlanDiv = document.getElementById("selectedPlan");

    let selectedPlan = "";
    const membershipCards = document.querySelectorAll('[id$="Card"]');

    // Show Popup
    openButton.addEventListener("click", function () {
        popup.style.display = "flex";
        setTimeout(() => popup.style.opacity = "1", 10);
        selectedPlanDiv.innerText = selectedPlan ? `Selected Plan: ${selectedPlan}` : "❌ No plan selected.";
        selectedPlanInput.value = selectedPlan;
    });

    // Close Popup
    closeButton.addEventListener("click", function () {
        popup.style.opacity = "0";
        setTimeout(() => popup.style.display = "none", 300);
    });

    // Format card number
    cardNumberInput.addEventListener("input", function (e) {
        let value = e.target.value.replace(/\D/g, "");
        value = value.replace(/(.{4})/g, "$1 ").trim();
        e.target.value = value;
    });

    // Handle plan selection
    membershipCards.forEach(card => {
        card.addEventListener("click", function () {
            membershipCards.forEach(c => c.classList.remove("ring", "ring-blue-600", "ring-offset-2", "dark:ring-offset-slate-900"));
            card.classList.add("ring", "ring-blue-600", "ring-offset-2", "dark:ring-offset-slate-900");

            const planType = card.querySelector(".mb-4").innerText.trim();
            const planPrice = card.querySelector("h2").innerText.trim();
            selectedPlan = `${planType} - ${planPrice}`;

            selectedPlanDiv.innerText = `Selected Plan: ${selectedPlan}`;
            selectedPlanInput.value = selectedPlan;

            const inputId = card.id.replace("Card", "");
            const radioInput = document.getElementById(inputId);
            if (radioInput) radioInput.checked = true;
        });
    });

    // Handle form submission
    checkoutForm.addEventListener("submit", function (e) {
        if (!selectedPlan) {
            e.preventDefault();
            alert("❌ Please select a plan first!");
            return;
        }

        // Optional success alert before redirect
        alert(`✅ Payment Successful for ${selectedPlan}!`);
    });
});
</script>


                

                </div>
            
                
            </div>
            
        </main>

    </div>


    
    </div>


    <!-- Javascript  -->
    <script src="./socialminds/assets/js/uikit.min.js"></script>
    <script src="./socialminds/assets/js/simplebar.js"></script>
    <script src="./socialminds/assets/js/script.js"></script>
 
 <script>
        const buttons = document.querySelectorAll('.btn');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                buttons.forEach(btn => btn.classList.remove('active')); // Remove active class from all
                this.classList.add('active'); // Add active class to clicked button
            });
        });
    </script>
 
    <!-- Ion icon -->
    <script type="module" src="../../unpkg.com/ionicons%405.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="../../unpkg.com/ionicons%405.5.2/dist/ionicons/ionicons.js"></script>
	<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>


</body>

</html>