<?php

require '../sql.php'; // Inclut le fichier 'sql.php'
require '../utils.php'; // Inclut le fichier 'utils.php'
require '../utils_display.php'; // Inclut le fichier 'utils.php'

$connexion = connexion(); // Se connecte a la base de données

checkCreds($connexion); // Vérifie que l'utilisateur est connecté

$succes = false;
if (isset($_POST['notification_id'])) { // Vérifie si l'action est définie
    $notification_id = mysqli_real_escape_string($connexion, $_POST['notification_id']); // Récupère l'id de la notification

    // ----------------- Exécute la requête demander ----------------- //
    $sql = "DELETE FROM notifications WHERE id='$notification_id'"; // Crée la requête SQL pour supprimer une notification
    try { // Essaie de supprimer une notification
        mysqli_query($connexion, $sql); 
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        $_SESSION['error_post'] = " Erreur lors de la tentative de suppression de la notification";
        header('Location: ../home.php');
    }
    // ----------------- Exécute la requête demander ----------------- //

    $succes = true;
}else{
    $succes = false;
}
    
echo $succes;


?>