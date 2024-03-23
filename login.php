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

        // Crée la requête SQL pour vérifier si le profil existe
        $stmt = $connexion->prepare("SELECT * FROM profil WHERE username=? AND password=?");
        $stmt->bind_param("ss", $name, $password);

        // Exécute la requête
        try {
            $result = $stmt->execute();
        } catch (Exception $e) { // Gère les erreurs
            $_SESSION['error'] = "Erreur lors de la connexion : " . mysqli_error($connexion);
            header('Location: login.php');
            exit();
        }

        $result = $stmt->get_result();

        if ($result->num_rows == 1) { // Si le profil existe
            echo "<div id='login-success'>"; // Affiche un message de succès
            echo "<p> Login réussi redirection en cour ... </p>";
            echo "</div>";
            $_SESSION['username'] = $name;  // Stocke le nom d'utilisateur dans la session
            header("Location: home.php");  // Redirige l'utilisateur vers la page d'accueil
        } else {  // Si le profil n'existe pas
            echo "<div id='login-error'>"; // Affiche un message d'erreur
            echo "Mauvais pseudo ou mot de passe !";
            echo "</div>";
        }

    }

    if (isset($_SESSION['error'])) { // Si une erreur est stockée dans la session
        echo "<div id='login-error'>"; // Affiche le message d'erreur
        echo $_SESSION['error']; 
        echo "</div>";
        unset($_SESSION['error']); // Supprime l'erreur de la session
    }

    ?>

    <div id="login-form-container"> <!-- Formulaire de connexion -->
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