<!DOCTYPE html>

    <?php
    // -------------------------- Connexion à la base de données -------------------------- //
    session_start();
    $serveur = "localhost"; // Adresse du serveur MySQL (généralement localhost)
    $utilisateur = "root"; // Nom d'utilisateur MySQL
    $motdepasse = ""; // Mot de passe MySQL
    $basededonnees = "rettiwt"; // Nom de la base de données

    $connexion = mysqli_connect($serveur, $utilisateur, $motdepasse, $basededonnees);

    // Vérifie la connextion
    if (!$connexion) {
        die("La connexion à la base de données a échoué : " . mysqli_connect_error());
    }
    ?>

<html>

<head>
    <title>Rettiwt</title>
</head>

<body>
    <h1>Welcome to the home page</h1>

    <div>Post a message : <br>
    <form method="POST" action="">
        <label for="text">Titre :</label>
        <input type="text" name="title"  required><br>

        <label for="text">Texte :</label>
        <input type="text" name="text" required><br>
        <input type="submit" value="Submit">
    </form>
    </div>

    <p>Click <a href="logout.php">here</a> to logout.</p>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!isset($_SESSION['username'])) {
                $_SESSION['error'] = "You need to be logged in to post a message.";
                header('Location: logout.php');
            }

            $author = $_SESSION['username'];
            $title = htmlspecialchars($_POST['title']);
            $text = htmlspecialchars($_POST['text']);

            $sql = "Select * from profil where username='$author'";

            $result = mysqli_query($connexion, $sql);

            if ($result->num_rows != 1) {
                $_SESSION['error'] = "You need to be logged in to post a message.";
                header('Location: logout.php');
            } 

            $result = mysqli_fetch_assoc($result);
            $id = $result['id'];


            // Crée la requête SQL
            $sql = "INSERT INTO post (author, title, text) VALUES ('$id', '$title', '$text')";

            // Exécute la requête
            if (mysqli_query($connexion, $sql)) {
                echo "<p> New record created successfully </p>";
            } else {
                echo "<p> Error: " . $sql . "<br>" . mysqli_error($connexion) . "</p>";
            }
        }

        // Crée la requête SQL
        $sql = "SELECT * FROM post ORDER BY date DESC";

        // Exécute la requête
        $result = mysqli_query($connexion, $sql);

        // Vérifie si la requête a retourné des lignes
        if (mysqli_num_rows($result) > 0) {
            // Affiche les données de chaque ligne
            while ($row = mysqli_fetch_assoc($result)) {

                $sql = "SELECT * FROM profil WHERE id=" . $row['author'];
                $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                
                echo "<p>";
                echo "ID: " . $row["id"] . "<br>";
                echo "Author: " . $profil['username'] . "<br>";
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