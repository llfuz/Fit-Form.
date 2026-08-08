<?php
// Start session to store logged-in user data
session_start();
// Connect to the database
include "config.php";
// Check if the login page is opened in admin mode
$admin_mode = isset($_GET['role']) && $_GET['role'] == 'admin';
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];
// Search for user using entered email
    $stmt = $conn->prepare("SELECT * FROM user WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if($res->num_rows > 0){

        $user = $res->fetch_assoc();

// Verify entered password with encrypted password in database
        if(password_verify($password, $user['password'])){

            if($admin_mode && strtolower($user['role']) != 'admin'){
                $error = "You are not an admin";
            } else {
// Save user information in session after successful login
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

// Redirect admin to dashboard and users to home page
                if(strtolower($user['role']) == 'admin'){
                    
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: home.php");
                }
                exit();
            }

        } else {
            $error = "Wrong password";
        }

    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

<!-- Navigation bar -->
<div class="navbar">
    <div class="logo">Fit & Form</div>
</div>
<!-- Main login page container -->
<div class="auth-page">

<!-- Display different title based on user role -->
    <?php if($admin_mode): ?>
        <h1>Admin Login</h1>
        <p>Login as admin to manage the system.</p>
    <?php else: ?>
        <h1>Login</h1>
        <p>Login to continue your order.</p>
    <?php endif; ?>

    <a href="choose_role.php" class="back-btn">← Back</a>

    <form method="POST" class="auth-card">

        <?php
        if(isset($_GET['message']) && $_GET['message'] == 'no_account'){
            echo "<p class='error'>You do not have an account. Please login or register first.</p>";
        }
// Display error messages if login fails 
        if(isset($error)){
            echo "<p class='error'>$error</p>";
        }
        ?>
<!-- Email input field -->
        <label>Email</label>
        <input type="email" name="email" placeholder="you@example.com" required>
<!-- Password input field -->
        <label>Password</label>
        <input type="password" name="password" placeholder="Password" required>
<!-- Login button -->
        <button type="submit" class="btn-primary auth-btn">
            Login
        </button>
<!-- Link to registration page -->

        <p class="auth-link">
            Don't have an account?
            <a href="register.php">Register</a>
        </p>

        <?php if(!$admin_mode): ?>
        <p class="auth-link">
            Or continue without an account:
            <!-- Allow users to continue without creating an account -->
            <a href="home.php">Continue as Guest</a>
        </p>
        <?php endif; ?>

    </form>

</div>

</body>
</html>