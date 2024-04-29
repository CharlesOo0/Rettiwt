<?php

require '../sql.php'; // Inclut le fichier 'sql.php'
require '../utils.php'; // Inclut le fichier 'utils.php'
require '../utils_display.php'; // Inclut le fichier 'utils.php'

$connexion = connexion(); // Se connecte a la base de données

checkCreds($connexion); // Vérifie que l'utilisateur est connecté

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($connexion, $_POST['username']); // Récupère le nom d'utilisateur
    $sub = mysqli_real_escape_string($connexion, $_POST['sub']); // Récupère le nom d'utilisateur
    $search = mysqli_real_escape_string($connexion, $_POST['search']); // Récupère le nom d'utilisateur
    $depth = mysqli_real_escape_string($connexion, $_POST['depth']); // Récupère le nom d'utilisateur

    echo displayPost($connexion, $username, $sub, $search, $depth);
}
?>
