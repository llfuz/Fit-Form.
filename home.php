<?php

// Start session and connect to database
session_start();
include 'config.php';

/* =====================================================
   FETCH WISHLIST DATA
   Retrieve wishlist product IDs for logged-in users
   or guest users using session storage
===================================================== */
 $wishlist_ids = [];


// Get wishlist products from database
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $res = $conn->query("SELECT product_id FROM wishlist WHERE user_id = $user_id");
    while($row = $res->fetch_assoc()) $wishlist_ids[] = $row['product_id'];
} else {

     // Use session wishlist for guest users
    $wishlist_ids = $_SESSION['guest_wishlist'] ?? [];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght+600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

<!-- Include reusable navigation bar -->
<?php include 'navbar.php'; ?>

<!-- ================= HERO SECTION ================= -->
<div class="home-hero">
    <h1>Fit & Form</h1>
    <p>A modern fashion store with customizable pieces. Explore clothing, footwear, and accessories designed for your style.</p>
    <div class="hero-buttons">
    <a href="products.php" class="btn-primary">Shop Now</a>
    <a href="product_details.php?id=1" class="btn-outline">Customize Dresses</a>
</div>
</div>

<!-- ================= HERO VIDEO ================= -->
<section class="hero-video">
    <video autoplay muted loop playsinline>
        <source src="Videos/fashion.MP4" type="video/mp4">
    </video>
</section>

<!-- ================= CATEGORIES SECTION ================= -->
<div class="categories-section">
    <h2>Shop by Category</h2>
    <div class="category-buttons">
        <a href="products.php#dresses">Dresses</a>
        <a href="products.php#tops">Tops & Shirts</a>
        <a href="products.php#pants">Pants</a>
        <a href="products.php#skirts">Skirts</a>
        <a href="products.php#footwear">Footwear</a>
        <a href="products.php#accessories">Accessories</a>
        <a href="products.php#bags">Bags</a>
    </div>
</div>

<!-- ================= FEATURED PICKS ================= -->
<div class="featured-section">
    <h2>Featured Picks</h2>

    <div class="featured-products">

        <?php
        // Fetch 4 specific products for the featured section
        $dress = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM product WHERE category_id = 1 ORDER BY product_id DESC LIMIT 1"));
        $bag = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM product WHERE category_id = 6 ORDER BY product_id DESC LIMIT 1"));
        $heel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM product WHERE category_id = 2 ORDER BY product_id DESC LIMIT 1"));
        $accessory = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM product WHERE category_id = 7 ORDER BY product_id DESC LIMIT 1"));

        $products = [$dress, $bag, $heel, $accessory];

            // Loop through featured products 
        foreach($products as $product){
            if($product){
        ?>

            <!-- Featured Card -->
            <div class="featured-card">

                <!-- Product Image -->
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>">

                <h3><?php echo htmlspecialchars($product['name']); ?></h3>

                <p class="price">
        <!-- Currency conversion price element -->
    <span class="home-price" data-price="<?php echo floatval($product['price']); ?>">
        <?php echo number_format($product['price'], 2); ?> SAR
    </span>
</p>

                    <!-- Product action buttons -->
                <div class="product-card-actions">
                    
                    <!-- View Details Button -->
                    <a href="product_details.php?id=<?php echo $product['product_id']; ?>" class="btn-primary">
                       View Details
                    </a>

                    <!-- Wishlist Toggle Button -->
                    <button class="wishlist-btn <?php echo in_array($product['product_id'], $wishlist_ids) ? 'active' : ''; ?>" 
                            onclick="toggleWishlist(this, <?php echo $product['product_id']; ?>)">
                        <?php echo in_array($product['product_id'], $wishlist_ids) ? '♥' : '♡'; ?>
                    </button>

                </div>

            </div>

        <?php 
            }
        }
        ?>

    </div>

    <a href="products.php" class="view-all-btn">View All Products</a>
</div>
<!-- Website footer -->
<footer class="footer">© 2026 Fit & Form. All rights reserved.</footer>

<script>
/* ================= WISHLIST TOGGLE ================= */
function toggleWishlist(btn, productId) {
    let isActive = btn.classList.contains('active');
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
            btn.classList.add('active');
            btn.innerHTML = '♥';
        } else if (data.status === 'removed') {
            btn.classList.remove('active');
            btn.innerHTML = '♡';
        } else if (data.status === 'exists') {
            alert('Product already in wishlist!');
        }
    })
    .catch(error => console.error('Error:', error));
}

/* ================= HOME CURRENCY UPDATE ================= */

// Run currency update after page loads
document.addEventListener("DOMContentLoaded", function () {

    // Select all product price elements
    const prices = document.querySelectorAll(".home-price");

    const rates = {
        SAR: 1,
        USD: 0.27,
        EUR: 0.25,
        AED: 0.98
    };


    // Update prices based on selected currency
    function updateHomeCurrency() {

            // Get selected currency from local storage
        const currency = localStorage.getItem("currency") || "SAR";
        const rate = rates[currency] || 1;


        // Update all displayed product prices
        prices.forEach(priceElement => {
            const basePrice = parseFloat(priceElement.dataset.price);

            if (!isNaN(basePrice)) {
                priceElement.textContent = (basePrice * rate).toFixed(2) + " " + currency;
            }
        });
    }

    updateHomeCurrency();

});
</script>

</body>
</html>