<?php 
// Include database configuration file
include 'config.php'; 
?>
<!DOCTYPE html>
<html>
<head>
<!-- Page Title and Stylesheet -->
<title>Contact Us</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
   <!-- Include Navigation Bar -->
   <?php include 'navbar.php'; ?>
<!-- Main Contact Page Container -->
<div class="contact-page">

<h1>Contact Us</h1>
<p>We’d love to hear from you. Send your message and we’ll respond soon.</p>

<!-- Display success message if present in URL -->
<?php if(isset($_GET['success'])): ?>
<p class="success-msg">Message sent successfully!</p>
<?php endif; ?>

<div class="contact-container">

<!-- CONTACT FORM Section -->
<form action="submit_contact.php" method="POST" class="contact-form">

<h2>Send a Message</h2>

<input type="text" name="full_name" placeholder="Full Name" required>

<input type="email" name="email" placeholder="Email" required>

<input type="text" name="subject" placeholder="Subject">

<textarea name="message" placeholder="Write your message..." required></textarea>

<button type="submit">Send Message</button>

</form>

<!-- CONTACT INFO Section -->
<div class="contact-info">

<h2>Shop Information</h2>

<p><strong>Email:</strong> support@fitform.com</p>
<p><strong>Phone:</strong> +966 50 347 3430</p>
<p><strong>Address:</strong> Jubail, Eastern Province, Saudi Arabia</p>

<h3>Location Map</h3>

<!-- Google Maps Embed -->
<iframe
src="https://www.google.com/maps?q=Jubail%20Saudi%20Arabia&output=embed"
width="100%"
height="220"
style="border:0; border-radius:12px;"
allowfullscreen=""
loading="lazy">
</iframe>

</div>

</div>

</div>

</body>
</html>