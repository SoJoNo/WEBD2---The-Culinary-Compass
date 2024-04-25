<?php
session_start();
require('connect.php');

// Check if recipe ID is provided in the URL
if(isset($_GET['id'])) {
    $recipe_id = $_GET['id'];

    try {
        // Fetch recipe details from the database
        $statement = $db->prepare("SELECT name AS recipe_name, category_id, area_id, ingredients, instructions, thumbnail
                                    FROM recipes
                                    WHERE recipe_id = ?");
        $statement->execute([$recipe_id]);
        $recipe = $statement->fetch();

        // Fetch category name from the categories table
        $category_statement = $db->prepare("SELECT category_name FROM categories WHERE category_id = ?");
        $category_statement->execute([$recipe['category_id']]);
        $category = $category_statement->fetchColumn();

        // Fetch area name from the areas table
        $area_statement = $db->prepare("SELECT area_name FROM areas WHERE area_id = ?");
        $area_statement->execute([$recipe['area_id']]);
        $area = $area_statement->fetchColumn();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Recipe ID not provided.";
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
    <title><?php echo $recipe['recipe_name']; ?></title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <h2><?php echo $recipe['recipe_name']; ?></h2>
    <?php if($recipe['thumbnail']!= null): ?>
    <img src="thumbnails\<?php echo $recipe['thumbnail']; ?>" alt="Picture of <?php echo $recipe['recipe_name']; ?>" width="300">
    <?php endif; ?>
    <p><strong>Category:</strong> <?php echo $category; ?></p>
    <p><strong>Area:</strong> <?php echo $area; ?></p>
    <h3>Ingredients:</h3>
    <p><?php echo $recipe['ingredients']; ?></p>
    <h3>Instructions:</h3>
    <p><?php echo $recipe['instructions']; ?></p>

    <!-- Don't forget to make these only visible to tier_id 3 -->
    <a href="update.php?id=<?php echo $_GET['id']; ?>">Update Recipe</a>
    <br><br>
    <a href="delete.php?id=<?php echo $_GET['id']; ?>">Delete Recipe</a>
</body>
</html>
