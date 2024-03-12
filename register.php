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
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        // Crée la requête SQL
        $sql = "INSERT INTO profil (username, email, password) VALUES ('$name', '$email', '$password')";

        // Exécute la requête
        try {
            $result = mysqli_query($connexion, $sql);
        } catch (Exception $e) {
            $_SESSION['error'] = "Erreur lors de la création du compte : " . mysqli_error($connexion);
            header('Location: register.php');
            exit();
        }
        

        if ($result) {
            echo "<p> Account creation successful ... </p>";
            $_SESSION['username'] = $name;
            header("Location: home.php");
        } else {
            echo "<p> Error while trying to create ... </p>";
        }

    }

    if (isset($_SESSION['error'])) {
        echo "<p>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }

    ?>

    <h1>Crée un compte :</h1>

    <form method="POST" action="register.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form> <br><br>
    <a href="login.php">Vous avez déjà un compte ?</a>

</body>

</html>