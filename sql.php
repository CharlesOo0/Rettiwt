<?php

function connexion() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $serveur = "localhost"; // Adresse du serveur MySQL (généralement localhost)
    $utilisateur = "root"; // Nom d'utilisateur MySQL
    $motdepasse = ""; // Mot de passe MySQL
    $basededonnees = "rettiwt"; // Nom de la base de données

    try {
        $connexion = mysqli_connect($serveur, $utilisateur, $motdepasse, $basededonnees);
    }catch (Exception $e) {
        $_SESSION['error'] = "La connexion à la base de données a échoué : " . mysqli_connect_error();
        header('Location: error.php');
        exit();
    }

    // Vérifie la connextion
    if (!$connexion) {
        $_SESSION['error'] = "La connexion à la base de données a échoué : " . mysqli_connect_error();
        header('Location: error.php');
        exit();
    }

    return $connexion;
}


?>