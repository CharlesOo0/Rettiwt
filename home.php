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
    <div class="post-form-container">
        <div class="post-form">
            <button id="close-post-button"><img src="img/quit.png" alt="close" width='20px' height='auto'></button>
            <form method="POST" action="">
                <input type="hidden" name="action" value="posting">
                <textarea name="text" placeholder="Bonjour tout le monde !"></textarea> <br>
                <input type="submit" value="Post">
            </form>
        </div>
    </div>
    <!-- Les popups -->

    <div class="container-fluid">

        <div class="row no-gutters">

            <div id="left-band" class="col-md-3 d-none d-md-block">

                    <div id="profil-link" class="container-fluid">
                        <a href="profil.php" id="profil-link-i" class="left-band-img">
                            <img src="img/profil.png" alt="Profil"><span class="pl-span"> Profil</span>
                        </a> <br>
                        <a href="home.php" id="profil-link-i" class="left-band-img">
                            <img src="img/home.png" alt="Profil"><span class="pl-span"> Actualités</span>
                        </a> <br>
                        <a href="?forYou=true" id="profil-link-i" class="left-band-img">
                            <img src="img/favorite.png" alt="Profil"><span class="pl-span"> Pour vous</span>
                        </a> <br>
                        <a href="" id="profil-link-i" class="left-band-img">
                            <img src="img/notification.png" alt="Notification"><span class="pl-span"> Notifications</span>
                        </a> <br>
                        <a href="logout.php"  id="profil-link-i" class="left-band-img">
                            <img src="img/disconnect.png" alt="Deconnexion"><span class="pl-span"> Déconnexion</span>
                        </a>
                    </div>

            </div>

            <?php
                // -------------------------- Crée un post -------------------------- //
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['text']) && isset($_POST['action']) && $_POST['action'] == "posting"){
                    if (!isset($_SESSION['username'])) {
                        $_SESSION['error'] = "Vous avez besoin d'être connecter pour poster un message.";
                        header('Location: logout.php');
                        exit();
                    }

                    $modify = true;

                    if (empty($_POST['text'])) {
                        $modify = false;
                    }

                    if (strlen($_POST['text']) > 270) {
                        $_SESSION['error_post'] = "Votre message est trop long.";
                        header('Location: home.php');
                        exit();
                    }

                    $author = $_SESSION['username'];
                    $text = htmlspecialchars($_POST['text']);

                    $sql = "SELECT * FROM profil WHERE username='$author'"; // Vérifie si l'utilisateur existe

                    try {
                        $result = mysqli_query($connexion, $sql);
                    } catch (Exception $e) {
                        $_SESSION['error_post'] = "Erreur lors de la création du post : " . mysqli_error($connexion);
                        header('Location: home.php');
                        exit();
                    }

                    if ($result->num_rows != 1) {
                        $_SESSION['error'] = "Vous avez besoin d'être connecter pour poster un message.";
                        header('Location: logout.php');
                        exit();
                    } 

                    $result = mysqli_fetch_assoc($result);
                    $id = $result['id'];

                    // Crée la requête SQL
                    $sql = "INSERT INTO post (author, text) VALUES ('$id', '$text')";

                    if ($modify) {
                        try {
                            mysqli_query($connexion, $sql);
                        } catch (Exception $e) {
                            echo "<p> Error: " . $sql . "<br>" . mysqli_error($connexion) . "</p>";
                        }
                    }

                }

                // -------------------------- Ajoute un like -------------------------- //
                handleLike($connexion, $_SESSION['username']);

                // -------------------------- Gérer erreur en cas de post -------------------------- //
                if (isset($_SESSION['error_post'])) {
                    echo "<p>" . $_SESSION['error_post'] . "</p>";
                    unset($_SESSION['error_post']);
                }
            ?>

            <?php
                // -------------------------- Affiche les posts -------------------------- //
                echo "<div id='posts' class='col-md-5 col-12'>";
                if (isset($_GET['displayFollower'])) {
                    displayFollower($connexion, $_SESSION['username']);
                } else if (isset($_GET['displayFollowing'])) {
                    displayFollowing($connexion, $_SESSION['username']);
                }else if (isset($_GET['forYou'])) {
                    displayPost($connexion, $_SESSION['username'], 1);
                }else {
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
                    </div>

                </div>

            </div>
        </div>

    </div>

    <script src="js/popup.js"></script>
    <script src="js/utils.js"></script>

</body>

</html>