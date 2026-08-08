<?php
// Start the session if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get the current page filename to highlight the active link
 $current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Main Navbar Container -->
<div class="navbar">
    <!-- Logo Section -->
    <div class="logo">
        <img src="images/logo.png" alt="Fit & Form Logo">
    </div>
    <!-- Currency Selector Dropdown -->
    <div class="nav-currency">
    <select id="currency">
        <option value="SAR">SAR</option>
        <option value="USD">USD</option>
        <option value="EUR">EUR</option>
        <option value="AED">AED</option>
    </select>
</div>

    <!-- Navigation Links Container -->
    <div class="nav-links">

        <!-- Navigation links with 'active' class logic based on current page -->
        <a href="home.php" class="<?= $current_page == 'home.php' ? 'active' : '' ?>">Home</a>
        <a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>">Shop</a>
        <a href="about.php" class="<?= $current_page == 'about.php' ? 'active' : '' ?>">About Us</a>
        <a href="contact.php" class="<?= $current_page == 'contact.php' ? 'active' : '' ?>">Contact</a>
        <a href="cart.php" class="<?= $current_page == 'cart.php' ? 'active' : '' ?>">Cart</a>
        <a href="wishlist.php" class="<?= $current_page == 'wishlist.php' ? 'active' : '' ?>">Wishlist</a>

        <!-- Account link: Displays user name if logged in, otherwise generic text -->
        <a href="account.php" class="<?= $current_page == 'account.php' ? 'active' : '' ?>">
            <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])): ?>
                <?= htmlspecialchars($_SESSION['user_name']) ?>'s Account
            <?php else: ?>
                My Account
            <?php endif; ?>
        </a>

        <!-- Conditional Login/Logout links based on session -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php" class="logout-btn">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>

    </div>
</div>
<!-- Script for Currency Selector Logic -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const currencySelect = document.getElementById("currency");
    if (!currencySelect) return;

    // Load saved currency from LocalStorage, default to SAR
    const savedCurrency = localStorage.getItem("currency") || "SAR";
    currencySelect.value = savedCurrency;

    // Event listener for when currency selection changes
    currencySelect.addEventListener("change", function () {
        // Save the selected currency to LocalStorage
        localStorage.setItem("currency", currencySelect.value);
        // Reload the page to apply changes
        location.reload();
    });

});
</script>