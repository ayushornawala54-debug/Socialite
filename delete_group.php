<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "❌ Connection failed"]));
}

if (!isset($_SESSION['user_email'])) {
    echo json_encode(["success" => false, "message" => "❌ User not logged in"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$group_id = $data['group_id'];
$email = $_SESSION['user_email'];

// Check if the user owns the group
$stmt = $conn->prepare("SELECT id FROM tbl_groups WHERE id = ? AND created_by = (SELECT id FROM tbl_register WHERE email = ?)");
$stmt->bind_param("is", $group_id, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "❌ You do not have permission to delete this group"]);
    exit();
}

// Delete the group
$deleteStmt = $conn->prepare("DELETE FROM tbl_groups WHERE id = ?");
$deleteStmt->bind_param("i", $group_id);
if ($deleteStmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "❌ Error deleting group"]);
}
?>
