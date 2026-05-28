<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

if (!isset($_SESSION['user_id'])) {
    die(json_encode(["success" => false, "message" => "User not logged in."]));
}

$userId = $_SESSION['user_id'];
$groupId = $_POST['group_id'];
$action = $_POST['action'];

if ($action === "join") {
    $query = "INSERT INTO group_members (user_id, group_id) VALUES (?, ?)";
} elseif ($action === "unfollow") {
    $query = "DELETE FROM group_members WHERE user_id = ? AND group_id = ?";
} else {
    die(json_encode(["success" => false, "message" => "Invalid action."]));
}

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $userId, $groupId);
if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Database error."]);
}
?>
