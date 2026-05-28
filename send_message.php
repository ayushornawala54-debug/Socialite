<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    exit("Error: User not logged in");
}

error_log("Session User ID: " . $_SESSION['user_id']);

if (!isset($_SESSION['user_id']) || !isset($_POST['message']) || !isset($_POST['receiver_id'])) {
    exit("Invalid request");
}

$sender_id = $_SESSION['user_id'];
$receiver_id = $_POST['receiver_id'];
$message = trim($_POST['message']);

// Retrieve sender's username
$sender_stmt = $conn->prepare("SELECT username FROM user_profiles WHERE id = ?");
$sender_stmt->bind_param("i", $sender_id);
$sender_stmt->execute();
$sender_result = $sender_stmt->get_result();
$sender_name = ($sender_result->num_rows > 0) ? $sender_result->fetch_assoc()['username'] : '';

if (!$sender_name) {
    exit("Error: Sender not found");
}

// Retrieve receiver's username
$receiver_stmt = $conn->prepare("SELECT username FROM user_profiles WHERE id = ?");
$receiver_stmt->bind_param("i", $receiver_id);
$receiver_stmt->execute();
$receiver_result = $receiver_stmt->get_result();
$receiver_name = ($receiver_result->num_rows > 0) ? $receiver_result->fetch_assoc()['username'] : '';

if (!$receiver_name) {
    exit("Error: Receiver not found");
}

// Store message in database with correct sender and receiver names
$insert_stmt = $conn->prepare("INSERT INTO tbl_message (sender_id, sender_name, receiver_id, receiver_name, message, timestamp) VALUES (?, ?, ?, ?, ?, NOW())");
$insert_stmt->bind_param("issss", $sender_id, $sender_name, $receiver_id, $receiver_name, $message);

if ($insert_stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to send message"]);
}
?>
