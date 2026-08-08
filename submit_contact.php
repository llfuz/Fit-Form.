<?php
include 'config.php';

// Get and sanitize form inputs

$full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$subject = mysqli_real_escape_string($conn, $_POST['subject']);
$message = mysqli_real_escape_string($conn, $_POST['message']);

// Insert contact message into database

mysqli_query($conn,"
INSERT INTO contact_messages
(full_name,email,subject,message)
VALUES
('$full_name','$email','$subject','$message')
");

// Redirect back with success flag

header("Location: contact.php?success=1");
exit();
?>