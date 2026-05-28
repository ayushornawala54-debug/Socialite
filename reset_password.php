<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the URL
    $email = $_GET['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
    // Update tbl_register
    $stmt1 = $conn->prepare("UPDATE tbl_register SET password = ? WHERE email = ?");
    $stmt1->bind_param("ss", $new_password, $email);
    $stmt1->execute();
    if ($stmt1->error) {
        echo "Error in tbl_register update: " . $stmt1->error;
    }
    $stmt1->close();

    // Update user_profiles
    $stmt2 = $conn->prepare("UPDATE user_profiles SET password = ? WHERE email = ?");
    $stmt2->bind_param("ss", $new_password, $email);
    $stmt2->execute();
    if ($stmt2->error) {
        echo "Error in user_profiles update: " . $stmt2->error;
    }
    $stmt2->close();

    $conn->close();

    header("Location: form-login.php?message=Password updated successfully");
    exit();
} else {
    $error = "Passwords do not match.";
}

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap CSS link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f7f7f7;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .reset-password-container {
            background-color: white;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .reset-password-container h3 {
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
    </style>
</head>
<body>

    <div class="reset-password-container">
        <!-- Logo at the top of the form -->
        <img src="./socialminds\assets\images\logo-light.png" alt="Logo" class="logo" />

        <h3>Reset Your Password</h3>

        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

        <form method="POST" action="">
            <div class="mb-3">
                <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
            </div>
            <div class="mb-3">
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>