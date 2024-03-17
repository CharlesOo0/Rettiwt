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
        $password_confirmation = htmlspecialchars($_POST['password_confirmation']);

        if (empty($name) || empty($email) || empty($password)) { // Vérifie si les champs sont vides
            $_SESSION['error'] = "Tous les champs sont requis.";
            header('Location: register.php');
            exit();
        }

        if (strlen($name) > 60) { // Vérifie si le username est trop long
            $_SESSION['error'] = "Le nom d'utilisateur est trop long 60 charactères maximum.";
            header('Location: register.php');
            exit();
        }

        if (strlen($email) > 256) { // Vérifie si l'email est trop long
            $_SESSION['error'] = "L'email est trop long 256 charactères maximum.";
            header('Location: register.php');
            exit();
        }

        if ($password != $password_confirmation) { // Vérifie si les mots de passe correspondent
            $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
            header('Location: register.php');
            exit();
        }

        if (strlen($password) > 256) { // Vérifie si le mot de passe est trop long
            $_SESSION['error'] = "Le mot de passe est trop long 256 charactères maximum.";
            header('Location: register.php');
            exit();
        }

        if (strlen($password) < 8) { // Vérifie si le mot de passe est trop court
            $_SESSION['error'] = "Le mot de passe est trop court 8 charactères minimum.";
            header('Location: register.php');
            exit();
        }

        // Prépare une requête SQL, empêche les injections SQL en utilisant des requêtes préparées
        $stmt = $connexion->prepare("INSERT INTO profil (username, email, password) VALUES (?, ?, ?)");

        // Lie les paramètres de la requête préparée aux variables
        $stmt->bind_param("sss", $name, $email, $password);

        // Exécute la requête
        try {
            $result = $stmt->execute();
        } catch (Exception $e) {
            $error = mysqli_error($connexion); // Récupère l'erreur

            if (strpos($error, 'Duplicate entry') !== false) { // Vérifie si l'erreur est un doublon

                if (strpos($error, "for key 'username'") !== false) { // Vérifie si le doublon est un username
                    $_SESSION['error'] = "Ce nom d'utilisateur est déjà pris."; // Stocke un message d'erreur
                } else if (strpos($error, "for key 'email'") !== false) { // Vérifie si le doublon est un email
                    $_SESSION['error'] = "Cet email est déjà pris."; // Stocke un message d'erreur
                }

            } else { // Si l'erreur n'est pas un doublon
                $_SESSION['error'] = "Erreur lors de la création du compte";
            }

            header('Location: register.php');
            exit();
        }
        

        if ($result) {
            echo "<div id='login-success'>";
            echo "Compte crée avec succés redirection en cour ...";
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

        <label id="form-label-password-conf" for="password">Confirmation mot de passe :</label>
        <input id="form-input" type="password" id="password" name="password_confirmation" placeholder="Réentrez votre mot de passe" required><br><br>

        <input id="submit-button" type="submit" value="Register">
    </form> <br><br>

    <a id="redirection-lg" href="login.php">Vous avez déjà un compte ?</a>

    </div>

</body>

</html>