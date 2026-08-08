<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Success</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="logo">Fit & Form</div>

    <div class="nav-links">
        <a href="home.php">Home</a>
        <a href="products.php">Shop</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact Us</a>
        <a href="cart.php">Cart</a>
        <a href="choose_role.php">Switch</a>
    </div>
</div>

<!-- SUCCESS SECTION -->
<div class="success-page">

    <div class="success-card">
        <h1>Order Placed Successfully</h1>

        <p>
            Thank you! Your order has been received and is being processed.
        </p>

        <div class="success-buttons">
            <a href="home.php" class="btn-primary">Back to Home</a>
            <a href="products.php" class="btn-outline">Continue Shopping</a>
        </div>
    </div>

</div>

<!-- FOOTER -->
<footer>
    © 2026 Fit & Form. All rights reserved.
</footer>

</body>
</html>