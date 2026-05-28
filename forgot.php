<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");
require __DIR__ . '/vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if it's the first step (email submission) or OTP submission
    if (isset($_POST['email'])) {
        $email = $_POST["email"];

        // Check if the email exists in the database
        $stmt = $conn->prepare("SELECT id FROM tbl_register WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Generate a unique OTP for the reset process
            $otp = rand(100000, 999999); // Generate a 6-digit OTP
            
            // Store the OTP and its expiration (e.g., 10 minutes) in the database
            $expiration = date('Y-m-d H:i:s', strtotime('+10 minutes')); // 10 minutes expiration time
            $stmt = $conn->prepare("UPDATE tbl_register SET otp = ?, otp_expiration = ? WHERE email = ?");
            $stmt->bind_param("sss", $otp, $expiration, $email);
            $stmt->execute();

            // Send the OTP to the user's email
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'socialite021@gmail.com'; // Your Gmail address
                $mail->Password = 'wvbrgzwvtmtkevkl'; // Gmail App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('socialite021@gmail.com', 'Kartik');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset OTP';
               $mail->Body = "
				<div style='font-family: Arial, sans-serif; max-width: 500px; padding: 20px; border-radius: 8px; border: 1px solid #ddd; box-shadow: 0px 4px 8px rgba(0,0,0,0.1);'>
				<center>
				<img src='https://www.google.com/url?sa=i&url=https%3A%2F%2Fcgc.partners%2Fproject%2Fsocialite%2F&psig=AOvVaw0q31WWY_dMjalPjpRzE1d8&ust=1744207635334000&source=images&cd=vfe&opi=89978449&ved=0CBQQjRxqFwoTCNjt7_rNyIwDFQAAAAAdAAAAABAK' alt='Socialite' style='max-width: 150px;'>
				<h2 style='color: #2d89ef;'>Reset Your Password</h2>
				</center>
				<p>Hello,</p>
				<p>We received a request to reset your password for your <b>Socialite</b> account.</p>
				<p>If you didn’t request this, please ignore this email. Otherwise, use the OTP below to reset your password:</p>
        
				<center>
				<p style='font-size: 22px; font-weight: bold; color: #ff6600; background-color: #f4f4f4; padding: 10px; border-radius: 5px; display: inline-block;'>
                $otp
				</p>
				</center>

				<p>Please enter this OTP within <b>10 minutes</b> to reset your password.</p>

				<p>If you have any issues, please contact our support team:</p>
				<p>📞 Contact: +91 63520 10444</p>
				<p>🌐 Website: <a href='https://www.socialite.com'>www.socialite.com</a></p>

				<br>
				<p>Thank you,</p>
				<p><b>Socialite Team</b></p>
				<hr>
				<p style='text-align: center; font-size: 12px; color: #888;'>© " . date("Y") . " Socialite. All rights reserved.</p>
				</div>
";


                $mail->send();
                $message = "An OTP has been sent to your email! Please enter the OTP below.";
                
                // Store email in session for later use
                $_SESSION['email'] = $email;

            } catch (Exception $e) {
                $error = "Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "No user found with that email.";
        }
        $stmt->close();
    }

    // OTP verification (after OTP is entered)
    if (isset($_POST['otp'])) {
        $entered_otp = $_POST['otp'];
        // Get the user's email from the session
        $email = $_SESSION['email'];

        // Check if the OTP is valid
        $stmt = $conn->prepare("SELECT id, otp_expiration FROM tbl_register WHERE otp = ? AND email = ?");
        $stmt->bind_param("ss", $entered_otp, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $otp_expiration);
            $stmt->fetch();

            // Check if the OTP has expired
            if (strtotime($otp_expiration) < time()) {
                $error = "The OTP has expired.";
            } else {
                // Redirect to the reset password page
                header("Location: reset_password.php?email=" . urlencode($email));
                exit();
            }
        } else {
            $error = "Invalid or expired OTP.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .forgot-password-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .forgot-password-container h3 {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            max-width: 200px; /* Adjust the size of the logo */
        }
        .alert {
            margin-bottom: 20px;
        }
        .form-container {
            padding: 20px;
        }
    </style>
</head>
<body>

    <div class="forgot-password-container">
        <!-- Logo at the top of the form -->
        <img src="./socialminds\assets\images\logo-light.png" alt="Logo" class="logo" />
		

        <h3>Forgot Password</h3>

        <?php if (isset($message)) { echo "<div class='alert alert-success'>$message</div>"; } ?>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <!-- Email Step -->
        <?php if (!isset($_POST['otp'])): ?>
        <div class="form-container">
            <form method="POST" action="">
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Send OTP</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- OTP Verification Step -->
        <?php if (isset($_POST['email']) && !isset($_POST['new_password'])): ?>
        <div class="form-container">
            <form method="POST" action="">
                <div class="mb-3">
                    <input type="text" name="otp" class="form-control" placeholder="Enter OTP" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
            </form>
        </div>
        <?php endif; ?>
    </div>

</body>
</html>