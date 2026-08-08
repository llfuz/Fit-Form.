<!DOCTYPE html>
<html>
<head>
    <title>Continue as User</title>

    <link rel="stylesheet" href="style.css">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
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

<!-- Navigation bar -->
<div class="guest-page">

    <h1>Continue as User</h1>
    <p>Select how you want to continue.</p>

    <a href="choose_role.php" class="back-btn">← Back to Role Selection</a>

    <div class="guest-card">

        <p class="guest-text">
            Continue as Guest (you can browse and add to cart).  
            <br>
            <strong>But to place an order you must login or register.</strong>
        </p>

        <div class="guest-buttons">

            <a href="home.php" class="btn-primary">Continue as Guest</a>

            <a href="login.php" class="btn-outline">Login</a>

            <a href="register.php" class="btn-outline">Create Account</a>

        </div>

    </div>

</div>

</body>
</html>