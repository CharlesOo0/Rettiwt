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
        if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
            return true; // Si oui, l'utilisateur follow déjà
        }
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la vérification du follow : " . mysqli_error($connexion) . "</p>";
    }

    return false; // Si la requête n'a pas retourné de lignes, l'utilisateur ne follow pas
}

?>