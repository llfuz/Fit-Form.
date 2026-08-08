<?php
session_start();
include "config.php";

// -------------------------
// Helper function
// -------------------------
if (!function_exists('renderColorPicker')) {
    function renderColorPicker() {
        echo '
        <label for="colorPicker">Choose Color:</label>
        <div class="color-picker-row">
            <input type="color" id="colorPicker" name="color_choice" value="#D8B4AE" required>
            <span id="colorText" class="color-text">#D8B4AE</span>
        </div>
        <input type="hidden" id="colorHex" name="color_hex" value="#D8B4AE">
        ';
    }
}

// -------------------------
// Product validation
// -------------------------
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($product_id <= 0) {
    header("Location: products.php");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM product WHERE product_id = $product_id");
if (!$query) {
    die("SQL Error: " . mysqli_error($conn));
}

$product = mysqli_fetch_assoc($query);
if ($product) {

    setcookie("last_product_name", $product['name'], time() + (86400 * 30), "/");

    setcookie("last_product_image", $product['image'], time() + (86400 * 30), "/");

    setcookie("last_product_id", $product['product_id'], time() + (86400 * 30), "/");
}
if (!$product) {
    header("Location: products.php");
    exit();
}

// -------------------------
// Wishlist check
// -------------------------
$is_in_wishlist = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $check = mysqli_query($conn, "SELECT * FROM wishlist WHERE user_id = $user_id AND product_id = $product_id");
    if (mysqli_num_rows($check) > 0) {
        $is_in_wishlist = true;
    }
} else {
    if (isset($_SESSION['guest_wishlist']) && in_array($product_id, $_SESSION['guest_wishlist'])) {
        $is_in_wishlist = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'navbar.php'; ?>

<div class="details-page">
<div class="details-container">

    <!-- IMAGE -->
    <div class="details-image">
        <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>

    <!-- INFO -->
    <div class="details-info">

        <h1><?= htmlspecialchars($product['name']) ?></h1>

        <!-- Unit Price -->
        <p class="price" id="unitPrice" data-price="<?= htmlspecialchars($product['price']) ?>">
            <?= htmlspecialchars($product['price']) ?> SAR
        </p>

        <form action="add_to_cart.php" method="POST" id="cartForm">
            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">

            <?php if ($product['category_id'] == 1): ?>

                <?php renderColorPicker(); ?>

                <label>Fabric:</label>
                <select name="fabric" required>
                    <option value="">Select Fabric</option>
                    <option>Cotton</option>
                    <option>Silk</option>
                    <option>Linen</option>
                    <option>Chiffon</option>
                </select>

                <label>Size Type:</label>
                <div class="size-toggle">
                    <button type="button" id="standardBtn" class="active">Standard</button>
                    <button type="button" id="customBtn">Custom</button>
                </div>

                <!-- hidden flag -->
                <input type="hidden" name="size_type" id="sizeType" value="standard">

                <div id="sizeContainer">
                    <label>Size:</label>
                    <select name="size" id="sizeSelect" required>
                        <option value="">Select Size</option>
                        <option>XS</option>
                        <option>S</option>
                        <option>M</option>
                        <option>L</option>
                        <option>XL</option>
                    </select>
                </div>

                <div id="measurementsFields" class="measurements-box">
                    <h3>Custom Measurements</h3>
                    <div class="measurements-grid">
                        <input type="number" name="height" placeholder="Height (cm)">
                        <input type="number" name="waist" placeholder="Waist (cm)">
                    </div>
                </div>

            <?php elseif ($product['category_id'] == 2): ?>

                <?php renderColorPicker(); ?>
                <label>Shoe Size:</label>
                <select name="size" required>
                    <option value="">Select Size</option>
                    <option>36</option>
                    <option>37</option>
                    <option>38</option>
                    <option>39</option>
                    <option>40</option>
                </select>

            <?php elseif (in_array($product['category_id'], [3,4,5])): ?>

                <?php renderColorPicker(); ?>
                <label>Size:</label>
                <select name="size" required>
                    <option value="">Select Size</option>
                    <option>XS</option>
                    <option>S</option>
                    <option>M</option>
                    <option>L</option>
                    <option>XL</option>
                </select>

            <?php elseif ($product['category_id'] == 6): ?>

                <label>Color:</label>
                <select name="color_choice" required>
                    <option value="">Select Color</option>
                    <option>Gold</option>
                    <option>Silver</option>
                </select>

            <?php elseif ($product['category_id'] == 7): ?>

                <?php renderColorPicker(); ?>

            <?php endif; ?>

            <label>Notes:</label>
            <textarea name="notes" placeholder="Any special request"></textarea>

            <label>Quantity:</label>
            <input type="number" name="quantity" id="quantity" value="1" min="1" required>

            <!-- Live Total -->
            <p class="live-total">
                Total: <span id="liveTotal"><?= htmlspecialchars($product['price']) ?> SAR</span>
            </p>

            <div class="product-actions">
                <button type="submit" class="btn-primary">Add to Cart</button>

                <button
                    type="button"
                    id="wishlistBtn"
                    class="btn-wishlist-toggle <?= $is_in_wishlist ? 'active-wl' : 'btn-outline' ?>"
                    onclick="toggleWishlist(this, <?= $product['product_id'] ?>)"
                >
                
                    <?= $is_in_wishlist ? '♥ Added' : '♡ Add to Wishlist' ?>
                </button>
            </div>

        </form>
<div class="help-section"> 
    <input type="checkbox" id="help-toggle" class="help-toggle-input"> 
     
    <label for="help-toggle" class="btn-help-popup">Confused?</label> 
 
    <div class="modal-overlay"> 
        <div class="modal-content"> 
            <label for="help-toggle" class="close-btn">&times;</label> 
             
            <h2>Steps to Order</h2> 
            <div class="steps-list"> 
                <div class="step-item"><strong>1. Choose options:</strong> Select color, fabric, size, and measurements.</div> 
                <div class="step-item"><strong>2. Quantity:</strong> Enter how many items you want.</div> 
                <div class="step-item"><strong>3. Size tip:</strong> If you're between sizes, choose the bigger size for comfort.</div> 
                <div class="step-item"><strong>4. Custom measurements:</strong> You can write your body measurements for a better fit.</div> 
                <div class="step-item"><strong>5. Problems?</strong> Return to Shop and open the product again.</div> 
            </div> 
            <div class="modal-footer"> 
                <p><strong>Need more help?</strong></p> 
                <p>If you still have questions, go to <a href="contact.php">Contact Us</a> and send your message.</p> 
            </div> 
        </div> 
    </div> 
</div>
    </div>
</div>
</div>


<!-- SUCCESS MODAL -->
<div id="successModal" class="success-overlay">
    <div class="success-box">
        <div class="success-icon">✓</div>
        <h2>Added to your cart</h2>
        <p>Your item has been added successfully.</p>
        <div class="success-actions">
            <a href="products.php" class="success-outline">Continue Shopping</a>
            <a href="cart.php" class="success-primary">View Cart</a>
        </div>
    </div>
</div>

<script>
function toggleWishlist(btn, productId) {
    let isActive = btn.classList.contains('active-wl');
    let url = isActive ? 'remove_wishlist.php' : 'add_to_wishlist.php';
    let formData = new FormData();
    formData.append('product_id', productId);

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'added') {
            btn.classList.remove('btn-outline');
            btn.classList.add('active-wl');
            btn.innerHTML = '♥ Added';
        } else if (data.status === 'removed') {
            btn.classList.remove('active-wl');
            btn.classList.add('btn-outline');
            btn.innerHTML = '♡ Add to Wishlist';
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const colorPicker = document.getElementById("colorPicker");
    const colorHex = document.getElementById("colorHex");
    const colorText = document.getElementById("colorText");

    if (colorPicker && colorHex && colorText) {
        colorHex.value = colorPicker.value;
        colorText.textContent = colorPicker.value.toUpperCase();
        colorPicker.addEventListener("input", function () {
            colorHex.value = colorPicker.value;
            colorText.textContent = colorPicker.value.toUpperCase();
        });
    }

    const standardBtn = document.getElementById("standardBtn");
    const customBtn = document.getElementById("customBtn");
    const sizeContainer = document.getElementById("sizeContainer");
    const sizeSelect = document.getElementById("sizeSelect");
    const measurements = document.getElementById("measurementsFields");
    const sizeType = document.getElementById("sizeType");

    function showStandard() {
        if (sizeContainer) sizeContainer.style.display = "block";
        if (sizeSelect) sizeSelect.required = true;
        if (measurements) {
            measurements.style.display = "none";
            measurements.querySelectorAll("input").forEach(input => {
                input.required = false;
                input.value = "";
            });
        }
        if (sizeType) sizeType.value = "standard";
        standardBtn.classList.add("active");
        customBtn.classList.remove("active");
    }

    function showCustom() {
        if (sizeContainer) sizeContainer.style.display = "none";
        if (sizeSelect) {
            sizeSelect.required = false;
            sizeSelect.value = "";
        }
        if (measurements) {
            measurements.style.display = "block";
            measurements.querySelectorAll("input").forEach(input => {
                input.required = true;
            });
        }
        if (sizeType) sizeType.value = "custom";
        customBtn.classList.add("active");
        standardBtn.classList.remove("active");
    }

    if (standardBtn && customBtn) {
        standardBtn.addEventListener("click", showStandard);
        customBtn.addEventListener("click", showCustom);
    }

    const quantityInput = document.getElementById("quantity");
    const unitPriceElement = document.getElementById("unitPrice");
    const liveTotal = document.getElementById("liveTotal");
    const basePrice = parseFloat(unitPriceElement.dataset.price);
    let exchangeRates = { SAR: 1, USD: 0.27, EUR: 0.25, AED: 0.98 };

    // Load live exchange rates from API
    async function loadCurrencyRates() {
        try {
            const response = await fetch("[api.exchangerate-api.com](https://api.exchangerate-api.com/v4/latest/SAR)");
            const data = await response.json();
            exchangeRates = { SAR: 1, USD: data.rates.USD, EUR: data.rates.EUR, AED: data.rates.AED };
        } catch {
            console.log("Currency API failed, fallback rates used.");
        }
    }

    // Update total price based on quantity and currency
    function updatePrices() {
        let selectedCurrency = localStorage.getItem("currency") || "SAR";
        let rate = exchangeRates[selectedCurrency] || 1;
        let quantity = parseInt(quantityInput.value) || 1;
        let convertedUnitPrice = basePrice * rate;
        let convertedTotal = basePrice * quantity * rate;
        unitPriceElement.textContent = convertedUnitPrice.toFixed(2) + " " + selectedCurrency;
        liveTotal.textContent = convertedTotal.toFixed(2) + " " + selectedCurrency;
    }
    // Event listeners
    if (quantityInput && unitPriceElement && liveTotal) {
        quantityInput.addEventListener("input", updatePrices);
        document.addEventListener("currencyChanged", updatePrices);
    // Start currency API
        loadCurrencyRates().then(updatePrices);
    }

    const cartForm = document.getElementById("cartForm");
    const modal = document.getElementById("successModal");

    if (cartForm && modal) {
        cartForm.addEventListener("submit", function (e) {
            e.preventDefault();
            fetch("add_to_cart.php", {
                method: "POST",
                body: new FormData(cartForm)
            })
            .then(res => res.ok ? modal.style.display = "flex" : alert("Error adding item"))
            .catch(() => alert("Something went wrong"));
        });
    }
});
</script>

</body>
</html>
