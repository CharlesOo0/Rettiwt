<?php

if (session_status() == PHP_SESSION_NONE) { // Vérifie si la session est démarrée
    session_start(); // Démarre la session si elle ne l'est pas
}

foreach ($_SESSION as $key => $value) { // Nettoie tout les cookies stockés
    unset($_SESSION[$key]);
}

foreach ($_COOKIE as $key => $value) { // Nettoie toutes les données de session stockées
    unset($_COOKIE[$key]);
}

if (!isset($_SESSION['error'])) { // Vérifie si une erreur est stockée
    $_SESSION['error'] = "Vous avez été déconnecté. Pour continuer reconnectez-vous."; // Stocke un message d'erreur si il n'y en a pas
}

header('Location: login.php'); // Redirige vers la page d'accueil

?>