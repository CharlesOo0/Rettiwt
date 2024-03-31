<?php

// -------------------------- PROTOTYPE AJOUT COMMENTAIRE AVEC AJAX ABANDONNER -------------------------- //

require '../sql.php'; // Inclut le fichier 'sql.php'
require '../utils_display.php'; // Inclut le fichier 'utils.php'

$connexion = connexion(); // Se connecte a la base de données

if (isset($_POST['post_id'])) {
    $post_id = mysqli_real_escape_string($connexion, $_POST['post_id']); // Récupère l'id du post
    $parent_id = mysqli_real_escape_string($connexion, $_POST['parent_id']); // Récupère l'id du commentaire parent
    $text = mysqli_real_escape_string($connexion, $_POST['text']); // Récupère le texte du commentaire

    if (isset($_FILES['comment_images'])) { // Si on a des images
        $images = $_FILES['comment_images']; // Récupère les images
    }

    $username = $_SESSION['username']; // Récupère le nom d'utilisateur

    $sql = "SELECT id FROM profil WHERE username='$username'"; // Récupère l'id de l'utilisateur
    try { // Essaie de récupérer l'id de l'utilisateur
        $result = mysqli_query($connexion, $sql);
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        $_SESSION['error_post'] = "Erreur lors de la récupération de l'id de l'utilisateur";
        header('Location: home.php');
    }

    if ($parent_id != 'NULL') {
        $sql = "INSERT INTO post (author, text, parent_id) VALUES ('$id', '$text', '$parent_id')";
        $parent_target_id = $parent_id;
    }else {
        $sql = "INSERT INTO post (author, text, parent_id) VALUES ('$id', '$text', '$post_id')";
        $parent_target_id = $post_id;
    }

    try { // Essaie d'ajouter le commentaire
        mysqli_query($connexion, $sql);
        $post_id = mysqli_insert_id($connexion); // Récupère l'id du commentaire ajouté
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        $_SESSION['error_post'] = "Erreur lors de l'ajout du commentaire";
        header('Location: home.php');
    }

    if (isset($images) && count(array_filter($_FILES['comment_images']['name'])) > 0) { // Si on a des images
        $count = 0; // Compteur pour les images
        $target_dir = "../post_images/"; // Le dossier où on va stocker les images
        for ($i = 0; $i < count($images['name']); $i++) { // Itère sur chaque image
            $identifiant_unique = uniqid(); // Crée un identifiant unique
            
            $file_extension = pathinfo($images['name'][$i], PATHINFO_EXTENSION); // Récupère l'extension du fichier

            $target_file = $target_dir . $identifiant_unique . "." . $file_extension; // Crée le chemin du fichier

            if (!move_uploaded_file($images['tmp_name'][$i], $target_file)) { // Si on ne peut pas déplacer le fichier
                $_SESSION['error_post'] = "Erreur lors de l'ajout de l'image.";
                header('Location: home.php');
                exit();
            }else {
                $sql = "INSERT INTO `post_images`(`post_id`, `image`) VALUES ((SELECT id FROM post WHERE author='$id' ORDER BY id DESC LIMIT 1),'$identifiant_unique.$file_extension')";
                try { // On essaye de faire la requête
                    mysqli_query($connexion, $sql);
                } catch (Exception $e) { // Si on a une erreur
                    $_SESSION['error_post'] = "Erreur lors de l'ajout de l'image.";
                    header('Location: home.php');
                    exit();
                }
            }
            ++$count; // On incrémente le compteur
        }
    }

}

if (isset($parent_target_id)) {
    $array = array("key1" => "value1", "key2" => "value2");

    echo $parent_target_id; // On renvoie l'id du commentaire ajouté
}

?>