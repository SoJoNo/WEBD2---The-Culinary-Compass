<?php
session_start();
require('connect.php');

// Define the number of results per page
$results_per_page = 5;

if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $category_filter = isset($_GET['category']) && $_GET['category'] !== 'all' ? $_GET['category'] : null;
    $area_filter = isset($_GET['area']) && $_GET['area'] !== 'all' ? $_GET['area'] : null;

    try {
        // Prepare the base SQL query
        $sql = "SELECT recipe_id, name FROM recipes WHERE name LIKE ?";
        $params = ["%$search_query%"];

        // Add category filter to the SQL query if provided
        if ($category_filter !== null) {
            $sql .= " AND category_id = ?";
            $params[] = $category_filter;
        }

        // Add area filter to the SQL query if provided
        if ($area_filter !== null) {
            $sql .= " AND area_id = ?";
            $params[] = $area_filter;
        }

        // Execute the prepared statement
        $search_statement = $db->prepare($sql);
        $search_statement->execute($params);
        $total_results = $search_statement->rowCount();

        // Calculate the total number of pages
        $total_pages = ceil($total_results / $results_per_page);

        // Determine the current page number
        $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

        // Calculate the offset for fetching results
        $offset = ($current_page - 1) * $results_per_page;

        // Modify the SQL query to include pagination
        $sql .= " LIMIT $results_per_page OFFSET $offset";

        // Execute the modified query
        $search_statement = $db->prepare($sql);
        $search_statement->execute($params);
        $search_results = $search_statement->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Search Results</title>
    <?php include('navbar.php'); ?>
</head>
<body>
    <h2>Search Results</h2>

    <?php if(isset($search_results)): ?>
        <ul>
            <?php foreach ($search_results as $result): ?>
                <li><a href="page.php?id=<?php echo $result['recipe_id']; ?>"><?php echo $result['name']; ?></a></li>
            <?php endforeach; ?>
        </ul>

        <?php if ($total_pages > 1): ?>
            <div>
                <?php if ($current_page > 1): ?>
                    <a href="?search=<?php echo $search_query; ?>&category=<?php echo $category_filter ?? 'all'; ?>&area=<?php echo $area_filter ?? 'all'; ?>&page=<?php echo $current_page - 1; ?>">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?search=<?php echo $search_query; ?>&category=<?php echo $category_filter ?? 'all'; ?>&area=<?php echo $area_filter ?? 'all'; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?search=<?php echo $search_query; ?>&category=<?php echo $category_filter ?? 'all'; ?>&area=<?php echo $area_filter ?? 'all'; ?>&page=<?php echo $current_page + 1; ?>">Next</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
