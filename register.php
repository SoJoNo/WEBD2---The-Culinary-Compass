<?php
require('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword']; // New input for password confirmation
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];

        // Check if passwords match
        if ($password !== $confirmPassword) {
            echo "Error: Passwords do not match.";
            exit;
        }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert user details into the Users table
        $stmt = $db->prepare("INSERT INTO Users (tier_id, user_name, password, first_name, last_name, email) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $tier_id = 1; // Initial tier_id for guest users
        $stmt->execute([$tier_id, $username, $hash, $firstname, $lastname, $email]);
        
        // Update user's tier_id to admin
        $user_id = $db->lastInsertId(); // Retrieve the last inserted user_id
        $stmt = $db->prepare("UPDATE Users SET tier_id = ? WHERE user_id = ?");
        $tier_id = 2; // Tier_id for admin users
        $stmt->execute([$tier_id, $user_id]);

        echo "Registration successful. You are now an admin user.";
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles.css">
<script src="script.js"></script>
    <title>User Registration</title>
</head>
<body>
    <?php include('navbar.php'); ?>    

    <h2>User Registration</h2>
    <form method="post" action="register.php">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="confirmPassword">Confirm Password:</label><br>
        <input type="password" id="confirmPassword" name="confirmPassword" required><br><br>

        <label for="firstname">First Name:</label><br>
        <input type="text" id="firstname" name="firstname" required><br><br>

        <label for="lastname">Last Name:</label><br>
        <input type="text" id="lastname" name="lastname" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email"><br><br>

        <input type="submit" value="Register">
    </form>
</body>
</html>
