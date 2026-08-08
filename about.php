<?php
// Start the session
session_start();
?>
<!DOCTYPE html> 
<html lang="en"> 
<head> 
    <!-- Meta tags for charset and viewport settings -->
    <meta charset="UTF-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>About Us | Fit & Form</title> 
    <!-- Link to main stylesheet and Google Fonts -->
    <link rel="stylesheet" href="style.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet"> 
</head> 
<body> 
 
<!-- NAVBAR -->
<!-- Include the navigation bar component -->
<?php include 'navbar.php'; ?>
 
<!-- Main section for the About Us page -->
<section class="about-page"> 

    <!-- Hero section with page title and introduction -->
    <div class="about-hero"> 
        <h1>About Fit & Form</h1> 
        <p> 
            Fit & Form is a customization-focused fashion platform where users can choose fabrics, colors, 
            and measurements to create their ideal look. 
        </p> 
    </div> 

    <!-- Container for the main content sections -->
    <div class="about-content"> 

        <!-- Section describing the company vision -->
        <div class="about-section"> 
            <h2>Our Vision</h2> 
            <p> 
                We aim to make fashion feel personal. Instead of buying the same ready-made pieces, 
                you can tailor the style to your preferences — fabric, color, size, and more. 
            </p> 
        </div> 

        <!-- Section listing user capabilities -->
        <div class="about-section"> 
            <h2>What You Can Do</h2> 
            <ul> 
                <li>Browse dresses and accessories</li> 
                <li>Customize product options (color, size, fabric)</li> 
                <li>Add items to cart and checkout</li> 
                <li>Save items to wishlist</li> 
            </ul> 
        </div> 

        <!-- Section explaining the brand's unique value -->
        <div class="about-section"> 
            <h2>Why Fit & Form?</h2> 
            <p> 
                Many online stores provide fixed designs. Fit & Form helps users express their style 
                by offering customization options, a clean interface, and a smooth shopping experience. 
            </p> 
        </div> 

    </div> 
<!-- Call-to-action buttons for shopping and contact -->
<div class="cta-buttons">
    <a href="products.php" class="btn-primary">Start Shopping</a>
    <a href="contact.php" class="btn-outline">Contact Us</a>
</div>
    
</section> 

<!-- FOOTER -->
<!-- Footer section with copyright information -->
<footer class="footer">
    © 2026 Fit & Form. All rights reserved.
</footer>

</body> 
</html>