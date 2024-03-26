<?php
/**
 * Gère les likes
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qui like
 * 
 * @return void
 */
function handleLike($connexion, $username) {

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id']) && isset($_POST['liking'])) { // Si on post sur cette page alors on veux liker un post
        $id = $_POST['post_id']; // Récupère l'id du post 

        $sql = "INSERT INTO likes (post_id, user_id) VALUES ('$id', (SELECT id FROM profil WHERE username = '$username'))"; // Crée la requête SQL pour ajouter un like

        try { // Essaie d'ajouter un like
            mysqli_query($connexion, $sql); 
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $error = mysqli_error($connexion); // Récupère l'erreur
            if (strpos($error, 'Duplicate entry') !== false) { // Si l'erreur est un doublon
                $sql = "DELETE FROM likes WHERE post_id='$id' AND user_id=(SELECT id FROM profil WHERE username = '$username')"; // Crée la requête SQL pour retirer le like
                try { // Essaie de retirer le like
                    mysqli_query($connexion, $sql);  
                } catch (Exception $e) { // Si ça échoue, affiche une erreur
                    echo "<p> Erreur lors de la suppression du like : " . mysqli_error($connexion) . "</p>";
                }
            }else { // Si l'erreur n'est pas un doublon 
                echo "<p> Erreur lors de l'ajout du like : " . mysqli_error($connexion) . "</p>"; // Affiche une erreur
            }
        }

    }
}

/**
 * Gère les followers
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qui follow
 * 
 * @return void
 */
function handleFollow($connexion, $username) {
    if (isset($_GET['follow'])) { // Si on a un GET request avec un follow
        $followed = $_GET['follow']; // Récupère le nom d'utilisateur à follow
        // Crée la requête SQL pour ajouter ou retirer un follow
        $sql = "INSERT INTO followers (follower_id, following_id) VALUES ((SELECT id FROM profil WHERE username = '$username'), (SELECT id FROM profil WHERE username = '$followed'))";

        try { // Essaie d'ajouter un follow
            mysqli_query($connexion, $sql);
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            $error = mysqli_error($connexion);
            if (strpos($error, 'Duplicate entry') !== false) { // Si l'erreur est un doublon
                // Crée la requête SQL pour retirer le follow
                $sql = "DELETE FROM followers WHERE follower_id=(SELECT id FROM profil WHERE username = '$username') AND following_id=(SELECT id FROM profil WHERE username = '$followed')";
                try { // Essaie de retirer le follow
                    mysqli_query($connexion, $sql);
                } catch (Exception $e) { // Si ça échoue, affiche une erreur
                    echo "<p> Erreur lors de la suppression du follow : " . mysqli_error($connexion) . "</p>";
                }
            }else { // Si l'erreur n'est pas un doublon
                echo "<p> Erreur lors de l'ajout du follow : " . mysqli_error($connexion) . "</p>"; // Affiche une erreur
            }
        }
    }
}

/**
 * Gère les post de posts
 * 
 * @param connexion La connexion à la base de données
 * 
 * @return void
 */
function handlePost($connexion) {
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
            $_SESSION['error_post'] = "Votre message est trop long pas plus de 270 charactères.";
            header('Location: home.php');
            exit();
        }

        if ($_FILES['images'] && is_array($_FILES['images']['name'])) { // Si on a des images
            $images = $_FILES['images']; // On récupère les images

            if (count($_FILES['images']['name']) > 3) { // Si on a plus de 3 images
                $_SESSION['error_post'] = "Vous ne pouvez pas ajouter plus de 3 images.";
                header('Location: home.php');
                exit();
            }

            $size = 0;
            for ($i = 0; $i < count($_FILES['images']['name']); $i++) { // Itère sur chaque image
                // Ajoute la taille de l'image à la taille totale
                $size += $images['size'][$i];
            }

            if ($size > 1000000) { // Si la taille totale des images dépasse 1Mo
                $_SESSION['error_post'] = "La taille totale des images est supérieur a 1Mo.";
                header('Location: home.php');
                exit();
            }
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

        if (isset($images) && count($_FILES['images']['name']) > 1) { // Si on a des images
            $count = 0; // Compteur pour les images
            $target_dir = "post_images/"; // Le dossier où on va stocker les images
            for ($i = 0; $i < count($_FILES['images']['name']); $i++) { // Itère sur chaque image
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
                        echo "Erreur : " . $e->getMessage();
                        exit();
                    }
                }
                ++$count; // On incrémente le compteur
            }
        }

    }
}

/**
 * Gère les commentaires
 * 
 * @param connexion La connexion à la base de données
 * 
 * @return void
 */
function handleComment($connexion) {
    // -------------------------- Crée un commentaire -------------------------- //
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['text']) && isset($_POST['commenting'])){ // Si on post sur cette page

        if (!isset($_SESSION['username'])) { // Si l'utilisateur n'est pas connecté
            $_SESSION['error'] = "Vous avez besoin d'être connecter pour poster un message.";
            header('Location: logout.php');
            exit();
        }

        if (!isset($_POST['post_id'])) { // Si on a pas d'id de post
            $_SESSION['error_post'] = "Erreur lors de la récupération du post.";
            header('Location: home.php');
            exit();
        }

        if (!isset($_POST['parent_id'])) { // Si on a pas d'id de parent
            $_SESSION['error_post'] = "Erreur lors de la récupération du parent.";
            header('Location: home.php');
            exit();
        }

        $post_id = $_POST['post_id']; // On récupère l'id du post
        $parent_id = $_POST['parent_id']; // On récupère l'id du parent
        // Initialise un booléen pour savoir si on peut poster
        $modify = true; 
        $text = htmlspecialchars($_POST['text']); // On récupère le texte

        if (empty($text)) { // Si le message est vide
            $modify = false; // On ne peut pas poster
        }

        if (strlen($text) > 270) { // Si le message est trop long
            $_SESSION['error_post'] = "Votre message est trop long pas plus de 270 charactères.";
            header('Location: home.php');
            exit();
        }

        if ($_FILES['comment_images'] && is_array($_FILES['comment_images']['name'])) { // Si on a des images
            $images = $_FILES['comment_images']; // On récupère les images

            if (count($_FILES['comment_images']['name']) > 3) { // Si on a plus de 3 images
                $_SESSION['error_post'] = "Vous ne pouvez pas ajouter plus de 3 images.";
                header('Location: home.php');
                exit();
            }

            $size = 0;
            for ($i = 0; $i < count($_FILES['comment_images']['name']); $i++) { // Itère sur chaque image
                // Ajoute la taille de l'image à la taille totale
                $size += $images['size'][$i];
            }

            if ($size > 1000000) { // Si la taille totale des images dépasse 1Mo
                $_SESSION['error_post'] = "La taille totale des images est supérieur a 1Mo.";
                header('Location: home.php');
                exit();
            }
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
        if ($parent_id != 'NULL') {
            $sql = "INSERT INTO post (author, text, parent_id) VALUES ('$id', '$text', '$parent_id')";
        }else {
            $sql = "INSERT INTO post (author, text, parent_id) VALUES ('$id', '$text', '$post_id')";
        }

        if ($modify) { //
            try { // On essaye de faire la requête
                mysqli_query($connexion, $sql);
            } catch (Exception $e) { // Si on a une erreur
                $_SESSION['error_post'] = "<p>Erreur lors de la création de votre post...</p>";
                header('Location: home.php');
                exit();
            }

            if (isset($images) && count($images['name']) > 1) { // Si on a des images
                $count = 0; // Compteur pour les images
                $target_dir = "post_images/"; // Le dossier où on va stocker les images
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
                            echo "Erreur : " . $e->getMessage();
                            exit();
                        }
                    }
                    ++$count; // On incrémente le compteur
                }
            }
        }


    }
}
?>