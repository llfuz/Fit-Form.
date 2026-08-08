<?php
session_start();
include "config.php";

 $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($product_id <= 0) {
    echo json_encode(['status' => 'error']);
    exit();
}

/* ===== USER ===== */
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $check = $conn->prepare("SELECT * FROM Wishlist WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows == 0) {
        $insert = $conn->prepare("INSERT INTO Wishlist (user_id, product_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $product_id);
        $insert->execute();
        echo json_encode(['status' => 'added']);
    } else {
        echo json_encode(['status' => 'exists']);
    }

/* ===== GUEST ===== */
} else {
    if (!isset($_SESSION['guest_wishlist'])) {
        $_SESSION['guest_wishlist'] = [];
    }

    if (!in_array($product_id, $_SESSION['guest_wishlist'])) {
        $_SESSION['guest_wishlist'][] = $product_id;
        echo json_encode(['status' => 'added']);
    } else {
        echo json_encode(['status' => 'exists']);
    }
}

?>