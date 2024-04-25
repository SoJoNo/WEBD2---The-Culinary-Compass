<?php
session_start();
require('connect.php');

// Remembere to check if user is logged in


// Function to fetch sorted list of recipes from the database
function getSortedRecipes($db, $sort_by) {
    $sql = "SELECT * FROM recipes ORDER BY $sort_by";
    $statement = $db->query($sql);
    $recipes = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $recipes;
}

// Default sort column
$default_sort_column = "name";

// Get sort column from query parameter if provided
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : $default_sort_column;

// Validate sort column to prevent SQL injection
$allowed_sort_columns = array("name", "date_created", "date_updated");
if (!in_array($sort_column, $allowed_sort_columns)) {
    $sort_column = $default_sort_column;
}

// Fetch sorted list of recipes
$recipes = getSortedRecipes($db, $sort_column);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <title>List of Recipes</title>
    <?php include('navbar.php'); ?>
</head>
<body>
    <h2>List of Recipes</h2>

    <!-- Sorting indication -->
    <p>Sorted by: <?php echo ucfirst($sort_column); ?></p>

    <!-- Table to display list of recipes -->
    <table border="1">
        <thead>
            <tr>
                <th><a href="?sort=name">Title</a></th>
                <th><a href="?sort=date_created">Date Created</a></th>
                <th><a href="?sort=date_updated">Date Updated</a></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recipes as $recipe): ?>
                <tr>
                    <td><a href="page.php?id=<?php echo $recipe['recipe_id'] ?>"><?php echo $recipe['name']; ?></a></td>
                    <td><?php echo $recipe['date_created']; ?></td>
                    <td><?php echo $recipe['date_updated']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
