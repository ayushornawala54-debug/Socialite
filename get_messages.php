<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id']) || !isset($_GET['receiver_id'])) {
    exit("Invalid request");
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'];

// Fetch all messages between the sender and receiver
$stmt = $conn->prepare("
    SELECT sender_id, sender_name, receiver_id, receiver_name, message, timestamp 
    FROM tbl_message 
    WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) 
    ORDER BY timestamp ASC
");
$stmt->bind_param("iiii", $user_id, $receiver_id, $receiver_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
