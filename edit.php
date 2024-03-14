
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

        $currentUser = $_SESSION['username'];
        $sql = "SELECT * FROM profil WHERE username = '$currentUser'";

        try {
            $result = mysqli_query($connexion, $sql);
            $row = mysqli_fetch_assoc($result);

            if (!empty($row)) {
                if (isset($row['avatar']) && $row['avatar'] != null) {
                    $picture = base64_encode($row['avatar']);
                    echo "<p>Avatar actuel : <img src='data:image/jpg;base64," . $picture . "' alt='avatar' width='64' height='64'>
                    <img src='data:image/jpg;base64," . $picture . "' alt='avatar' width='128' height='128'>
                    <img src='data:image/jpg;base64," . $picture . "' alt='avatar' width='256' height='256'>
                    </p>";
                }else {
                    echo "<p>Avatar actuel : <img src='img/default_pfp.png' alt='avatar' width='64' height='64'>
                    <img src='img/default_pfp.png' alt='avatar' width='128' height='128'>
                    <img src='img/default_pfp.png' alt='avatar' width='256' height='256'>
                    </p>";
                }
                echo "<p>Pseudo : " . $row['username'] . "</p>";
                echo "<p>Email : " . $row['email'] . "</p>";
                echo "<p>Bio : " . $row['bio'] . "</p>";

            } else {
                // Code to handle when the fetch returned nothing
                echo "The fetch returned nothing";
            }
        } catch (Exception $e) {
            echo "<p>Erreur lors de la récupération du profil : " . mysqli_error($connexion) . "</p>";
        }
        ?>


        <?php
        
        // -------------------------- Modifie le profil de l'utilisateur -------------------------- //
        if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  (isset($_POST['username']) || isset($_POST['email']) || (isset($_POST['password']) && isset($_POST['confirm_password'])) || isset($_POST['bio']) || isset($_FILES['avatar']))) {

            $setClauses = []; // Initialise un tableau pour les clauses SET
            $modify = true;
            $passwordModified = false;

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
                $avatar = file_get_contents($_FILES['avatar']['tmp_name']); // Read the file data

                $imageData = base64_encode($avatar);

                if (strlen($imageData) > 65535) { // Vérifie si l'avatar ne dépasse pas 65535 caractères
                    echo "<p>L'avatar ne peut pas dépasser 64Kb </p>";
                    $modify = false;
                }

                $setClauses[] = "avatar = '$imageData'";

            }

            if (empty($setClauses)) { // Si il n'y a pas eu de modification
                $modify = false;
            }


            $currentUser = $_SESSION['username'];
            $sql = "UPDATE profil SET " . implode(', ', $setClauses). " WHERE username = '$currentUser'";

            if ($modify == true) { // Si il n'y a pas eu d'erreur lors de la modification du profil
                try {
                    mysqli_query($connexion, $sql);
                    if ($passwordModified) {
                        $modifyiedUsername = null;
                        foreach ($setClauses as $clause) {
                            if (strpos($clause, 'username') !== false) {
                                // Extract the username value from the clause
                                $parts = explode(" = ", $clause);
                                $modifyiedUsername = trim($parts[1], "'");
                                break;
                            }
                        } // Add the missing closing brace for the foreach loop here
                        $_SESSION['username'] = $modifyiedUsername;
                        echo("<meta http-equiv='refresh' content='0'>"); //Refresh by HTTP 'meta'

                    }
                    $_SESSION['modifyied'] = true;
                } catch (Exception $e) {
                    echo "<p>Erreur lors de la modification du profil : " . mysqli_error($connexion) . "</p>";
                }
            }  
        }

        if (isset($_SESSION['modifyied']) && !isset($modify)) {
            echo "<p>Profil modifié avec succès</p>";
            unset($_SESSION['modifyied']);
        }

        ?>

        <h1>Modifier votre profil :</h1>

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
