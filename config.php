<?php
$conn = mysqli_connect("localhost", "root", "", "fit_form");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?><?php
$host = "localhost";
$user = "root";
$password = "";
$database = "fit_form";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>