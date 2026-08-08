<?php

// Start session and connect to database
session_start();
include "config.php";

// Restrict page access to admin users only
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: home.php");
    exit();
}

// Check if admin submitted order status update
if (isset($_POST['update_status'])) {

    // Convert order ID to integer for security
    $order_id = intval($_POST['order_id']);

    // Retrieve selected order status
    $status = $_POST['status'];

    // Update order status in database
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE order_id=?");

    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    // Redirect back after updating order
    header("Location: manage_orders.php");
    exit();
}

// Retrieve all customer orders sorted from newest to oldest
 $orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

<!-- NAVBAR (Admin Version) -->
<div class="navbar">
    <div class="logo">Fit & Form</div>

    <div class="nav-links">
        <a href="home.php">View Site</a>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="manage_products.php">Products</a>
        <a href="manage_orders.php">Orders</a>
        <a href="manage_messages.php">Messages</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<!-- Main orders management page -->
<div class="admin-page">

    <h1>Manage Orders</h1>
    <p>Review customer orders and update their status.</p>

    <!-- Orders list container -->
    <div class="admin-product-list">

    <!-- Check if orders exist -->
        <?php if(mysqli_num_rows($orders) > 0) { ?>

        <!-- Loop through all customer orders -->
            <?php while($row = mysqli_fetch_assoc($orders)) { ?>

                <div class="admin-text-item">

                    <div class="admin-text-info">

            <!-- Display order number -->
                        <h3>Order #<?php echo $row['order_id']; ?></h3>

                        <p><strong>Customer:</strong> 
                            <?php echo htmlspecialchars($row['name']); ?>
                        </p>

                        <!-- Display payment method and total price -->
                        <p>
                            <strong>Payment:</strong> 
                            <?php echo ucfirst(htmlspecialchars($row['payment'])); ?> 
                            | 
                            <strong>Total:</strong> 
                            <?php echo $row['total']; ?> SAR
                        </p>

                        <!-- Display shipping address -->
                        <p style="font-size: 12px; color: #aaa;">
                            Address: <?php echo htmlspecialchars($row['address']); ?>
                        </p>

                    </div>

                    <div class="admin-actions" style="flex-direction: column; align-items: flex-end; gap: 10px;">

                    <!-- Display current order status -->
                        <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>

                        <!-- Form to update order status -->
                        <form method="post" class="order-status-form">

                            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">

                            <select name="status">
                                <option value="placed" <?php if($row['status']=='placed') echo 'selected'; ?>>Placed</option>
                                <option value="shipped" <?php if($row['status']=='shipped') echo 'selected'; ?>>Shipped</option>
                                <option value="delivered" <?php if($row['status']=='delivered') echo 'selected'; ?>>Delivered</option>
                                <option value="cancelled" <?php if($row['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>

                            <!-- Submit updated order status -->
                            <button type="submit" name="update_status" class="btn-edit">
                                Update
                            </button>

                        </form>

                    </div>

                </div>

            <?php } ?>

        <?php } else { ?>

        <!-- Display message when no orders are available -->
            <div class="empty-history" style="background:white; padding:40px; border-radius:14px;">
                <p>No orders found.</p>
            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>