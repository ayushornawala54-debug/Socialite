<?php
session_start();
$servername = "localhost";
$username = "root"; 
$password = ""; 
$database = "db_socialmedia"; 

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id']; // Ensure user is logged in
$event_id = $_POST['event_id'];

// Check if the user has already shown interest
$query = $conn->prepare("SELECT * FROM event_interests WHERE user_id = ? AND event_id = ?");
$query->bind_param("ii", $user_id, $event_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    // If user is already interested, remove interest
    $conn->query("DELETE FROM event_interests WHERE user_id = $user_id AND event_id = $event_id");
    $conn->query("UPDATE events SET interested = interested - 1 WHERE id = $event_id");

    echo json_encode(["status" => "uninterested"]);
} else {
    // If not interested, add interest
    $conn->query("INSERT INTO event_interests (user_id, event_id, status) VALUES ($user_id, $event_id, 'interested')");
    $conn->query("UPDATE events SET interested = interested + 1 WHERE id = $event_id");

    echo json_encode(["status" => "interested"]);
}

$conn->close();
?>
