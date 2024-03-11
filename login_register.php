<!DOCTYPE html>

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

        // SQL query to select data
        $sql = "SELECT * FROM profil WHERE username='$name' AND password='$password'";

        // Execute the query
        $result = mysqli_query($connexion, $sql);

        if ($result->num_rows == 1) {
            echo "<p> Login succesful redirecting ... </p>";
            header("Location: http://localhost/WE4A_projet/home.php");
        } else {
            echo "<p> Wrong password or email </p>";
        }

    }





    ?>

    <h1>Login page :</h1>

    <form method="POST" action="http://localhost/WE4A_projet/login_register.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
</body>

</html>