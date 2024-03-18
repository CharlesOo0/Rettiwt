<!DOCTYPE html>

    <?php
    // -------------------------- Vérifie tout ce qui est nécessaire -------------------------- //
    require 'sql.php'; // Inclut le fichier 'sql.php
    require 'utils.php'; // Inclut le fichier 'utils.php

    $connexion = connexion(); // Se connecte a la base de données

    checkCreds(); // Vérifie si l'utilisateur est connecté

    ?>

<html>

<head>
    <title>Rettiwt</title>
    <link rel="stylesheet" type="text/css" href="css/home_style.css">
</head>

<body>

    <?php
        // -------------------------- Crée un post -------------------------- //
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['text'])){
            if (!isset($_SESSION['username'])) {
                $_SESSION['error'] = "Vous avez besoin d'être connecter pour poster un message.";
                header('Location: logout.php');
                exit();
            }

            $author = $_SESSION['username'];
            $title = htmlspecialchars($_POST['title']);
            $text = htmlspecialchars($_POST['text']);

            $sql = "SELECT * FROM profil WHERE username='$author'"; // Vérifie si l'utilisateur existe

            try {
                $result = mysqli_query($connexion, $sql);
            } catch (Exception $e) {
                $_SESSION['error_post'] = "Erreur lors de la création du post : " . mysqli_error($connexion);
                header('Location: home.php');
                exit();
            }

            if ($result->num_rows != 1) {
                $_SESSION['error'] = "Vous avez besoin d'être connecter pour poster un message.";
                header('Location: logout.php');
                exit();
            } 

            $result = mysqli_fetch_assoc($result);
            $id = $result['id'];


            // Crée la requête SQL
            $sql = "INSERT INTO post (author, title, text) VALUES ('$id', '$title', '$text')";

            try {
                mysqli_query($connexion, $sql);
                echo "<p> Post crée ! </p>";
            } catch (Exception $e) {
                echo "<p> Error: " . $sql . "<br>" . mysqli_error($connexion) . "</p>";
            }

        }

        // -------------------------- Ajoute un like -------------------------- //
        handleLike($connexion, $_SESSION['username']);

        // -------------------------- Gérer erreur en cas de post -------------------------- //
        if (isset($_SESSION['error_post'])) {
            echo "<p>" . $_SESSION['error_post'] . "</p>";
            unset($_SESSION['error_post']);
        }

        // -------------------------- Affiche les posts -------------------------- //
        echo "<div id='posts'>";
        displayPost($connexion, NULL);
        echo "</div>";
        ?>

    <div id="profil-band">

        <div id="profil">
            <div id="profil-detail">
                <?php
                // -------------------------- Affiche le profil de l'utilisateur -------------------------- //
                displayProfil($connexion, $_SESSION['username']);
                ?>
            </div>

            <div id="profil-link">
                <a href="profil.php">Profil</a> <br>
                <img src="img/bell.png" alt="Notification" width="64" height="50"> <br>
                <a href="logout.php">Déconnexion</a>
            </div>
        </div>

        <div id="post-form">Post a message : <br>
        <form method="POST" action="">
            <label for="text">Titre :</label> <br>
            <input type="text" name="title"  required><br>

            <label for="text">Texte :</label> <br>
            <input type="text" name="text" required><br>
            <input type="submit" value="Post">
        </form>
        </div>
    </div>

</body>

</html>