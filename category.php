<?php
session_start();
require('connect.php');

// Check if category ID is provided in the URL
if(isset($_GET['id'])) {
    $category_id = $_GET['id'];

    try {
        // Fetch category name
        $category_statement = $db->prepare("SELECT category_name FROM categories WHERE category_id = ?");
        $category_statement->execute([$category_id]);
        $category = $category_statement->fetchColumn();

        // Fetch recipes belonging to the selected category
        $recipes_statement = $db->prepare("SELECT recipe_id, name FROM recipes WHERE category_id = ?");
        $recipes_statement->execute([$category_id]);
        $recipes = $recipes_statement->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Category ID not provided.";
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
    <title><?php echo $category; ?></title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <h2><?php echo $category; ?></h2>
    <ul>
        <?php foreach ($recipes as $recipe): ?>
            <li><a href="page.php?id=<?php echo $recipe['recipe_id']; ?>"><?php echo $recipe['name']; ?></a></li>
        <?php endforeach; ?>
    </ul>

    <a href="updatecategories.php?id=<?php echo $_GET['id']; ?>">Update Category</a>
    <!-- <?php if ($tier_id == 3): ?> -->
    <!-- <?php endif; ?> -->
</body>
</html>
