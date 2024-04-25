<?php
session_start();
require('connect.php');

use Gumlet\ImageResize;

$imageDim = 500;

// Function to resize and save image using Gumlet library
function resize_and_save_image($file, $target_file, $max_file_size) {
    $image = new ImageResize($file);
    $image->resizeToBestFit($imageDim, $imageDim);
    $image->save($target_file);
}

// Function to check if the uploaded file is an image
function file_is_an_image($temporary_path, $new_path) {
    $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
    $actual_mime_type        = getimagesize($temporary_path)['mime'];

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

// Function to fetch categories from the database
function getCategories($db) {
    $categories = array();
    $sql = "SELECT * FROM categories";
    $statement = $db->query($sql);
    $categories = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $categories;
}

// Function to fetch areas from the database
function getAreas($db) {
    $areas = array();
    $sql = "SELECT * FROM areas";
    $statement = $db->query($sql);
    $areas = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $areas;
}

// Handling form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input data
    $name = $_POST["name"];
    $instructions = $_POST["instructions"];
    $thumbnail = $_FILES["thumbnail"]["name"];
    $video = $_POST["video"];
    $ingredients = $_POST["ingredients"];
    $category_id = $_POST["category_id"];
    $area_id = $_POST["area_id"];

    // Define maximum file size in bytes (e.g., 1 MB = 1000000 bytes)
    $max_file_size = 1000000; // 1 MB
    if (file_is_an_image($_FILES["thumbnail"]["tmp_name"], $thumbnail)) {
        // Check if uploaded file exceeds maximum file size
        if ($_FILES["thumbnail"]["size"] > $max_file_size) {
            // Resize and save image
            resize_and_save_image($_FILES["thumbnail"]["tmp_name"], $thumbnail, $max_file_size);
        } else {
            // Move uploaded thumbnail file to a directory
            $target_dir = "thumbnails/";
            $target_file = $target_dir . basename($_FILES["thumbnail"]["name"]);
            move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file);
        }
    } else {
        echo "Error: Invalid file format. Please upload a valid image file.";
        exit; // Exit the script if the uploaded file is not an image
    }

    try {
        // Insert the recipe into the database using prepared statement
        $sql = "INSERT INTO recipes (category_id, area_id, name, instructions, thumbnail, video, ingredients)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $statement = $db->prepare($sql);

        // Don't forget to add the user_id once login is created.

        $statement->execute([$category_id, $area_id, $name, $instructions, $thumbnail, $video, $ingredients]);

        // Check if insertion was successful
        if ($statement->rowCount() > 0) {
            echo "Recipe added successfully.";
        } else {
            echo "Error adding recipe.";
        }
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
    <title>Add New Recipe</title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <h2>Add New Recipe</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="instructions">Instructions:</label><br>
        <textarea id="instructions" name="instructions" rows="4" required></textarea><br><br>

        <label for="thumbnail">Thumbnail:</label><br>
        <input type="file" id="thumbnail" name="thumbnail" accept="image/png"><br><br>

        <label for="video">Video:</label><br>
        <input type="text" id="video" name="video"><br><br>

        <label for="ingredients">Ingredients:</label><br>
        <textarea id="ingredients" name="ingredients" rows="4" required></textarea><br><br>

        <label for="category_id">Category:</label><br>
        <select id="category_id" name="category_id" required>
        <option value="">--- Select a category ---</option>
            <?php $categories = getCategories($db); ?>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="area_id">Area:</label><br>
        <select id="area_id" name="area_id" required>
        <option value="">--- Select an area ---</option>
            <?php $areas = getAreas($db); ?>
            <?php foreach ($areas as $area): ?>
                <option value="<?php echo $area['area_id']; ?>"><?php echo $area['area_name']; ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <input type="submit" value="Add Recipe">
    </form>
</body>
</html>
