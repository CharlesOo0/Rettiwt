<!DOCTYPE html>

    <?php
    // -------------------------- Vérifie tout ce qui est nécessaire -------------------------- //
    require 'sql.php'; // Inclut le fichier 'sql.php

    $connexion = connexion(); // Se connecte a la base de données
    ?>

<html>

<head>
    <title>Rettiwt</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
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
            echo "<div id='login-success'>";
            echo "<p> Login réussi redirection en cour ... </p>";
            echo "</div>";
            $_SESSION['username'] = $name;
            header("Location: home.php");
        } else {
            echo "<div id='login-error'>";
            echo "Mauvais pseudo ou mot de passe !";
            echo "</div>";
        }

    }

    if (isset($_SESSION['error'])) {
        echo "<div id='login-error'>";
        echo $_SESSION['error'];
        echo "</div>";
        unset($_SESSION['error']);
    }

    ?>

    <div id="login-form-container">
        <h1 id="login-form-title">Login</h1>

        <form id="login-form" method="POST" action="login.php">
            <label id="form-label-username" for="username">Pseudo :</label> <br>
            <input id="form-input" type="text" id="username" name="username" placeholder="Entrer votre pseudo" required><br><br>

            <label id="form-label-password" for="password">Mot de passe :</label> <br>
            <input id="form-input" type="password" id="password" name="password" placeholder="Entrer votre mot de passe" required><br><br>

            <input id="submit-button" type="submit" value="Login">
        </form> <br><br>
        <a id="redirection-lg" href="register.php">Vous n'avez pas de compte ?</a>
    </div>

</body>

</html>