<?php
$conn = new mysqli("localhost", "root", "", "db_socialmedia");
$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM ad_product WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
if (!$product) {
    echo "<h2 class='text-center text-red-500 mt-10 text-xl'>Product not found</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> - Product Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(to right, #dbeafe, #f0fdfa);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 font-sans">

    <div class="bg-white max-w-4xl w-full shadow-2xl rounded-3xl overflow-hidden grid grid-cols-1 md:grid-cols-2 animate-fade-in">
        <div class="h-72 md:h-auto">
            <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="w-full h-full object-cover rounded-l-3xl">
        </div>

        <div class="p-8 flex flex-col justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-3"><?= htmlspecialchars($product['name']) ?></h1>
                <p class="text-gray-600 mb-5"><?= htmlspecialchars($product['description']) ?></p>
                <div class="text-2xl text-green-600 font-extrabold mb-4">₹<span id="price"><?= $product['price'] ?></span></div>

                <!-- Quantity -->
                <div class="flex items-center gap-4 mb-4">
                    <span class="text-lg font-medium">Quantity:</span>
                    <button onclick="adjustQty(-1)" class="bg-gray-200 px-3 py-1 rounded hover:bg-gray-300">−</button>
                    <input type="number" id="quantity" value="1" min="1" class="w-16 text-center border rounded">
                    <button onclick="adjustQty(1)" class="bg-gray-200 px-3 py-1 rounded hover:bg-gray-300">+</button>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col md:flex-row items-center gap-4">
                <button onclick="openModal()" class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-blue-500 to-green-400 text-white font-semibold rounded-xl shadow-md hover:from-blue-600 hover:to-green-500 transition-all duration-300">
                    🛒 Buy Now
                </button>
                <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-800 font-medium underline transition duration-200">
                    ← Go Back
                </a>
            </div>
        </div>
    </div>

    <!-- Modal -->
   <!-- Modal -->
<div id="paymentModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-xl p-8 w-full max-w-lg space-y-4 shadow-lg animate-fade-in">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Select Payment Method</h2>

    <!-- Payment Method -->
    <div class="space-y-2">
      <label><input type="radio" name="payment" value="UPI" checked> UPI</label><br>
      <label><input type="radio" name="payment" value="CARD"> Card</label><br>
      <label><input type="radio" name="payment" value="COD"> Cash on Delivery</label>
    </div>

    <!-- Payment Details Section -->
    <div id="upiField" class="mt-4 hidden">
      <label class="block font-medium">UPI ID:</label>
      <input type="text" id="upiId" class="w-full border rounded p-2 mt-1" placeholder="example@upi">
    </div>

    <div id="cardFields" class="mt-4 hidden">
      <label class="block font-medium">Card Number:</label>
      <input type="text" id="cardNumber" class="w-full border rounded p-2 mt-1" placeholder="1234 5678 9012 3456">
      <label class="block font-medium mt-3">Expiry Date:</label>
      <input type="text" id="cardExpiry" class="w-full border rounded p-2 mt-1" placeholder="MM/YY">
    </div>

    <!-- Address Field -->
    <div id="addressField" class="mt-4">
      <label class="block font-medium">Delivery Address:</label>
      <textarea id="address" rows="3" class="w-full border rounded p-2 mt-1" placeholder="Enter your full address..."></textarea>
    </div>

    <!-- Final Amount -->
    <div id="finalAmount" class="mt-4 text-lg font-semibold text-gray-700">
      Final Amount: ₹<span id="final">0</span>
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-4 mt-6">
      <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
      <button onclick="confirmPurchase()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Confirm</button>
    </div>
  </div>
</div>

    <style>
        .animate-fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        const price = <?= $product['price'] ?>;
        const quantityInput = document.getElementById("quantity");
        const finalAmount = document.getElementById("final");


        function openModal() {
            calculateFinalAmount();
            document.getElementById("paymentModal").classList.remove("hidden");
        }

        function closeModal() {
            document.getElementById("paymentModal").classList.add("hidden");
        }

        function calculateFinalAmount() {
            const qty = parseInt(quantityInput.value);
            const baseTotal = price * qty;
            const gst = baseTotal * 0.10;
            let total = baseTotal + gst;

            const method = document.querySelector('input[name="payment"]:checked').value;
            if (method === "COD") {
                total += 120; // Delivery charge
            }

            finalAmount.innerText = total.toFixed(2);
        }

        document.querySelectorAll('input[name="payment"]').forEach(input => {
            input.addEventListener("change", calculateFinalAmount);
        });

        quantityInput.addEventListener("input", calculateFinalAmount);

        function confirmPurchase() {
            const qty = quantityInput.value;
            const method = document.querySelector('input[name="payment"]:checked').value;
            const total = finalAmount.innerText;

            alert(`Purchase Confirmed!\nQuantity: ${qty}\nMethod: ${method}\nTotal: ₹${total}`);

            // TODO: Send this data via AJAX or form to save in the database
            closeModal();
        }
		function adjustQty(change) {
    let current = parseInt(quantityInput.value);
    current = Math.max(1, current + change);
    quantityInput.value = current;
    calculateFinalAmount();
}

function openModal() {
    calculateFinalAmount();
    showPaymentFields();
    document.getElementById("paymentModal").classList.remove("hidden");
}

function closeModal() {
    document.getElementById("paymentModal").classList.add("hidden");
}

function showPaymentFields() {
    const method = document.querySelector('input[name="payment"]:checked').value;
    document.getElementById("upiField").style.display = method === "UPI" ? "block" : "none";
    document.getElementById("cardFields").style.display = method === "CARD" ? "block" : "none";
}

function calculateFinalAmount() {
    const qty = parseInt(quantityInput.value);
    const baseTotal = price * qty;
    const gst = baseTotal * 0.10;
    let total = baseTotal + gst;

    const method = document.querySelector('input[name="payment"]:checked').value;
    if (method === "COD") {
        total += 120;
    }

    finalAmount.innerText = total.toFixed(2);
    showPaymentFields();
}

document.querySelectorAll('input[name="payment"]').forEach(input => {
    input.addEventListener("change", () => {
        calculateFinalAmount();
        showPaymentFields();
    });
});

quantityInput.addEventListener("input", calculateFinalAmount);

function confirmPurchase() {
    const qty = quantityInput.value;
    const method = document.querySelector('input[name="payment"]:checked').value;
    const total = finalAmount.innerText;
    const address = document.getElementById("address").value.trim();

    let paymentDetails = "";

    if (!address) {
        alert("Please enter your delivery address.");
        return;
    }

    if (method === "UPI") {
        const upi = document.getElementById("upiId").value.trim();
        if (!upi) {
            alert("Please enter your UPI ID.");
            return;
        }
        paymentDetails = upi;
    }

    if (method === "CARD") {
        const card = document.getElementById("cardNumber").value.trim();
        const expiry = document.getElementById("cardExpiry").value.trim();
        if (!card || !expiry) {
            alert("Please fill in your card details.");
            return;
        }
        paymentDetails = `Card: ${card}, Exp: ${expiry}`;
    }

    if (method === "COD") {
        paymentDetails = "Cash on Delivery";
    }

    alert(`Order Confirmed!\nQuantity: ${qty}\nPayment: ${method}\nDetails: ${paymentDetails}\nAddress: ${address}\nTotal: ₹${total}`);

    // TODO: Send this to the backend to insert into the database
    closeModal();
}

    </script>
<script>
    const price = <?= $product['price'] ?>;
    const quantityInput = document.getElementById("quantity");
    const finalAmount = document.getElementById("final");

    function adjustQty(change) {
        let current = parseInt(quantityInput.value);
        current = Math.max(1, current + change);
        quantityInput.value = current;
        calculateFinalAmount();
    }

    function openModal() {
        calculateFinalAmount();
        showPaymentFields();
        document.getElementById("paymentModal").classList.remove("hidden");
    }

    function closeModal() {
        document.getElementById("paymentModal").classList.add("hidden");
    }

    function showPaymentFields() {
        const method = document.querySelector('input[name="payment"]:checked').value;
        document.getElementById("upiField").style.display = method === "UPI" ? "block" : "none";
        document.getElementById("cardFields").style.display = method === "CARD" ? "block" : "none";
    }

    function calculateFinalAmount() {
        const qty = parseInt(quantityInput.value);
        const baseTotal = price * qty;
        const gst = baseTotal * 0.10;
        let total = baseTotal + gst;

        const method = document.querySelector('input[name="payment"]:checked').value;
        if (method === "COD") {
            total += 120; // Delivery charge
        }

        finalAmount.innerText = total.toFixed(2);
        showPaymentFields(); // Keep payment fields synced
    }

    function confirmPurchase() {
        const qty = quantityInput.value;
        const method = document.querySelector('input[name="payment"]:checked').value;
        const total = finalAmount.innerText;
        const address = document.getElementById("address").value.trim();

        if (!address) {
            alert("Please enter your delivery address.");
            return;
        }

        if (method === "UPI") {
            const upi = document.getElementById("upiId").value.trim();
            if (!upi) {
                alert("Please enter your UPI ID.");
                return;
            }
        } else if (method === "CARD") {
            const card = document.getElementById("cardNumber").value.trim();
            const expiry = document.getElementById("cardExpiry").value.trim();
            if (!card || !expiry) {
                alert("Please enter full card details.");
                return;
            }
        }

        alert(`✅ Purchase Confirmed!\nQuantity: ${qty}\nMethod: ${method}\nTotal: ₹${total}`);

        // TODO: Send data to server via AJAX to record the purchase
        closeModal();
    }

    // Trigger recalculations on input change
    document.querySelectorAll('input[name="payment"]').forEach(input => {
        input.addEventListener("change", calculateFinalAmount);
    });

    quantityInput.addEventListener("input", calculateFinalAmount);
	
	function showPaymentFields() {
    const method = document.querySelector('input[name="payment"]:checked').value;
    document.getElementById("upiField").style.display = method === "UPI" ? "block" : "none";
    document.getElementById("cardFields").style.display = method === "CARD" ? "block" : "none";
    // Address field is always visible, so we don't hide it here
}


document.querySelectorAll('input[name="payment"]').forEach(input => {
    input.addEventListener("change", () => {
        showPaymentFields();
        calculateFinalAmount();
    });
});

</script>

</body>
</html>
