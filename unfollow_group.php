<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "db_socialmedia");
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['group_id'])) {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit();
}

$group_id = $data['group_id'];

// Delete follow record
$deleteQuery = "DELETE FROM tbl_group_followers WHERE user_id = ? AND group_id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("ii", $user_id, $group_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Unfollowed the group successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to unfollow the group."]);
}

$conn->close();
?>
