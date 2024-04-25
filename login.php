<?php
require('connect.php');

session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE user_name = :user_name";
    $statement = $db->prepare($sql);
    $statement ->bindValue(':user_name', $user_name);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);

    // Check if user exists and verify password
    if ($user && password_verify($password, $user['password'])) {
        // Authentication successful, set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['tier_id'] = $user['tier_id'];

        $login_error = "Login was successful.";

        // Redirect to home page or any other desired page
        header("Location: index.php");
        exit;
    }
    else {
        // Authentication failed, display error message
        $login_error = "Invalid username or password. Please try again.";
    }
}
else {
    // Authentication failed, display error message
    $echo = "User not found.";
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
    <title>Login</title>
    <?php include('navbar.php'); ?>
</head>
<body>
    <h2>Login</h2>
    <?php if(isset($login_error)): ?>
        <?php echo $login_error ?>
    <?php endif ?>
    <form method="post" action="login.php">
        <label for="username">Username:</label><br>
        <input type="text" id="user_name" name="user_name" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" name="submit" value="Login">
    </form>
</body>
</html>
