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
// Validate if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$content = isset($_POST['story_text']) ? mysqli_real_escape_string($conn, $_POST['story_text']) : '';
$media_path = '';
$media_type = '';

if (isset($_FILES['story_file']) && $_FILES['story_file']['error'] === 0) {
    $file = $_FILES['story_file'];
    $file_name = uniqid() . "_" . basename($file['name']);
    $target_dir = "uploads/stories/";
    $target_path = $target_dir . $file_name;

    // Create directory if not exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_type = mime_content_type($file['tmp_name']);
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        $media_path = $target_path;
        $media_type = strpos($file_type, "video") !== false ? "video" : "image";
    } else {
        echo json_encode(["status" => "error", "message" => "File upload failed"]);
        exit;
    }
}

$created_at = date('Y-m-d H:i:s');
$expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

$sql = "INSERT INTO tbl_stories (user_id, content, media_path, media_type, created_at, expires_at)
        VALUES ('$user_id', '$content', '$media_path', '$media_type', '$created_at', '$expires_at')";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

$conn->close();
?>