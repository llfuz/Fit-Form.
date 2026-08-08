<?php
// Include database configuration file
include "config.php";

// Check if 'id' parameter is set in the URL
if(isset($_GET['id'])){

    // Get the product ID from the URL
    $id = $_GET['id'];

    // Prepare SQL statement to delete the product
    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    // Bind the product ID parameter to the query
    $stmt->bind_param("i", $id);

    // Execute the statement and check if successful
    if($stmt->execute()){
        // Redirect to the product management page
        header("Location: manage_products.php");
        exit();
    }
    else{
        // Display error message if deletion fails
        echo "Failed to delete product.";
    }
}
?>