<?php
// Start session to store user login information
session_start();
// Include database connection file
include "config.php";

// Enable error reporting for debugging during development
error_reporting(E_ALL);
ini_set('display_errors', 1);


// Check if register button was clicked
if(isset($_POST['register'])){

    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $phone  = trim($_POST['phone']);
     // Store selected gender and password
    $gender = $_POST['gender'];
    $raw_password = $_POST['password'];

   // Validate that all fields are filled
    if(empty($name) || empty($email) || empty($phone) || empty($gender) || empty($raw_password)){
        $error = "All fields are required";
    }

    // Validate email format
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "Invalid email format";
    }
    else{
    // Encrypt password before saving it in database
        $password = password_hash($raw_password, PASSWORD_DEFAULT);

    // Check if email already exists in database
        $stmt = $conn->prepare("SELECT user_id FROM user WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0){
            $error = "Email already exists";
        } else {

            // Insert new user into database
            $stmt = $conn->prepare("
                INSERT INTO user (name, email, phone, gender, password, role)
                VALUES (?, ?, ?, ?, ?, 'user')
            ");
            // Bind user data securely to SQL query
            $stmt->bind_param("sssss", $name, $email, $phone, $gender, $password);
            $stmt->execute();
             // Save registered user data in session
            $_SESSION['user_id']   = $stmt->insert_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['role']      = 'user';
        // Redirect user to home page after successful registration
            header("Location: home.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="style.css">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>

<div class="navbar">
    <div class="logo">Fit & Form</div>
</div>
<!-- Main register page container -->
<div class="auth-page">

    <h1>Create Account</h1>
    <!-- Short description for users -->
    <p>Register to save wishlist and orders later.</p>

    <a href="choose_role.php" class="back-btn">← Back</a>

    <form method="POST" class="auth-card">
<!-- Display error message if validation fails -->
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

        <label>Full Name</label>
        <input type="text" name="name" placeholder="Your Name" required>

        <label>Email</label>
        <input type="email" name="email" placeholder="you@example.com" required>

        <label>Phone Number</label>
        <input type="text" name="phone" placeholder="+966 5X XXX XXXX" required>

        <label>Gender</label>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Female">Female</option>
            <option value="Male">Male</option>
        </select>

        <label>Password</label>
        <input type="password" name="password" placeholder="Create Password" required>

        <button type="submit" name="register" class="btn-primary auth-btn">
            Create Account
        </button>

        <p class="auth-link">
            Already have an account?
            <!-- Link to login page -->
            <a href="login.php">Login</a>
        </p>

    </form>

</div>

</body>
</html>