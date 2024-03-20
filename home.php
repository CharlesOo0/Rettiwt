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
    
</head>

<body>

    <div class="container-fluid">

        <div class="row">

            <div id="left-band" class="col-md-3 d-none d-md-block">

                    <div id="profil-link" class="container-fluid">
                        <!-- <div class='col'>
                            <a href="profil.php" id="profil-link-profil" class="left-band-img"><img src="img/profil.png" width="40px" height="auto" alt="Profil"> Profil</a><br>
                            <a href="home.php" id="profil-link-home" class="left-band-img"><img src="img/notification.png" alt="Notification" width="40px" height="auto"> Notification</a> <br>
                            <a href="logout.php"  id="profil-link-logout" class="left-band-img"><img src="img/disconnect.png"  width="40px" height="auto" alt="Deconnexion"> Deconnexion</a>
                        </div> -->

                        <a href="profil.php" id="profil-link-i" class="left-band-img">
                            <img src="img/profil.png" alt="Profil"><span class="pl-span"> Profil</span>
                        </a> <br>
                        <a href="home.php" id="profil-link-i" class="left-band-img">
                            <img src="img/home.png" alt="Profil"><span class="pl-span"> Actualité</span>
                        </a> <br>
                        <a href="home.php" id="profil-link-i" class="left-band-img">
                            <img src="img/favorite.png" alt="Profil"><span class="pl-span"> Pour vous</span>
                        </a> <br>
                        <a href="home.php" id="profil-link-i" class="left-band-img">
                            <img src="img/notification.png" alt="Notification"><span class="pl-span"> Notification</span>
                        </a> <br>
                        <a href="logout.php"  id="profil-link-i" class="left-band-img">
                            <img src="img/disconnect.png" alt="Deconnexion"><span class="pl-span"> Deconnexion</span>
                        </a>
                    </div>

            </div>

            <?php
                // -------------------------- Crée un post -------------------------- //
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title']) && isset($_POST['text'])){
                    if (!isset($_SESSION['username'])) {
                        $_SESSION['error'] = "Vous avez besoin d'être connecter pour poster un message.";
                        header('Location: logout.php');
                        exit();
                    }

                    $author = $_SESSION['username'];
                    $title = htmlspecialchars($_POST['title']);
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
                    $sql = "INSERT INTO post (author, title, text) VALUES ('$id', '$title', '$text')";

                    try {
                        mysqli_query($connexion, $sql);
                        echo "<p> Post crée ! </p>";
                    } catch (Exception $e) {
                        echo "<p> Error: " . $sql . "<br>" . mysqli_error($connexion) . "</p>";
                    }

                }

                // -------------------------- Ajoute un like -------------------------- //
                handleLike($connexion, $_SESSION['username']);

                // -------------------------- Gérer erreur en cas de post -------------------------- //
                if (isset($_SESSION['error_post'])) {
                    echo "<p>" . $_SESSION['error_post'] . "</p>";
                    unset($_SESSION['error_post']);
                }

                // -------------------------- Affiche les posts -------------------------- //
                echo "<div id='post' class='col-md-5 col-12'>";
                displayPost($connexion, NULL);
                echo "</div>";
            ?>

            <div id="right-band" class="col-md-4 d-none d-md-block">

                <div id="profil" class="container-fluid">

                        <?php
                        // -------------------------- Affiche le profil de l'utilisateur -------------------------- //
                        displayProfil($connexion, $_SESSION['username']);
                        ?>

                    <div id="post-form" class="row">
                        
                        <h4 id="title">Post a message :</h4> <br>
                        <form method="POST" action="">
                            <label id="label-title" for="text">Titre :</label> <br>
                            <input class="input" type="text" name="title"  required><br>

                            <label id="label-text" for="text">Texte :</label> <br>
                            <input class="input" type="text" name="text" required><br>
                            <input type="submit" value="Post">
                        </form>

                    </div>

                </div>

            </div>
        </div>

    </div>

</body>

</html>