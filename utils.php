<?php

/**
 * Vérifie si l'utilisateur est connecté
 * 
 * @return void
 */
function checkCreds($connexion) {
    if (!isset($_SESSION['username'])) { // Vérifie si l'utilisateur est connecté a travers le cookie qui devrait être set
        $_SESSION['error'] = "You need to be logged in to access this page."; // Stocke un message d'erreur
        header('Location: logout.php'); // Redirige vers la page de déconnexion
        exit();
    }

    if (isBanned($connexion, $_SESSION['username'])) { // Vérifie si l'utilisateur est banni
        $sql = "SELECT * FROM profil WHERE username='" . $_SESSION['username'] . "' AND isBanned=1"; // Crée la requête SQL pour vérifier si l'utilisateur est banni

        try { // Essaie de récupérer l'utilisateur
            $result = mysqli_query($connexion, $sql);
            if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
                $_SESSION['error'] = "You are banned."; // Stocke un message d'erreur
                header('Location: logout.php'); // Redirige vers la page de déconnexion
                exit();
            }
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            echo "<p> Erreur lors de la vérification du bannissement : " . mysqli_error($connexion) . "</p>";
        }

        $_SESSION['error'] = "You are banned until ".$result['ban_date']."."; // Stocke un message d'erreur
        header('Location: logout.php'); // Redirige vers la page de déconnexion
        exit();
    }
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

?>