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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/home.css">
    <link rel="stylesheet" type="text/css" href="css/popup.css">
    
</head>

<body>

    <!-- Les popups -->

        <!-- Popup de post -->
    <div class="post-form-container">
        <div class="post-form">
            <?php
            // -------------------------- Gérer erreur en cas de post -------------------------- //
            if (isset($_SESSION['error_post'])) { // Si on a une erreur
                echo "<div class='error-post'>" . $_SESSION['error_post'] . "</div>";
                unset($_SESSION['error_post']);
            }
            ?>
            <button id="close-post-button"><img src="img/quit.png" alt="close" width='20px' height='auto'></button>
            <form method="POST" action="">
                <input type="hidden" name="action" value="posting">
                <textarea name="text" placeholder="Bonjour tout le monde !"></textarea> <br>
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

            <div id="left-band" class="col-md-3 d-none d-md-block">

                    <div id="profil-link" class="container-fluid">
                        <a href="profil.php" class="left-band-img profil-link-i">
                            <img src="img/profil.png" alt="Profil"><span class="pl-span"> Profil</span>
                        </a> <br>
                        <a href="home.php" class="left-band-img profil-link-i">
                            <img src="img/home.png" alt="Actualités"><span class="pl-span"> Actualités</span>
                        </a> <br>
                        <a href="?forYou=true" class="left-band-img profil-link-i">
                            <img src="img/favorite.png" alt="Pour vous"><span class="pl-span"> Pour vous</span>
                        </a> <br>

                        <button id="show-notification-button" class="left-band-img profil-link-i"><img src="img/notification.png" alt="Notification"><span class="pl-span"> Notifications</span></button><br>
                        
                        <a href="home.php"  class="left-band-img profil-link-i">
                            <img src="img/stat.png" alt="Statistique"><span class="pl-span"> Statistique</span>
                        </a>

                        <a href="edit.php"  class="left-band-img profil-link-i">
                            <img src="img/edit.png" alt="Modifier"><span class="pl-span"> Modifier</span>
                        </a>

                        <a href="logout.php"  class="left-band-img profil-link-i">
                            <img src="img/disconnect.png" alt="Deconnexion"><span class="pl-span"> Déconnexion</span>
                        </a>
                    </div>

            </div>

            <?php
                // -------------------------- Crée un post -------------------------- //
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['text']) && isset($_POST['action']) && $_POST['action'] == "posting"){ // Si on post sur cette page
                    if (!isset($_SESSION['username'])) { // Si l'utilisateur n'est pas connecté
                        $_SESSION['error'] = "Vous avez besoin d'être connecter pour poster un message.";
                        header('Location: logout.php');
                        exit();
                    }

                    // Initialise un booléen pour savoir si on peut poster
                    $modify = true; 
                    $text = htmlspecialchars($_POST['text']); // On récupère le texte

                    if (empty($text)) { // Si le message est vide
                        $modify = false; // On ne peut pas poster
                    }

                    if (strlen($text) > 270) { // Si le message est trop long
                        $_SESSION['error_post'] = "Votre message est trop long.";
                        header('Location: home.php');
                        exit();
                    }

                    $author = $_SESSION['username']; // On récupère l'auteur

                    $sql = "SELECT * FROM profil WHERE username='$author'"; // Vérifie si l'utilisateur existe

                    try { // On essaye de faire la requête
                        $result = mysqli_query($connexion, $sql);
                    } catch (Exception $e) { // Si on a une erreur
                        $_SESSION['error_post'] = "Erreur lors de la récupération de votre profil.";
                        header('Location: home.php');
                        exit();
                    }

                    if ($result->num_rows != 1) { // Si l'utilisateur n'existe pas
                        $_SESSION['error'] = "Erreur lors de la récupération de votre profil, vous avez besoin d'être connecter pour poster un message.";
                        header('Location: logout.php');
                        exit();
                    } 

                    $result = mysqli_fetch_assoc($result); // On récupère les informations de l'utilisateur
                    $id = $result['id']; // On récupère l'id de l'utilisateur

                    // Crée la requête SQL pour ajouter le post
                    $sql = "INSERT INTO post (author, text) VALUES ('$id', '$text')"; 

                    if ($modify) { // Si on peut poster
                        try { // On essaye de faire la requête
                            mysqli_query($connexion, $sql);
                        } catch (Exception $e) { // Si on a une erreur
                            $_SESSION['error_post'] = "<p>Erreur lors de la création de votre post...</p>";
                            header('Location: home.php');
                            exit();
                        }
                    }

                }
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
                    displayFollow($connexion, $_GET['username'], 0);
                } else if (isset($_GET['displayFollowing']) && isset($_GET['username']) && $_GET['username'] != ''){ // Si on veut afficher les suivis
                    echo "<h4>Suivis</h4>";
                    displayFollow($connexion, $_GET['username'], 1);
                }else if (isset($_GET['forYou'])) { // Si on veut afficher les posts pour l'utilisateur
                    echo "<h4>Pour vous</h4>";
                    displayPost($connexion, $_SESSION['username'], 1); 
                }else { // Sinon on affiche les posts normaux
                    echo "<h4>Actualités</h4>";
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

                </div>

            </div>
        </div>

    </div>

    <script src="js/popup.js"></script>
    <script src="js/utils.js"></script>

</body>

</html>