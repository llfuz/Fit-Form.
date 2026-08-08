<?php
// Start the session
session_start();
// Include database configuration file
include "config.php";

/* USER Mode: Clear cart for logged-in users */
if (isset($_SESSION['user_id'])) {

    // Get user ID from session
    $user_id = $_SESSION['user_id'];

    // Prepare SQL statement to delete all items from the user's cart
    // It joins Cart_Item with Cart to ensure we delete items for the specific user
    $stmt = $conn->prepare("
        DELETE Cart_Item 
        FROM Cart_Item
        JOIN Cart ON Cart_Item.cart_id = Cart.cart_id
        WHERE Cart.user_id = ?
    ");
    // Bind the user ID parameter
    $stmt->bind_param("i", $user_id);
    // Execute the deletion
    $stmt->execute();

/* GUEST Mode: Clear cart for guests */
} else {
    // Remove the guest cart session variable
    unset($_SESSION['guest_cart']);
}

// Redirect back to the cart page
header("Location: cart.php");
exit();
?>