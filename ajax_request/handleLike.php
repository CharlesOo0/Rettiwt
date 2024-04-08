<?php

require '../sql.php'; // Inclut le fichier 'sql.php'
require '../utils.php'; // Inclut le fichier 'utils.php'
require '../utils_display.php'; // Inclut le fichier 'utils.php'

$connexion = connexion(); // Se connecte a la base de données

checkCreds($connexion); // Vérifie que l'utilisateur est connecté

$id = mysqli_real_escape_string($connexion, $_POST['post_id']); // Récupère l'id du post

$username = $_SESSION['username']; // Récupère le nom d'utilisateur

$sql = "INSERT INTO likes (post_id, user_id) VALUES ('$id', (SELECT id FROM profil WHERE username = '$username'))"; // Crée la requête SQL pour ajouter un like

$like = true;
try { // Essaie d'ajouter un like
    mysqli_query($connexion, $sql); 
} catch (Exception $e) { // Si ça échoue, affiche une erreur
    $error = mysqli_error($connexion); // Récupère l'erreur
    if (strpos($error, 'Duplicate entry') !== false) { // Si l'erreur est un doublon
        $sql = "DELETE FROM likes WHERE post_id='$id' AND user_id=(SELECT id FROM profil WHERE username = '$username')"; // Crée la requête SQL pour retirer le like
        try { // Essaie de retirer le like
            mysqli_query($connexion, $sql);  
            $like = false;
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la tentative de like / dislike";
            Location: header('Location: ../home.php');
        }
    }
}

$liker_id = getUserId($connexion); // Récupère l'id de l'utilisateur qui a liké

// Récupère l'id de l'auteur du post
$sql = "SELECT author FROM post WHERE id='$id'";

try { // Essaie de récupérer l'auteur du post
    $result = mysqli_query($connexion, $sql);
    $row = mysqli_fetch_assoc($result);
    $author_id = $row['author'];
} catch (Exception $e) { // Si ça échoue, affiche une erreur
    $_SESSION['error_post'] = " Erreur lors de la récupération de l'auteur du post";
    header('Location: ../home.php');
}
if ($author_id != $liker_id) { // Si l'auteur du post n'est pas l'utilisateur qui a liké
    if ($like) {
        createNotification($connexion, $author_id, $liker_id, 'like', $id); // Crée une notification
    }
}

echo $like;

?>