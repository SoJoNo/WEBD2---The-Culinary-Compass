<?php
session_start();
require('connect.php');
// require('baseAuth.php');

// // Redirect if user is not logged in or not an admin
// if (!isset($_SESSION['user_id']) || $_SESSION['tier_id'] != 3) {
//     header("Location: login.php");
//     exit;
// }

// Handling form submission for updating an existing category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];
    $thumbnail = $_POST['thumbnail'];
    $description = $_POST['description'];

    try {
        // Update the category in the database
        $sql = "UPDATE categories SET category_name = ?, thumbnail = ?, description = ? WHERE category_id = ?";
        $statement = $db->prepare($sql);
        $statement->execute([$category_name, $thumbnail, $description, $category_id]);

        // Redirect to the list of categories after successful update
        header("Location: category.php?id=" . $_GET['id']);
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
    <title>Update Category</title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <h2>Update Category</h2>
    <?php
    // Fetch the category details based on the provided category_id
    if (isset($_GET['id'])) {
        $category_id = $_GET['id'];
        $sql = "SELECT * FROM categories WHERE category_id = ?";
        $statement = $db->prepare($sql);
        $statement->execute([$category_id]);
        $category = $statement->fetch(PDO::FETCH_ASSOC);
    ?>
    <form method="post">
        <input type="hidden" name="category_id" value="<?php echo $category_id; ?>">
        <label for="category_name">Category Name:</label><br>
        <input type="text" id="category_name" name="category_name" value="<?php echo $category['category_name']; ?>" required><br><br>

        <label for="thumbnail">Thumbnail:</label><br>
        <input type="text" id="thumbnail" name="thumbnail" value="<?php echo $category['thumbnail']; ?>"><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" rows="4"><?php echo $category['description']; ?></textarea><br><br>

        <input type="submit" value="Update Category">
    </form>
    <?php } ?>
</body>
</html>
