<!DOCTYPE html>

<html>

<head>
    <title>Rettiwt</title>
</head>

<body>
    <h1>Welcome to the home page</h1>
    <p>Click <a href="logout.php">here</a> to logout.</p>

        <?php
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
        $sql = "SELECT * FROM post ORDER BY date DESC";

        // Exécute la requête
        $result = mysqli_query($connexion, $sql);

        // Vérifie si la requête a retourné des lignes
        if (mysqli_num_rows($result) > 0) {
            // Affiche les données de chaque ligne
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<p>";
                echo "ID: " . $row["id"] . "<br>";
                echo "Author: " . $row["author"] . "<br>";
                echo "Title: " . $row["title"] . "<br>";
                echo "Text: " . $row["text"] . "<br>";
                echo "Date: " . $row["date"] . "<br><br>";
                echo "</p>";
            }
        } else {
            echo "Aucun résultat trouvé.";
        }

        ?>


</body>

</html>