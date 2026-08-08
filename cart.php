<?php
// Start the session
session_start();
// Include database configuration file
include "config.php";

// Check if the user is logged in to determine guest mode
 $guest_mode = !isset($_SESSION['user_id']);
// Initialize cart state variables
 $empty_cart = true;
 $total = 0;
 $cart_items = [];

/* Helper: display color as a circle, not as text */
function colorCircle($color) {
    // Trim whitespace from the color input
    $color = trim((string)$color);

    // Return empty string if no color is provided
    if ($color === '') {
        return '';
    }

    // If color is saved as FFFFFF instead of #FFFFFF, fix it.
    if (preg_match('/^[A-Fa-f0-9]{6}$/', $color)) {
        $color = '#' . $color;
    }

    // If color is saved as a number, do not print the number.
    // The circle will appear with a light border only because numeric color is not a valid CSS color.
    // Escape output for security
    $safeColor = htmlspecialchars($color);

    // Return the HTML for the color circle
    return '<p class="color-line"><strong>Color:</strong><span class="color-circle" title="Selected color" style="background-color: ' . $safeColor . ';"></span></p>';
}

/* UPDATE QUANTITY Logic */
if (isset($_POST['update_qty'])) {
    // Get the new quantity from POST request
    $new_qty = intval($_POST['quantity']);

    // Ensure quantity is at least 1
    if ($new_qty < 1) {
        $new_qty = 1;
    }

    // Logic for Guest Mode (update session)
    if ($guest_mode) {
        $index = intval($_POST['item_index']);

        if (isset($_SESSION['guest_cart'][$index])) {
            $_SESSION['guest_cart'][$index]['quantity'] = $new_qty;
        }
    } else {
        // Logic for User Mode (update database)
        $item_id = intval($_POST['item_id']);

        $update_stmt = $conn->prepare("UPDATE Cart_Item SET quantity = ? WHERE cart_item_id = ?");
        $update_stmt->bind_param("ii", $new_qty, $item_id);
        $update_stmt->execute();
    }

    // Redirect to refresh the page
    header("Location: cart.php");
    exit();
}

/* REMOVE ITEM Logic */
if (isset($_POST['remove_item'])) {

    // Logic for Guest Mode (remove from session array)
    if ($guest_mode) {
        $index = intval($_POST['item_index']);

        if (isset($_SESSION['guest_cart'][$index])) {
            unset($_SESSION['guest_cart'][$index]);
            // Re-index the array to prevent gaps
            $_SESSION['guest_cart'] = array_values($_SESSION['guest_cart']);
        }
    } else {
        // Logic for User Mode (delete from database)
        $item_id = intval($_POST['item_id']);

        $delete_stmt = $conn->prepare("DELETE FROM Cart_Item WHERE cart_item_id = ?");
        $delete_stmt->bind_param("i", $item_id);
        $delete_stmt->execute();
    }

    // Redirect to refresh the page
    header("Location: cart.php");
    exit();
}

/* GUEST MODE - Retrieve cart data from session */
if ($guest_mode) {

    $guest_cart = $_SESSION['guest_cart'] ?? [];

    if (!empty($guest_cart)) {

        foreach ($guest_cart as $index => $item) {

            $product_id = intval($item['product_id']);

            // Fetch product details from database
            $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();

            $res = $stmt->get_result();
            $product = $res->fetch_assoc();

            if ($product) {
                // Merge session data with product data
                $product['quantity'] = $item['quantity'] ?? 1;
                $product['size'] = $item['size'] ?? '';
                $product['color'] = $item['color'] ?? '';
                $product['height'] = $item['height'] ?? '';
                $product['waist'] = $item['waist'] ?? '';
                $product['notes'] = $item['notes'] ?? '';
                $product['index'] = $index;

                $cart_items[] = $product;
                $empty_cart = false;
            }
        }
    }

/* USER MODE - Retrieve cart data from database */
} else {

    $user_id = $_SESSION['user_id'];

    // Get the user's cart ID
    $cart_query = $conn->prepare("SELECT cart_id FROM Cart WHERE user_id = ?");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();

    $cart_result = $cart_query->get_result();

    if ($cart_result->num_rows > 0) {

        $cart = $cart_result->fetch_assoc();
        $cart_id = $cart['cart_id'];

        // Fetch cart items joining with product table
        $query = $conn->prepare("
            SELECT 
                product.name,
                product.image,
                Cart_Item.quantity,
                Cart_Item.price,
                Cart_Item.size,
                Cart_Item.color,
                Cart_Item.height,
                Cart_Item.waist,
                Cart_Item.notes,
                Cart_Item.cart_item_id
            FROM Cart_Item
            JOIN product ON Cart_Item.product_id = product.product_id
            WHERE Cart_Item.cart_id = ?
        ");

        $query->bind_param("i", $cart_id);
        $query->execute();

        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $empty_cart = false;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <!-- Link to stylesheet -->
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- Include Navigation Bar -->
<?php include "navbar.php"; ?>

<!-- Main Cart Container -->
<div class="cart-main-container">
    <h1>Shopping Cart</h1>

    <?php if ($empty_cart): ?>

        <!-- Empty Cart State Message -->
        <div class="empty-cart-state">
    <div class="empty-cart-state-box">
        <h2>Your cart is a little bit light... 🛒</h2>
        <p>Go back and add some items to your collection.</p>

        <a href="products.php" class="btn-primary">
            Go back to shopping
        </a>
    </div>
</div>

    <?php else: ?>

        <div class="cart-items-list">

            <!-- GUEST CART Iteration -->
            <?php if ($guest_mode): ?>

                <?php foreach ($cart_items as $row): 
                    // Calculate item total price
                    $item_total = $row['price'] * $row['quantity'];
                    $total += $item_total;
                ?>

                    <!-- Single Cart Item Card -->
                    <div class="new-cart-item">

                        <!-- Item Image Section -->
                        <div class="item-visual">
                            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        </div>

                        <!-- Item Details Section -->
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>

                            <div class="cart-meta">

                                <!-- Update Quantity Form -->
                                <form method="POST" class="cart-qty-form">
                                    <input type="hidden" name="item_index" value="<?php echo $row['index']; ?>">

                                    <label>Quantity:</label>
                                    <input 
                                        type="number" 
                                        name="quantity" 
                                        value="<?php echo htmlspecialchars($row['quantity']); ?>" 
                                        min="1" 
                                        onchange="this.form.submit()"
                                    >

                                    <input type="hidden" name="update_qty" value="1">
                                </form>

                                <!-- Display Size if available -->
                                <?php if (!empty($row['size'])): ?>
                                    <p><strong>Size:</strong> <?php echo htmlspecialchars($row['size']); ?></p>
                                <?php endif; ?>

                                <!-- Display Height if available -->
                                <?php if (!empty($row['height'])): ?>
                                    <p><strong>Height:</strong> <?php echo htmlspecialchars($row['height']); ?> cm</p>
                                <?php endif; ?>

                                <!-- Display Waist if available -->
                                <?php if (!empty($row['waist'])): ?>
                                    <p><strong>Waist:</strong> <?php echo htmlspecialchars($row['waist']); ?> cm</p>
                                <?php endif; ?>

                                <!-- Display Color Circle -->
                                <?php echo colorCircle($row['color'] ?? ''); ?>

                                <!-- Display Notes if available -->
                                <?php if (!empty($row['notes'])): ?>
                                    <p><strong>Notes:</strong> <?php echo htmlspecialchars($row['notes']); ?></p>
                                <?php endif; ?>

                            </div>

                            <!-- Item Price Display -->
                            <p class="price">
                                <span class="item-price" data-price="<?php echo $item_total; ?>">
                                    <?php echo number_format($item_total, 2); ?> SAR
                                </span>
                            </p>

                            <!-- Remove Item Form -->
                            <form method="POST" class="remove-form" onsubmit="return confirm('Remove this item from cart?');">
                                <input type="hidden" name="item_index" value="<?php echo $row['index']; ?>">
                                <button type="submit" name="remove_item" class="remove-btn">Remove</button>
                            </form>

                        </div>
                    </div>

                <?php endforeach; ?>

            <!-- USER CART Iteration -->
            <?php else: ?>

                <?php while ($row = $result->fetch_assoc()):
                    // Calculate item total price
                    $item_total = $row['price'] * $row['quantity'];
                    $total += $item_total;
                ?>

                    <!-- Single Cart Item Card -->
                    <div class="new-cart-item">

                        <!-- Item Image Section -->
                        <div class="item-visual">
                            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        </div>

                        <!-- Item Details Section -->
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($row['name']); ?></h3>

                            <div class="cart-meta">

                                <!-- Update Quantity Form -->
                                <form method="POST" class="cart-qty-form">
                                    <input type="hidden" name="item_id" value="<?php echo $row['cart_item_id']; ?>">

                                    <label>Quantity:</label>
                                    <input 
                                        type="number" 
                                        name="quantity" 
                                        value="<?php echo htmlspecialchars($row['quantity']); ?>" 
                                        min="1" 
                                        onchange="this.form.submit()"
                                    >

                                    <input type="hidden" name="update_qty" value="1">
                                </form>

                                <!-- Display Size if available -->
                                <?php if (!empty($row['size'])): ?>
                                    <p><strong>Size:</strong> <?php echo htmlspecialchars($row['size']); ?></p>
                                <?php endif; ?>

                                <!-- Display Height if available -->
                                <?php if (!empty($row['height'])): ?>
                                    <p><strong>Height:</strong> <?php echo htmlspecialchars($row['height']); ?> cm</p>
                                <?php endif; ?>

                                <!-- Display Waist if available -->
                                <?php if (!empty($row['waist'])): ?>
                                    <p><strong>Waist:</strong> <?php echo htmlspecialchars($row['waist']); ?> cm</p>
                                <?php endif; ?>

                                <!-- Display Color Circle -->
                                <?php echo colorCircle($row['color'] ?? ''); ?>

                                <!-- Display Notes if available -->
                                <?php if (!empty($row['notes'])): ?>
                                    <p><strong>Notes:</strong> <?php echo htmlspecialchars($row['notes']); ?></p>
                                <?php endif; ?>

                            </div>

                            <!-- Item Price Display -->
                            <p class="price">
                                <span class="item-price" data-price="<?php echo $item_total; ?>">
                                    <?php echo number_format($item_total, 2); ?> SAR
                                </span>
                            </p>

                            <!-- Remove Item Form -->
                            <form method="POST" class="remove-form" onsubmit="return confirm('Remove this item from cart?');">
                                <input type="hidden" name="item_id" value="<?php echo $row['cart_item_id']; ?>">
                                <button type="submit" name="remove_item" class="remove-btn">Remove</button>
                            </form>

                        </div>
                    </div>

                <?php endwhile; ?>

            <?php endif; ?>

        </div>

        <!-- Cart Summary Section -->
        <div class="cart-summary">
            <?php if(isset($_COOKIE['last_product_name'])) { ?>

<div class="recent-product">

    <img src="images/<?php echo $_COOKIE['last_product_image']; ?>" alt="Recent Product">

    <div class="recent-info">

        <p class="recent-title">
            Recently Viewed
        </p>

        <h4>
            <?php echo $_COOKIE['last_product_name']; ?>
        </h4>

        <a href="product_details.php?id=<?php echo $_COOKIE['last_product_id']; ?>" class="recent-btn">
            Continue Shopping
        </a>

    </div>

</div>

<?php } ?>

            <h2>
                Total:
                <span id="cartTotal" data-total="<?php echo $total; ?>">
                    <?php echo number_format($total, 2); ?> SAR
                </span>
            </h2>

            <!-- Action Buttons -->
            <div class="cart-actions">
                <a href="empty_cart.php" class="btn-outline">Clear Cart</a>
                <a href="checkout.php" class="btn-primary">Checkout</a>
            </div>

        </div>

    <?php endif; ?>

</div>

<!-- JavaScript for Currency Conversion -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Select all item price elements and the total element
    const itemPrices = document.querySelectorAll(".item-price");
    const cartTotal = document.getElementById("cartTotal");

    if (!cartTotal) return;

    // Default exchange rates object
    let exchangeRates = {
        SAR: 1,
        USD: 0.27,
        EUR: 0.25,
        AED: 0.98
    };

    // Function to load exchange rates from API
    async function loadRates() {
        try {
            const res = await fetch("https://api.exchangerate-api.com/v4/latest/SAR");
            const data = await res.json();

            // Update rates with live data
            exchangeRates = {
                SAR: 1,
                USD: data.rates.USD,
                EUR: data.rates.EUR,
                AED: data.rates.AED
            };
        } catch {
            console.log("Using fallback rates");
        }
    }

    // Function to update cart totals based on selected currency
    function updateCart() {
        const currency = localStorage.getItem("currency") || "SAR";
        const rate = exchangeRates[currency] || 1;

        let total = 0;

        // Update each item's price
        itemPrices.forEach(item => {
            const base = parseFloat(item.dataset.price);
            const converted = base * rate;

            item.textContent = converted.toFixed(2) + " " + currency;
            total += converted;
        });

        // Update the cart total
        cartTotal.textContent = total.toFixed(2) + " " + currency;
    }

    // Load rates then update the cart
    loadRates().then(updateCart);
});
</script>

</body>
</html>