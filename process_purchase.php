<?php
include 'db_connect.php'; // make sure this connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $user_id = $_POST['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $payment_method = $_POST['payment_method'];
    $upi_id = $_POST['upi_id'] ?? null;
    $card_number = $_POST['card_number'] ?? null;
    $card_expiry = $_POST['card_expiry'] ?? null;
    $address = $_POST['address'];
    $total_amount = $_POST['total_amount'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO tbl_purchases 
        (user_id, product_id, quantity, payment_method, upi_id, card_number, card_expiry, address, total_amount) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("iiisssssd", 
        $user_id, $product_id, $quantity, $payment_method, $upi_id, $card_number, $card_expiry, $address, $total_amount
    );

    if ($stmt->execute()) {
        echo "Purchase recorded successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
