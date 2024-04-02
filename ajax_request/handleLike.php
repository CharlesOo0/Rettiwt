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

echo $like;

?>