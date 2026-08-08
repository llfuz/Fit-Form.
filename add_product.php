<?php

// Start session and connect to database
session_start();
include "config.php";

// Restrict page access to admin users only
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: home.php");
    exit();
}

// Retrieve all categories from database
 $categories = mysqli_query($conn, "SELECT * FROM category");

 // Variable to store success or error messages
 $msg = "";

 // Check if add product form was submitted
if (isset($_POST['add_product'])) {

    // Remove extra spaces from input fields
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);


    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $cat_id = $_POST['category_id'];

    // Retrieve uploaded image information
    $image_name = $_FILES['image']['name'];
    $image_tmp  = $_FILES['image']['tmp_name'];


     // Rename image using current time to avoid duplicate names
    $image_name = time() . "_" . $image_name;

        // Define image storage path
    $image_folder = "images/" . $image_name;

        // Validate required fields
    if(empty($name) || empty($price) || empty($stock)){
        $msg = "Please fill all required fields.";
    }
    else {

        if (move_uploaded_file($image_tmp, $image_folder)) {

             // Insert new product into database
            $stmt = $conn->prepare("
                INSERT INTO product (name, description, price, stock, category_id, image)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssdiss", $name, $desc, $price, $stock, $cat_id, $image_name);

            
            if ($stmt->execute()) {
            // Display success message after adding product
                $msg = "Product added successfully!";
            } else {
                $msg = "Database error.";
            }

        } else {
            $msg = "Image upload failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
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

<!-- Main add product page -->
<div class="admin-page">

    <h1>Add New Product</h1>
    <p>Fill in the details below.</p>

    <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

    <form method="post" enctype="multipart/form-data" class="admin-card">

<!-- Display success or error message -->
        <?php if($msg != "") echo "<p class='error'>$msg</p>"; ?>

        <label>Product Name</label>
        <input type="text" name="name" required>

        <label>Description</label>
        <input type="text" name="description">

        <label>Price (SAR)</label>
        <input type="number" step="0.01" name="price" required>

        <label>Stock Quantity</label>
        <input type="number" name="stock" required>

        <label>Category</label>
        <select name="category_id">

        <!-- Display categories dynamically from database -->
            <?php while($cat = mysqli_fetch_assoc($categories)) { ?>
                <option value="<?php echo $cat['category_id']; ?>">
                    <?php echo $cat['name']; ?>
                </option>
            <?php } ?>
        </select>

        <label>Product Image</label>
        <!-- Product image upload input -->
        <input type="file" name="image" accept="image/*" required>

        <!-- Submit button to add product -->
        <button type="submit" name="add_product" class="admin-btn">
            Add Product
        </button>

    </form>

</div>

</body>
</html>