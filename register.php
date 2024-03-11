<!DOCTYPE html>

<html>

<head>
    <title>Rettiwt</title>
</head>

<body>

    <?php

    session_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST") { // Rajoute un event listener a la page pour attendre un POST request

        $name = $email = "";
        $name = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);


        // -------------------------- Connexion à la base de données -------------------------- //
        $serveur = "localhost"; // Adresse du serveur MySQL (généralement localhost)
        $utilisateur = "root"; // Nom d'utilisateur MySQL
        $motdepasse = ""; // Mot de passe MySQL
        $basededonnees = "rettiwt"; // Nom de la base de données

        $connexion = mysqli_connect($serveur, $utilisateur, $motdepasse, $basededonnees);

        // Vérifie la connextion
        if (!$connexion) {
            die("La connexion à la base de données a échoué : " . mysqli_connect_error());
        }

        // Crée la requête SQL
        $sql = "INSERT INTO profil (username, email, password) VALUES ('$name', '$email', '$password')";

        // Exécute la requête
        $result = mysqli_query($connexion, $sql);

        if ($result) {
            echo "<p> Account creation successful ... </p>";
            $_SESSION['username'] = $name;
            header("Location: http://localhost/WE4A_projet/home.php");
        } else {
            echo "<p> Error while trying to create ... </p>";
        }

    }

    ?>

    <h1>Crée un compte :</h1>

    <form method="POST" action="http://localhost/WE4A_projet/register.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form> <br><br>
    <a href="http://localhost/WE4A_projet/login.php">Vous avez déjà un compte ?</a>

</body>

</html>