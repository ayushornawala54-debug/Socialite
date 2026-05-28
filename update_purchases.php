<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized: Please log in.");
}

$user_id = $_SESSION['user_id'];

// Validate POST data
if (
    !isset($_POST['product_id']) ||
    !isset($_POST['quantity']) ||
    !isset($_POST['payment_method'])
) {
    die("Missing required fields.");
}

$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);
$payment_method = $conn->real_escape_string($_POST['payment_method']);
$payment_details = isset($_POST['payment_details']) ? $conn->real_escape_string($_POST['payment_details']) : "";

// Fetch product price
$product_stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
$product_stmt->bind_param("i", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

if ($product_result->num_rows === 0) {
    die("Invalid product.");
}

$product = $product_result->fetch_assoc();
$base_price = $product['price'];

// Calculate totals
$subtotal = $base_price * $quantity;
$gst = $subtotal * 0.10;
$delivery = $payment_method === "COD" ? 120 : 0;
$total_amount = $subtotal + $gst + $delivery;

// Insert purchase
$insert_stmt = $conn->prepare("INSERT INTO purchases (user_id, product_id, quantity, payment_method, payment_details, total_amount, purchase_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
$insert_stmt->bind_param("iiissd", $user_id, $product_id, $quantity, $payment_method, $payment_details, $total_amount);

if ($insert_stmt->execute()) {
    echo "Purchase successful!";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
