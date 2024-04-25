<?php
session_start();
require('connect.php');

// Check if the user is not LH user
if ($_SESSION['tier_id'] != 3) {
    header("Location: index.php"); // Redirect if not LH user
    exit;
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $username = $_POST['username'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Hash the password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the database
    $sql = "INSERT INTO Users (tier_id, user_name, password, first_name, last_name, email) VALUES (?, ?, ?, ?, ?, ?)";
    $statement = $db->prepare($sql);
    $statement->execute([2, $username, $hash, $first_name, $last_name, $email]);

    // Redirect to usersindex.php after user creation
    header("Location: usersindex.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <title>Add New User</title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <h2>Add New User</h2>
    <form method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="first_name">First Name:</label><br>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="last_name">Last Name:</label><br>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email"><br><br>

        <input type="submit" value="Create">
    </form>
</body>
</html>
