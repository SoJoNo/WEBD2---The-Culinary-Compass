<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
</head>
<body>
    <a href='index.php'>
        <img id="logo" src="Black And White Aesthetic Minimalist Modern Simple Typography Coconut Cosmetics Logo.png" alt="The Culinary Compass Logo" width="150">
    </a>
    <table class="navbar">
        <tr id="">
            <td> <a href='index.php'> Home </a> </td>
            <td> <a href='insert.php'> Add Recipe </a> </td>
            <td> <a href="addcategories.php">Add Categories</td>
            <td> <a href='usersindex.php'> Users </a> </td>
            <td><a href="list.php">List Recipes</a></td>
        </tr>
        <?php if (!isset($_SESSION['user_id'])): ?>
        <tr>
            <td> <a href='register.php'> Register </a> </td>
            <td> <a href='login.php'> Login </a> </td>
        </tr>
        <?php else: ?>
        <tr>
            <td> <a href="logoutConfirm.php"> Logout </a> </td>
        </tr>
        <?php endif; ?>
    </table>
    <br><br>
</body>
</html>