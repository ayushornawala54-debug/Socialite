<?php
session_start();
// Database connection
$conn = new mysqli("localhost", "root", "", "db_socialmedia");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: form-login.php");
    exit();
}
$username = $_SESSION['username'] ?? '';

$query = "SELECT * FROM products WHERE username = '$username'";
$result = mysqli_query($conn, $query);
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <?php while ($row = mysqli_fetch_assoc($result)) {
        $product_id = $row['id'];
        $purchase_query = "SELECT COUNT(*) AS purchase_count FROM purchases WHERE product_id = '$product_id'";
        $purchase_result = mysqli_query($conn, $purchase_query);
        $purchase_count = mysqli_fetch_assoc($purchase_result)['purchase_count'] ?? 0;

        $rating_query = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE product_id = '$product_id'";
        $rating_result = mysqli_query($conn, $rating_query);
        $avg_rating = round($rating_result->fetch_assoc()['avg_rating'] ?? 0, 1);
    ?>
    <div class="card border rounded-lg shadow-md p-4 bg-white">
        <div class="card-media sm:aspect-[2/1.7] h-36 overflow-hidden">
            <img src="uploads/<?php echo $row['image']; ?>" class="w-full h-full object-cover rounded-lg" alt="">
        </div>
        <div class="card-body relative mt-2">
            <span class="text-teal-600 font-semibold text-xs">Seller: <?php echo $row['username']; ?></span>
            <p class="card-text block text-black mt-0.5 font-medium"><?php echo $row['product_name']; ?></p>
            <p class="text-sm text-gray-600 mt-1"><?php echo $row['product_description']; ?></p>
            <p class="text-sm text-yellow-500 mt-1">⭐ <?php echo $avg_rating > 0 ? "$avg_rating / 5" : "No ratings yet"; ?></p>
            <div class="absolute top-2 right-2 bg-blue-100 font-medium px-2 py-0.5 rounded-full text-blue-500 text-sm">
                ₹<?php echo $row['price']; ?>
            </div>
        </div>

        <!-- Edit & Remove Buttons -->
        <div class="flex justify-between gap-2 mt-3">
            <a href="edit-product.php?id=<?php echo $product_id; ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded-lg w-full text-center">Edit</a>
            <form action="delete-product.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" class="w-full">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded-lg w-full">Remove</button>
            </form>
        </div>
    </div>
    <?php } ?>
</div>
