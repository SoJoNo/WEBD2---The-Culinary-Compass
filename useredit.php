<?php
session_start();
require('connect.php');

// Check if the user is not LH user
if ($_SESSION['tier_id'] != 3) {
    header("Location: index.php"); // Redirect if not LH user
    exit;
}

// Check if user ID is provided in the URL
if (!isset($_GET['user_id'])) {
    echo "User ID is missing.";
    exit;
}

$user_id = $_GET['user_id'];

// Fetch user details based on user ID
$sql = "SELECT * FROM Users WHERE user_id = ?";
$statement = $db->prepare($sql);
$statement->execute([$user_id]);
$user = $statement->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

// Update user details if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Update user details in the database
    $sql = "UPDATE users SET user_name = ?, first_name = ?, last_name = ?, email = ? WHERE user_id = ?";
    $statement = $db->prepare($sql);
    $statement->execute([$username, $first_name, $last_name, $email, $user_id]);

    // Redirect to usersindex.php after updating
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
    <title>Edit User</title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <h2>Edit User</h2>
    <form method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" value="<?php echo $user['user_name']; ?>" required><br><br>

        <label for="first_name">First Name:</label><br>
        <input type="text" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required><br><br>

        <label for="last_name">Last Name:</label><br>
        <input type="text" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>"><br><br>

        <input type="submit" value="Update">
    </form>
</body>
</html>
