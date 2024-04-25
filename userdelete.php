<?php
session_start();
require('connect.php');

$user_id = $_GET['user_id'];

// Delete user from the database
$sql = "DELETE FROM users WHERE user_id = ?";
$statement = $db->prepare($sql);
$statement->execute([$user_id]);

// Redirect to usersindex.php after deletion
header("Location: usersindex.php");
exit;
?>
