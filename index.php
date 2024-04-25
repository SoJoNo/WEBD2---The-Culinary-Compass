<?php
session_start();
require('connect.php');

$page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Current page number
$recipesPerPage = 5; // Number of recipes to display per page

// Search functionality
if (isset($_GET['search'])) {

    $search_query = $_GET['search'];
    $category_query = $_GET['category'];
    $area_query = $_GET['area'];

    try {
        // Search for recipes based on the keyword entered
        $search_statement = $db->prepare("SELECT recipe_id, name FROM recipes WHERE name LIKE ?");
        $search_statement->execute(["%$search_query%"]);
        $search_results = $search_statement->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    if (isset($_GET['category']) && $_GET['category'] !== 'all')
    {
        $search_query = $search_query . "&category=$category_query";
    }

    if (isset($_GET['area']) && $_GET['area'] !== 'all')
    {
        $search_query = $search_query . "&area=$area_query";
    }

    // Redirect to search results page with query parameter
    header("Location: search_results.php?search=$search_query");
    exit;
}

// Function to fetch all recipes from the database
function getPaginatedRecipes($db, $page, $recipesPerPage) {
    $offset = ($page - 1) * $recipesPerPage;
    $sql = "SELECT * FROM recipes LIMIT $offset, $recipesPerPage";
    $statement = $db->query($sql);
    $recipes = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $recipes;
}

// Function to fetch all categories from the database
function getCategories($db) {
    $categories = array();
    $sql = "SELECT * FROM categories";
    $statement = $db->query($sql);
    $categories = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $categories;
}

function getAreas($db) {
    $areas = array();
    $sql = "SELECT * FROM areas";
    $statement = $db->query($sql);
    $areas = $statement->fetchAll(PDO::FETCH_ASSOC);
    return $areas;
}

// Handle form submission for adding comments
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['comment'])) {
        $recipe_id = $_POST['recipe_id'];
        $name = $_POST['name'];
        $comment = $_POST['comment'];

        try {
            $sql = "INSERT INTO comments (recipe_id, name, comment) VALUES (?, ?, ?)";
            $statement = $db->prepare($sql);
            $statement->execute([$recipe_id, $name, $comment]);
        } catch(PDOException $e) {
            echo "Error adding comment: " . $e->getMessage();
        }
    }
}

$tier_id = isset($_SESSION['tier_id']) ? $_SESSION['tier_id'] : 0;

// Fetch all
$recipes = getPaginatedRecipes($db, $page, $recipesPerPage);
$categories = getCategories($db);
$areas = getAreas($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <?php include('navbar.php'); ?>
</head>
<body>
    <!-- Search form with category dropdown -->
    <form method="GET">
        <input type="text" name="search" placeholder="Search for recipes...">

        <!-- Categories filter dropdown -->
        <select name="category">
            <option value="all">All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Area filter dropdown -->
        <select name="area">
            <option value="all">All Areas</option>
            <?php foreach ($areas as $area): ?>
                <option value="<?php echo $area['area_id']; ?>"><?php echo $area['area_name']; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Search</button>
    </form>

    <h2>Menu of Available Pages</h2>
    <ul class="displays">
        <?php foreach ($recipes as $recipe): ?>
            <!-- Display recipe thumbnail and name -->
            <li>
                <?php if ($recipe['thumbnail'] != null): ?>
                    <img src="thumbnails/<?php echo $recipe['thumbnail']; ?>" alt="Picture of <?php echo $recipe['name']; ?>" width="100">
                <?php endif; ?>
                <a href="page.php?id=<?php echo $recipe['recipe_id']; ?>"><?php echo $recipe['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

        <!-- Pagination links -->
        <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo ($page - 1); ?>">Previous</a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= ceil(count($recipes) / $recipesPerPage); $i++): ?>
            <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>

        <?php if (count($recipes) == $recipesPerPage): ?>
            <a href="?page=<?php echo ($page + 1); ?>">Next</a>
        <?php endif; ?>
    </div>

    <h2>Categories</h2>
    <ul class = "displays">
        <?php foreach ($categories as $category): ?>
            <li><a href="category.php?id=<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
