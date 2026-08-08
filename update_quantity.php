<?php

include "config.php";
/* Update cart item quantity if data is received */

if(isset($_POST['cart_item_id']) && isset($_POST['new_quantity'])){
    $id = intval($_POST['cart_item_id']);
    $qty = intval($_POST['new_quantity']);
    $conn->query("UPDATE Cart_Item SET quantity = $qty WHERE cart_item_id = $id");
}
/* Redirect back to cart page */

header("Location: cart.php");
?>