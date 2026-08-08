<?php
// Start the session
session_start();
// Include database configuration file
include "config.php";

// Protect page: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if session user_id is not set
    header("Location: login.php?message=no_account");
    exit();
}

// Get user ID from session
 $user_id = $_SESSION['user_id'];

// Get user data: Prepare SQL statement to select user details
 $stmt = $conn->prepare("
    SELECT name, email, phone, gender
    FROM user
    WHERE user_id = ?
");

// Bind the user ID parameter to the query
 $stmt->bind_param("i", $user_id);
// Execute the query
 $stmt->execute();
// Bind the result columns to variables
 $stmt->bind_result($name, $email, $phone, $gender);
// Fetch the results
 $stmt->fetch();
// Close the statement
 $stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tag for character encoding -->
    <meta charset="UTF-8">
    <title>My Account</title>
    <!-- Link to stylesheet with version parameter -->
    <link rel="stylesheet" href="style.css?v=11">
</head>

<body>

<!-- Include the navigation bar component -->
<?php include 'navbar.php'; ?>

<!-- Main container for the account page -->
<div class="account-page">

    <!-- Display user's name using htmlspecialchars for security -->
    <h1><?= htmlspecialchars($name) ?>’s Account</h1>
    <p>Manage your profile and view your activity.</p>

    <div class="account-container">

        <!-- PROFILE Section -->
        <div class="account-card">
            <h3>Profile Information</h3>

            <div class="account-info">
                <!-- Display profile details with escaping -->
                <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
                <p><strong>Gender:</strong> <?= htmlspecialchars($gender) ?></p>
            </div>

            <div class="account-actions">
                <!-- Button to edit profile -->
                <a href="edit_profile.php" class="btn-outline full-btn">
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- ACTIVITY Section -->
        <div class="activity-card">
            <h3>My Activity</h3>

            <div class="account-actions">
                <!-- Link to order history -->
                <a href="order_history.php" class="btn-primary full-btn">
                    Order History
                </a>

                <!-- Link to wishlist -->
                <a href="wishlist.php" class="btn-outline full-btn">
                    Wishlist
                </a>

                <!-- Link to logout -->
                <a href="logout.php" class="btn-outline full-btn">
                    Logout
                </a>
            </div>
        </div>

    </div>

</div>

</body>
</html>