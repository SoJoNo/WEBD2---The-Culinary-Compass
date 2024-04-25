<?php
session_start();
require('connect.php');
require '\xampp\htdocs\a\php-image-resize-master\lib\ImageResize.php';
require '\xampp\htdocs\a\php-image-resize-master\lib\ImageResizeException.php';

use Gumlet\ImageResize;

if (!isset($_SESSION['user_id'])) {
    echo "You have to be logged in to access this page. Click <a href='login.php'>here</a> to login.";
    exit;
}

// Check if the user is an admin (tier_id 2)
if ($_SESSION['tier_id'] != 2) {
    echo "You do not have permission to access this page.";
    exit;
}

// Check if recipe ID is provided
if (!isset($_GET['id'])) {
    echo "Recipe ID not provided.";
    exit;
}

$recipe_id = $_GET['id'];

$imageDim = 500;

// Function to resize and save image using Gumlet library
function resize_and_save_image($file, $target_file, $max_file_size) {
    $image = new ImageResize($file);
    $image->resizeToBestFit($imageDim, $imageDim);
    $image->save($target_file);
}

// Function to check if the uploaded file is an image
function file_is_an_image($temporary_path, $new_path) {
    // Check if the temporary path and new path are not empty
    if (!empty($temporary_path) && !empty($new_path)) {
        $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
        $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

        $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
        $actual_mime_type        = getimagesize($temporary_path)['mime'];

        $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
        $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);

        return $file_extension_is_valid && $mime_type_is_valid;
    } else {
        // Return false if either path is empty
        return false;
    }
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

// Fetch recipe details from the database
try {
    $sql = "SELECT * FROM recipes WHERE recipe_id = ?";
    $statement = $db->prepare($sql);
    $statement->execute([$recipe_id]);
    $recipe = $statement->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error fetching recipe details: " . $e->getMessage();
}

// Check if the form is submitted for updating the recipe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve updated recipe details from the form
    $name = $_POST["name"];
    $instructions = $_POST["instructions"];
    $thumbnail = isset($_FILES["thumbnail"]["name"]) ? $_FILES["thumbnail"]["name"] : null;
    $video = $_POST["video"];
    $ingredients = $_POST["ingredients"];
    $category_id = $_POST["category_id"];
    $area_id = $_POST["area_id"];

    // Check if the "Delete Thumbnail" checkbox is checked
    if (isset($_POST['delete_thumbnail'])) {
        // Remove the thumbnail from the file system and set thumbnail value to null in the database
        $null = '';
        unlink('thumbnails/' . $recipe['thumbnail']); // Delete the file from the file system
        $thumbnail = null; // Set thumbnail to null in the database
        $sql = "UPDATE recipes SET thumbnail = ? WHERE recipe_id = ?";
        $statement = $db->prepare($sql);
        $statement->execute([$null, $recipe_id]);
    }

    // Define maximum file size in bytes (e.g., 1 MB = 1000000 bytes)
    $max_file_size = 1000000; // 1 MB

    // Check if thumbnail is uploaded
    if ($thumbnail !== null) {
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
    }

    // Update the recipe in the database
    $sql = "UPDATE recipes SET category_id = ?, area_id = ?, name = ?, instructions = ?, video = ?, ingredients = ?";
    $params = [$category_id, $area_id, $name, $instructions, $video, $ingredients];

    // Append thumbnail parameter if not null
    if ($thumbnail !== null) {
        $sql .= ", thumbnail = ?";
        $params[] = $thumbnail;
    }

    $sql .= " WHERE recipe_id = ?";
    $params[] = $recipe_id;

    $statement = $db->prepare($sql);
    $statement->execute($params);

    // Redirect to the page displaying the updated recipe
    header("Location: page.php?id=$recipe_id");
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
    <title>Update Recipe</title>
</head>
<body>
    <h2>Update Recipe</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="recipe_id" value="<?php echo $recipe_id; ?>">
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" value="<?php echo $recipe['name']; ?>" required><br><br>

        <label for="instructions">Instructions:</label><br>
        <textarea id="instructions" name="instructions" rows="4" required><?php echo $recipe['instructions']; ?></textarea><br><br>

        <label for="thumbnail">Thumbnail:</label><br>
        <input type="file" id="thumbnail" name="thumbnail" accept="image/png" value="<?php echo $recipe['thumbnail']; ?>"><br><br>

        <!-- Checkbox to delete thumbnail -->
        <input type="checkbox" id="delete_thumbnail" name="delete_thumbnail">
        <label for="delete_thumbnail">Delete Thumbnail</label><br><br>

        <label for="video">Video:</label><br>
        <input type="text" id="video" name="video" value="<?php echo $recipe['video']; ?>"><br><br>

        <label for="ingredients">Ingredients:</label><br>
        <textarea id="ingredients" name="ingredients" rows="4" required><?php echo $recipe['ingredients']; ?></textarea><br><br>

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

        <input type="submit" value="Update Recipe">
    </form>
</body>
</html>
