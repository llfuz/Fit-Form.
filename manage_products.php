<?php
// Start session and connect to database

session_start();
include "config.php";

// Allow access for admin users only
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: home.php");
    exit();
}

// Store search keyword
 $search = "";

// Check if admin searched for a product
if (isset($_GET['search']) && !empty($_GET['search'])) {

    $search = $_GET['search'];

    // Search products by name using LIKE
    $stmt = $conn->prepare("
        SELECT p.*, c.name as cat_name
        FROM product p
        JOIN category c ON p.category_id = c.category_id
        WHERE p.name LIKE ?
    ");

    // Add % symbols for partial search matching
    $like = "%" . $search . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();

} else {

    // Retrieve all products with category names
    $sql = "
        SELECT p.*, c.name as cat_name
        FROM product p
        JOIN category c ON p.category_id = c.category_id
    ";

    $result = mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <link rel="stylesheet" href="style.css?v=2">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>


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

<!-- Main admin products page -->
<div class="admin-page">

    <h1>Manage Products</h1>
    <p>View, search, edit or delete products.</p>

    <!-- Product search form -->
    <form method="get" class="admin-card" style="display:flex; gap:10px;">
        <input type="text" name="search" placeholder="Search product..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-primary">Search</button>
        <a href="manage_products.php" class="btn-outline">Reset</a>
    </form>

    
    <div class="admin-product-list">

        <!-- Loop through products from database -->
        <?php while($row = mysqli_fetch_assoc($result)) { ?>

            <div class="admin-product-item">

                <div style="display:flex; align-items:center;">

                    <!-- Product image -->
                    <img src="images/<?php echo $row['image']; ?>">

                    <!-- Product information section -->
                    <div class="admin-product-info">
                        <h3><?php echo $row['name']; ?></h3>
                        <p>Price: <?php echo $row['price']; ?> SAR</p>
                        <p>Stock: <?php echo $row['stock']; ?></p>
                        <p>Category: <?php echo $row['cat_name']; ?></p>
                    </div>

                </div>

                <div class="admin-actions">
                    <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn-edit">Edit</a>

                    <!-- Delete product with confirmation message -->
                    <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" 
                       class="btn-delete"
                       onclick="return confirm('Are you sure?')">
                       Delete
                    </a>
                </div>

            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>