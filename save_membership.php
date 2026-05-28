<?php
session_start();

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "db_socialmedia";

$conn = new mysqli($host, $username, $password, $database);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"] ?? null;
    $plan = $_POST["plan"] ?? null;
    $end_date = $_POST["end_date"] ?? null;

    if (!$user_id || !$plan || !$end_date) {
        echo "Invalid request.";
        exit;
    }

    // Store membership in database
    $stmt = $conn->prepare("INSERT INTO tbl_subscription (user_id, plan, start_date, end_date) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("sss", $user_id, $plan, $end_date);

    if ($stmt->execute()) {
        echo "Membership activated until " . $end_date;
    } else {
        echo "Error saving membership.";
    }

    $stmt->close();
    $conn->close();
}
?>
