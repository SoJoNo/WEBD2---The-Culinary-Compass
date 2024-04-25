<?php
    if(isset($_POST["cancel"])) {
        header("Location: index.php");
        exit;
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
    <title>Logout?</title>
</head>
<body>
    <h2>Are you sure you want to logout?</h2>
    <form method="post" action="logout.php">
        <button type="submit" name="logout">Logout</button>
    </form>
    <form method="post">
        <button type="submit" name="cancel">Cancel</button>
    </form>
</body>
</html>