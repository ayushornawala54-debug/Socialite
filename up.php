<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");

if (!isset($_SESSION['username'])) {
    die("Please log in first.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $product_description = mysqli_real_escape_string($conn, $_POST['product_description']);
    $price = floatval($_POST['price']);
    $username = $_SESSION['username'];

    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $image_name = time() . '_' . $_FILES['product_image']['name'];
        $image_tmp = $_FILES['product_image']['tmp_name'];
        $image_path = 'uploads/' . $image_name;

        if (move_uploaded_file($image_tmp, $image_path)) {
            $query = "INSERT INTO products (username, product_name, product_description, price, image) VALUES ('$username', '$product_name', '$product_description', '$price', '$image_name')";
            if (mysqli_query($conn, $query)) {
                echo "Product uploaded successfully!";
            } else {
                echo "Database error: " . mysqli_error($conn);
            }
        } else {
            echo "Failed to upload image.";
        }
    } else {
        echo "No image uploaded or an error occurred.";
    }
}
?>

<!-- Keep your PHP part as is above -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Product</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@tailwindcss/forms"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert for nicer alerts -->
    <style>
        body {
            background: url('./images/tree.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            z-index: -1;
        }
        .preview-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 0 8px rgba(0,0,0,0.2);
            display: none;
            transition: all 0.3s ease-in-out;
        }
        .error {
            color: red;
            font-size: 14px;
            margin-top: 4px;
        }
    </style>
</head>
<body>
    <div class="max-w-md w-full bg-white bg-opacity-90 p-8 rounded-2xl shadow-2xl animate-fadeIn">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Upload Product</h2>
        <form id="uploadForm" action="product.php" method="POST" enctype="multipart/form-data" class="space-y-4">

            <div>
                <label class="block text-gray-700 mb-1">Product Name</label>
                <input type="text" name="product_name" id="product_name" class="w-full border border-gray-300 p-2 rounded-md focus:ring-2 focus:ring-blue-400 transition" required>
                <p id="nameError" class="error"></p>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Product Description</label>
                <textarea name="product_description" id="product_description" class="w-full border border-gray-300 p-2 rounded-md focus:ring-2 focus:ring-blue-400 transition" rows="3" required></textarea>
                <p id="descError" class="error"></p>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Price (₹)</label>
                <input type="number" name="price" id="price" step="0.01" class="w-full border border-gray-300 p-2 rounded-md focus:ring-2 focus:ring-blue-400 transition" required>
                <p id="priceError" class="error"></p>
            </div>

            <div>
                <label class="block text-gray-700 mb-1">Upload Image</label>
                <input type="file" name="product_image" id="product_image" accept="image/*" class="w-full border border-gray-300 p-2 rounded-md" required>
                <div class="preview-container flex justify-center mt-3">
                    <img id="preview" class="preview-img" alt="Image Preview">
                </div>
                <p id="imageError" class="error"></p>
            </div>

            <button type="submit" id="submitBtn" class="bg-blue-500 hover:bg-blue-600 text-white w-full p-2 rounded-md transition duration-300 flex items-center justify-center">
                <span id="btnText">Upload</span>
                <svg id="loadingSpinner" class="w-5 h-5 ml-2 text-white animate-spin hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </button>
        </form>
    </div>

    <script>
        const preview = document.getElementById('preview');
        const fileInput = document.getElementById('product_image');

        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('uploadForm').addEventListener('submit', function (e) {
            let isValid = true;
            document.querySelectorAll('.error').forEach(el => el.textContent = '');

            const name = document.getElementById('product_name').value.trim();
            const desc = document.getElementById('product_description').value.trim();
            const price = document.getElementById('price').value.trim();
            const image = fileInput.files.length;

            if (name.length < 3) {
                document.getElementById('nameError').textContent = "Name must be at least 3 characters.";
                isValid = false;
            }
            if (desc.length < 10) {
                document.getElementById('descError').textContent = "Description must be at least 10 characters.";
                isValid = false;
            }
            if (!price || parseFloat(price) <= 0) {
                document.getElementById('priceError').textContent = "Price must be a positive number.";
                isValid = false;
            }
            if (image === 0) {
                document.getElementById('imageError').textContent = "Please upload an image.";
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                return;
            }

            document.getElementById('btnText').textContent = "Uploading...";
            document.getElementById('loadingSpinner').classList.remove('hidden');
        });

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <?php if (isset($query) && $query): ?>
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Product Uploaded!',
                    text: 'Your product has been successfully uploaded.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }, 200);
        <?php elseif (isset($query)): ?>
            setTimeout(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: 'Something went wrong while uploading.',
                });
            }, 200);
        <?php endif; ?>
        <?php endif; ?>
    </script>
</body>
</html>

