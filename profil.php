<!DOCTYPE html>

    <?php
    // -------------------------- Vérifie tout ce qui est nécessaire -------------------------- //
    require 'sql.php'; // Inclut le fichier 'sql.php
    require 'utils.php'; // Inclut le fichier 'utils.php

    $connexion = connexion(); // Se connecte a la base de données

    checkCreds(); // Vérifie si l'utilisateur est connecté

    ?>

<html>
<head>
    <title>Rettiwt</title>
    <link rel="stylesheet" type="text/css" href="css/profil.css">
</head>
<body>

    <?php

        handleLike($connexion, $_SESSION['username']); // Gère les likes
        handleFollow($connexion, $_SESSION['username']); // Gère les follows

        // -------------------------- Affiche le profil de l'utilisateur -------------------------- //

        if (isset($_GET['profil_detail'])) { // Si on post sur cette page alors on veux voir le profil d'un autre utilisateur

            
            $username = $_GET['profil_detail'];  // On récupère le username de l'autre utilisateur
            displayProfil($connexion, $username); // On affiche le profil de l'autre utilisateur
            if ($username == $_SESSION['username']) { // Si l'autre utilisateur est l'utilisateur connecté
                echo "<a href='edit.php'>Modifier le profil</a> <br>"; // On affiche un lien pour donner l'option de pouvoir modifier le profil
                echo "<a href='stat.php?profil_detail=". urlencode($_SESSION['username']) ."'>Statistique</a> <br>";
                echo "<a href='home.php'> Fil rettiwt. </a>";
            }else {
                if (isFollowing($connexion, $_SESSION['username'], $username)){ // Si l'utilisateur connecté follow déjà l'autre utilisateur
                    echo "<a href='profil.php?follow=" . urlencode($username) . "&profil_detail=". urlencode($username) ."'>Désabonner</a>"; // On affiche un lien pour donner l'option de pouvoir unfollow l'autre utilisateur
                }else {// Sinon (si l'utilisateur connecté ne follow pas l'autre utilisateur) 
                    echo "<a href='profil.php?follow=" . urlencode($username) . "&profil_detail=". urlencode($username) ."'>S'abonner</a>"; // On affiche un lien pour donner l'option de pouvoir follow l'autre utilisateur
                }
                echo "<br>";
                echo "<a href='home.php'> Fil rettiwt. </a>";
            }
            displayPost($connexion, $username, NULL); // On affiche les posts de l'autre utilisateur

        }else { // Sinon on affiche le profil de l'utilisateur connecté

            displayProfil($connexion, $_SESSION['username']);  // On affiche le profil de l'utilisateur connecté
            echo "<a href='edit.php'>Modifier le profil</a> <br>";
            echo "<a href='stat.php?profil_detail=". urlencode($_SESSION['username']) ."'>Statistique</a> <br>";
            echo "<a href='home.php'> Fil rettiwt. </a>";
            displayPost($connexion, $_SESSION['username'], NULL); // On affiche les posts de l'utilisateur connecté
        }

    ?>

</body>
</html>