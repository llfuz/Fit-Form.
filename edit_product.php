<?php
// Start session and connect to database
session_start();
include "config.php";

// Restrict page access to admin users only
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: home.php");
    exit();
}

// Check if product ID exists in URL
if(!isset($_GET['id'])){
    header("Location: manage_products.php");
    exit();
}

// Convert product ID to integer for security
 $id = intval($_GET['id']);

 // Retrieve selected product data from database
 $stmt = $conn->prepare("SELECT * FROM product WHERE product_id=?");
 $stmt->bind_param("i", $id);
 $stmt->execute();
 $product = $stmt->get_result()->fetch_assoc();

 // Retrieve all categories for dropdown menu
 $categories = mysqli_query($conn, "SELECT * FROM category");

 $msg = "";

 // Check if update form was submitted
if (isset($_POST['update'])) {

// Retrieve updated product information
    $name = trim($_POST['name']);
    $price = $_POST['price'];

    $stock = $_POST['stock'];
    $cat_id = $_POST['category_id'];

    // Check if admin uploaded a new image
    if (!empty($_FILES['image']['name'])) {

    // Rename image to avoid duplicate file names
        $image_name = time() . "_" . $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

         // Upload new image to images folder
        move_uploaded_file($tmp, "images/" . $image_name);

     // Update product including new image
        $stmt = $conn->prepare("
            UPDATE product 
            SET name=?, price=?, stock=?, category_id=?, image=? 
            WHERE product_id=?
        ");
        $stmt->bind_param("sdiisi", $name, $price, $stock, $cat_id, $image_name, $id);

    } else {

     // Update product without changing current image
        $stmt = $conn->prepare("
            UPDATE product 
            SET name=?, price=?, stock=?, category_id=? 
            WHERE product_id=?
        ");
        $stmt->bind_param("sdiii", $name, $price, $stock, $cat_id, $id);
    }

    $stmt->execute();

    header("Location: manage_products.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght+600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
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

<!-- Main edit product page -->
<div class="admin-page">

    <h1>Edit Product</h1>
    <p>Update product details.</p>

<!-- Product edit form -->
    <form method="post" enctype="multipart/form-data" class="admin-card">

        <label>Name</label>
        <input type="text" name="name" value="<?php echo $product['name']; ?>" required>

        <label>Price</label>
        <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required>

        <label>Stock</label>
        <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>

        <label>Category</label>
        <select name="category_id">
            <?php while($cat = mysqli_fetch_assoc($categories)) { 
                //Preselect current product category
                $selected = ($cat['category_id'] == $product['category_id']) ? "selected" : "";
            ?>
                <option value="<?php echo $cat['category_id']; ?>" <?php echo $selected; ?>>
                    <?php echo $cat['name']; ?>
                </option>
            <?php } ?>
        </select>

    <!-- Display current product image name -->
        <label>Current Image:</label>
        <p><?php echo $product['image']; ?></p>

        <!-- Upload new image if needed -->
        <label>Change Image (Optional)</label>
        <input type="file" name="image">

    <!-- Save updated product information -->
        <button type="submit" name="update" class="admin-btn">
            Save Changes
        </button>

    </form>

</div>

</body>
</html>