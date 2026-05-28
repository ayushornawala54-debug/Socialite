<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    die("❌ Error: Please log in first.");
}

// Handle Profile Picture Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $userID = $_SESSION['userID'];
    $targetDir = "uploads/";
    $fileName = basename($_FILES['profile_pic']['name']);
    $targetFilePath = $targetDir . $fileName;

    // Allowed file types
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    if (in_array($fileType, $allowedTypes)) {
        // Upload the file
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetFilePath)) {
            // Update profile picture in the database
            $stmt = $conn->prepare("UPDATE tbl_register SET profile_pic = ? WHERE id = ?");
            $stmt->bind_param("si", $fileName, $userID);

            if ($stmt->execute()) {
                echo "✅ Profile picture updated successfully!";
            } else {
                echo "❌ Database update failed.";
            }
        } else {
            echo "❌ Error uploading the file.";
        }
    } else {
        echo "❌ Only JPG, JPEG, PNG, and GIF files are allowed.";
    }
}
?>
