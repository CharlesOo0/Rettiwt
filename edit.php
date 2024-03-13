
    <?php
    // -------------------------- Vérifie tout ce qui est nécessaire -------------------------- //
    require 'sql.php'; // Inclut le fichier 'sql.php
    require 'utils.php'; // Inclut le fichier 'utils.php

    $connexion = connexion(); // Se connecte a la base de données

    checkCreds(); // Vérifie si l'utilisateur est connecté

    ?>

<!DOCTYPE html>
<html>
<head>
    <title>Rettiwt</title>
</head>
<body>
    <h1>Edit Profile</h1>


    <?php
    
    // -------------------------- Modifie le profil de l'utilisateur -------------------------- //
    if ($_SERVER['REQUEST_METHOD'] === 'POST' &&  (isset($_POST['username']) || isset($_POST['email']) || (isset($_POST['password']) && isset($_POST['confirm_password'])) || isset($_POST['bio']) || isset($_FILES['avatar']))) {

        $setClauses = []; // Initialise un tableau pour les clauses SET
        $modify = true;

        if (isset($_POST['username']) && $_POST['username'] != "") { // Si l'utilisateur veux modifier son pseudo

            $username = htmlspecialchars($_POST['username']); // Récupère le nouveau pseudo

            if (strlen($username) > 60) { // Vérifie si le pseudo ne dépasse pas 60 caractères 
                echo "<p>Le pseudo ne peut pas dépasser 60 caractères</p>";
                $modify = false;
            }else {
                $setClauses[] = "username = '$username'";
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

        if (isset($_POST['bio']) && $_POST['bio'] != "" && strlen($$_POST['bio']) > 256) { // Si l'utilisateur veux modifier sa bio
            $bio = htmlspecialchars($_POST['bio']); // Récupère la nouvelle bio

            if (strlen($bio) > 256) { // Vérifie si la bio ne dépasse pas 256 caractères
                echo "<p>La bio ne peut pas dépasser 256 caractères</p>";
                $modify = false;
            }else {
                $setClauses[] = "bio = '$bio'";
            }

        }

        if (isset($_FILES['avatar'])) { // Si l'utilisateur veux modifier son avatar
            $avatar = $_FILES['avatar']; // Récupère le nouvel avatar
        
            // Regarde si il y a une erreur lors de l'upload
            if ($avatar['error'] === UPLOAD_ERR_OK) {
                // Genere un nom de fichier unique
                $filename = uniqid() . '.' . pathinfo($avatar['name'], PATHINFO_EXTENSION);
        
                // Specifie le dossier de destination
                $uploadDir = 'uploads/';
        
                // Genere le chemin complet
                $uploadFile = $uploadDir . $filename;
        
                // Deplace le fichier temporaire dans le dossier de destination
                if (move_uploaded_file($avatar['tmp_name'], $uploadFile)) {
                    echo "Image valide est uploader avec succés.\n";
                    $setClauses[] = "avatar = '$uploadFile'";
                } else {
                    echo "Attaque via fichier possible !\n";
                    $modify = false;
                }
            } elseif ($avatar['error'] !== UPLOAD_ERR_NO_FILE) {
                echo "Erreur dans l'upload du fichier : " . $avatar['error'];
                $modify = false;
            }

        }


        $currentUser = $_SESSION['username'];
        $sql = "UPDATE profil SET " . implode(', ', $setClauses). "WHERE username = '$currentUser'";

        if ($modify) { // Si il n'y a pas eu d'erreur lors de la modification du profil
            try {
                mysqli_query($connexion, $sql);
                echo "<p>Profil modifié avec succès</p>";
            } catch (Exception $e) {
                echo "<p>Erreur lors de la modification du profil : " . mysqli_error($connexion) . "</p>";
            }
        }
        
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