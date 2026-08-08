<?php
session_start();
include "config.php";

/* Get and validate input data (product, quantity, options) */

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity   = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

$size  = $_POST['size'] ?? null;
$color = $_POST['color_hex'] ?? ($_POST['color_choice'] ?? null);
$notes = $_POST['notes'] ?? null;

if ($product_id <= 0) {
    die("Product not found.");
}

if ($quantity <= 0) {
    $quantity = 1;
}

/* GUEST MODE */
if (!isset($_SESSION['user_id'])) {

    if (!isset($_SESSION['guest_cart'])) {
        $_SESSION['guest_cart'] = [];
    }

    $found = false;

    foreach ($_SESSION['guest_cart'] as &$item) {
        if (
            $item['product_id'] == $product_id &&
            ($item['size'] ?? null) == $size &&
            ($item['color'] ?? null) == $color &&
            ($item['notes'] ?? null) == $notes
        ) {
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }
    unset($item);

    if (!$found) {
        $_SESSION['guest_cart'][] = [
            'product_id' => $product_id,
            'quantity'   => $quantity,
            'size'       => $size,
            'color'      => $color,
            'notes'      => $notes
        ];
    }

    header("Location: cart.php");
    exit();
}

/* Logged-in user: get user ID */

$user_id = $_SESSION['user_id'];

/* Get product from database */
$stmt = $conn->prepare("SELECT * FROM product WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();

if (!$product) {
    die("Product not found.");
}

/* Get or create cart */
$cart_query = $conn->prepare("SELECT cart_id FROM Cart WHERE user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();

if ($cart_result->num_rows == 0) {
    $insert_cart = $conn->prepare("INSERT INTO Cart (user_id) VALUES (?)");
    $insert_cart->bind_param("i", $user_id);
    $insert_cart->execute();
    $cart_id = $conn->insert_id;
} else {
    $cart = $cart_result->fetch_assoc();
    $cart_id = $cart['cart_id'];
}

/* Check same product with same details */
$check = $conn->prepare("
    SELECT cart_item_id, quantity 
    FROM Cart_Item 
    WHERE cart_id = ? 
      AND product_id = ?
      AND (size <=> ?)
      AND (color <=> ?)
      AND (notes <=> ?)
");
$check->bind_param("iisss", $cart_id, $product_id, $size, $color, $notes);
$check->execute();
$check_res = $check->get_result();

if ($check_res->num_rows > 0) {
    $row = $check_res->fetch_assoc();
    $new_qty = $row['quantity'] + $quantity;

    $update = $conn->prepare("UPDATE Cart_Item SET quantity = ? WHERE cart_item_id = ?");
    $update->bind_param("ii", $new_qty, $row['cart_item_id']);
    $update->execute();
} else {
    $insert = $conn->prepare("
        INSERT INTO Cart_Item 
        (cart_id, product_id, quantity, price, size, color, notes)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $insert->bind_param(
        "iiidsss",
        $cart_id,
        $product_id,
        $quantity,
        $product['price'],
        $size,
        $color,
        $notes
    );
    $insert->execute();
}

echo json_encode(['status' => 'success', 'message' => 'Added successfully!']);
exit();
?>