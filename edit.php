
        <?php
        // -------------------------- Vérifie tout ce qui est nécessaire -------------------------- //
        require 'sql.php'; // Inclut le fichier 'sql.php
        require 'utils_display.php'; // Inclut le fichier 'utils.php

        session_start(); // Start the session

        $connexion = connexion(); // Se connecte a la base de données

        checkCreds(); // Vérifie si l'utilisateur est connecté

        ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Rettiwt</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="css/edit.css">
    </head>
    <body>


        <a href="home.php" id="href-home"><img src="img/arrow.png" alt="Acceuil" width="60px" height="auto"></a> <br>

        <div id="container" class="container-fluid row">
            
                <div id="current-information" class="col">
                    <h1>Informations actuelles</h1>

                    <?php
                    // -------------------------- Affiche les informations de l'utilisateur -------------------------- //

                    $currentUser = $_SESSION['username']; // Récupère le username de l'utilisateur connecté
                    // Crée la requête SQL qui récupère les informations de l'utilisateur connecté
                    $stmt = $connexion->prepare("SELECT * FROM profil WHERE username=?"); // Prépare la requête
                    $stmt->bind_param("s", $currentUser); // Lie les paramètres à la requête
                    $stmt->execute(); // Exécute la requête
                    $result = $stmt->get_result(); // Récupère le résultat de la requête
                    $picture = NULL; // Initialise une variable pour l'avatar de l'utilisateur a NULL

                    try { // Exécute la requête
                        $stmt->execute(); // Exécute la requête
                        $result = $stmt->get_result(); // Récupère le résultat de la requête
                    } catch (Exception $e) { // Si il y a une erreur lors de la récupération du profil
                        echo "<p>Erreur lors de la récupération du profil : " . mysqli_error($connexion) . "</p>";
                    }

                    $row = mysqli_fetch_assoc($result);
                    echo "<div id='current-information-text'>";
                    if (!empty($row)) { // Si le fetch a retourné quelque chose
                        if (isset($row['avatar']) && $row['avatar'] != null) { // Si l'utilisateur a un avatar
                            $picture = $row['avatar']; // Récupère l'avatar de l'utilisateur
                            // Affiche l'avatar de l'utilisateur en 2 tailles différentes
                            echo "<p>Avatar actuel : <img src='pfp/" . $picture . "' alt='avatar' width='64' height='64' style='border-radius:50%;'>
                            <img src='pfp/" . $picture . "' alt='avatar' width='128' height='128' style='border-radius:50%;'>
                            </p>";
                        }else { // Sinon (si l'utilisateur n'a pas d'avatar)
                            // Affiche l'avatar par défaut en 2 tailles différentes
                            echo "<p>Avatar actuel : <img src='img/default_pfp.png' alt='avatar' width='64' height='64' style='border-radius:50%;'>
                            <img src='img/default_pfp.png' alt='avatar' width='128' height='128' style='border-radius:50%;'>
                            </p>";
                        }

                        // Affiche les informations de l'utilisateur
                        echo "<p>Pseudo : <br>" . $row['username'] . "</p>";
                        echo "<p>Email : <br>" . $row['email'] . "</p>";
                        echo "<p>Bio : <br>" . $row['bio'] . "</p>";

                    } else { // Sinon (si le fetch n'a rien retourné)
                        echo "<p> Erreur profil non trouvé </p>";
                    }
                    echo "</div>";

                    
                    if (isset($_SESSION['modifyied']) && !isset($modify)) { // Si le profil a été modifié
                        echo "<div id='success-profil'>Profil modifié avec succès</div>"; // Affiche un message de confirmation
                        unset($_SESSION['modifyied']);
                    }
                    
                    if (isset($_GET['error_modify'])) { // Si il y a une erreur lors de la modification du profil
                        echo "<div id='error-profil'>". $_GET['error_modify'] ."</div>"; // Affiche un message d'erreur
                    }
                    ?>
                </div>


                <?php
                
                // -------------------------- Modifie le profil de l'utilisateur -------------------------- //
                if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  (isset($_POST['username']) || isset($_POST['email']) || (isset($_POST['password']) && isset($_POST['confirm_password'])) || isset($_POST['bio']) || isset($_FILES['avatar']))) {

                    $setClauses = []; // Initialise un tableau pour les clauses SET
                    $modify = true; // Initialise une variable pour savoir si il y a eu une modification
                    $passwordModified = false; // Initialise une variable pour savoir si le mot de passe a été modifié

                    if (isset($_POST['username']) && $_POST['username'] != "") { // Si l'utilisateur veux modifier son pseudo
                        $username = htmlspecialchars($_POST['username']); // Récupère le nouveau pseudo

                        if (strlen($username) > 60) { // Vérifie si le pseudo ne dépasse pas 60 caractères 
                            header("Location: edit.php?error_modify=Le pseudo ne peut pas dépasser 60 caractères");
                            exit();
                            $modify = false;
                        }else {
                            $setClauses[] = "username = '$username'";
                            $passwordModified = true;
                        }

                    }

                    if (isset($_POST['email']) && $_POST['email'] != "") { // Si l'utilisateur veux modifier son email
                        $email = htmlspecialchars($_POST['email']); // Récupère le nouvel email

                        if (strlen($email) > 256) { // Vérifie si l'email ne dépasse pas 256 caractères
                            header("Location: edit.php?error_modify=L'email ne peut pas dépasser 256 caractères");
                            exit();
                            $modify = false;
                        }else {
                            $setClauses[] = "email = '$email'";
                        }

                    }

                    if (isset($_POST['password']) && isset($_POST['confirm_password']) && $_POST['password'] != "") { // Si l'utilisateur veux modifier son mot de passe
                        $password = htmlspecialchars($_POST['password']); // Récupère le nouveau mot de passe
                        $confirm_password = htmlspecialchars($_POST['confirm_password']); // Récupère la confirmation du nouveau mot de passe

                        if ($password !== $confirm_password) { // Vérifie si les mots de passe correspondent
                            header("Location: edit.php?error_modify=Les mots de passe ne correspondent pas");
                            exit();
                            $modify = false;
                        }

                        if (strlen($password) > 256) { // Vérifie si le mot de passe ne dépasse pas 256 caractères
                            header("Location: edit.php?error_modify=Le mot de passe ne peut pas dépasser 256 caractères");
                            exit();
                            $modify = false;
                        }

                        if (strlen($password) < 8) { // Vérifie si le mot de passe fait au moins 8 caractères
                            header("Location: edit.php?error_modify=Le mot de passe doit faire au moins 8 caractères");
                            exit();
                            $modify = false;
                        } else {
                            $setClauses[] = "password = '$password'";
                        }
                        
                    }

                    if (isset($_POST['bio']) && $_POST['bio'] != "") { // Si l'utilisateur veux modifier sa bio
                        $bio = htmlspecialchars($_POST['bio']); // Récupère la nouvelle bio

                        if (strlen($bio) > 256) { // Vérifie si la bio ne dépasse pas 256 caractères
                            header("Location: edit.php?error_modify=La bio ne peut pas dépasser 256 caractères");
                            exit();
                            $modify = false;
                        }else {
                            $setClauses[] = "bio = '$bio'";
                        }

                    }

                    $avatarModified = false; // Initialise une variable pour savoir si l'avatar a été modifié
                    if (isset($_FILES['avatar']) && $_FILES['avatar']['tmp_name'] != "") { // Si l'utilisateur veux modifier son avatar

                        $avatar = $_FILES['avatar']['tmp_name']; // Récupère le fichier

                        if ($_FILES['avatar']['size'] > 64 * 1024) { // 64 KB = 64 * 1024 bytes
                            header("Location: edit.php?error_modify=L'image ne peut pas dépasser 64 KB");
                            exit();
                        } else {
                            $target_dir = "pfp/"; // Fichier dans lequel on enregistre nos images
                            $identifiant_unique = uniqid('avatar_');

                            // Récupère l'extension du fichier
                            $file_extension = pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);

                            $target_file = $target_dir . $identifiant_unique . "." . $file_extension; // Crée le chemin du fichier

                            if (!move_uploaded_file($avatar, $target_file)) { // Déplace le fichier dans le dossier img
                                header("Location: edit.php?error_modify=Erreur lors de l'upload de l'image");
                                exit();
                            }else {
                                $setClauses[] = "avatar = '" . $identifiant_unique . "." . $file_extension ."'";
                                $avatarModified = true;
                            }

                        }

                    }

                    if (empty($setClauses)) { // Si il n'y a pas eu de modification
                        $modify = false;
                    }


                    $currentUser = $_SESSION['username']; // Récupère le username de l'utilisateur connecté
                    $sql = "UPDATE profil SET " . implode(', ', $setClauses) . " WHERE username = '$currentUser'"; // Crée la requête SQL pour modifier le profil de l'utilisateur connecté

                    if ($modify == true) { // Si il n'y a pas eu d'erreur lors de la modification du profil

                        try { // Exécute la requête
                            mysqli_query($connexion, $sql);
                        } catch (Exception $e) { // Si il y a une erreur lors de la modification du profil
                            header("Location: edit.php?error_modify=Erreur lors de la modification du profil liée a la base de données");
                            exit();
                        }

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

                        if ($picture != NULL && $avatarModified) { // Si l'utilisateur a un avatar
                            unlink("pfp/" . $picture); // Supprime l'ancien avatar
                        }

                        echo("<meta http-equiv='refresh' content='0'>"); // Rafraichit la page pour afficher les nouvelles informations
                        // En utilisant meta refresh pour éviter le message de confirmation de rechargement de la page

                        $_SESSION['modifyied'] = true; // Ajoute une variable de session pour savoir si le profil a été modifié

                    } 
                    
                }

                ?>

                <div id="new-information" class="col">
                    <h1>Modifier votre profil</h1>

                    <form method="POST" action="edit.php" enctype="multipart/form-data" id="new-information-form">
                        <label for="username">Pseudo :</label><br>
                        <input type="text" name="username"><br><br>

                        <label for="email">Email :</label><br>
                        <input type="text" name="email"><br><br>

                        <label for="password">Mot de passe :</label><br>
                        <input type="password" name="password"><br><br>

                        <label for="password">Confirmer le mot de passe :</label><br>
                        <input type="password" name="confirm_password"><br><br>

                        <label for="bio">Bio :</label><br>
                        <input type="text" name="bio"><br><br>

                        <label for="avatar">Avatar :</label><br>
                        <input type="file" name="avatar" accept="image/*" onchange="previewImage(event)">
                        <img id="preview" src="" alt="Image Preview" style="width: 64px; height: auto; border-radius: 50%; display: none;"><br><br>
                        
                        <div id="submit">
                        <input id="submit-button" type="submit" value="Valider">
                        </div>
                    </form>
                </div>
            
        </div>

        <script>
        // Fonction pour prévisualiser l'image sélectionnée
        function previewImage(event) {
            var output = document.getElementById('preview'); // Récupère l'élément img
            if (event.target.files.length === 0) { // Si aucun fichier n'est sélectionné
                output.style.display = 'none'; // Cache l'image
                return; // Arrête la fonction
            } else { // Sinon
                output.style.display = 'inline-block'; // Affiche l'image
            } 
            var reader = new FileReader(); // Crée un objet FileReader
            reader.onload = function() { // Quand le fichier est chargé
                output.src = reader.result; // Affiche l'image
            };
            reader.readAsDataURL(event.target.files[0]); // Lit le fichier
        }
        </script>
    </body>
    </html>
