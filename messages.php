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
?>

<?php
$conn = new mysqli("localhost", "root", "", "db_socialmedia");


$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) AS unread_count FROM tbl_message WHERE receiver_id = ? AND timestamp >= NOW() - INTERVAL 1 DAY");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$unread_messages = $row['unread_count'];

if ($unread_messages > 0) {
    echo "<script>alert('You have $unread_messages new messages!');</script>";
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
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
	$.ajax({
    url: "send_message.php",
    type: "POST",
    data: { 
        message: $("#messageInput").val(), 
        receiver_id: selectedReceiverId 
    },
    success: function(response) {
        console.log(response); // Debug response
    }
});

function loadMessages(userId) {
    $.get("get_messages.php", { receiver_id: userId }, function(data) {
        let messages = JSON.parse(data);
        let chatHtml = '';

        messages.forEach(m => {
            let isSender = (m.sender_id == <?php echo $_SESSION['user_id']; ?>);
            let displayName = isSender ? 'You' : m.sender_name;
            let alignmentClass = isSender ? 'sent' : 'received';

            chatHtml += `<div class="message-container ${alignmentClass}">
                            <div class="message">
                                <strong>${displayName}:</strong> ${m.message}
                            </div>
                            <small class="timestamp">${m.timestamp}</small>
                         </div>`;
        });

        $("#chatMessages").html(chatHtml);
        $("#chatMessages").scrollTop($("#chatMessages")[0].scrollHeight);
    });
}

$(document).on("click", ".user-list", function(){
    let userId = $(this).data("id");
    let username = $(this).data("username");

    $(".chat-box").show();
    $("#chatUser").text(username);
    $(".chat-box").attr("data-user", userId);
    $("#chatMessages").html(''); // Clear previous messages
    loadMessages(userId);
});

setInterval(function(){
    let receiverId = $(".chat-box").attr("data-user");
    if (receiverId) {
        loadMessages(receiverId);
    }
}, 3000);

</script>
   <style>
        .chat-box { display: none; width: 350px; border: 1px solid #ccc; padding: 10px; position: fixed; bottom: 10px; right: 10px; background: white; }
        .messages { height: 250px; overflow-y: scroll; border-bottom: 1px solid #ccc; margin-bottom: 10px; padding: 5px; }
        .message { padding: 5px 10px; margin: 5px; border-radius: 10px; max-width: 70%; }
        .sent { background: #DCF8C6; align-self: flex-end; text-align: right; }
        .received { background: #EAEAEA; align-self: flex-start; text-align: left; }
        .message-container { display: flex; flex-direction: column; }
        .user-list { cursor: pointer; padding: 5px; border-bottom: 1px solid #ddd; }
		.message-container {
    display: flex;
    margin: 5px 0;
}

.sent {
    justify-content: flex-end;
}

.received {
    justify-content: flex-start;
}

.message {
    padding: 10px;
    border-radius: 10px;
    max-width: 60%;
    word-wrap: break-word;
}

.sent .message {
    background-color: #007bff;
    color: white;
    text-align: right;
}

.received .message {
    background-color: #f1f1f1;
    color: black;
    text-align: left;
}

    </style>
	<style>
    .chat-box {
        border: 1px solid #ccc;
        padding: 20px;
        border-radius: 10px;
        max-width: 700px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        height: 80vh;
    }
    #chatUser {
        margin: 0;
        font-size: 24px;
        color: #333;
    }
    #chatEmail {
        margin: 0 0 10px;
        color: #666;
        font-size: 14px;
    }
    .messages {
        flex: 1;
        overflow-y: auto;
        padding: 10px;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }
    .message-row {
        margin-bottom: 10px;
        display: flex;
        flex-direction: column;
    }
    .left .message-bubble {
        align-self: flex-start;
        background-color: #eee;
        color: #000;
    }
    .right .message-bubble {
        align-self: flex-end;
        background-color: #a8d5ff;
        color: #000;
    }
    .message-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 16px;
        margin-top: 4px;
        word-wrap: break-word;
    }
    #chatInput {
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
        resize: none;
        height: 60px;
    }
    #sendMessage {
        padding: 10px 20px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        align-self: flex-end;
    }
    #sendMessage:hover {
        background-color: #0056b3;
    }
</style>
</head>
<body class="bg-white darkd">
 
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
                        
                       <!-- Search Box -->
<div id="search--box" class="relative xl:w-[680px] sm:w-96 rounded-xl overflow-visible z-50 bg-secondery max-md:hidden w-screen left-0 max-sm:fixed max-sm:top-2 dark:!bg-white/5">
    <ion-icon name="search" class="absolute left-4 top-1/2 -translate-y-1/2"></ion-icon>
    <input type="text" id="searchInput" placeholder="Search Friends, videos .." class="w-full !pl-10 !font-normal !bg-transparent h-12 !text-sm">
</div>

<!-- Search Results Dropdown -->
<div id="searchResults" class="absolute z-50 bg-white dark:bg-dark3 w-screen sm:w-96 p-2 rounded-lg shadow-lg top-full hidden">
    <div id="resultsContainer"></div>
</div>

<script>
document.getElementById("searchInput").addEventListener("keyup", function() {
    let query = this.value;
    if (query.length > 2) {
        fetch("search.php?q=" + query)
        .then(response => response.text())
        .then(data => {
            document.getElementById("resultsContainer").innerHTML = data;
            document.getElementById("searchResults").classList.remove("hidden");
        });
    } else {
        document.getElementById("searchResults").classList.add("hidden");
    }
});
</script>

                        <!-- header icons -->
                        <div class="flex items-center sm:gap-4 gap-2 absolute right-5 top-1/2 -translate-y-1/2 text-black">
                            <!-- create -->
                            
                            <div    class="hidden bg-white p-4 rounded-lg overflow-hidden drop-shadow-xl dark:bg-slate-700 md:w-[324px] w-screen border2"
                                    uk-drop="offset:6;pos: bottom-right; mode: click; animate-out: true; animation: uk-animation-scale-up uk-transform-origin-top-right ">
                                
                                   

                            </div>
    
                            <!-- notification -->
                            
                                <!-- heading -->
                                

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

            <div class="relative overflow-hidden border -m-2.5 dark:border-slate-700">

                <div class="flex bg-white dark:bg-dark2">
    
                    <!-- sidebar -->
                    <div class="md:w-[360px] relative border-r dark:border-slate-700">
    
                        <div id="side-chat" class="top-0 left-0 max-md:fixed max-md:w-5/6 max-md:h-screen bg-white z-50 max-md:shadow max-md:-translate-x-full dark:bg-dark2">
    
                            <!-- heading title -->
                            <div class="p-4 border-b dark:border-slate-700">
                                
                                <div class="flex mt-2 items-center justify-between">
    
                                    <h2 class="text-2xl font-bold text-black ml-1 dark:text-white"> Chats </h1>
                                          
                                    <!-- right action buttons -->
                                    <div class="flex items-center gap-2.5">
    
                                        
                                        <button class="group">
                                            <ion-icon name="settings-outline" class="text-2xl flex group-aria-expanded:rotate-180"></ion-icon>
                                        </button>
                                        <div  class="md:w-[270px] w-full" uk-dropdown="pos: bottom-left; offset:10; animation: uk-animation-slide-bottom-small"> 
                                            <nav>
                                                <a href="#"> <ion-icon class="text-2xl shrink-0 -ml-1" name="checkmark-outline"></ion-icon> Mark all as read </a>  
                                                <a href="#"> <ion-icon class="text-2xl shrink-0 -ml-1" name="notifications-outline"></ion-icon> notifications setting </a>  
                                                <a href="#"> <ion-icon class="text-xl shrink-0 -ml-1" name="volume-mute-outline"></ion-icon> Mute notifications </a>     
                                            </nav>
                                        </div>
                                        
                                        <button class="">
                                            <ion-icon name="checkmark-circle-outline" class="text-2xl flex"></ion-icon>
                                        </button>
    
                                        <!-- mobile toggle menu -->
                                        <button type="button" class="md:hidden" uk-toggle="target: #side-chat ; cls: max-md:-translate-x-full">
                                            <ion-icon name="chevron-down-outline"></ion-icon>
                                        </button>
    
                                    </div>
    
                                </div>
    
                                <!-- search -->
                               <div class="relative mt-4">
    <div class="absolute left-3 bottom-1/2 translate-y-1/2 flex"><ion-icon name="search" class="text-xl"></ion-icon></div>
    <input type="text" id="searchUser" placeholder="Search" class="w-full !pl-10 !py-2 !rounded-lg">
</div>
<div id="userList"></div>

<!-- Chat Box -->
<div class="chat-box">
    <h3 id="chatUser">Select a user</h3>
    <p id="chatEmail"></p>
    <div class="messages" id="chatMessages">No conversation selected.</div>
    <textarea id="chatInput" placeholder="Type your message..."></textarea>
    <button id="sendMessage">Send</button>
</div>

<script>
$(document).ready(function(){
    function loadUserMessages() {
        $.get("get_user_messages.php", function(data){
            let users = JSON.parse(data);
            let userListHtml = '';
            
            if(users.length > 0){
                users.forEach(user => {
                    userListHtml += `<div class="user-list" data-id="${user.chat_user_id}" data-username="${user.chat_username}">
                                        ${user.chat_username}
                                     </div>`;
                });
            } else {
                userListHtml = "<div>No messages yet</div>";
            }

            $("#userList").html(userListHtml);
        });
    }

    loadUserMessages(); // Load messages when the page loads

    $(document).on("click", ".user-list", function(){
        let userId = $(this).data("id");
        let username = $(this).data("username");

        $(".chat-box").show();
        $("#chatUser").text(username);
        $(".chat-box").attr("data-user", userId);
        loadMessages(userId);
    });

    function loadMessages(userId) {
    $.get("get_messages.php", { receiver_id: userId }, function(data){
        let messages = JSON.parse(data);
        let chatHtml = '';

        messages.forEach(m => {
            let isSender = (m.sender_id == <?php echo $_SESSION['user_id']; ?>);
            let alignmentClass = isSender ? 'sent' : 'received';

            chatHtml += `<div class="message-container ${alignmentClass}">
                            <div class="message">${m.message}</div>
                         </div>`;
        });

        $("#chatMessages").html(chatHtml);
        $("#chatMessages").scrollTop($("#chatMessages")[0].scrollHeight);
    });
}



    $("#sendMessage").click(function(){
        let message = $("#chatInput").val();
        let receiverId = $(".chat-box").attr("data-user");

        $.post("send_message.php", {message: message, receiver_id: receiverId}, function(){
            loadMessages(receiverId);
            $("#chatInput").val('');
            loadUserMessages(); // Refresh user list after sending message
        });
    });

    setInterval(function(){
        let receiverId = $(".chat-box").attr("data-user");
        if (receiverId) loadMessages(receiverId);
    }, 3000);
});

$(document).ready(function(){
    // Search users
    $("#searchUser").keyup(function(){
        let query = $(this).val();
        if (query.length > 0) {
            $.get("search_users.php", {query: query}, function(data){
                let users = JSON.parse(data);
                let userListHtml = '';
                
                if(users.length > 0){
                    users.forEach(user => {
                        userListHtml += `<div class="user-list" data-id="${user.id}" data-username="${user.username}" data-email="${user.email}">
                                            ${user.username} (${user.email})
                                         </div>`;
                    });
                } else {
                    userListHtml = "<div>No users found</div>";
                }

                $("#userList").html(userListHtml);
            });
        } else {
            $("#userList").html('');
        }
    });

    // Open chat when clicking on a user
    $(document).on("click", ".user-list", function(){
        let userId = $(this).data("id");
        let username = $(this).data("username");
        let email = $(this).data("email");

        $(".chat-box").show();
        $("#chatUser").text(username);
        $("#chatEmail").text(email);
        $("#chatMessages").html('');
        $(".chat-box").attr("data-user", userId);
        loadMessages(userId);
    });

   function loadMessages(userId) {
    $.get("get_messages.php", { receiver_id: userId }, function(data){
        let messages = JSON.parse(data);
        let chatHtml = '';

        messages.forEach(m => {
            let isSender = (m.sender_id == <?php echo $_SESSION['user_id']; ?>);
            let alignmentClass = isSender ? 'sent' : 'received';

            chatHtml += `<div class="message-container ${alignmentClass}">
                            <div class="message">${m.message}</div>
                         </div>`;
        });

        $("#chatMessages").html(chatHtml);
        $("#chatMessages").scrollTop($("#chatMessages")[0].scrollHeight);
    });
}



    // Auto-refresh messages
    setInterval(function(){
        let receiverId = $(".chat-box").attr("data-user");
        if (receiverId) loadMessages(receiverId);
    }, 3000);
});
</script>
    
                            </div> 
    
    
                            <!-- users list -->
            
    
                        </div>
                        
                        <!-- overly -->
                        
                    </div> 
    
                    <!-- message center -->
                   
                </div>
    
            </div> 
            
        </main>

    </div>


    <!-- Javascript  -->
    <script src="./socialminds/assets/js/uikit.min.js"></script>
    <script src="./socialminds/assets/js/simplebar.js"></script>
    <script src="./socialminds/assets/js/script.js"></script>
 
 
    <!-- Ion icon -->
    <script type="module" src="../../unpkg.com/ionicons%405.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="../../unpkg.com/ionicons%405.5.2/dist/ionicons/ionicons.js"></script>
 

</body>

</html>