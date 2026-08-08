<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
     <!-- Make page responsive on different screen sizes -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Role | Fit & Form</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Include reusable navigation bar -->
    <?php include 'navbar.php'; ?>
 <!-- Main role selection page -->
    <div class="choice-page">
 <!-- Center container for all role options -->
        <div class="choice-box">
<!-- Website logo image -->
            <img src="images/logo.png" alt="Fit & Form Logo" class="choice-logo">

            <h1>Welcome to Fit & Form</h1>
            <p>Choose how you would like to continue</p>

<!-- Main buttons for user navigation -->
            <div class="main-buttons">

                <a href="register.php" class="main-btn">Create Account</a>
                <a href="login.php" class="main-btn">Log In</a>
                <!-- Allow users to continue browsing without creating an account -->
                <a href="home.php" class="main-btn guest-btn">Continue as Guest</a>
            </div>

            <div class="admin-link-box">
                <!-- Redirect admins to admin login page -->
                <a href="login.php?role=admin" class="admin-link">I am an admin</a>
            </div>
        </div>
    </div>

</body>
</html>