<?php

require '../sql.php'; // Inclut le fichier 'sql.php'
require '../utils.php'; // Inclut le fichier 'utils.php'
require '../utils_display.php'; // Inclut le fichier 'utils.php'

$connexion = connexion(); // Se connecte a la base de données

checkCreds($connexion); // Vérifie que l'utilisateur est connecté

$adminId = getUserId($connexion); // Récupère l'id de l'utilisateur

if ($adminId == null) { // Vérifie si l'id est null
    $_SESSION['error_post'] = " Erreur lors de la récupération de l'id de l'utilisateur";
    header('Location: ../home.php');
}


if (isset($_POST['action'])) { // Vérifie si l'action est définie
    $action = $_POST['action']; // Récupère l'action
    $username = mysqli_real_escape_string($connexion, $_POST['user_id']); // Récupère le nom d'utilisateur

    if ($action =='delete') {

        // ----------------- Exécute la requête demander ----------------- //
        $id = mysqli_real_escape_string($connexion, $_POST['post_id']); // Récupère l'id du post
        $sql = "DELETE FROM post WHERE id='$id'"; // Crée la requête SQL pour supprimer un post
        try { // Essaie de supprimer un post
            mysqli_query($connexion, $sql); 
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la tentative de suppression du post";
            header('Location: ../home.php');
        }
        // ----------------- Exécute la requête demander ----------------- //

    }else if ($action == 'delete-admin' && isAdmin($connexion, $_SESSION['username'])) {

        // ----------------- Exécute la requête demander ----------------- //
        $id = mysqli_real_escape_string($connexion, $_POST['post_id']); // Récupère l'id du post
        $sql = "Update post SET isDeleted=1 WHERE id='$id'"; // Crée la requête SQL pour supprimer un post
        try { // Essaie de supprimer un post
            mysqli_query($connexion, $sql); 
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la tentative de suppression du post";
            header('Location: ../home.php');
        }
        // ----------------- Exécute la requête demander ----------------- //

        $reason = mysqli_real_escape_string($connexion, $_POST['reason']); // Récupère la raison de la suppression
        addAdminLog($connexion, $adminId, $username, 'delete', $reason); // Ajoute une entrée dans les logs


    } else if ($action == 'ban' && isAdmin($connexion, $_SESSION['username'])) {

        // ----------------- Exécute la requête demander ----------------- //
        $ban_date = $_POST['ban_date']; // Récupère la date de bannissement

        $ban_date = date('Y-m-d', strtotime(str_replace('-', '/', $ban_date))); // Formate la date de bannissement

        $sql = "UPDATE profil SET isBanned=1, ban_date='$ban_date' WHERE id='$username'"; // Crée la requête SQL pour bannir un utilisateur
        try { // Essaie de bannir un utilisateur
            mysqli_query($connexion, $sql); 
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la tentative de bannissement de l'utilisateur";
            header('Location: ../home.php');
        }
        // ----------------- Exécute la requête demander ----------------- //

        $reason = mysqli_real_escape_string($connexion, $_POST['reason']); // Récupère la raison du bannissement
        addAdminLog($connexion, $adminId, $username, 'ban', $reason); // Ajoute une entrée dans les logs

    } else if ($action == 'unban' && isAdmin($connexion, $_SESSION['username'])) {

        // ----------------- Exécute la requête demander ----------------- //
        $sql = "UPDATE profil SET isBanned=0, ban_date=NULL WHERE id='$username'"; // Crée la requête SQL pour débannir un utilisateur
        try { // Essaie de débannir un utilisateur
            mysqli_query($connexion, $sql); 
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la tentative de débannissement de l'utilisateur";
            header('Location: ../home.php');
        }
        // ----------------- Exécute la requête demander ----------------- //

        $reason = mysqli_real_escape_string($connexion, $_POST['reason']); // Récupère la raison du débannissement
        addAdminLog($connexion, $adminId, $username, 'unban', $reason); // Ajoute une entrée dans les logs

    }else if ($action == 'warn' && isAdmin($connexion, $_SESSION['username'])) {

        // TODO : Envoie une notification à l'utilisateur

        $reason = mysqli_real_escape_string($connexion, $_POST['reason']); // Récupère la raison du bannissement
        addAdminLog($connexion, $adminId, $username, 'warn', $reason); // Ajoute une entrée dans les logs

    } else if ($action == 'flag' && isAdmin($connexion, $_SESSION['username'])) {

        // ----------------- Exécute la requête demander ----------------- //
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
            $type = 'unflag'; // Définit le type de log
        } else {
            $sql = "UPDATE post SET isFlag=1 WHERE id='$id'"; // Crée la requête SQL pour signaler un post
            $type = 'flag'; // Définit le type de log
        }
        
        try { // Essaie de signaler un post
            mysqli_query($connexion, $sql); 
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $_SESSION['error_post'] = " Erreur lors de la tentative de signalement du post";
            header('Location: ../home.php');
        }
        // ----------------- Exécute la requête demander ----------------- //

        $reason = mysqli_real_escape_string($connexion, $_POST['reason']); // Récupère la raison du signalement
        addAdminLog($connexion, $adminId, $username, $type, $reason); // Ajoute une entrée dans les logs

    }

    $success = 1; // Affiche un message de succès

} else {
    $_SESSION['error_post'] = " Erreur lors de la tentative de suppression du post";
    header('Location: ../home.php');

    $success = 0; // Affiche un message d'erreur
}

echo json_encode($success); // Retourne le message de succès

?>