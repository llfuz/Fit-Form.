<?php
// Start the session
session_start();
// Include database configuration file
include "config.php";

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Required</title>
    <!-- Link to stylesheet -->
    <link rel="stylesheet" href="style.css?v=100">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body>

<!-- Login Required Message Container -->
<div class="login-required">
    <div class="login-box">

        <h1>Login Required</h1>

        <p>
            Please login first to continue to checkout.
        </p>

        <!-- Login Button -->
        <a href="login.php" class="login-btn">
            Login
        </a>

    </div>
</div>

</body>
</html>
<?php
// Stop script execution if not logged in
exit();
}

// Initialize user and cart variables
 $user_id = $_SESSION['user_id'];
 $total = 0;
 $cart_id = 0;


/* GET CART DATA */
 $cart_res = mysqli_query($conn, "
    SELECT cart_id 
    FROM cart 
    WHERE user_id = $user_id
");

 $cart = mysqli_fetch_assoc($cart_res);

if ($cart) {
    $cart_id = $cart['cart_id'];

    // Calculate total price for items in cart
    $items_total_res = mysqli_query($conn, "
        SELECT cart_item.quantity, product.price
        FROM cart_item
        JOIN product ON product.product_id = cart_item.product_id
        WHERE cart_item.cart_id = $cart_id
    ");

    while ($row = mysqli_fetch_assoc($items_total_res)) {
        $total += $row['price'] * $row['quantity'];
    }
}

/* PROCESS ORDER SUBMISSION */
if (isset($_POST['place_order'])) {

    // Validate required fields
    if (
        empty($_POST['name']) ||
        empty($_POST['street']) ||
        empty($_POST['city']) ||
        empty($_POST['postal']) ||
        empty($_POST['country']) ||
        empty($_POST['payment'])
    ) {
        echo "<script>alert('Please fill all required fields');</script>";
    } 
    // Validate cart exists
    elseif (!$cart || $cart_id == 0) {
        echo "<script>alert('Cart is empty');</script>";
    } 
    else {

        // Sanitize input data
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $street = mysqli_real_escape_string($conn, $_POST['street']);
        $city = mysqli_real_escape_string($conn, $_POST['city']);
        $postal = mysqli_real_escape_string($conn, $_POST['postal']);
        $country = mysqli_real_escape_string($conn, $_POST['country']);
        $payment = mysqli_real_escape_string($conn, $_POST['payment']);

        // Handle Card Payment Logic
        // Handle Card Payment Logic
if ($payment === 'card') {

    $card_number = trim($_POST['card_number'] ?? '');
    $expiry = trim($_POST['expiry'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');

    // Validate card details
    if (empty($card_number) || empty($expiry) || empty($cvv)) {
        echo "<script>alert('Please fill card information');</script>";
    } 
    elseif (!preg_match('/^[0-9]{16}$/', $card_number)) {
        echo "<script>alert('Card number must be exactly 16 digits');</script>";
    } 
    elseif (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $expiry)) {
        echo "<script>alert('Expiry date must be in MM/YY format');</script>";
    } 
    elseif (!preg_match('/^[0-9]{3}$/', $cvv)) {
        echo "<script>alert('CVV must be exactly 3 digits');</script>";
    } 
    else {
                // Format address
                $address = "$street, $city, $postal, $country";

                // Insert order into database
                mysqli_query($conn, "
                    INSERT INTO orders (user_id, name, address, payment, total)
                    VALUES ('$user_id', '$name', '$address', '$payment', '$total')
                ");

                $order_id = mysqli_insert_id($conn);

                // Fetch cart items to move to order_items
                $items_res = mysqli_query($conn, "
                    SELECT * FROM cart_item 
                    WHERE cart_id = $cart_id
                ");

                // Insert items into order_items table
                while ($item = mysqli_fetch_assoc($items_res)) {
                    mysqli_query($conn, "
                        INSERT INTO order_items (order_id, product_id, quantity)
                        VALUES (
                            '$order_id',
                            '".$item['product_id']."',
                            '".$item['quantity']."'
                        )
                    ");
                }

                // Clear the cart after order
                mysqli_query($conn, "
                    DELETE FROM cart_item 
                    WHERE cart_id = $cart_id
                ");

                // Redirect to success page
                header("Location: order_success.php");
                exit();
            }
        } else {
            // Handle Cash on Delivery Logic
            // Format address
            $address = "$street, $city, $postal, $country";

            // Insert order into database
            mysqli_query($conn, "
                INSERT INTO orders (user_id, name, address, payment, total)
                VALUES ('$user_id', '$name', '$address', '$payment', '$total')
            ");

            $order_id = mysqli_insert_id($conn);

            // Fetch cart items to move to order_items
            $items_res = mysqli_query($conn, "
                SELECT * FROM cart_item 
                WHERE cart_id = $cart_id
            ");

            // Insert items into order_items table
            while ($item = mysqli_fetch_assoc($items_res)) {
                mysqli_query($conn, "
                    INSERT INTO order_items (order_id, product_id, quantity)
                    VALUES (
                        '$order_id',
                        '".$item['product_id']."',
                        '".$item['quantity']."'
                    )
                ");
            }

            // Clear the cart after order
            mysqli_query($conn, "
                DELETE FROM cart_item 
                WHERE cart_id = $cart_id
            ");

            // Redirect to success page
            header("Location: order_success.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <!-- Link to stylesheet -->
    <link rel="stylesheet" href="style.css?v=100">
</head>

<body>

<!-- Main Checkout Page Container -->
<div class="checkout-page">

    <h1>Checkout</h1>
    <p>Fill in your address, then choose a payment option.</p>

    <!-- Back to Cart Button -->
    <a href="cart.php" class="back-btn">← Back to Cart</a>

    <!-- Checkout Form -->
    <form id="checkoutForm" method="POST">
        <input type="hidden" name="place_order" value="1">

        <div class="checkout-container">

            <!-- LEFT: Shipping Address Section -->
            <div class="checkout-form-card">

                <h3>Shipping Address</h3>

                <input type="text" name="name" placeholder="Full Name" required>
                <input type="text" name="street" placeholder="Street Name / Number" required>
                <input type="text" name="city" placeholder="City" required>
                <input type="text" name="postal" placeholder="Postal Code" required>
                <input type="text" name="country" placeholder="Country" required>

                <textarea name="notes" placeholder="Additional Details (Optional)"></textarea>

            </div>
<!-- RIGHT: Order Summary and Payment Section -->
<div class="checkout-summary-card">

    <h3 class="summary-title">Order Summary</h3>

    <div class="summary-items">
        <?php
        // Fetch cart items for display
        $items_res = mysqli_query($conn, "
            SELECT 
                cart_item.product_id,
                cart_item.quantity,
                product.price,
                product.name,
                product.image
            FROM cart_item
            JOIN product ON product.product_id = cart_item.product_id
            WHERE cart_item.cart_id = $cart_id
        ");

        while ($row = mysqli_fetch_assoc($items_res)) {
            $quantity = (int)$row['quantity'];
            $price = (float)$row['price'];
            $item_total = $price * $quantity;
        ?>

            <!-- Single Item Row -->
            <div class="checkout-item">

                <img 
                    src="images/<?= htmlspecialchars($row['image']) ?>" 
                    alt="<?= htmlspecialchars($row['name']) ?>"
                    class="checkout-item-img"
                >

                <div class="checkout-item-info">

                    <div class="checkout-item-top">
                        <h4><?= htmlspecialchars($row['name']) ?></h4>

                        <div class="qty-box">
                            <span>Qty</span>
                            <input 
                                type="number"
                                class="item-qty"
                                value="<?= htmlspecialchars($quantity) ?>"
                                min="1"
                            >
                        </div>
                    </div>

                    <div class="checkout-item-row">
                        <span>Unit Price</span>
                        <strong 
                            class="item-price" 
                            data-price="<?= htmlspecialchars($price) ?>"
                        >
                            <?= number_format($price, 2) ?> SAR
                        </strong>
                    </div>

                    <div class="checkout-item-row">
                        <span>Item Total</span>
                        <strong>
                            <span class="item-total"><?= number_format($item_total, 2) ?></span> SAR
                        </strong>
                    </div>

                </div>

            </div>

        <?php } ?>
    </div>

    <!-- Total Summary Box -->
    <div class="summary-total-box">
        <div class="summary-total-row">
            <span>Subtotal</span>
            <strong><span id="grandTotal"><?= number_format($total, 2) ?></span> SAR</strong>
        </div>

        <div class="summary-total-row">
            <span>Shipping</span>
            <strong>Free</strong>
        </div>

        <div class="summary-total-row final-row">
            <span>Total</span>
            <strong><span id="finalTotal"><?= number_format($total, 2) ?></span> SAR</strong>
        </div>
    </div>

    <!-- Payment Method Section -->
    <div class="payment-section">
        <h3 class="payment-title">Payment</h3>

        <div class="payment-options">
            <label class="payment-card">
                <input type="radio" name="payment" value="cash" checked>
                <span>Cash on Delivery</span>
            </label>

            <label class="payment-card">
                <input type="radio" name="payment" value="card">
                <span>Pay by Card</span>
            </label>
        </div>

        <!-- Card Input Fields (Hidden by default via JS) -->
 <div id="cardFields" class="card-fields">

    <input 
        type="text" 
        id="card_number"
        name="card_number" 
        placeholder="Card Number"
        maxlength="16"
        inputmode="numeric"
    >
    <small class="field-error" id="cardNumberError"></small>

    <input 
        type="text" 
        id="expiry"
        name="expiry" 
        placeholder="MM/YY"
        maxlength="5"
    >
    <small class="field-error" id="expiryError"></small>

    <input 
        type="text" 
        id="cvv"
        name="cvv" 
        placeholder="CVV"
        maxlength="3"
        inputmode="numeric"
    >
    <small class="field-error" id="cvvError"></small>

</div>

    <!-- Place Order Button -->
    <button type="submit" name="place_order" class="checkout-btn">
        Place Order
    </button>

</div>

<!-- Confirmation Modal -->
<div id="checkoutModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:30px; border-radius:20px; text-align:center; max-width:400px;">
        <h2 style="color:#D8B4AE;">Confirm Your Order</h2>
        <p>Are you sure you want to place this order? This action cannot be undone.</p>

        <div style="display:flex; gap:12px; margin-top:25px;">
            <button type="button" id="confirmOrderBtn" class="btn-primary" style="flex:1; cursor:pointer; border:none; padding:12px;">
                Yes, Confirm
            </button>

            <button type="button" onclick="closeCheckoutModal()" class="btn-outline" style="flex:1; cursor:pointer; background:none; border:1px solid #ccc; padding:12px;">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- JavaScript for Interactivity -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const qtyInputs = document.querySelectorAll(".item-qty");
    const grandTotalEl = document.getElementById("grandTotal");
    const finalTotalEl = document.getElementById("finalTotal");

    const paymentInputs = document.querySelectorAll('input[name="payment"]');
    const cardFields = document.getElementById("cardFields");

    const form = document.getElementById("checkoutForm");
    const modal = document.getElementById("checkoutModal");
    const confirmBtn = document.getElementById("confirmOrderBtn");

    // Function to update totals when quantity changes
    function updateCartTotal() {
        let grandTotal = 0;

        document.querySelectorAll(".checkout-item").forEach(item => {
            const priceEl = item.querySelector(".item-price");
            const qtyEl = item.querySelector(".item-qty");
            const totalEl = item.querySelector(".item-total");

            const price = parseFloat(priceEl.dataset.price);
            let qty = parseInt(qtyEl.value);

            if (isNaN(qty) || qty < 1) {
                qty = 1;
                qtyEl.value = 1;
            }

            const itemTotal = price * qty;

            totalEl.textContent = itemTotal.toFixed(2);
            grandTotal += itemTotal;
        });

        grandTotalEl.textContent = grandTotal.toFixed(2);
        finalTotalEl.textContent = grandTotal.toFixed(2);
    }

    // Function to toggle card input visibility
    function toggleCardFields() {
        const selectedPayment = document.querySelector('input[name="payment"]:checked').value;

        if (selectedPayment === "card") {
            cardFields.style.display = "block";
        } else {
            cardFields.style.display = "none";
        }
    }

    // Event listeners for quantity inputs
    qtyInputs.forEach(input => {
        input.addEventListener("input", updateCartTotal);
    });

    // Event listeners for payment method change
    paymentInputs.forEach(input => {
        input.addEventListener("change", toggleCardFields);
    });

    let orderConfirmed = false;

// Function to validate card information
// Function to validate card information
function validateCardInfo() {
    const selectedPayment = document.querySelector('input[name="payment"]:checked').value;

    const cardNumberInput = document.getElementById("card_number");
    const expiryInput = document.getElementById("expiry");
    const cvvInput = document.getElementById("cvv");

    const cardNumberError = document.getElementById("cardNumberError");
    const expiryError = document.getElementById("expiryError");
    const cvvError = document.getElementById("cvvError");

    let isValid = true;

    // Clear old errors
    cardNumberError.textContent = "";
    expiryError.textContent = "";
    cvvError.textContent = "";

    cardNumberInput.classList.remove("input-error");
    expiryInput.classList.remove("input-error");
    cvvInput.classList.remove("input-error");

    // If payment is cash, no need to validate card fields
    if (selectedPayment !== "card") {
        return true;
    }

    const cardNumber = cardNumberInput.value.trim();
    const expiry = expiryInput.value.trim();
    const cvv = cvvInput.value.trim();

    // Card number validation
    if (!/^\d{16}$/.test(cardNumber)) {
        cardNumberError.textContent = "Card number must be exactly 16 digits.";
        cardNumberInput.classList.add("input-error");
        isValid = false;
    }

    // Expiry date validation
    if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(expiry)) {
        expiryError.textContent = "Expiry date must be in MM/YY format.";
        expiryInput.classList.add("input-error");
        isValid = false;
    }

    // CVV validation
    if (!/^\d{3}$/.test(cvv)) {
        cvvError.textContent = "CVV must be exactly 3 digits.";
        cvvInput.classList.add("input-error");
        isValid = false;
    }

    return isValid;
}
// Clear error message when user starts typing again
document.getElementById("card_number").addEventListener("input", function () {
    document.getElementById("cardNumberError").textContent = "";
    this.classList.remove("input-error");
});

document.getElementById("expiry").addEventListener("input", function () {
    document.getElementById("expiryError").textContent = "";
    this.classList.remove("input-error");
});

document.getElementById("cvv").addEventListener("input", function () {
    document.getElementById("cvvError").textContent = "";
    this.classList.remove("input-error");
});

// Form submission logic
form.addEventListener("submit", function (e) {

    if (!orderConfirmed) {
        e.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        if (!validateCardInfo()) {
            return;
        }

        modal.style.display = "flex";
    }
});

// Confirm button logic
confirmBtn.addEventListener("click", function () {
    modal.style.display = "none";
    confirmBtn.disabled = true;
    confirmBtn.innerText = "Processing...";

    orderConfirmed = true;
    form.requestSubmit();
});
    // Initial setup
    updateCartTotal();
    toggleCardFields();
});

// Function to close modal
function closeCheckoutModal() {
    document.getElementById("checkoutModal").style.display = "none";
}
</script>

</body>
</html>