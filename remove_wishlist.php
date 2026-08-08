<?php
session_start();
include "config.php";

// Get product ID from POST request and validate it

 $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($product_id <= 0) {
    echo json_encode(['status' => 'error']);
    exit();
}

/* Remove from wishlist for logged-in user */
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    echo json_encode(['status' => 'removed']);

/* Remove from wishlist for guest user */
} else {
    if (isset($_SESSION['guest_wishlist'])) {
        $_SESSION['guest_wishlist'] = array_filter(
            $_SESSION['guest_wishlist'],
            function($id) use ($product_id) { return $id != $product_id; }
        );
        $_SESSION['guest_wishlist'] = array_values($_SESSION['guest_wishlist']);
    }
    echo json_encode(['status' => 'removed']);
}

?>