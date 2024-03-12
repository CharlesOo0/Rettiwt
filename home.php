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
</head>

<body>
    <h1>Welcome to the home page</h1>

    <p href="profil.php">Profil</p>


    <?php
    // -------------------------- Affiche le profil de l'utilisateur -------------------------- //
    $username = $_SESSION['username'];

    // Crée la requête SQL pour récupérer le profil
    $sql = "SELECT * FROM profil WHERE username='$username'";

    $recuperationProfilFailed = false;
    try { 
        $resultProfil = mysqli_query($connexion, $sql);
    } catch (Exception $e) {
        echo "<p> Erreur lors de la récupération du profil : " . mysqli_error($connexion) . "</p>";
        $recuperationProfilFailed = true;
    }

    // Crée la requête SQL pour récupérer le nombre de followers
    $sql = " SELECT COUNT(follower_id) FROM followers WHERE following_id = (SELECT id FROM profil WHERE username = '$username') ";

    try {
        $resultFollower = mysqli_query($connexion, $sql);
    } catch (Exception $e) {
        echo "<p> Erreur lors de la récupération du nombre de followers : " . mysqli_error($connexion) . "</p>";
        $recuperationProfilFailed = true;
    }

    if (mysqli_num_rows($resultProfil) > 0 && !$recuperationProfilFailed) { // Vérifie si la requête a retourné des lignes et qu'elle n'a pas échoué
        // Affiche les données de l'utilisateur
        $rowProfil = mysqli_fetch_assoc($resultProfil);
        $rowFollower = mysqli_fetch_assoc($resultFollower);
        echo "<p>";
        echo "Username: " . $rowProfil["username"] . "<br>";
        echo "Email: " . $rowProfil["email"] . "<br>";
        echo "Followers: " . $rowFollower["COUNT(follower_id)"] . "<br>";
        echo "</p>";
    }
    ?>

    <div>Post a message : <br>
    <form method="POST" action="">
        <label for="text">Titre :</label>
        <input type="text" name="title"  required><br>

        <label for="text">Texte :</label>
        <input type="text" name="text" required><br>
        <input type="submit" value="Post">
    </form>
    </div>

    <p>Click <a href="logout.php">here</a> to logout.</p>

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
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id'])) {
            $id = $_POST['post_id'];
            $sql = "INSERT INTO likes (post_id, user_id) VALUES ('$id', (SELECT id FROM profil WHERE username = '$username'))";

            try {
                mysqli_query($connexion, $sql);
                echo "<p> Like ajouté ! </p>";
            } catch (Exception $e) {
                $error = mysqli_error($connexion);
                if (strpos($error, 'Duplicate entry') !== false) {
                    $sql = "DELETE FROM likes WHERE post_id='$id' AND user_id=(SELECT id FROM profil WHERE username = '$username')";
                    try {
                        mysqli_query($connexion, $sql);
                        echo "<p> Like retiré ! </p>";
                    } catch (Exception $e) {
                        echo "<p> Erreur lors de la suppression du like : " . mysqli_error($connexion) . "</p>";
                    }
                }else {
                    echo "<p> Erreur lors de l'ajout du like : " . mysqli_error($connexion) . "</p>";
                }
            }

        }

        // -------------------------- Gérer erreur en cas de post -------------------------- //
        if (isset($_SESSION['error_post'])) {
            echo "<p>" . $_SESSION['error_post'] . "</p>";
            unset($_SESSION['error_post']);
        }

        // -------------------------- Affiche les posts -------------------------- //
        // Crée la requête SQL
        $sql = "SELECT * FROM post ORDER BY date DESC";

        // Exécute la requête
        try {
            $result = mysqli_query($connexion, $sql);
            $requestFailed = false;
        } catch (Exception $e) {
            echo "<p> Erreur lors de la récupération des posts :  " . mysqli_error($connexion) . "</p>";
            $requestFailed = true;
        }

        // Vérifie si la requête a retourné des lignes
        if (mysqli_num_rows($result) > 0  && !$requestFailed) {
            // Affiche les données de chaque ligne
            while ($row = mysqli_fetch_assoc($result)) {

                echo "<p>";

                $sql = "SELECT * FROM profil WHERE id=" . $row['author'];
                try {
                    $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                    echo "Author: " . $profil['username'] . "<br>";
                } catch (Exception $e) {
                    echo "Author: Error when trying to get the name. <br>";
                }

                echo "Title: " . $row["title"] . "<br>";
                echo "Text: " . $row["text"] . "<br>";
                echo "Date: " . $row["date"] . "<br>";

                $sql = "SELECT COUNT(post_id) FROM likes WHERE post_id=" . $row['id'];
                try {
                    $likes = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                    echo "Likes: " . $likes['COUNT(post_id)'] . "<br>";
                } catch (Exception $e) {
                    echo "Likes: Error when trying to get the number of likes. <br>";
                }

                echo    "<form method='post' action=''>";
                echo            "<input type='hidden' name='post_id' value='" . $row["id"] . "'>";
                echo            "<input type='submit' value='Like'>";
                echo    "</form>";
                echo "</p> <br>";
            }
        } else {
            echo "Aucun résultat trouvé.";
        }

        ?>



</body>

</html>