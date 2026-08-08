<?php
// Start session and connect to database
session_start();
include "config.php";

// Restrict page access to admin users only
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: home.php");
    exit();
}

// Check if admin requested to delete a message
if (isset($_GET['delete'])) {

    // Convert message ID to integer for security
    $id = intval($_GET['delete']);

        // Delete selected message from database
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE message_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

        // Redirect back after deletion
    header("Location: manage_messages.php");
    exit();
}

// Retrieve all contact messages ordered by latest first
 $messages = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Messages</title>
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

<!-- Main messages management page -->
<div class="admin-page">

    <h1>Contact Messages</h1>
    <p>View and manage messages from customers.</p>

<!-- Messages list container -->
    <div class="admin-product-list">

    <!-- Check if there are messages available -->
        <?php if(mysqli_num_rows($messages) > 0) { ?>

    <!-- Loop through all messages -->
            <?php while($row = mysqli_fetch_assoc($messages)) { ?>

            <!-- Customer message card -->
                <div class="admin-text-item">

                    <div class="admin-text-info">

                        <h3><?php echo htmlspecialchars($row['full_name']); ?></h3>

                        <p><strong>Email:</strong> 
                            <?php echo htmlspecialchars($row['email']); ?>
                        </p>

                        <?php if(!empty($row['subject'])) { ?>
                            <p><strong>Subject:</strong> 
                                <?php echo htmlspecialchars($row['subject']); ?>
                            </p>
                        <?php } ?>

                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                        </div>

                        <p style="font-size: 12px; color: #aaa; margin-top: 8px;">
                            Sent: <?php echo date('M d, Y - h:i A', strtotime($row['created_at'])); ?>
                        </p>

                    </div>

                    <div class="admin-actions">
                        <a href="manage_messages.php?delete=<?php echo $row['message_id']; ?>"
                           class="btn-delete"
                           onclick="return confirm('Delete this message?')">
                           Delete
                        </a>
                    </div>

                </div>

            <?php } ?>

        <?php } else { ?>

            <div class="empty-history" style="background:white; padding:40px; border-radius:14px;">
                <!-- Display message if no contact messages exist -->
                <p>No messages found.</p>
            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>