<?php

require '../sql.php'; // Inclut le fichier 'sql.php'
require '../utils.php'; // Inclut le fichier 'utils.php'
require '../utils_display.php'; // Inclut le fichier 'utils.php'

$connexion = connexion(); // Se connecte a la base de données

checkCreds($connexion); // Vérifie que l'utilisateur est connecté

if (isset($_POST['action'])) { // Vérifie si l'action est définie
    $action = $_POST['action']; // Récupère l'action
    $username = mysqli_real_escape_string($connexion, $_POST['user_id']); // Récupère le nom d'utilisateur

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

        $ban_date = $_POST['ban_date']; // Récupère la date de bannissement

        $ban_date = date('Y-m-d', strtotime(str_replace('-', '/', $ban_date))); // Formate la date de bannissement

        $sql = "UPDATE profil SET isBanned=1, ban_date='$ban_date' WHERE id='$username'"; // Crée la requête SQL pour bannir un utilisateur
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

        $sql = "SELECT * FROM post WHERE id='$id'"; // Crée la requête SQL pour récupérer un post
        try { // Essaie de récupérer un post
            $result = mysqli_query($connexion, $sql);
            if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
                $result = mysqli_fetch_assoc($result); // Récupère les données du post
            }
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la récupération du post";
            header('Location: ../home.php');
        }

        if ($result['isFlag'] == 1) { // Vérifie si le post est déjà signalé
            $sql = "UPDATE post SET isFlag=0 WHERE id='$id'"; // Crée la requête SQL pour signaler un post
        } else {
            $sql = "UPDATE post SET isFlag=1 WHERE id='$id'"; // Crée la requête SQL pour signaler un post
        }
        
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