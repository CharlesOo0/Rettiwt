<?php

session_start();

foreach ($_SESSION as $key => $value) { // Nettoie tout les cookies stockés
    unset($_SESSION[$key]);
}

foreach ($_COOKIE as $key => $value) { // Nettoie toutes les données de session stockées
    unset($_COOKIE[$key]);
}

$_SESSION['error'] = "You have been logged out, to resume please login again.";

header('Location: login.php'); // Redirige vers la page d'accueil

?>