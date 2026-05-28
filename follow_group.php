<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    die("❌ Please log in.");
}

if (isset($_POST['join_group'])) {
    $groupId = intval($_POST['group_id']);

    // Check if the user is already following the group
    $checkQuery = "SELECT * FROM tbl_group_followers WHERE user_id = ? AND group_id = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ii", $userId, $groupId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        $followQuery = "INSERT INTO tbl_group_followers (user_id, group_id) VALUES (?, ?)";
        $followStmt = $conn->prepare($followQuery);
        $followStmt->bind_param("ii", $userId, $groupId);
        if ($followStmt->execute()) {
            echo "<script>alert('✅ You have joined the group!'); window.location.href='groups.php';</script>";
        } else {
            echo "<script>alert('❌ Failed to join group.');</script>";
        }
    } else {
        echo "<script>alert('⚠️ You are already a member of this group.');</script>";
    }
}
?>
