<?php
// Start the session
session_start();
// Include database configuration file
include "config.php";

// Protect page: Check if user is logged in
if(!isset($_SESSION['user_id'])){
// Redirect to login page if not logged in
header("Location: login.php");
exit();
}

// Get user ID from session
 $user_id = $_SESSION['user_id'];

// Check if the update form was submitted
if(isset($_POST['update_profile'])){

// Get form input values
 $name = $_POST['name'];
 $email = $_POST['email'];
 $phone = $_POST['phone'];
 $gender = $_POST['gender'];

// Update user information in the database
mysqli_query($conn,"
UPDATE user
SET
name='$name',
email='$email',
phone='$phone',
gender='$gender'
WHERE user_id='$user_id'
");

// Set success message and redirect to account page
 $success = "Profile updated successfully!";
header("Location: account.php");
    exit();
}

// Fetch current user data from database to pre-fill the form
 $query = mysqli_query($conn,"
SELECT * FROM user
WHERE user_id='$user_id'
");

 $user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html>
<head>
<!-- Page Title and Stylesheet -->
<title>Edit Profile</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Main Container for Edit Profile Page -->
<div class="auth-page">

<h1>Edit Profile</h1>
<p>Update your account information.</p>

<!-- Profile Edit Form -->
<form method="POST" id="editProfileForm" class="auth-card">
<input type="hidden" name="update_profile" value="1">
<?php if(isset($success)) echo "<p class='success-msg'>$success</p>"; ?>

<label>Full Name</label>
<!-- Input field for Name with value from database -->
<input type="text" name="name" value="<?php echo $user['name']; ?>" required>

<label>Email</label>
<!-- Input field for Email with value from database -->
<input type="email" name="email" value="<?php echo $user['email']; ?>" required>

<label>Phone Number</label>
<!-- Input field for Phone with value from database -->
<input type="text" name="phone" value="<?php echo $user['phone']; ?>" required>

<label>Gender</label>
<!-- Select field for Gender with logic to keep current selection -->
<select name="gender" required>
<option value="Female" <?php if($user['gender']=='Female') echo 'selected'; ?>>Female</option>
<option value="Male" <?php if($user['gender']=='Male') echo 'selected'; ?>>Male</option>
</select>

<!-- Submit Button -->
<button type="submit" class="btn-primary auth-btn">
Save Changes
</button>

</form>

</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal-overlay">
    <div class="profile-modal">
        <h2>Save changes?</h2>
        <p>Your updated profile information will be saved to your account.</p>

        <div class="profile-modal-actions">
            <button id="saveBtn" class="btn-primary modal-save-btn">
                Save Changes
            </button>

            <button type="button" onclick="closeModal()" class="btn-outline modal-cancel-btn">
                Cancel
            </button>
        </div>
    </div>
</div>


<!-- JavaScript for Form Submission and Modal Logic -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    // Get form, modal, and save button elements
    const f = document.getElementById('editProfileForm'),
          m = document.getElementById('confirmModal'),
          btn = document.getElementById('saveBtn');

    if (!f) {
        console.error("Form not found! Make sure ID is 'editProfileForm'");
        return;
    }

    // Intercept form submission
    f.addEventListener('submit', (e) => {
        // Check if modal is not already visible
        if (m.style.display !== 'flex') {
            e.preventDefault(); 
            // Check form validity before showing modal
            if (f.checkValidity()) {
                m.style.display = 'flex';
            } else {
                f.reportValidity();
            }
        }
    });

    // Handle save button click
    btn.onclick = () => {
        m.style.display = 'none';
        btn.disabled = true;
        btn.innerText = "Saving...";
        // Submit the form programmatically
        f.submit();
    };
});

// Function to close the modal
const closeModal = () => document.getElementById('confirmModal').style.display = 'none';
</script>
</body>
</html>