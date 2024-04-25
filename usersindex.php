<?php
session_start();
require('connect.php');

// if ($_SESSION['tier_id'] != 3) {
//     header("Location: index.php"); // Redirect if not LH user
//     exit;
// }

// Fetch all users from the Users table
$sql = "SELECT * FROM users";
$statement = $db->query($sql);
$users = $statement->fetchAll(PDO::FETCH_ASSOC);

// Display users with options to edit, add, or delete
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <title>User Management</title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <button onclick="location.href='usercreate.php';">Add New User</button>
    <h2>User Management</h2>
    <table>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['user_id']; ?></td>
                <td><?php echo $user['user_name']; ?></td>
                <td><?php echo $user['first_name']; ?></td>
                <td><?php echo $user['last_name']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td>
                    <button onclick="location.href='useredit.php?id=<?php echo $user['user_id']; ?>';">Edit</button> 
                    <button onclick="location.href='userdelete.php?id=<?php echo $user['user_id']; ?>';">Delete</button>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
