<?php
session_start();
require('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try { 
        $statement = $db->prepare("SELECT * FROM Users WHERE user_name = ?");
        $statement->execute([$username]);
        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['user_name'];
            $_SESSION['tier_id'] = $user['tier_id'];
            header("Location: index.php");
            exit;
        } else {
            echo "Invalid username or password.";
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
