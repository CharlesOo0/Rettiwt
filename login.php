<!DOCTYPE html>

    <?php
    // -------------------------- Vérifie tout ce qui est nécessaire -------------------------- //
    require 'sql.php'; // Inclut le fichier 'sql.php

    $connexion = connexion(); // Se connecte a la base de données
    ?>

<html>

<head>
    <title>Rettiwt</title>
</head>

<body>

    <?php

    if ($_SERVER["REQUEST_METHOD"] == "POST") { // Rajoute un event listener a la page pour attendre un POST request

        $name = $email = "";
        $name = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        // Crée la requête SQL
        $sql = "SELECT * FROM profil WHERE username='$name' AND password='$password'";

        // Exécute la requête
        try {
            $result = mysqli_query($connexion, $sql);
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de la connexion : " . mysqli_error($connexion);
            header('Location: login.php');
            exit();
        }

        if ($result->num_rows == 1) {
            echo "<p> Login succesful redirecting ... </p>";
            $_SESSION['username'] = $name;
            header("Location: home.php");
        } else {
            echo "<p> Wrong username or password </p>";
        }

    }

    if (isset($_SESSION['error'])) {
        echo "<p>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }

    ?>

    <h1>Se connecter :</h1>

    <form method="POST" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form> <br><br>
    <a href="register.php">Vous n'avez pas de compte ?</a>

</body>

</html>