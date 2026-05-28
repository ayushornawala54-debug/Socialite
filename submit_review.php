<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$product_id = intval($_POST['product_id']);
$username = $_POST['username'] ?? 'Anonymous';
$rating = intval($_POST['rating']);
$review_text = trim($_POST['review_text']);

if ($rating < 1 || $rating > 5) {
    echo "Invalid rating.";
    exit;
}

// Insert review into the database
$stmt = $conn->prepare("INSERT INTO reviews (product_id, username, rating, review_text) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isis", $product_id, $username, $rating, $review_text);

if ($stmt->execute()) {
    echo "Review submitted successfully!";
} else {
    echo "Error submitting review.";
}
?>
