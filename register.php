<!DOCTYPE html>

    <?php
    // -------------------------- Vérifie tout ce qui est nécessaire -------------------------- //
    require 'sql.php'; // Inclut le fichier 'sql.php

    $connexion = connexion(); // Se connecte a la base de données
    ?>

<html>

<head>
    <title>Rettiwt</title>
    <link rel="stylesheet" type="text/css" href="css/register.css">
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
            echo "div id='login-success'>";
            echo "Account creation successful ...";
            echo "</div>";
            $_SESSION['username'] = $name;
            header("Location: home.php");
        } else {
            echo "<div id='login-error'>";
            echo "<p> Erreur en tentant de crée votre compte ... </p>";
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

    <div id="register-form-container">

    <h1 id="register-form-title">Register :</h1>
    <form id="register-form" method="POST" action="register.php">
        <label id="form-label-username" for="username">Pseudo :</label>
        <input id="form-input" type="text" id="username" name="username" placeholder="Entrer votre pseudo"  required><br><br>

        <label id="form-label-email" for="email">Email :</label>
        <input id="form-input" type="email" id="email" name="email" placeholder="Entrer votre email" required><br><br>

        <label id="form-label-password" for="password">Mot de passe:</label>
        <input id="form-input" type="password" id="password" name="password" placeholder="Entrer votre mot de passe" required><br><br>

        <input id="submit-button" type="submit" value="Register">
    </form> <br><br>

    <a id="redirection-lg" href="login.php">Vous avez déjà un compte ?</a>

    </div>

</body>

</html>