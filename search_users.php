<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

$search = $_GET['query'] ?? '';

$sql = "SELECT id, username, email FROM tbl_register WHERE username LIKE ?";
$stmt = $conn->prepare($sql);
$search = "%$search%";
$stmt->bind_param("s", $search);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>
