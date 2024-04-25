<?php
session_start();

// Check if user is logged in
if(isset($_SESSION['user_id'])) {
    // Destroy the session
    session_destroy();
    // Redirect to the home page or any desired location after logout
    header("Location: index.php");
    exit;
}
?>