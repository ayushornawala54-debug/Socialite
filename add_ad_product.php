<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $uploader = 'admin';

    $imagePath = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    $conn = new mysqli("localhost", "root", "", "db_socialmedia");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $stmt = $conn->prepare("INSERT INTO ad_product (name, description, price, image, uploader) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $name, $description, $price, $imagePath, $uploader);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    echo "<script>alert('Product added successfully'); window.location.href='product-view-1.php';</script>";
}
?>

<!-- Frontend starts here -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-white min-h-screen flex flex-col items-center justify-center p-6">

    <!-- Header -->
    <div class="w-full max-w-xl mb-6 flex items-center justify-between">
        <h2 class="text-3xl font-bold text-gray-800">Add New Product</h2>
        <a href="admin_feed.php" class="text-sm text-blue-600 hover:underline">← Back to Products</a>
    </div>

    <!-- Form -->
    <form action="add_ad_product.php" method="POST" enctype="multipart/form-data"
        class="bg-white w-full max-w-xl p-8 rounded-xl shadow-lg space-y-6">

        <!-- Product Name -->
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Product Name</label>
            <input type="text" name="name" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>

        <!-- Description -->
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="3" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none resize-none"></textarea>
        </div>

        <!-- Price -->
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Price (₹)</label>
            <input type="number" name="price" min="1" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>

        <!-- Image Upload -->
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Upload Product Image</label>
            <input type="file" name="image" accept="image/*" required
                class="w-full file:rounded file:border-0 file:bg-blue-600 file:text-white file:cursor-pointer hover:file:bg-blue-700">
        </div>

        <!-- Submit Button -->
        <div class="text-right">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                Add Product
            </button>
        </div>

    </form>

</body>
</html>
