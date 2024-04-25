<?php
session_start();
require('connect.php');
//require('baseAuth.php');

// Remember to redirect if user is not logged in or not an admin once login is fixed

// Handling form submission for adding a new category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = $_POST['category_name'];
    $thumbnail = $_POST['thumbnail'];
    $description = $_POST['description'];

    try {
        // Insert the new category into the database
        $sql = "INSERT INTO categories (category_name, thumbnail, description) VALUES (?, ?, ?)";
        $statement = $db->prepare($sql);
        $statement->execute([$category_name, $thumbnail, $description]);

        // Redirect to the list of categories after successful addition
        header("Location: index.php");
        exit;
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
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
    <title>Add Category</title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <h2>Add New Category</h2>
    <form method="post">
        <label for="category_name">Category Name:</label><br>
        <input type="text" id="category_name" name="category_name" required><br><br>

        <label for="thumbnail">Thumbnail:</label><br>
        <input type="text" id="thumbnail" name="thumbnail"><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4"></textarea><br><br>

        <input type="submit" value="Add Category">
    </form>
</body>
</html>
