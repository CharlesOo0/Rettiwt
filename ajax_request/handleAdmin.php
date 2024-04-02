<?php

require '../sql.php'; // Inclut le fichier 'sql.php'
require '../utils.php'; // Inclut le fichier 'utils.php'
require '../utils_display.php'; // Inclut le fichier 'utils.php'

$connexion = connexion(); // Se connecte a la base de données

checkCreds($connexion); // Vérifie que l'utilisateur est connecté

if (isset($_POST['action'])) { // Vérifie si l'action est définie
    $action = $_POST['action']; // Récupère l'action

    if ($action =='delete') {

        $id = mysqli_real_escape_string($connexion, $_POST['post_id']); // Récupère l'id du post
        $sql = "DELETE FROM post WHERE id='$id'"; // Crée la requête SQL pour supprimer un post
        try { // Essaie de supprimer un post
            mysqli_query($connexion, $sql); 
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la tentative de suppression du post";
            header('Location: ../home.php');
        }

    }else if ($action == 'delete-admin' && isAdmin($connexion, $_SESSION['username'])) {

        $id = mysqli_real_escape_string($connexion, $_POST['post_id']); // Récupère l'id du post
        $sql = "DELETE FROM post WHERE id='$id'"; // Crée la requête SQL pour supprimer un post
        try { // Essaie de supprimer un post
            mysqli_query($connexion, $sql); 
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la tentative de suppression du post";
            header('Location: ../home.php');
        }

    } else if ($action == 'ban' && isAdmin($connexion, $_SESSION['username'])) {

        $username = mysqli_real_escape_string($connexion, $_POST['username']); // Récupère le nom d'utilisateur
        $sql = "UPDATE profil SET isBanned=1 WHERE username='$username'"; // Crée la requête SQL pour bannir un utilisateur
        try { // Essaie de bannir un utilisateur
            mysqli_query($connexion, $sql); 
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la tentative de bannissement de l'utilisateur";
            header('Location: ../home.php');
        }

    } else if ($action == 'warn' && isAdmin($connexion, $_SESSION['username'])) {

        // TODO : Envoie une notification à l'utilisateur

    } else if ($action == 'flag' && isAdmin($connexion, $_SESSION['username'])) {

        $id = mysqli_real_escape_string($connexion, $_POST['post_id']); // Récupère l'id du post
        $sql = "UPDATE post SET isFlag=1 WHERE id='$id'"; // Crée la requête SQL pour signaler un post
        try { // Essaie de signaler un post
            mysqli_query($connexion, $sql); 
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la tentative de signalement du post";
            header('Location: ../home.php');
        }

    }

} else {
    $_SESSION['error_post'] = " Erreur lors de la tentative de suppression du post";
    header('Location: ../home.php');
}

?>