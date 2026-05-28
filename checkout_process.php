<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("❌ User not logged in. Please log in first.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submitt'])) {
    $username = $_SESSION['username']; // Get username from session
    $name = htmlspecialchars($_POST['name']);
    $card_number = htmlspecialchars($_POST['card_number']);
    $expiry_date = htmlspecialchars($_POST['expiry_date']);
    $cvv = htmlspecialchars($_POST['cvv']);

    // Insert data into payments table
    $stmt = $conn->prepare("INSERT INTO payments (username, name, card_number, expiry_date, cvv) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $name, $card_number, $expiry_date, $cvv);

    if ($stmt->execute()) {
        echo "✅ Payment successful!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
