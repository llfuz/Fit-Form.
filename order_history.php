<?php
// Start the session
session_start();
// Include database configuration file
include "config.php";

// Protect page: Redirect to login if user is not logged in
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Get user ID from session
 $user_id = $_SESSION['user_id'];

// Fetch all orders for the current user, ordered by latest first
 $orders = mysqli_query($conn,
"SELECT * FROM orders 
 WHERE user_id = $user_id 
 ORDER BY order_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Page Title and Stylesheet -->
    <title>Order History</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Main Container for Order History Page -->
<div class="history-page">

<!-- Back button to return to previous page -->
<a href="javascript:history.back()" class="back-btn">← Back</a>
    <h1>My Orders</h1>
    <p>Track your previous purchases.</p>

    <div class="history-container">

        <!-- Check if there are any orders -->
        <?php if(mysqli_num_rows($orders) > 0){ ?>

            <!-- Loop through each order -->
            <?php while($order = mysqli_fetch_assoc($orders)) { ?>

                <!-- Order Card -->
                <div class="order-card">

                    <!-- Order Header: ID and Total -->
                    <div class="order-header">
                        <h3>Order #<?php echo $order['order_id']; ?></h3>
                        <span><?php echo $order['total']; ?> SAR</span>
                    </div>

                    <!-- Order Details -->
                    <p><strong>Address:</strong> <?php echo $order['address']; ?></p>
                    <p><strong>Payment:</strong> <?php echo $order['payment']; ?></p>

                    <!-- Order Items Section -->
                    <div class="order-items">
                        <h4>Items</h4>

                        <?php
                        // Fetch items for the current order by joining product and order_items tables
                        $items = mysqli_query($conn,
                        "SELECT product.name, order_items.quantity
                         FROM order_items
                         JOIN product 
                         ON product.product_id = order_items.product_id
                         WHERE order_items.order_id = ".$order['order_id']);

                        // Loop through each item in the order
                        while($item = mysqli_fetch_assoc($items)) {
                        ?>

                            <!-- Item Row: Name and Quantity -->
                            <div class="order-item">
                                <?php echo $item['name']; ?>
                                <span>x<?php echo $item['quantity']; ?></span>
                            </div>

                        <?php } ?>
                    </div>

                </div>

            <?php } ?>

        <!-- Message if no orders exist -->
        <?php } else { ?>

            <div class="empty-history">
                No orders yet.
            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>