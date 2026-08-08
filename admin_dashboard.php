<?php
// Start session and connect to database

session_start();
include "config.php";
// Restrict page access to admin users only
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: home.php");
    exit();
}

// ===== STATS =====

// Retrieve total number of products from database
$p_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM product");
$p_row = mysqli_fetch_assoc($p_res);
$total_products = $p_row['total'];

// Retrieve number of pending orders
$po_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status='placed'");
$po_row = mysqli_fetch_assoc($po_res);
$pending_orders = $po_row['total'];

// Retrieve number of completed orders
$co_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status='delivered'");
$co_row = mysqli_fetch_assoc($co_res);
$completed_orders = $co_row['total'];

// Retrieve total number of customer messages
$m_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM contact_messages");
$m_row = mysqli_fetch_assoc($m_res);
$total_messages = $m_row['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
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

<!-- DASHBOARD -->
<div class="dashboard-container">
        
    <h1>Admin Dashboard</h1>
    <p>Overview and quick access to management pages.</p>

<!-- Statistics cards section -->
    <div class="stats-row">

<!-- Display total products count -->
        <div class="stat-card">
            <h2><?php echo $total_products; ?></h2>
            <p>Total Products</p>
        </div>

        <div class="stat-card">
            <h2><?php echo $pending_orders; ?></h2>
            <p>Pending Orders</p>
        </div>

        <div class="stat-card">
            <h2><?php echo $completed_orders; ?></h2>
            <p>Completed Orders</p>
        </div>

        <div class="stat-card">
            <h2><?php echo $total_messages; ?></h2>
            <p>New Messages</p>
        </div>

    </div>

<!-- Quick action buttons for admin management -->
    <div class="actions-row">

        <a href="manage_products.php" class="action-btn primary">
            Manage Products
        </a>

        <a href="add_product.php" class="action-btn outline">
            Add New Product
        </a>

        <a href="manage_orders.php" class="action-btn outline">
            Manage Orders
        </a>

        <a href="manage_messages.php" class="action-btn outline">
            Manage Messages
        </a>

    </div>

</div>

<!-- FOOTER -->
<div class="footer" style="background: #F7F2EC; margin-top: 20px;">
    © 2026 Fit & Form. Admin Panel
</div>

</body>
</html>