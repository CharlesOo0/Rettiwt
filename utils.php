<?php

/**
 * Vérifie si l'utilisateur est connecté
 * 
 * @return void
 */
function checkCreds() {
    if (!isset($_SESSION['username'])) { // Vérifie si l'utilisateur est connecté a travers le cookie qui devrait être set
        $_SESSION['error'] = "You need to be logged in to access this page."; // Stocke un message d'erreur
        header('Location: logout.php'); // Redirige vers la page de déconnexion
        exit();
    }
}

/**
 * Affiche le profil de l'utilisateur
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qu'on vise pour afficher le profil
 * 
 * @return void
 */
function displayProfil($connexion, $username) {

        // Crée la requête SQL pour récupérer le profil
        $sql = "SELECT * FROM profil WHERE username='$username'";

        $recuperationProfilFailed = false;
        try {  // Essaie de récupérer le profil
            $resultProfil = mysqli_query($connexion, $sql);
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            echo "<p> Erreur lors de la récupération du profil : " . mysqli_error($connexion) . "</p>";
            $recuperationProfilFailed = true;
        }

        // Crée la requête SQL pour récupérer le nombre de followers
        $sql = " SELECT COUNT(follower_id) FROM followers WHERE following_id = (SELECT id FROM profil WHERE username = '$username') ";

        try { // Essaie de récupérer le nombre de followers
            $resultFollower = mysqli_query($connexion, $sql);
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            echo "<p> Erreur lors de la récupération du nombre de followers : " . mysqli_error($connexion) . "</p>";
            $recuperationProfilFailed = true;
        }

        if (mysqli_num_rows($resultProfil) > 0 && !$recuperationProfilFailed) { // Vérifie si la requête a retourné des lignes et qu'elle n'a pas échoué
            // Affiche les données de l'utilisateur
            $rowProfil = mysqli_fetch_assoc($resultProfil);
            $rowFollower = mysqli_fetch_assoc($resultFollower);
            echo "<p>";
            echo "Username: " . $rowProfil["username"] . "<br>";
            echo "Followers: " . $rowFollower["COUNT(follower_id)"] . "<br>";
            echo "Bio: " . $rowProfil["bio"] . "<br>";
            echo "</p>";
        }
}


/**
 * Affiche les posts de l'utilisateur
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qu'on vise pour afficher les posts (NULL pour tous les utilisateurs)
 * 
 * @return void
 */
function displayPost($connexion, $username) {
    if ($username != NULL) { // Si on veut afficher les posts d'un utilisateur précis
        $sql = "SELECT * FROM profil WHERE username='$username'";  // Crée la requête SQL pour récupérer l'id de l'utilisateur

        $recuperationProfilFailed = false;
        try {  // Essaie de récupérer l'id de l'utilisateur
            $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            echo "<p> Erreur lors de la récupération du profil : " . mysqli_error($connexion) . "</p>";
            $recuperationProfilFailed = true;
        }

        if ($profil == NULL) { // Si l'utilisateur n'existe pas
            echo "Aucun résultat trouvé."; // Affiche un message d'erreur
            return;
        }

        $profilId = $profil['id']; // Récupère l'id de l'utilisateur
        $sql = "SELECT * FROM post WHERE author='$profilId' ORDER BY date DESC"; // Crée la requête SQL pour récupérer les posts de l'utilisateur

    }else { // Si on veut afficher les posts de tous les utilisateurs
        $sql = "SELECT * FROM post ORDER BY date DESC"; // Crée la requête SQL pour récupérer tous les posts
    }

    $recuperationPostFailed = false;
    try {  // Essaie de récupérer les posts
        $resultPost = mysqli_query($connexion, $sql);
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la récupération des posts : " . mysqli_error($connexion) . "</p>";
        $recuperationPostFailed = true;
    }

    if (mysqli_num_rows($resultPost) > 0  && !$recuperationPostFailed) { // Vérifie si la requête a retourné des lignes et qu'elle n'a pas échoué
        // Affiche les données de chaque ligne
        while ($row = mysqli_fetch_assoc($resultPost)) { // Pour chaque post

            echo "<p>";
            // Récupère le nom de l'auteur
            $sql = "SELECT * FROM profil WHERE id=" . $row['author'];
            try { // Essaie de récupérer le nom de l'auteur
                $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                echo "<a href='profil.php?profil_detail=" . urlencode($profil['username']) . "'>"; // Crée un lien vers le profil de l'auteur
                echo "Author: " . $profil['username'] . "<br>"; // Affiche le nom de l'auteur
                echo "</a>";
            } catch (Exception $e) { // Si ça échoue, affiche une erreur
                echo "Author: Error when trying to get the name. <br>";
            }

            // Affiche les informations du post
            echo "Title: " . $row["title"] . "<br>";
            echo "Text: " . $row["text"] . "<br>";
            echo "Date: " . $row["date"] . "<br>";

            // Récupère le nombre de likes
            $sql = "SELECT COUNT(post_id) FROM likes WHERE post_id=" . $row['id'];
            try { // Essaie de récupérer le nombre de likes
                $likes = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                echo "Likes: " . $likes['COUNT(post_id)'] . "<br>"; // Affiche le nombre de likes
            } catch (Exception $e) { // Si ça échoue, affiche une erreur
                echo "Likes: Error when trying to get the number of likes. <br>";
            }

            // Affiche le bouton pour liker
            echo    "<form method='post' action=''>";
            echo            "<input type='hidden' name='post_id' value='" . $row["id"] . "'>";
            echo            "<input type='submit' value='Like'>";
            echo    "</form>";
            echo "</p> <br>";
        }
    } else {
        echo "Aucun résultat trouvé.";
    }
}

/**
 * Vérifie si l'utilisateur follow déjà
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qui follow
 * @param followed Le nom d'utilisateur qui est follow
 * 
 * @return bool
 */
function isFollowing($connexion, $username, $followed) {
    $sql = "SELECT * FROM followers WHERE follower_id=(SELECT id FROM profil WHERE username = '$username') AND following_id=(SELECT id FROM profil WHERE username = '$followed')";

    try { // Essaie de récupérer les followers
        $result = mysqli_query($connexion, $sql);
        if (mysqli_num_rows($result) > 0) {
            return true;
        }
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la vérification du follow : " . mysqli_error($connexion) . "</p>";
    }

    return false;
}

/**
 * Gère les likes
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qui like
 * 
 * @return void
 */
function handleLike($connexion, $username) {

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
}

/**
 * Gère les followers
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qui follow
 * 
 * @return void
 */
function handleFollow($connexion, $username) {
    if (isset($_GET['follow'])) {
        $followed = $_GET['follow'];
        $sql = "INSERT INTO followers (follower_id, following_id) VALUES ((SELECT id FROM profil WHERE username = '$username'), (SELECT id FROM profil WHERE username = '$followed'))";

        try {
            mysqli_query($connexion, $sql);
            echo "<p> Follow ajouté ! </p>";
        } catch (Exception $e) {
            $error = mysqli_error($connexion);
            if (strpos($error, 'Duplicate entry') !== false) {
                $sql = "DELETE FROM followers WHERE follower_id=(SELECT id FROM profil WHERE username = '$username') AND following_id=(SELECT id FROM profil WHERE username = '$followed')";
                try {
                    mysqli_query($connexion, $sql);
                    echo "<p> Follow retiré ! </p>";
                } catch (Exception $e) {
                    echo "<p> Erreur lors de la suppression du follow : " . mysqli_error($connexion) . "</p>";
                }
            }else {
                echo "<p> Erreur lors de l'ajout du follow : " . mysqli_error($connexion) . "</p>";
            }
        }
    }
}
?>