<?php
session_start();
session_unset(); // Clear old session data
session_destroy(); // Destroy old session
session_start(); // Start a new session

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_socialmedia';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Initialize message
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    
    $email = trim(strtolower($_POST['email']));
    $password = $_POST['password'];

    // 🔹 Admin Login
    $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE LOWER(emailId) = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            $_SESSION['admin_email'] = $user['emailId'];
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['fullName']; // Store Full Name for Later Retrieval
            
            // 🔹 Generate and Store Session ID in `user_id`
            $sessionID = session_id();
            $updateSession = $conn->prepare("UPDATE tbl_admin SET user_id = ? WHERE id = ?");
            $updateSession->bind_param("si", $sessionID, $user['id']);
            $updateSession->execute();

            echo "✅ Admin Session Stored: " . $sessionID; // For Debugging
            header("Location: admin_feed.php");
            exit();
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ Admin not found.";
    }

    // 🔹 Regular User Login
    $stmt = $conn->prepare("SELECT * FROM tbl_register WHERE LOWER(email) = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($password === $user['password']) {
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // 🔹 Generate and Store Session ID in `tbl_register`
            $sessionID = session_id();
            $updateSession = $conn->prepare("UPDATE tbl_register SET user_id = ? WHERE id = ?");
            $updateSession->bind_param("si", $sessionID, $user['id']);
            $updateSession->execute();

            echo "✅ User Session Stored: " . $sessionID; // For Debugging
            header("Location: timeline.php");
            exit();
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ User not found.";
    }
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
    <title>Social Minds</title>
   
   
    <!-- css files -->
    <link rel="stylesheet" href="./socialminds/assets/css/tailwind.css">
    <link rel="stylesheet" href="./socialminds/assets/css/style.css">
			<style>
			.message {
            margin-top: 10px;
            color: red;
            text-align: center;
        }
			</style>
			<style>
    
	.faq-button {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: none;
  background-color: #ffe53b;
  background-image: linear-gradient(147deg, #ffe53b 0%, #ff2525 74%);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0px 10px 10px rgba(0, 0, 0, 0.151);
  position: relative;
}
.faq-button svg {
  height: 1.5em;
  fill: white;
}
.faq-button:hover svg {
  animation: jello-vertical 0.7s both;
}
@keyframes jello-vertical {
  0% {
    transform: scale3d(1, 1, 1);
  }
  30% {
    transform: scale3d(0.75, 1.25, 1);
  }
  40% {
    transform: scale3d(1.25, 0.75, 1);
  }
  50% {
    transform: scale3d(0.85, 1.15, 1);
  }
  65% {
    transform: scale3d(1.05, 0.95, 1);
  }
  75% {
    transform: scale3d(0.95, 1.05, 1);
  }
  100% {
    transform: scale3d(1, 1, 1);
  }
}

.tooltip {
  position: absolute;
  top: -20px;
  opacity: 0;
  background-color: #ffe53b;
  background-image: linear-gradient(147deg, #ffe53b 0%, #ff2525 74%);
  color: white;
  padding: 5px 10px;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition-duration: 0.2s;
  pointer-events: none;
  letter-spacing: 0.5px;
}

.tooltip::before {
  position: absolute;
  content: "";
  width: 10px;
  height: 10px;
  background-color: #ff2525;
  background-size: 1000%;
  background-position: center;
  transform: rotate(45deg);
  bottom: -15%;
  transition-duration: 0.3s;
}

.faq-button:hover .tooltip {
  top: -40px;
  opacity: 1;
  transition-duration: 0.3s;
}
.faq-button {
  width: 40px; /* Smaller size */
  height: 40px;
  position: fixed;
  bottom: 20px; /* Adjusts distance from bottom */
  left: 20px; /* Adjusts distance from left */
  z-index: 1000; /* Ensures it's above other content */
}

.faq-button svg {
  height: 1.2em; /* Smaller icon size */
}

/* Tooltip positioning update */
.faq-button:hover .tooltip {
  top: -35px;
}

.tooltip {
  font-size: 12px; /* Smaller tooltip text */
}
</style>
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
 <script>
 
// On form-login page
function login(username, password) {
    if (validateCredentials(username, password)) {
        // Store username in session
        sessionStorage.setItem('username', username);
        // Redirect to timeline page
        window.location.href = 'timeline.html';
    } else {
        alert('Invalid credentials');
    }
}
 </script>
</head>
<body>

  <div class="sm:flex">
    
    <div class="relative lg:w-[580px] md:w-96 w-full p-10 min-h-screen bg-white shadow-xl flex items-center pt-10 dark:bg-slate-900 z-10">

      <div class="w-full lg:max-w-sm mx-auto space-y-10" uk-scrollspy="target: > *; cls: uk-animation-scale-up; delay: 100 ;repeat: true">

        <!-- logo image-->
        <a href="#"> <img src="./socialminds/assets/images/logo.png" class="w-28 absolute top-10 left-10 dark:hidden" alt=""></a>
        <a href="#"> <img src="./socialminds/assets/images/logo-light.png" class="w-28 absolute top-10 left-10 hidden dark:!block" alt=""></a>

        <!-- logo icon optional -->
        <div class="hidden">
          <img class="w-12" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&amp;shade=600" alt="Socialite php template">
        </div>

        <!-- title -->
        <div>
          <h2 class="text-2xl font-semibold mb-1.5"> Sign in to your account </h2>
          <p class="text-sm text-gray-700 font-normal">If you haven’t signed up yet. <a href="form-register.php" class="text-blue-700">Register here!</a></p>
        </div>
 

        <!-- form -->
        <form method="POST" action="#" class="space-y-7 text-sm text-black font-medium dark:text-white"  uk-scrollspy="target: > *; cls: uk-animation-scale-up; delay: 100 ;repeat: true">
            
          <!-- email -->
          <div>
              <label for="email" class="">Email address</label>
              <div class="mt-2.5">
                  <input id="email" name="email" type="email" autofocus=""  placeholder="Email" required="" class="!w-full !rounded-lg !bg-transparent !shadow-sm !border-slate-200 dark:!border-slate-800 dark:!bg-white/5"> 
              </div>
          </div>
          <!-- password -->
          <div>
            <label for="email" class="">Password</label>
            <div class="mt-2.5">
                <input id="password" name="password" type="password" placeholder="***"  class="!w-full !rounded-lg !bg-transparent !shadow-sm !border-slate-200 dark:!border-slate-800 dark:!bg-white/5">  
            </div>
          </div>

          <div class="flex items-center justify-between">

            <div class="flex items-center gap-2.5">
              <input id="rememberme" name="rememberme" type="checkbox">
              <label for="rememberme" class="font-normal">Remember me</label>
            </div>
            <a href="forgot.php" class="text-blue-700">Forgot password </a>
          </div>

          <!-- submit button -->
          <div>
            <button type="submit" name="submit" class="button bg-primary text-white w-full">Sign in</button>
          </div>
			 <?php if ($message): ?>
            <div class="message"> <?php echo $message; ?> </div>
        <?php endif; ?>
          <div class="text-center flex items-center gap-6"> 
            <hr class="flex-1 border-slate-200 dark:border-slate-800"> 
            Or continue with  
            <hr class="flex-1 border-slate-200 dark:border-slate-800">
          </div> 

          <!-- social login -->
          <div class="flex gap-2" uk-scrollspy="target: > *; cls: uk-animation-scale-up; delay: 400 ;repeat: true">
            <a href="https://www.facebook.com/" class="button flex-1 flex items-center gap-2 bg-primary text-white text-sm"> <ion-icon name="logo-facebook" class="text-lg"></ion-icon> facebook  </a>
            <a href="https://www.instagram.com/accounts/login/" class="button flex-1 flex items-center gap-2 bg-sky-600 text-white text-sm"> <ion-icon name="logo-twitter"></ion-icon> Instagram  </a>
            <a href="https://github.com/signup?source=login" class="button flex-1 flex items-center gap-2 bg-black text-white text-sm"> <ion-icon name="logo-github"></ion-icon> github  </a>
          </div>
          
        </form>
<br><small>
			
			<button class="faq-button">
			<a href="./dina/faq.php" class="button">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
					<path
					d="M80 160c0-35.3 28.7-64 64-64h32c35.3 0 64 28.7 64 64v3.6c0 21.8-11.1 42.1-29.4 53.8l-42.2 27.1c-25.2 16.2-40.4 44.1-40.4 74V320c0 17.7 14.3 32 32 32s32-14.3 32-32v-1.4c0-8.2 4.2-15.8 11-20.2l42.2-27.1c36.6-23.6 58.8-64.1 58.8-107.7V160c0-70.7-57.3-128-128-128H144C73.3 32 16 89.3 16 160c0 17.7 14.3 32 32 32s32-14.3 32-32zm80 320a40 40 0 1 0 0-80 40 40 0 1 0 0 80z"
					></path>
				</svg>
				<span class="tooltip">FAQ</span>
				</a>
			</button>
			
			</small>

      </div>

    </div>

    <!-- image slider -->
    <div class="flex-1 relative bg-primary max-md:hidden">


      <div class="relative w-full h-full" tabindex="-1" uk-slideshow="animation: slide; autoplay: true">
    
        <ul class="uk-slideshow-items w-full h-full"> 
            <li class="w-full">
                <img src="./socialminds/assets/images/post/img-3.jpg"  alt="" class="w-full h-full object-cover uk-animation-kenburns uk-animation-reverse uk-transform-origin-center-left">
                <div class="absolute bottom-0 w-full uk-tr ansition-slide-bottom-small z-10">
                    <div class="max-w-xl w-full mx-auto pb-32 px-5 z-30 relative"  uk-scrollspy="target: > *; cls: uk-animation-scale-up; delay: 100 ;repeat: true" > 
                        <img class="w-12" src="./socialminds/assets/images/logo-icon.png" alt="Socialite php template">
                        <h4 class="!text-white text-2xl font-semibold mt-7"  uk-slideshow-parallax="y: 600,0,0">  Connect With Friends </h4> 
                        <p class="!text-white text-lg mt-7 leading-8"  uk-slideshow-parallax="y: 800,0,0;"> This phrase is more casual and playful. It suggests that you are keeping your friends updated on what’s happening in your life.</p>   
                    </div> 
                </div>
                <div class="w-full h-96 bg-gradient-to-t from-black absolute bottom-0 left-0"></div>
            </li>
            <li class="w-full">
              <img src="./socialminds/assets/images/post/img-2.jpg"  alt="" class="w-full h-full object-cover uk-animation-kenburns uk-animation-reverse uk-transform-origin-center-left">
              <div class="absolute bottom-0 w-full uk-tr ansition-slide-bottom-small z-10">
                  <div class="max-w-xl w-full mx-auto pb-32 px-5 z-30 relative"  uk-scrollspy="target: > *; cls: uk-animation-scale-up; delay: 100 ;repeat: true" > 
                      <img class="w-12" src="./socialminds/assets/images/logo-icon.png" alt="Socialite php template">
                      <h4 class="!text-white text-2xl font-semibold mt-7"  uk-slideshow-parallax="y: 800,0,0">  Connect With Friends </h4> 
                      <p class="!text-white text-lg mt-7 leading-8"  uk-slideshow-parallax="y: 800,0,0;"> This phrase is more casual and playful. It suggests that you are keeping your friends updated on what’s happening in your life.</p>   
                  </div> 
              </div>
              <div class="w-full h-96 bg-gradient-to-t from-black absolute bottom-0 left-0"></div>
          </li>
        </ul>
 
        <!-- slide nav -->
        <div class="flex justify-center">
            <ul class="inline-flex flex-wrap justify-center  absolute bottom-8 gap-1.5 uk-dotnav uk-slideshow-nav"> </ul>
        </div>
      
        
    </div>
  

    </div>
  
  </div>
  
   
    
    <script src="./socialminds/assets/js/uikit.min.js"></script>
    <script src="./socialminds/assets/js/script.js"></script>

    <!-- Ion icon -->
    <script type="module" src="../../unpkg.com/ionicons%405.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="../../unpkg.com/ionicons%405.5.2/dist/ionicons/ionicons.js"></script>

      <!-- Dark mode -->
      <script>
        // On page load or when changing themes, best to add inline in `head` to avoid FOUC
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark')
        } else {
        document.documentElement.classList.remove('dark')
        }

        // Whenever the user explicitly chooses light mode
        localStorage.theme = 'light'

        // Whenever the user explicitly chooses dark mode
        localStorage.theme = 'dark'

        // Whenever the user explicitly chooses to respect the OS preference
        localStorage.removeItem('theme')
    </script>

</body>


</html>
<?php $conn->close(); ?>