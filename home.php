<!DOCTYPE html>

    <?php
    // -------------------------- Vérifie tout ce qui est nécessaire -------------------------- //
    require 'sql.php'; // Inclut le fichier 'sql.php
    require 'utils_display.php'; // Inclut le fichier 'utils.php
    require 'utils_handle.php'; // Inclut le fichier 'utils_handle.php

    $connexion = connexion(); // Se connecte a la base de données

    checkCreds(); // Vérifie si l'utilisateur est connecté

    ?>

<html>

<head>
    <title>Rettiwt</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <link rel="stylesheet" type="text/css" href="css/popup.css">
    
</head>

<body>

    <!-- Les popups -->

        <!-- Popup de post -->
    <div class="post-form-container">
        <div class="post-form">
            <button id="close-post-button"><img src="img/quit.png" alt="close" width='20px' height='auto'></button>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="posting">
                <textarea name="text" placeholder="Bonjour tout le monde !"></textarea> <br>
                <div id="file-input-post">
                    <label for="images">Ajouter des images (3 maximum) </label><input type="file" name="images[]" multiple> <br>
                </div>
                <input type="submit" value="Post">
            </form>
        </div>
    </div>

        <!-- Popup de notification -->

    <div class="notification-container">
        <div class="notification">
            <button id="close-notification-button"><img src="img/quit.png" alt="close" width='20px' height='auto'></button>
            <h3>Notifications</h3>
            <div class="notification-content">
                <?php // TODO: Rajouter les notifications ?>
                <p>Vous n'avez pas de notifications.</p>
            </div>
        </div>
    </div>
    <!-- Les popups -->

    <div class="container-fluid">

        <div class="row no-gutters">

            <div id="left-band" class="col-md-3 d-none d-md-block"> <!-- Bandeau de gauche avec les liens -->

                    <div id="profil-link" class="container-fluid">
                        <a href="home.php?profil_detail=<?php echo urlencode($_SESSION['username']); ?>" class="left-band-img profil-link-i">
                            <img src="img/profil.png" alt="Profil"><span class="pl-span"> Profil</span>
                        </a> <br>
                        
                        <button id="show-search-button" class="left-band-img profil-link-i"><img src="img/search.png" alt="Rechercher"><span class="pl-span"> Recherche</span></button><br>

                        <a href="home.php" class="left-band-img profil-link-i">
                            <img src="img/home.png" alt="Actualités"><span class="pl-span"> Actualités</span>
                        </a> <br>
                        <a href="?forYou=true" class="left-band-img profil-link-i">
                            <img src="img/favorite.png" alt="Pour vous"><span class="pl-span"> Pour vous</span>
                        </a> <br>

                        <button id="show-notification-button" class="left-band-img profil-link-i"><img src="img/notification.png" alt="Notification"><span class="pl-span"> Notifications</span></button><br>
                        
                        <a href="home.php"  class="left-band-img profil-link-i">
                            <img src="img/stat.png" alt="Statistique"><span class="pl-span"> Statistique</span>
                        </a><br>

                        <a href="edit.php"  class="left-band-img profil-link-i">
                            <img src="img/edit.png" alt="Modifier"><span class="pl-span"> Modifier</span>
                        </a><br>

                        <a href="home.php"  class="left-band-img profil-link-i">
                            <img src="img/admin.png" alt="Admin"><span class="pl-span"> Admin</span>
                        </a><br>

                        <a href="logout.php"  class="left-band-img profil-link-i">
                            <img src="img/disconnect.png" alt="Deconnexion"><span class="pl-span"> Déconnexion</span>
                        </a>
                    </div>

            </div>

            <?php
                // -------------------------- Crée un post -------------------------- //
                handlePost($connexion);
            ?>

            <?php

                // -------------------------- Ajoute/enleve un like -------------------------- //
                handleLike($connexion, $_SESSION['username']);

                // -------------------------- Ajoute/enleve un follow -------------------------- //
                handleFollow($connexion, $_SESSION['username']);

            ?>

            <?php
                // -------------------------- Affiche les posts -------------------------- //
                echo "<div id='posts' class='col-md-5 col-12'>";
                if (isset($_GET['displayFollower']) && isset($_GET['username']) && $_GET['username'] != '') { // Si on veut afficher les abonnés
                    echo "<h4>Abonnés</h4>";
                    displayCommentForm($connexion, $_SESSION['username']);
                    displayFollow($connexion, $_GET['username'], 0);
                } else if (isset($_GET['displayFollowing']) && isset($_GET['username']) && $_GET['username'] != ''){ // Si on veut afficher les suivis
                    echo "<h4>Suivis</h4>";
                    displayCommentForm($connexion, $_SESSION['username']);
                    displayFollow($connexion, $_GET['username'], 1);
                }else if (isset($_GET['forYou'])) { // Si on veut afficher les posts pour l'utilisateur
                    echo "<h4>Pour vous</h4>";
                    displayCommentForm($connexion, $_SESSION['username']);
                    displayPost($connexion, $_SESSION['username'], 1); 
                }else if(isset($_GET['profil_detail'])) {
                    echo "<h4>Profil</h4>";
                    displayCommentForm($connexion, $_SESSION['username']);
                    $username = $_GET['profil_detail'];  // On récupère le username de l'autre utilisateur
                    displayProfil($connexion, $username); // On affiche le profil de l'autre utilisateur
                    echo "<div class='style-display-profil'>Post du profil</div>";
                    displayPost($connexion, $username, NULL); // On affiche les posts de l'autre utilisateur
                }else { // Sinon on affiche les posts normaux
                    echo "<h4>Actualités</h4>";
                    displayCommentForm($connexion, $_SESSION['username']);
                    displayPost($connexion, NULL, NULL);
                }
                echo "</div>";
            ?>

            <div id="right-band" class="col-md-4 d-none d-md-block">

                <div id="profil" class="container-fluid">

                        <?php
                        // -------------------------- Affiche le profil de l'utilisateur -------------------------- //
                        displayProfil($connexion, $_SESSION['username']);
                        ?>

                    <div id="show-post-container" class="row">
                        <button id="show-post-button">Post</button>
                        <label for="show-post-button">Parler au monde entier !</label>
                    </div>

                    <?php
                    // -------------------------- Gérer erreur en cas de post -------------------------- //
                    if (isset($_SESSION['error_post'])) { // Si on a une erreur
                        echo "<div class='error-post'>" . $_SESSION['error_post'] . "</div>";
                        unset($_SESSION['error_post']); // On enlève l'erreur
                    }
                    ?>

                </div>

            </div>
        </div>

    </div>

    <script src="js/popup.js"></script>
    <script src="js/utils.js"></script>
    <script src="js/handle.js"></script>

</body>

</html>