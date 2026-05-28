<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Check if file is uploaded
if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
    die("❌ Error: No file uploaded.");
}

// Allowed file types
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$file_type = $_FILES['profile_photo']['type'];

if (!in_array($file_type, $allowed_types)) {
    die("❌ Error: Only JPG, PNG, and GIF files are allowed.");
}

// Create uploads directory if it doesn't exist
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Generate unique file name
$filename = $upload_dir . time() . "_" . basename($_FILES['profile_photo']['name']);
move_uploaded_file($_FILES['profile_photo']['tmp_name'], $filename);

// Update the database
$sql = "UPDATE tbl_register SET profile_photo = '$filename' WHERE id = '$user_id'";
if ($conn->query($sql) === TRUE) {
    $_SESSION['profile_photo'] = $filename; // Update session
    echo "✅ Profile photo updated successfully.";
} else {
    echo "❌ Error updating profile photo: " . $conn->error;
}

$conn->close();
?>
