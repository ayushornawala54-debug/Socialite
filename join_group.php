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

// Check if already following the group
$checkQuery = "SELECT * FROM tbl_group_followers WHERE user_id = ? AND group_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $user_id, $group_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Already following this group."]);
    exit();
}

// Insert follow record
$insertQuery = "INSERT INTO tbl_group_followers (user_id, group_id) VALUES (?, ?)";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("ii", $user_id, $group_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Followed the group successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to follow the group."]);
}

$conn->close();
?>
