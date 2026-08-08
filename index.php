<?php
session_start();

if(isset($_SESSION['user_id'])){
    header("Location: home.php");
} else {
    header("Location: choose_role.php");
}
exit();
?>