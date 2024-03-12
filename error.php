<!DOCTYPE html>
<html>
<head>
    <title>Error Page</title>
</head>
<body>
    <h1>Error Page</h1>
    
    <?php
    // Display the error message
    session_start();
    if (isset($_SESSION['error'])) {
        echo "<p>Error: " . $_SESSION['error'] . "</p>";
    }
    ?>
    
</body>
</html>