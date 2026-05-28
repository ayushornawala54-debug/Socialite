<?php
session_start();
// Database Connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'db_socialmedia';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id']; // assuming user session is stored
$text = $_POST['text'];
$media_type = 'text';
$media_path = null;

// Handle file upload if exists
if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
    $filename = time() . '_' . basename($_FILES['file']['name']);
    $target_dir = "uploads/stories/";
    $target_file = $target_dir . $filename;
    move_uploaded_file($_FILES['file']['tmp_name'], $target_file);

    $media_path = $target_file;
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $media_type = in_array(strtolower($ext), ['mp4', 'mov']) ? 'video' : 'image';
}

// Set expiry time
$created_at = date("Y-m-d H:i:s");
$expires_at = date("Y-m-d H:i:s", strtotime("+24 hours"));

// Insert into DB
$stmt = $conn->prepare("INSERT INTO tbl_stories (user_id, content, media_type, media_path, created_at, expires_at) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $user_id, $text, $media_type, $media_path, $created_at, $expires_at);
$stmt->execute();
$stmt->close();

echo "Story uploaded!";
?>
