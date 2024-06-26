<?php

/**
 * Vérifie si l'utilisateur est connecté
 * 
 * @return void
 */
function checkCreds($connexion) {
    if (!isset($_SESSION['username'])) { // Vérifie si l'utilisateur est connecté a travers le cookie qui devrait être set
        $_SESSION['error'] = "Vous devez être connecter pour accéder a cette page."; // Stocke un message d'erreur
        echo "<meta http-equiv='refresh' content='0;url=login.php'>"; // Redirige vers la page de connexion
        exit();
    }

    if (isBanned($connexion, $_SESSION['username'])) { // Vérifie si l'utilisateur est banni
        $sql = "SELECT * FROM profil WHERE username='" . $_SESSION['username'] . "' AND isBanned=1"; // Crée la requête SQL pour vérifier si l'utilisateur est banni

        try { // Essaie de récupérer l'utilisateur
            $result = mysqli_query($connexion, $sql);
            if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
                $result = mysqli_fetch_assoc($result); // Récupère les données de l'utilisateur
                $_SESSION['error'] = "Vous êtes bannis jusqu'au " . $result['ban_date'] . "."; // Stocke un message d'erreur
                echo "<meta http-equiv='refresh' content='0;url=logout.php'>"; // Redirige vers la page de déconnexion
                exit();
            }
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            echo "<p> Erreur lors de la vérification du bannissement : " . mysqli_error($connexion) . "</p>";
        }

        $_SESSION['error'] = "Vous êtes bannis jusqu'au ".$result['ban_date']."."; // Stocke un message d'erreur
        echo "<meta http-equiv='refresh' content='0;url=logout.php'>"; // Redirige vers la page de déconnexion
        exit();
    }
}

/**
 * Récupère l'id de l'utilisateur connecter
 * 
 * @param connexion La connexion à la base de données
 * 
 * @return int
 */
function getUserId($connexion) {
    $sql = "SELECT id FROM profil WHERE username='" . $_SESSION['username'] . "'"; // Crée la requête SQL pour récupérer l'id de l'utilisateur

    try { // Essaie de récupérer l'id de l'utilisateur
        $result = mysqli_query($connexion, $sql);
        if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
            $result = mysqli_fetch_assoc($result); // Récupère les données de l'utilisateur
            return $result['id']; // Retourne l'id de l'utilisateur
        }
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la récupération de l'id de l'utilisateur</p>";
    }

    return null; // Si la requête n'a pas retourné de lignes, retourne null
}

/**
 * Récupère le profil utilisateur d'un utilisateur avec son nom
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur
 * 
 * @return array
 */
function getUserProfile($connexion, $username) {
    $sql = "SELECT * FROM profil WHERE username='$username'"; // Crée la requête SQL pour récupérer le profil de l'utilisateur

    try { // Essaie de récupérer le profil de l'utilisateur
        $result = mysqli_query($connexion, $sql);
        if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
            $result = mysqli_fetch_assoc($result); // Récupère les données de l'utilisateur
            return $result; // Retourne le profil de l'utilisateur
        }
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la récupération du profil de l'utilisateur</p>";
    }

    return null; // Si la requête n'a pas retourné de lignes, retourne null
}

/**
 * Vérifie si l'utilisateur est banni
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur
 * 
 * @return bool
 *
 */
function isBanned($connexion, $username) {
    $sql = "SELECT * FROM profil WHERE username='$username' AND isBanned=1"; // Crée la requête SQL pour vérifier si l'utilisateur est banni

    try { // Essaie de récupérer l'utilisateur
        $result = mysqli_query($connexion, $sql);
        if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
            return true; // Si oui, l'utilisateur est banni
        }
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la vérification du bannissement : " . mysqli_error($connexion) . "</p>";
    }

    return false; // Si la requête n'a pas retourné de lignes, l'utilisateur n'est pas banni
}

/**
 * Vérifie si l'utilisateur est un admin
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur
 * 
 * @return bool
 */
function isAdmin($connexion, $username) {
    $sql = "SELECT * FROM profil WHERE username='$username' AND isAdmin=1"; // Crée la requête SQL pour vérifier si l'utilisateur est un admin

    try { // Essaie de récupérer l'utilisateur
        $result = mysqli_query($connexion, $sql);
        if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
            return true; // Si oui, l'utilisateur est un admin
        }
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la vérification de l'admin : " . mysqli_error($connexion) . "</p>";
    }

    return false; // Si la requête n'a pas retourné de lignes, l'utilisateur n'est pas un admin
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

/**
 * Ajoute une log d'admin dans la base de données
 * 
 * @param connexion La connexion à la base de données
 * @param username_source L'id de l'utilisateur source de l'action
 * @param username_target L'id de l'utilisateur cible de l'action
 * @param action L'action effectuée par l'admin
 * @param reason La raison de l'action
 */
function addAdminLog($connexion, $username_source, $username_target, $action, $reason) {
    $sql = "INSERT INTO admin_logs (admin_id, target_user_id, action_type, reason) VALUES ('$username_source', '$username_target', '$action', '$reason')"; // Crée la requête SQL pour ajouter une log d'admin

    try { // Essaie d'ajouter une log d'admin
        mysqli_query($connexion, $sql);
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de l'ajout de la log d'admin.</p>";
    }

}

/**
 * Crée une notification
 * 
 * @param connexion La connexion à la base de données
 * @param user_notified L'id de l'utilisateur qui est notifié
 * @param user_notifying L'id de l'utilisateur qui notifie
 * @param type Le type de notification
 * @param post_id L'id du post concerné si il y en a un
 */
function createNotification($connexion, $user_notified, $user_notifying, $type, $post_id = null) {
    if ($post_id == null){ // Vérifie si l'id du post est null (si il n'y a pas de post concerné par la notification)
        $sql = "INSERT INTO notifications (user_id, created_by_user_id, type) VALUES ('$user_notified', '$user_notifying', '$type')"; // Crée la requête SQL pour ajouter une notification
    }else {// Si il y a un post concerné par la notification
        $sql = "INSERT INTO notifications (user_id, created_by_user_id, type, post_id) VALUES ('$user_notified', '$user_notifying', '$type', '$post_id')"; // Crée la requête SQL pour ajouter une notification
    }

    try { // Essaie d'ajouter une notification
        mysqli_query($connexion, $sql);
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de l'ajout de la notification.</p>". mysqli_error($connexion);
    }
}

?>