<?php

require '../sql.php'; // Inclut le fichier 'sql.php'
require '../utils.php'; // Inclut le fichier 'utils.php'
require '../utils_display.php'; // Inclut le fichier 'utils.php'

$connexion = connexion(); // Se connecte a la base de données

checkCreds($connexion); // Vérifie que l'utilisateur est connecté

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Vérifie si l'action est définie
    // Récupère l'id de l'utilisateur
    $result = getUserId($connexion);

    // Met comme lu toutes les notifications non lues de l'utilisateur
    $sql = "UPDATE `notifications` SET `read`='1' WHERE `user_id`='$result' AND `read`='0'";

    try { // Essaie de mettre comme lu toutes les notifications non lues de l'utilisateur
        mysqli_query($connexion, $sql); 
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        $_SESSION['error_post'] = " Erreur lors de la tentative de mise à jour des notifications";
        header('Location: ../home.php');
    }

    $success = true;
}else {
    $success = false;
}

echo $success;
?>