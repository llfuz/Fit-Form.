<?php
session_start();
include "config.php";

/* Clear all wishlist items */
// We check if 'clear_all' is submitted
if(isset($_POST['clear_all'])){
    
    // 1. Perform the Deletion (Regardless of AJAX or not)
    if(isset($_SESSION['user_id'])){
        $user_id = $_SESSION['user_id'];
        mysqli_query($conn, "DELETE FROM wishlist WHERE user_id = $user_id");
    } else {
        unset($_SESSION['guest_wishlist']);
    }

    // 2. Handle Response
    if(isset($_POST['ajax'])){
        // If it's an AJAX request, return JSON and stop
        echo json_encode(['status' => 'cleared']);
        exit();
    } else {
        // If it's a normal form submit, redirect back
        header("Location: wishlist.php");
        exit();
    }
}

/* ================= FETCH WISHLIST DATA ================= */
 $wishlist_items = [];

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = $conn->prepare("SELECT product.* FROM wishlist JOIN product ON wishlist.product_id = product.product_id WHERE wishlist.user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    while ($row = $result->fetch_assoc()) { $wishlist_items[] = $row; }
} else {
    $guest_wishlist = $_SESSION['guest_wishlist'] ?? [];
    foreach ($guest_wishlist as $product_id) {
        $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $product = $res->fetch_assoc();
        if ($product) { $wishlist_items[] = $product; }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Wishlist</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navbar include -->

<?php include 'navbar.php'; ?>

<div class="wishlist-page">
    <h1>My Wishlist</h1>

    <?php if(count($wishlist_items) > 0): ?>
        <div class="wishlist-actions">
            <!-- The button calls the JS function -->
            <button onclick="clearAllWishlist()" class="btn-clear-all">Clear Wishlist</button>
        </div>
    <?php endif; ?>

    <div class="wishlist-grid" id="wishlistGrid">

 <!-- Clear all button (shown only if items exist) -->

    <?php if(count($wishlist_items) > 0): ?>
        <?php foreach($wishlist_items as $product): ?>
            <div class="wishlist-card" id="product-<?php echo $product['product_id']; ?>">
                <img src="images/<?php echo htmlspecialchars($product['image']); ?>">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p><?php echo htmlspecialchars($product['price']); ?> SAR</p>
                <div class="btn-group">
                    <button onclick="removeFromWishlist(<?php echo $product['product_id']; ?>)" class="remove-btn">Remove</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty-msg">Your wishlist is empty.</p>
    <?php endif; ?>

    </div>
</div>

<script>
    // Remove single item
    function removeFromWishlist(productId) {
        let formData = new FormData();
        formData.append('product_id', productId);

        fetch('remove_wishlist.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'removed') {
                let card = document.getElementById('product-' + productId);
                if(card) {
                    card.style.transition = '0.3s';
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 300);
                }
                checkIfEmpty();
            }
        });
    }

    // Clear all items
    function clearAllWishlist() {
        if(!confirm('Are you sure you want to clear all items?')) return;

        // Send AJAX request with 'ajax=1' parameter
        fetch('wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'clear_all=1&ajax=1'
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'cleared') {
                // Update UI
                document.getElementById('wishlistGrid').innerHTML = '<p class="empty-msg">Your wishlist is empty.</p>';
                let actions = document.querySelector('.wishlist-actions');
                if(actions) actions.style.display = 'none';
            }
        });
    }

    // Check if list is empty to show message
    function checkIfEmpty() {
        let grid = document.getElementById('wishlistGrid');
        // Check if any .wishlist-card remains
        if (grid.querySelectorAll('.wishlist-card').length === 0) {
            grid.innerHTML = '<p class="empty-msg">Your wishlist is empty.</p>';
            let actions = document.querySelector('.wishlist-actions');
            if(actions) actions.style.display = 'none';
        }
    }
</script>

</body>
</html>