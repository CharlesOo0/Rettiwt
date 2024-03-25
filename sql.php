<?php

/**
 * Fonction de connexion à la base de données
 * 
 * @return mysqli La connexion à la base de données
 */
function connexion() {
    if (session_status() == PHP_SESSION_NONE) { // Vérifie si la session est démarrée
        session_start();
    }
    $serveur = "localhost"; // Adresse du serveur MySQL (généralement localhost)
    $utilisateur = "root"; // Nom d'utilisateur MySQL
    $motdepasse = ""; // Mot de passe MySQL
    $basededonnees = "rettiwt"; // Nom de la base de données

    try { // Essaie de se connecter à la base de données
        $connexion = mysqli_connect($serveur, $utilisateur, $motdepasse, $basededonnees);
    }catch (Exception $e) { // Si ça échoue, stocke un message d'erreur
        $_SESSION['error'] = "La connexion à la base de données a échoué : " . mysqli_connect_error();
        header('Location: login.php'); // Redirige vers la page d'erreur
        exit();
    }

    // Vérifie la connextion
    if (!$connexion) { // Si la connexion a échoué, stocke un message d'erreur
        $_SESSION['error'] = "La connexion à la base de données a échoué : " . mysqli_connect_error();
        header('Location: login.php'); // Redirige vers la page d'erreur
        exit();
    }

    return $connexion;
}


?>