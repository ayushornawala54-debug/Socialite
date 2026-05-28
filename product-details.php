<?php
session_start();
$conn = new mysqli("localhost", "root", "", "db_socialmedia");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("Product not found.");
}

$product_id = intval($_GET['id']);

// Fetch product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("Product not found.");
}

// Fetch average rating
$rating_query = "SELECT AVG(rating) AS avg_rating FROM reviews WHERE product_id = ?";
$rating_stmt = $conn->prepare($rating_query);
$rating_stmt->bind_param("i", $product_id);
$rating_stmt->execute();
$rating_result = $rating_stmt->get_result();
$rating_data = $rating_result->fetch_assoc();
$avg_rating = round($rating_data['avg_rating'] ?? 0, 1);

// Fetch reviews
$reviews_query = "SELECT username, rating, review_text, created_at FROM reviews WHERE product_id = ? ORDER BY created_at DESC";
$reviews_stmt = $conn->prepare($reviews_query);
$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();
$reviews_result = $reviews_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['product_name']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    const quantityInput = document.getElementById("quantity");
    const priceElement = document.getElementById("total-price");
    const basePrice = parseFloat("<?php echo $product['price']; ?>");

    quantityInput.addEventListener("input", updateTotal);
    document.querySelectorAll('.payment-option').forEach(button => {
        button.addEventListener("click", function() {
            document.querySelectorAll('.payment-option').forEach(btn => btn.classList.remove('border-blue-500', 'shadow-lg'));
            this.classList.add('border-blue-500', 'shadow-lg');
            updateTotal();
            showPaymentFields(this.dataset.value);
        });
    });

    function updateTotal() {
        let quantity = parseInt(quantityInput.value);
        if (quantity < 1) quantity = 1;

        let selectedMethod = document.querySelector('.payment-option.border-blue-500')?.dataset.value || "";
        let productTotal = basePrice * quantity;
        let gst = productTotal * 0.10;
        let deliveryCharge = selectedMethod === "COD" ? 120 : 0;
        let finalTotal = productTotal + gst + deliveryCharge;

        document.getElementById("final-amount").innerText = `₹${finalTotal.toFixed(2)}`;
        document.getElementById("charges-info").innerHTML = `GST: ₹${gst.toFixed(2)}<br>Delivery: ₹${deliveryCharge}`;
    }

    function showPaymentFields(method) {
        document.getElementById("upi-input").classList.add("hidden");
        document.getElementById("card-inputs").classList.add("hidden");

        if (method === "UPI") {
            document.getElementById("upi-input").classList.remove("hidden");
        } else if (method === "CARD") {
            document.getElementById("card-inputs").classList.remove("hidden");
        }
    }
});

function confirmPurchase() {
    let paymentMethod = document.querySelector('.payment-option.border-blue-500')?.dataset.value;
    let address = document.getElementById("user-address").value;
    let quantity = parseInt(document.getElementById("quantity").value);
    let productId = "<?php echo $product_id; ?>"; 

    if (!paymentMethod) {
        alert("Please select a payment method.");
        return;
    }

    if (address.trim() === "") {
        alert("Please enter your address.");
        return;
    }

    let paymentDetails = "";
    if (paymentMethod === "UPI") {
        paymentDetails = document.getElementById("upi-id").value.trim();
        if (!paymentDetails) {
            alert("Please enter your UPI ID.");
            return;
        }
    } else if (paymentMethod === "CARD") {
        let cardNumber = document.getElementById("card-number").value.trim();
        let expiry = document.getElementById("card-expiry").value.trim();
        let cvv = document.getElementById("card-cvv").value.trim();
        if (!cardNumber || !expiry || !cvv) {
            alert("Please enter complete card details.");
            return;
        }
        paymentDetails = `Card Ending in ${cardNumber.slice(-4)}`;
    }

    fetch("update_purchases.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `product_id=${productId}&quantity=${quantity}&payment_method=${paymentMethod}&payment_details=${encodeURIComponent(paymentDetails)}&address=${encodeURIComponent(address)}`
    })
    .then(response => response.text())
    .then(data => {
        alert(`Purchase Confirmed!\nPayment Mode: ${paymentMethod}\nDetails: ${paymentDetails}\nTotal: ` + document.getElementById("final-amount").innerText);
        closePopup();
    })
    .catch(error => console.error("Error:", error));
}



        function showPopup() {
            document.getElementById("payment-popup").classList.remove("hidden");
            setTimeout(() => document.getElementById("popup-box").classList.add("scale-100"), 10);
        }

        function closePopup() {
            document.getElementById("popup-box").classList.remove("scale-100");
            setTimeout(() => document.getElementById("payment-popup").classList.add("hidden"), 300);
        }



    </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden mt-10 p-6">
        <h1 class="text-3xl font-bold text-gray-900"><?php echo $product['product_name']; ?></h1>
        <div class="flex flex-col md:flex-row gap-6 mt-4">
            <div class="md:w-1/2">
                <img id="product-img" src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['product_name']; ?>" class="w-full h-80 object-cover rounded-lg transition-transform duration-300 hover:scale-110">
            </div>
            <div class="md:w-1/2 flex flex-col">
                <p class="text-gray-600 text-lg"><?php echo $product['product_description']; ?></p>
                
                <p class="text-2xl font-semibold text-blue-700 mt-3" id="total-price">₹<?php echo $product['price']; ?></p>
                <div class="mt-2 flex items-center gap-2">
                    <label for="quantity" class="font-semibold">Quantity:</label>
                    <input type="number" id="quantity" value="1" min="1" class="border p-2 w-16 text-center rounded-md shadow-sm">
                </div>

                <p class="text-lg text-yellow-500 font-semibold mt-2">⭐ <?php echo $avg_rating > 0 ? $avg_rating . " / 5" : "No ratings available"; ?></p>
                
                <button onclick="showPopup()" class="mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg text-lg flex items-center justify-center gap-2 transition-all duration-300">
                    🛒 Buy Now
                </button>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="mt-8">
            <h2 class="text-xl font-bold text-gray-800">Customer Reviews</h2>
            <div class="mt-4">
                <?php if ($reviews_result->num_rows > 0): ?>
                    <?php while ($review = $reviews_result->fetch_assoc()): ?>
                        <div class="bg-gray-50 p-4 rounded-lg shadow-md mb-4">
                            <p class="text-gray-800 font-semibold"><?php echo htmlspecialchars($review['username']); ?></p>
                            <p class="text-yellow-500">⭐ <?php echo $review['rating']; ?> / 5</p>
                            <p class="text-gray-700 mt-1"><?php echo htmlspecialchars($review['review_text']); ?></p>
                            <p class="text-gray-400 text-sm mt-1"><?php echo date("F j, Y", strtotime($review['created_at'])); ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-500">No reviews available for this product.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
<div id="payment-popup" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 flex justify-center items-center transition-opacity duration-300">
    <div id="popup-box" class="bg-white p-6 rounded-xl shadow-2xl w-96 transform scale-90 transition-transform duration-300">
        <h2 class="text-xl font-bold mb-4 text-center text-gray-800">Select Payment Method</h2>

        <div class="grid grid-cols-3 gap-3 mb-4">
            <button class="payment-option border border-gray-300 p-3 rounded-lg text-center transition-all duration-300" data-value="UPI">
                💳 <br> UPI
            </button>
            <button class="payment-option border border-gray-300 p-3 rounded-lg text-center transition-all duration-300" data-value="CARD">
                🏦 <br> Card
            </button>
            <button class="payment-option border border-gray-300 p-3 rounded-lg text-center transition-all duration-300" data-value="COD">
                🚚 <br> COD
            </button>
        </div>

        <!-- UPI ID Input -->
        <div id="upi-input" class="hidden">
            <label for="upi-id" class="block mb-2 text-gray-700">Enter UPI ID:</label>
            <input type="text" id="upi-id" class="w-full p-2 border rounded focus:border-blue-500 focus:ring-blue-500" placeholder="example@upi">
        </div>

        <!-- Card Details Input -->
        <div id="card-inputs" class="hidden">
            <label class="block mb-2 text-gray-700">Card Details:</label>
            <input type="text" id="card-number" class="w-full p-2 border rounded mb-2 focus:border-blue-500 focus:ring-blue-500" placeholder="Card Number">
            <div class="flex gap-2">
                <input type="text" id="card-expiry" class="w-1/2 p-2 border rounded focus:border-blue-500 focus:ring-blue-500" placeholder="MM/YY">
                <input type="text" id="card-cvv" class="w-1/2 p-2 border rounded focus:border-blue-500 focus:ring-blue-500" placeholder="CVV">
            </div>
        </div>

        <label for="user-address" class="block mt-4 mb-2 text-gray-700">Enter Address:</label>
        <textarea id="user-address" class="w-full p-2 border rounded mb-4 focus:border-blue-500 focus:ring-blue-500" placeholder="Enter your delivery address"></textarea>

        <div class="bg-gray-100 p-3 rounded-lg text-center">
            <p class="text-lg font-semibold text-gray-800">Total: <span id="final-amount" class="text-blue-600">₹<?php echo $product['price']; ?></span></p>
            <p class="text-sm text-gray-500" id="charges-info">GST: ₹0<br>Delivery: ₹0</p>
        </div>

        <div class="flex justify-between mt-4">
            <button onclick="closePopup()" class="bg-gray-500 text-white px-4 py-2 rounded-lg">Cancel</button>
            <button onclick="confirmPurchase()" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg">Confirm</button>
        </div>
    </div>
</div>

</body>
</html>
