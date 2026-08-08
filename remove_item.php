<?php
session_start();
include "config.php";
/* Logged-in user: remove item from database */
if (isset($_SESSION['user_id'])) {

    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);

        $stmt = $conn->prepare("DELETE FROM Cart_Item WHERE cart_item_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
/* Guest user: remove item from session cart */} else {

    if (isset($_GET['index'])) {
        $index = intval($_GET['index']);
        unset($_SESSION['guest_cart'][$index]);
        $_SESSION['guest_cart'] = array_values($_SESSION['guest_cart']);
    }
}

// Redirect back to cart page

header("Location: cart.php");
exit();
?>