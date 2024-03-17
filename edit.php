
        <?php
        // -------------------------- Vérifie tout ce qui est nécessaire -------------------------- //
        require 'sql.php'; // Inclut le fichier 'sql.php
        require 'utils.php'; // Inclut le fichier 'utils.php

        session_start(); // Start the session

        $connexion = connexion(); // Se connecte a la base de données

        checkCreds(); // Vérifie si l'utilisateur est connecté

        ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Rettiwt</title>
    </head>
    <body>
        
        <a href="home.php">Home</a> <br>
        <a href="profil.php">Profil</a>

        <h1>Informations actuelles :</h1>

        <?php
        // -------------------------- Affiche les informations de l'utilisateur -------------------------- //

        $currentUser = $_SESSION['username']; // Récupère le username de l'utilisateur connecté
        $sql = "SELECT * FROM profil WHERE username = '$currentUser'"; // Crée la requête SQL qui récupère les informations de l'utilisateur connecté
        $picture = NULL; // Initialise une variable pour l'avatar de l'utilisateur a NULL

        try { // Exécute la requête
            $result = mysqli_query($connexion, $sql);
            $row = mysqli_fetch_assoc($result);

            if (!empty($row)) { // Si le fetch a retourné quelque chose
                if (isset($row['avatar']) && $row['avatar'] != null) { // Si l'utilisateur a un avatar
                    $picture = $row['avatar']; // Récupère l'avatar de l'utilisateur
                    // Affiche l'avatar de l'utilisateur en 3 tailles différentes
                    echo "<p>Avatar actuel : <img src='img/" . $picture . "' alt='avatar' width='64' height='64'>
                    <img src='img/" . $picture . "' alt='avatar' width='128' height='128'>
                    <img src='img/" . $picture . "' alt='avatar' width='256' height='256'>
                    </p>";
                }else { // Sinon (si l'utilisateur n'a pas d'avatar)
                    // Affiche l'avatar par défaut en 3 tailles différentes
                    echo "<p>Avatar actuel : <img src='img/default_pfp.png' alt='avatar' width='64' height='64'>
                    <img src='img/default_pfp.png' alt='avatar' width='128' height='128'>
                    <img src='img/default_pfp.png' alt='avatar' width='256' height='256'>
                    </p>";
                }

                // Affiche les informations de l'utilisateur
                echo "<p>Pseudo : " . $row['username'] . "</p>";
                echo "<p>Email : " . $row['email'] . "</p>";
                echo "<p>Bio : " . $row['bio'] . "</p>";

            } else { // Sinon (si le fetch n'a rien retourné)
                echo "<p> Erreur profil non trouvé </p>";
            }
        } catch (Exception $e) { // Si il y a une erreur lors de la récupération du profil
            echo "<p>Erreur lors de la récupération du profil : " . mysqli_error($connexion) . "</p>";
        }
        ?>


        <?php
        
        // -------------------------- Modifie le profil de l'utilisateur -------------------------- //
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  (isset($_POST['username']) || isset($_POST['email']) || (isset($_POST['password']) && isset($_POST['confirm_password'])) || isset($_POST['bio']) || isset($_FILES['avatar']))) {

            $setClauses = []; // Initialise un tableau pour les clauses SET
            $modify = true; // Initialise une variable pour savoir si il y a eu une modification
            $passwordModified = false; // Initialise une variable pour savoir si le mot de passe a été modifié

            if (isset($_POST['username']) && $_POST['username'] != "") { // Si l'utilisateur veux modifier son pseudo
                $username = htmlspecialchars($_POST['username']); // Récupère le nouveau pseudo

                if (strlen($username) > 60) { // Vérifie si le pseudo ne dépasse pas 60 caractères 
                    echo "<p>Le pseudo ne peut pas dépasser 60 caractères</p>";
                    $modify = false;
                }else {
                    $setClauses[] = "username = '$username'";
                    $passwordModified = true;
                }

            }

            if (isset($_POST['email']) && $_POST['email'] != "") { // Si l'utilisateur veux modifier son email
                $email = htmlspecialchars($_POST['email']); // Récupère le nouvel email

                if (strlen($email) > 256) { // Vérifie si l'email ne dépasse pas 256 caractères
                    echo "<p>L'email ne peut pas dépasser 256 caractères</p>";
                    $modify = false;
                }else {
                    $setClauses[] = "email = '$email'";
                }

            }

            if (isset($_POST['password']) && isset($_POST['confirm_password']) && $_POST['password'] != "") { // Si l'utilisateur veux modifier son mot de passe
                $password = htmlspecialchars($_POST['password']); // Récupère le nouveau mot de passe
                $confirm_password = htmlspecialchars($_POST['confirm_password']); // Récupère la confirmation du nouveau mot de passe

                if ($password !== $confirm_password) { // Vérifie si les mots de passe correspondent
                    echo "<p>Les mots de passe ne correspondent pas</p>";
                    $modify = false;
                }

                if (strlen($password) > 256) { // Vérifie si le mot de passe ne dépasse pas 256 caractères
                    echo "<p>Le mot de passe ne peut pas dépasser 256 caractères</p>";
                    $modify = false;
                }

                if (strlen($password) < 8) { // Vérifie si le mot de passe fait au moins 8 caractères
                    echo "<p>Le mot de passe doit faire au moins 8 caractères</p>";
                    $modify = false;
                } else {
                    $setClauses[] = "password = '$password'";
                }
                
            }

            if (isset($_POST['bio']) && $_POST['bio'] != "") { // Si l'utilisateur veux modifier sa bio
                $bio = htmlspecialchars($_POST['bio']); // Récupère la nouvelle bio

                if (strlen($bio) > 256) { // Vérifie si la bio ne dépasse pas 256 caractères
                    echo "<p>La bio ne peut pas dépasser 256 caractères</p>";
                    $modify = false;
                }else {
                    $setClauses[] = "bio = '$bio'";
                }

            }

            if (isset($_FILES['avatar']) && $_FILES['avatar']['tmp_name'] != "") { // Si l'utilisateur veux modifier son avatar

                $avatar = $_FILES['avatar']['tmp_name']; // Récupère le fichier

                if ($_FILES['avatar']['size'] > 64 * 1024) { // 64 KB = 64 * 1024 bytes
                    echo "<p>L'avatar ne peut pas dépasser 64Kb </p>";
                } else {
                    $target_dir = "img/"; // Fichier dans lequel on enregistre nos images
                    $identifiant_unique = uniqid('avatar_');

                    // Récupère l'extension du fichier
                     $file_extension = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);

                    $target_file = $target_dir . $identifiant_unique . "." . $file_extension; // Crée le chemin du fichier

                    if (!move_uploaded_file($avatar, $target_file)) { // Déplace le fichier dans le dossier img
                        echo "<p> Il y a eu une erruer en téléchargant votre image. </p>";
                    }else {
                        $setClauses[] = "avatar = '" . $identifiant_unique . "." . $file_extension ."'";
                    }

                }

            }

            if (empty($setClauses)) { // Si il n'y a pas eu de modification
                $modify = false;
            }


            $currentUser = $_SESSION['username']; // Récupère le username de l'utilisateur connecté
            $sql = "UPDATE profil SET " . implode(', ', $setClauses). " WHERE username = '$currentUser'"; // Crée la requête SQL pour modifier le profil de l'utilisateur connecté

            if ($modify == true) { // Si il n'y a pas eu d'erreur lors de la modification du profil

                try { // Exécute la requête
                    mysqli_query($connexion, $sql);

                    if ($passwordModified) { // Si le mot de passe a été modifié
                        $modifyiedUsername = null;
                        foreach ($setClauses as $clause) {
                            if (strpos($clause, 'username') !== false) {
                                // Récupère le nouveau pseudo
                                $parts = explode(" = ", $clause);
                                $modifyiedUsername = trim($parts[1], "'");
                                break;
                            }
                        }

                        $_SESSION['username'] = $modifyiedUsername;  // Change le username de la session pour le nouveau pseudo
                    }

                    if ($picture != NULL) { // Si l'utilisateur a un avatar
                        unlink("img/" . $picture); // Supprime l'ancien avatar
                    }

                    echo("<meta http-equiv='refresh' content='0'>"); // Rafraichit la page pour afficher les nouvelles informations
                    // En utilisant meta refresh pour éviter le message de confirmation de rechargement de la page

                    $_SESSION['modifyied'] = true; // Ajoute une variable de session pour savoir si le profil a été modifié

                } catch (Exception $e) { // Si il y a une erreur lors de la modification du profil
                    echo "<p>Erreur lors de la modification du profil : " . mysqli_error($connexion) . "</p>";
                }

            }  
        }

        ?>

        <h1>Modifier votre profil :</h1>

        <?php
            if (isset($_SESSION['modifyied']) && !isset($modify)) { // Si le profil a été modifié
                echo "<p>Profil modifié avec succès</p>"; // Affiche un message de confirmation
                unset($_SESSION['modifyied']);
            }
        ?>

        <form method="POST" action="edit.php" enctype="multipart/form-data">
            <label for="username">Pseudo :</label>
            <input type="text" name="username"><br><br>

            <label for="email">Email :</label>
            <input type="text" name="email"><br><br>

            <label for="password">Mot de passe :</label>
            <input type="password" name="password"><br><br>

            <label for="password">Confirmer le mot de passe :</label>
            <input type="password" name="confirm_password"><br><br>

            <label for="bio">Bio :</label>
            <input type="text" name="bio"><br><br>

            <label for="avatar">Avatar :</label>
            <input type="file" name="avatar" accept="image/*"><br><br>
            
            <input type="submit" value="Save">
        </form>
    </body>
    </html>
