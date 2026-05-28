<?php
session_start();
session_regenerate_id(); // Generates a secure new session ID

// Database connection
$hname = "localhost";
$uname = "root";
$pass = "";
$dbname = "db_socialmedia";

$con = mysqli_connect($hname, $uname, $pass, $dbname);
if (!$con) {
    echo "Error connecting to database.";
    die();
}

$message = ""; 

if (isset($_POST['submit'])) {
    $first = htmlspecialchars($_POST['first']);
    $last = htmlspecialchars($_POST['last']);
    $email = htmlspecialchars($_POST['email']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $conpassword = $_POST['conpassword'];

    if ($password === $conpassword) {

        // Generate User ID (Session ID)
        $userID = session_id();

        // Insert into tbl_register
        $stmt1 = $con->prepare("INSERT INTO tbl_register 
            (first, last, email, username, password, conpassword, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt1->bind_param("sssssss", $first, $last, $email, $username, $password, $conpassword, $userID);
        $stmt1->execute();

        // Insert into user_profiles
        $stmt2 = $con->prepare("INSERT INTO user_profiles 
            (username, email, password, user_id) 
            VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("ssss", $username, $email, $password, $userID);
        $stmt2->execute();

        if ($stmt1 && $stmt2) {
            // Store session data for future use
            $_SESSION['user_id'] = $userID;       // Store `user_id` as session
            $_SESSION['username'] = $username;     // Store username
            $_SESSION['user_email'] = $email;      // Store email

            $message = "✅ Registration successful!";
            header('Location: form-login.php'); // Redirect to feed
            exit();
        } else {
            $message = "❌ Error: " . $con->error;
        }

        $stmt1->close();
        $stmt2->close();
    } else {
        $message = "❌ Passwords do not match. Please try again.";
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
          <img class="w-12" src="./socialminds/assets/images/logo-icon.png" alt="Socialite php template">
        </div>

        <!-- title -->
        <div>
          <h2 class="text-2xl font-semibold mb-1.5"> Sign up to get started </h2>
          <p class="text-sm text-gray-700 font-normal">If you already have an account, <a href="form-login.php" class="text-blue-700">Login here!</a></p>
        </div>
 

        <!-- form -->
        <form method="POST" action="#" class="space-y-7 text-sm text-black font-medium dark:text-white"  uk-scrollspy="target: > *; cls: uk-animation-scale-up; delay: 100 ;repeat: true">
            
          <div class="grid grid-cols-2 gap-4 gap-y-7">
     
            <!-- first name -->
            <div>
                <label for="email" class="">First name</label>
                <div class="mt-2.5">
                    <input id="text" name="first" type="text"  autofocus="" placeholder="First name" required="" class="!w-full !rounded-lg !bg-transparent !shadow-sm !border-slate-200 dark:!border-slate-800 dark:!bg-white/5"> 
                </div>
            </div>

            <!-- Last name -->
            <div>
                <label for="email" class="">Last name</label>
                <div class="mt-2.5">
                    <input id="text" name="last" type="text" placeholder="Last name" required="" class="!w-full !rounded-lg !bg-transparent !shadow-sm !border-slate-200 dark:!border-slate-800 dark:!bg-white/5"> 
                </div>
            </div>
          
            <!-- email -->
            <div class="col-span-2">
                <label for="email" class="">Email address</label>
                <div class="mt-2.5">
                    <input id="email" name="email" type="email" placeholder="Email" required="" class="!w-full !rounded-lg !bg-transparent !shadow-sm !border-slate-200 dark:!border-slate-800 dark:!bg-white/5"> 
                </div>
            </div>
			
			<!-- username -->
            <div class="col-span-2">
                <label for="email" class="">Username</label>
                <div class="mt-2.5">
                    <input id="username" name="username" type="username" placeholder="Username" required="" class="!w-full !rounded-lg !bg-transparent !shadow-sm !border-slate-200 dark:!border-slate-800 dark:!bg-white/5"> 
                </div>
            </div>

            <!-- password -->
            <div>
              <label for="email" class="">Password</label>
              <div class="mt-2.5">
                  <input id="password" name="password" type="password" placeholder="***"  class="!w-full !rounded-lg !bg-transparent !shadow-sm !border-slate-200 dark:!border-slate-800 dark:!bg-white/5">  
              </div>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="email" class="">Confirm Password</label>
                <div class="mt-2.5">
                    <input id="password" name="conpassword" type="password" placeholder="***"  class="!w-full !rounded-lg !bg-transparent !shadow-sm !border-slate-200 dark:!border-slate-800 dark:!bg-white/5">  
                </div>
            </div>

            <div class="col-span-2">

              <label class="inline-flex items-center" id="rememberme">
                <input type="checkbox" id="accept-terms" class="!rounded-md accent-red-800" />
                <span class="ml-2">you agree to our <a href="#" class="text-blue-700 hover:underline">terms of use </a> </span>
              </label>
              
            </div>


            <!-- submit button -->
            <div class="col-span-2">
              <button type="submit" name="submit" class="button bg-primary text-white w-full">Get Started</button>
            </div>

          </div>
<?php 
if ($message): 
?>
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
			<br>
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
<?php $con->close(); ?>