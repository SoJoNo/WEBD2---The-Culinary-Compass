<?php
session_start();
require('connect.php');
//Replace this baseAuth.php.
require('login.php');

// Prompt the user to ask if they really want to delete the recipe once they click the "delete" button

// Remember to check if user is logged in

// Remember to check if user is an admin (tier_id 3)

// Check if recipe ID is provided
if (!isset($_GET['id'])) {
    echo "Recipe ID not provided.";
    exit;
}

$recipe_id = $_GET['id'];

// Delete the recipe from the database
try {
    $sql = "DELETE FROM recipes WHERE recipe_id = ?";
    $statement = $db->prepare($sql);
    $statement->execute([$recipe_id]);
    echo "Recipe deleted successfully.";
    
    header("Location: index.php");
    exit;
} catch(PDOException $e) {
    echo "Error deleting recipe: " . $e->getMessage();
}
?>
