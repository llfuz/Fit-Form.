<?php
session_start();
include "config.php";

/* ================= WISHLIST IDS ================= */
$wishlist_ids = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $wishlist_query = mysqli_query($conn, "
        SELECT product_id 
        FROM wishlist 
        WHERE user_id = $user_id
    ");

    if ($wishlist_query) {
        while ($wish = mysqli_fetch_assoc($wishlist_query)) {
            $wishlist_ids[] = $wish['product_id'];
        }
    }

} else {
    if (isset($_SESSION['guest_wishlist'])) {
        $wishlist_ids = $_SESSION['guest_wishlist'];
    }
}

/* ================= CATEGORIES ================= */
$categories = [
    1 => ["Dresses", "dresses"],
    3 => ["Tops & Shirts", "tops"],
    4 => ["Skirts", "skirts"],
    5 => ["Pants", "pants"],
    2 => ["Footwear", "footwear"],
    7 => ["Bags", "bags"],
    6 => ["Accessories", "accessories"]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include "navbar.php"; ?>

<!-- ================= PRODUCTS HERO ================= -->
<section class="products-hero">
    <h1>Shop Our Collection</h1>
    <p>Explore customizable fashion pieces designed for your unique style.</p>
</section>

<!-- ================= CATEGORY LINKS ================= -->
<div class="category-links">
    <a href="#dresses">Dresses</a>
    <a href="#tops">Tops & Shirts</a>
    <a href="#skirts">Skirts</a>
    <a href="#pants">Pants</a>
    <a href="#footwear">Footwear</a>
    <a href="#bags">Bags</a>
    <a href="#accessories">Accessories</a>
</div>

<?php
/* ================= DISPLAY PRODUCTS BY CATEGORY ================= */
foreach ($categories as $category_id => $category_info):

    $title = $category_info[0];
    $section_id = $category_info[1];

    $query = mysqli_query($conn, "
        SELECT *
        FROM product
        WHERE category_id = $category_id
    ");

    if (!$query) {
        die("SQL Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($query) > 0):
?>

<!-- ================= CATEGORY SECTION ================= -->
<section class="category-section" id="<?php echo $section_id; ?>">
    <h2><?php echo htmlspecialchars($title); ?></h2>

    <div class="products-grid">

        <?php while ($product = mysqli_fetch_assoc($query)): ?>

            <!-- PRODUCT CARD -->
            <div class="product-card">

                <!-- PRODUCT IMAGE -->
                <img 
                    src="images/<?php echo htmlspecialchars($product['image']); ?>" 
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                >

                <!-- PRODUCT NAME -->
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>

                <!-- PRODUCT PRICE -->
                <p class="price">
                    <span 
                        class="product-price" 
                        data-price="<?php echo floatval($product['price']); ?>"
                    >
                        <?php echo number_format($product['price'], 2); ?> SAR
                    </span>
                </p>

                <!-- ACTION BUTTONS -->
                <div class="product-card-actions">

                    <!-- VIEW DETAILS -->
                    <a 
                        href="product_details.php?id=<?php echo $product['product_id']; ?>" 
                        class="btn-primary"
                    >
                        View Details
                    </a>

                    <!-- WISHLIST BUTTON -->
                    <button 
                        class="wishlist-btn <?php echo in_array($product['product_id'], $wishlist_ids) ? 'active' : ''; ?>" 
                        onclick="toggleWishlist(this, <?php echo $product['product_id']; ?>)"
                    >
                        <?php echo in_array($product['product_id'], $wishlist_ids) ? '♥' : '♡'; ?>
                    </button>

                </div>

            </div>

        <?php endwhile; ?>

    </div>
</section>

<?php
    endif;
endforeach;
?>

<!-- ================= JAVASCRIPT ================= -->
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


/* ================= CURRENCY UPDATE ================= */
document.addEventListener("DOMContentLoaded", function () {

    const prices = document.querySelectorAll(".product-price");

    const rates = {
        SAR: 1,
        USD: 0.27,
        EUR: 0.25,
        AED: 0.98
    };

    function updateProductsCurrency() {
        const currency = localStorage.getItem("currency") || "SAR";
        const rate = rates[currency] || 1;

        prices.forEach(priceElement => {
            const basePrice = parseFloat(priceElement.dataset.price);

            if (!isNaN(basePrice)) {
                priceElement.textContent = (basePrice * rate).toFixed(2) + " " + currency;
            }
        });
    }

    updateProductsCurrency();

});
</script>

</body>
</html>