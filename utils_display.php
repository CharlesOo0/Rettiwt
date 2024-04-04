
<?php

/**
 * Fonction qui récupère l'arborescence des commentaires d'un post
 * 
 * @param connexion La connexion à la base de données
 * @param root L'id du post racine
 * @param parentId L'id du commentaire parent
 * 
 * @return array L'arborescence des commentaires
 */
function getComments($connexion, $root, $parentId = NULL) {

    if ($parentId === NULL) { // On est a la racine
        $sql = "SELECT * FROM post WHERE parent_id=$root"; // Crée la requête SQL pour récupérer les commentaires de la racine
    }else { // On est dans un commentaire
        $sql = "SELECT * FROM post WHERE parent_id=$parentId"; // Crée la requête SQL pour récupérer les commentaires du 
    }
    
    try { // Essaie de récupérer les commentaires
        $comments = mysqli_query($connexion, $sql);

    } catch (Exception $e) { // Si ça échoue
        return []; // Retourne un tableau vide
    }

    // Récupère les commentaires sous forme de tableau associatif
    $comments = mysqli_fetch_all($comments, MYSQLI_ASSOC);

    // Pour chaque commentaire, récupère les réponses
    foreach ($comments as &$comment) {
        // Récupère les réponses avec un appel récursif
        if ($comment['isDeleted'] == 0) {
            $comment['replies'] = getComments($connexion, $root, $comment['id']);
        }
    }
    
    return $comments; // Retourne les commentaires
}

/**
 * Fonction qui permet d'afficher le menu dropdown
 * 
 * @param postId L'id du post pour lequel on affiche le menu dropdown
 * 
 * @return void
 */
function displayDropdown($postId) {
    // Crée la requête SQL pour récupérer le profil de l'utilisateur connecté
    $sql = "SELECT * FROM profil WHERE username = '" . $_SESSION['username'] . "'";
    $connexion = connexion_mysqli();

    try { // Essaie de récupérer le profil de l'utilisateur connecté
        $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        $_SESSION['error_post'] = "Erreur en tentant d'afficher le dropdown."; // Stocke un message d'erreur
        echo "<meta http-equiv='refresh' content='0'>"; // Actualise la page
        exit();
    }

    // Crée la requête SQL pour récupérer le profil de l'utilisateur autheur du post
    $sql = "SELECT * FROM post WHERE id = $postId";

    try { // Essaie de récupérer le post
        $post = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        $_SESSION['error_post'] = "Erreur en tentant d'afficher le dropdown."; // Stocke un message d'erreur
        echo "<meta http-equiv='refresh' content='0'>"; // Actualise la page
        exit();
    }

    $isAuthor = $profil['id'] == $post['author']; // Vérifie si l'utilisateur est l'auteur du post

    if ($isAuthor == NULL && !$profil['isAdmin']) { // Si le post n'existe pas
        return; // Ne fait rien
    }

    // Sinon affiche le menu dropdown
    echo '<div class="dropdown dropdown-post">'; // Affiche le menu dropdown
    echo '    <button class="btn btn-secondary" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">';
    echo '        <i class="fas fa-ellipsis-v"></i>';
    echo '    </button>';
    echo '    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

    if ($isAuthor) { // Si l'utilisateur est l'auteur du post
        echo "  <li>
                    <button class='delete-post-button dropdown-item' data-post-id='" . $postId . "' data-username='".$post['author']."'>Supprimer</button>
                </li>";
    }

    if ($profil['isAdmin'] && !$isAuthor) { // Si l'utilisateur est un admin
        echo "  <li>
                    <button class='delete-admin-post-button dropdown-item' data-post-id='" . $postId . "' data-username='".$post['author']."'>Supprimer (Admin)</button>
                </li>";

        echo "  <li>
                    <button class='ban-post-button dropdown-item' data-post-id='" . $postId . "' data-username='".$post['author']."'>Bannir (Admin)</button>
                </li>";

        echo "  <li>
                    <button class='warn-post-button dropdown-item' data-post-id='" . $postId . "' data-username='".$post['author']."'>Avertissement (Admin)</button>
                </li>";
        
        echo "  <li>
                    <button id='warn-".$post['id']."' class='flag-post-button dropdown-item' data-post-id='" . $postId . "' data-username='".$post['author']."'>";

        if ($post['isFlag'] == 0) {
            echo "Flag";
        }else {
            echo "Unflag";
        
        }
        echo        "</button>";
        echo    "</li>";
    }
    echo '    </ul>';
    echo '</div>';
}

/** Fonction qui permet d'afficher le menu dropdown des profils
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur pour lequel on affiche le menu dropdown
 * 
 * @return void
 */
function displayProfilDropdown($connexion, $username) {
    // Crée la requête SQL pour récupérer le profil de l'utilisateur connecté
    $sql = "SELECT * FROM profil WHERE username = '" . $_SESSION['username'] . "'";

    try { // Essaie de récupérer le profil de l'utilisateur connecté
        $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        $_SESSION['error_post'] = "Erreur en tentant d'afficher le dropdown."; // Stocke un message d'erreur
        echo "<meta http-equiv='refresh' content='0'>"; // Actualise la page
        exit();
    }

    $sql = "SELECT * FROM profil WHERE username = '$username'";

    try { // Essaie de récupérer le profil de l'utilisateur
        $target = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        $_SESSION['error_post'] = "Erreur en tentant d'afficher le dropdown."; // Stocke un message d'erreur
        echo "<meta http-equiv='refresh' content='0'>"; // Actualise la page
        exit();
    }

    if ($profil['isAdmin'] == 0 || $profil['id'] == $target['id']) { // Si l'utilisateur n'est pas un admin et qu'il est le propriétaire du profil
        return; // Ne fait rien
    }

    // Sinon affiche le menu dropdown
    echo '<div class="dropdown dropdown-profil col-2">'; // Affiche le menu dropdown
    echo '    <button class="btn btn-secondary" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">';
    echo '        <i class="fas fa-ellipsis-v"></i>';
    echo '    </button>';
    echo '    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
            echo "  <li>
                <button class='ban-post-button dropdown-item' data-post-id='NULL' data-username='".$target['id']."'>Bannir (Admin)</button>
            </li>";
    echo '    </ul>';

    echo '</div>';

}

/**
 * Fonction qui affiche les commentaires
 * 
 * @param comments Les commentaires à afficher
 * 
 * @return void
 */
function displayComments($connexion, $comments) {
    echo "<div class='comments'>";
        foreach ($comments as $comment) { // Pour chaque commentaire
            echo "<div id='post-".$comment['id']."' class='container-fluid row comment-container'>";
                echo "<div class='comment col-1'>";
                echo "<div class='comment-line'></div>";
                echo "</div>";

                echo "<div class='post col'>";
                    // Récupère le nom de l'auteur
                    $sql = "SELECT * FROM profil WHERE id=" . $comment['author'];
                    try { // Essaie de récupérer le nom de l'auteur
                        $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                    } catch (Exception $e) { // Si ça échoue, affiche une erreur
                        echo "Author: Error when trying to get the name. <br>";
                    }

                    echo "<div class='row'>";

                            //  Affiche l'avatar et le nom d'utilisateur de l'auteur
                            echo "<div class='col post-avatar-username'>";
                                echo "<a href='home.php?profil_detail=" . urlencode($profil['username']) . "'>"; // Crée un lien vers le profil de l'auteur
                                if ($profil['avatar'] != NULL) {
                                    echo "<img src='pfp/" . $profil['avatar'] . "' alt='avatar' width='50' height='auto' style='border-radius: 50%;border: solid 1px black;'>"; // Affiche l'avatar de l'auteur
                                } else {
                                    echo "<img src='img/default_pfp.png' alt='avatar' width='50' height='auto' style='border-radius: 50%;border: solid 1px black;'>"; // Affiche l'avatar par défaut
                                }
                                
                                echo "@" . $profil['username']; 
                                echo "</a>";
                            echo "</div>";
                            
                            echo "<div class='col post-date'>";
                            echo $comment["date"]; // Affiche la date du commentaire

                            displayDropdown($comment['id']); // Affiche le menu dropdown

                            echo "</div>";
                    
                    echo "</div>";

                    echo "<div class='container-fluid'>";
                        // Affiche le texte du commentaire
                        echo "<div class='col post-text'>" . $comment["text"] . "</div>";

                        // Affiche les images du commentaire
                        $sql = "SELECT * FROM post_images WHERE post_id=" . $comment['id'];
                        echo "<div class='col post-img'>";
                        try { // Essaie de récupérer les images du commentaire
                            $images = mysqli_query($connexion, $sql);
                            if (mysqli_num_rows($images) > 2) { // Vérifie si la requête a retourné des lignes
                                while ($image = mysqli_fetch_assoc($images)) { // Pour chaque image
                                    echo "<img class='post-img-3' src='post_images/" . $image['image'] . "' alt='image'>"; // Affiche l'image
                                }
                            }else if (mysqli_num_rows($images) > 1) { // Vérifie si la requête a retourné des lignes
                                while ($image = mysqli_fetch_assoc($images)) { // Pour chaque image
                                    echo "<img class='post-img-2' src='post_images/" . $image['image'] . "' alt='image'>"; // Affiche l'image
                                }
                            }else if (mysqli_num_rows($images) > 0) { // Vérifie si la requête a retourné des lignes
                                while ($image = mysqli_fetch_assoc($images)) { // Pour chaque image
                                    echo "<img class='post-img-1' src='post_images/" . $image['image'] . "' alt='image'>"; // Affiche l'image
                                }
                            }
                        } catch (Exception $e) { // Si ça échoue, affiche une erreur
                            echo "Images: Error when trying to get the images. <br>";
                        }
                        echo "</div>";

                        // Récupère le nombre de likes
                        $sql = "SELECT COUNT(post_id) FROM likes WHERE post_id=" . $comment['id'];
                        try { // Essaie de récupérer le nombre de likes
                            $likes = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                        } catch (Exception $e) { // Si ça échoue, affiche une erreur
                            echo "Likes: Error when trying to get the number of likes. <br>";

                        }
                    
                        // Permet de savoir si l'utilisateur a liké le commentaire
                        $sql = "SELECT * FROM likes WHERE post_id = " . $comment["id"] . " AND user_id=(SELECT id FROM profil WHERE username = '" . $_SESSION['username'] . "')";
                        try { // Essaie de récupérer le like
                            $result = mysqli_query($connexion, $sql);
                        } catch (Exception $e) { // Si ça échoue, affiche une erreur
                            echo "<p> Erreur lors de la récupération du like : " . mysqli_error($connexion) . "</p>";
                        }

                        // Affiche le bouton pour liker
                        echo "<div class='row like-comment'>";
                            echo "<div class='col'></div>"; // Colonne pour centrer les boutons

                            echo    "<div class='col text-right'>";
                            // Crée un formulaire pour liker
                            echo    "<form class='like-form' method='post' action=''>";
                            echo            "<input type='hidden' name='post_id' value='" . $comment["id"] . "'>";
                            // echo            "<input type='hidden' name='liking' value='true'>"; Ca essaie sans ca

                            if (mysqli_num_rows($result) > 0) { // Si l'utilisateur a déjà liké
                                // Affiche le bouton de like rempli
                                echo         "<input class='like-button' type='image' src='img/like_filled.png' width='20' height='20'  value='Like'> <div class='like-count' style='display:inline-block'>" . $likes['COUNT(post_id)'] . "</div> <br>";
                            } else { // Si l'utilisateur n'a pas liké
                                // Affiche le bouton de like vide
                                echo         "<input class='like-button' type='image' src='img/like_empty.png' width='20' height='20' value='Like'> <div class='like-count' style='display:inline-block'>" . $likes['COUNT(post_id)'] . " </div><br>";
                            }
                            echo    "</form>";
                            echo   "</div>";

                            echo    "<div class='col text-left'>";
                            // Crée un bouton pour afficher le formulaire de like du commentaire
                            echo    "<button class='comment-button' name='post_id' data-parent-id='".$comment["id"]."' data-post-id='".$comment["parent_id"]."' data-identifier-id='".$comment["id"]."'> <img src='img/comment.png' width='20' height='20'> </button> ". count($comment['replies']) ." <br>";
                            echo    "</div>";

                            echo "<div class='col'></div>"; // Colonne pour centrer les boutons

                        echo "</div>";

                    echo "</div>"; // Fin de la div container-fluid     

                    echo "<div class='show-more'>";
                    if (count($comment['replies']) > 0) { // Si le commentaire a des réponses
                        echo "<button class='show-hidde-comment-button' id='show-button-".$comment["id"]."' value='".$comment["id"]."'>Afficher les commentaires</button>"; // Affiche un bouton pour afficher les réponses
                    }
                    echo "</div>";

                echo "</div>";
            echo "</div>";

            
            
            echo "<div style='display: none;' id='comment-".$comment["id"]."'>"; // Crée une div pour afficher les réponses
            // Affiche les réponses
            if (isset($comment['replies'])) { // Si le commentaire a des réponses
                displayComments($connexion, $comment['replies']); // Affiche les réponses
            }
            echo "</div>";
        }

    echo "</div>";
}

/**
 * Affiche le profil de l'utilisateur
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qu'on vise pour afficher le profil
 * 
 * @return void
 */
function displayProfil($connexion, $username) {

        // Crée la requête SQL pour récupérer le profil
        $sql = "SELECT * FROM profil WHERE username='$username'";

        $recuperationProfilFailed = false;
        try {  // Essaie de récupérer le profil
            $resultProfil = mysqli_query($connexion, $sql);
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            echo "<p> Erreur lors de la récupération du profil : " . mysqli_error($connexion) . "</p>";
            $recuperationProfilFailed = true;
        }

        // Crée la requête SQL pour récupérer le nombre de followers
        $sql = " SELECT COUNT(follower_id) FROM followers WHERE following_id = (SELECT id FROM profil WHERE username = '$username') ";

        try { // Essaie de récupérer le nombre de followers
            $resultFollower = mysqli_query($connexion, $sql);
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            echo "<p> Erreur lors de la récupération du nombre de followers : " . mysqli_error($connexion) . "</p>";
            $recuperationProfilFailed = true;
        }

        // Crée la requête SQL pour récupérer le nombre de following
        $sql = " SELECT COUNT(following_id) FROM followers WHERE follower_id = (SELECT id FROM profil WHERE username = '$username') ";

        try { // Essaie de récupérer le nombre de following
            $resultFollowing = mysqli_query($connexion, $sql);
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            echo "<p> Erreur lors de la récupération du nombre de following : " . mysqli_error($connexion) . "</p>";
            $recuperationProfilFailed = true;
        }

        if (mysqli_num_rows($resultProfil) > 0 && !$recuperationProfilFailed) { // Vérifie si la requête a retourné des lignes et qu'elle n'a pas échoué
            // Affiche les données de l'utilisateur
            $rowProfil = mysqli_fetch_assoc($resultProfil);
            $rowFollower = mysqli_fetch_assoc($resultFollower);
            $rowFollowing = mysqli_fetch_assoc($resultFollowing);


            echo "<div id='profil-detail' class='row'>"; 

                echo "<div id='avatar' class='col'>";
                    // Affiche l'avatar de l'utilisateur
                    echo "<a href='home.php?profil_detail=" . urlencode($username) . "'>"; // Crée un lien vers le profil de l'auteur
                    if ($rowProfil['avatar'] != NULL) { // Si l'utilisateur a un avatar
                        echo "<img src='pfp/" . $rowProfil['avatar'] . "' alt='avatar' width='64' height='64' style='border-radius: 50%;border: solid 1px black;' id='avatar'> <br>";
                    } else { // Si l'utilisateur n'a pas d'avatar
                        echo "<img src='img/default_pfp.png' alt='avatar' width='64' height='64' style='border-radius: 50%;border: solid 1px black;' id='avatar'> <br>";
                    }
                    echo "</a>";
                echo "</div>";

                echo "<div id='pseudo-follow' class='col'>"; // Affiche le nom d'utilisateur et le nombre de followers et following

                if ($username != $_SESSION['username']) {
                    echo "<div class='follow-sub-href'>"; // Affiche le bouton pour follow ou unfollow l'utilisateur
                    if (isFollowing($connexion, $_SESSION['username'], $username)){ // Si l'utilisateur connecté follow déjà l'autre utilisateur
                        echo "<a href='home.php?follow=" . urlencode($username) . "&profil_detail=". urlencode($username) ."'>Désabonner</a>"; // On affiche un lien pour donner l'option de pouvoir unfollow l'autre utilisateur
                    }else {// Sinon (si l'utilisateur connecté ne follow pas l'autre utilisateur) 
                        echo "<a href='home.php?follow=" . urlencode($username) . "&profil_detail=". urlencode($username) ."'>S'abonner</a>"; // On affiche un lien pour donner l'option de pouvoir follow l'autre utilisateur
                    }
                    echo "</div>";
                }
                    echo "<div id='pseudo'><a href='home.php?profil_detail=" . urlencode($username) . "'>@" . $rowProfil["username"] . "</a></div>";
                    echo "<div id='sub'><a href='?displayFollower=true&username=". urlencode($username) ."' >" . $rowFollower["COUNT(follower_id)"] . " Followers</a> </div>";
                    echo "<div id='follow'><a href='?displayFollowing=true&username=". urlencode($username) ."' >". $rowFollowing["COUNT(following_id)"] ." Suivies</a> </div>";
                echo "</div>";

                displayProfilDropdown($connexion, $username); // Affiche le menu dropdown


            echo "</div>";

            echo "<div id='bio' class='row'>"; // Affiche la biographie de l'utilisateur
                echo "<div id='bio-text'> <span id='bio-text-title'>Biographie :</span><br>" . $rowProfil["bio"] . " </div>";
            echo "</div>";

        }
}


/**
 * Affiche les posts de l'utilisateur
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qu'on vise pour afficher les posts (NULL pour tous les utilisateurs)
 * @param sub Le sujet des posts à afficher (NULL pour tous les sujets sinon affiche les posts des utilisateurs suivis si username est défini)
 * 
 * @return void
 */
function displayPost($connexion, $username, $sub) {
    if ($username != NULL) { // Si on veut afficher les posts d'un utilisateur précis
        $sql = "SELECT * FROM profil WHERE username='$username'";  // Crée la requête SQL pour récupérer l'id de l'utilisateur

        $recuperationProfilFailed = false;
        try {  // Essaie de récupérer l'id de l'utilisateur
            $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
        } catch (Exception $e) { // Si ça échoue, affiche une erreur
            echo "<p> Erreur lors de la récupération du profil : " . mysqli_error($connexion) . "</p>";
            $recuperationProfilFailed = true;
        }

        if ($profil == NULL) { // Si l'utilisateur n'existe pas
            echo "Aucun résultat trouvé."; // Affiche un message d'erreur
            return;
        }

        $profilId = $profil['id']; // Récupère l'id de l'utilisateur
        
    
        if ($sub != NULL) { // Si on veut afficher les posts des utilisateurs suivis
            // Crée la requete qui permet de récuperer les posts des utilisateurs suivis
            $sql = "SELECT * FROM post WHERE author IN (SELECT following_id FROM followers WHERE follower_id='$profilId') ORDER BY date DESC";
        }else { // Si on veut afficher les posts de l'utilisateur
            // Crée la requête SQL pour récupérer les posts de l'utilisateur
            $sql = "SELECT * FROM post WHERE author='$profilId' ORDER BY date DESC";
        }

    }else { // Si on veut afficher les posts de tous les utilisateurs
        $sql = "SELECT * FROM post WHERE isDeleted=0 AND parent_id IS NULL ORDER BY date DESC"; // Crée la requête SQL pour récupérer tous les posts
    }

    $recuperationPostFailed = false;
    try {  // Essaie de récupérer les posts
        $resultPost = mysqli_query($connexion, $sql);
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la récupération des posts : " . mysqli_error($connexion) . "</p>";
        $recuperationPostFailed = true;
    }

    if (mysqli_num_rows($resultPost) > 0  && !$recuperationPostFailed) { // Vérifie si la requête a retourné des lignes et qu'elle n'a pas échoué
        // Affiche les données de chaque ligne
        while ($row = mysqli_fetch_assoc($resultPost)) { // Pour chaque post

            echo "<div id='post-".$row['id']."' class='post'>";
                // Récupère le nom de l'auteur
                $sql = "SELECT * FROM profil WHERE id=" . $row['author'];
                try { // Essaie de récupérer le nom de l'auteur
                    $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                } catch (Exception $e) { // Si ça échoue, affiche une erreur
                    echo "Author: Error when trying to get the name. <br>";
                }

                echo "<div class='row'>";

                        //  Affiche l'avatar et le nom d'utilisateur de l'auteur
                        echo "<div class='col post-avatar-username'>";
                            echo "<a href='home.php?profil_detail=" . urlencode($profil['username']) . "'>"; // Crée un lien vers le profil de l'auteur
                            if ($profil['avatar'] != NULL) {
                                echo "<img src='pfp/" . $profil['avatar'] . "' alt='avatar' width='50' height='auto' style='border-radius: 50%;border: solid 1px black;'>"; // Affiche l'avatar de l'auteur
                            } else {
                                echo "<img src='img/default_pfp.png' alt='avatar' width='50' height='auto' style='border-radius: 50%;border: solid 1px black;'>"; // Affiche l'avatar par défaut
                            }
                            
                            echo "@" . $profil['username']; 
                            echo "</a>";
                        echo "</div>";

                        echo "<div class='col post-date'>";

                            echo '<div class="row">';
                                echo $row["date"]; // Affiche la date du post
                                displayDropdown($row["id"]); // Affiche le menu dropdown
                            echo '</div>';

                        echo "</div>";

                echo "</div>";

                echo "<div class='container-fluid'>";
                    // Affiche le texte du post
                    echo "<div class='col post-text'>" . $row["text"] . "</div>";

                    // Affiche les images du post
                    $sql = "SELECT * FROM post_images WHERE post_id=" . $row['id'];
                    echo "<div class='col post-img'>";
                    try { // Essaie de récupérer les images du post
                        $images = mysqli_query($connexion, $sql);
                        if (mysqli_num_rows($images) > 2) { // Vérifie si la requête a retourné des lignes
                            while ($image = mysqli_fetch_assoc($images)) { // Pour chaque image
                                echo "<img class='post-img-3' src='post_images/" . $image['image'] . "' alt='image'>"; // Affiche l'image
                            }
                        }else if (mysqli_num_rows($images) > 1) { // Vérifie si la requête a retourné des lignes
                            while ($image = mysqli_fetch_assoc($images)) { // Pour chaque image
                                echo "<img class='post-img-2' src='post_images/" . $image['image'] . "' alt='image'>"; // Affiche l'image
                            }
                        }else if (mysqli_num_rows($images) > 0) { // Vérifie si la requête a retourné des lignes
                            while ($image = mysqli_fetch_assoc($images)) { // Pour chaque image
                                echo "<img class='post-img-1' src='post_images/" . $image['image'] . "' alt='image'>"; // Affiche l'image
                            }
                        }
                    } catch (Exception $e) { // Si ça échoue, affiche une erreur
                        echo "Images: Error when trying to get the images. <br>";
                    }
                    echo "</div>";
                    // Récupère le nombre de likes
                    $sql = "SELECT COUNT(post_id) FROM likes WHERE post_id=" . $row['id'];
                    try { // Essaie de récupérer le nombre de likes
                        $likes = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                    } catch (Exception $e) { // Si ça échoue, affiche une erreur
                        echo "Likes: Error when trying to get the number of likes. <br>";
                    }

                    // Permet de savoir si l'utilisateur a liké le post
                    $sql = "SELECT * FROM likes WHERE post_id = " . $row["id"] . " AND user_id=(SELECT id FROM profil WHERE username = '" . $_SESSION['username'] . "')";

                    try { // Essaie de récupérer le like
                        $result = mysqli_query($connexion, $sql);
                    } catch (Exception $e) { // Si ça échoue, affiche une erreur
                        echo "<p> Erreur lors de la récupération du like : " . mysqli_error($connexion) . "</p>";
                    }

                    // Récupère les commentaires
                    $sql = "SELECT * FROM post WHERE parent_id=" . $row['id'];

                    try { // Essaie de récupérer les commentaires
                        $comments = mysqli_query($connexion, $sql);
                    } catch (Exception $e) { // Si ça échoue, affiche une erreur
                        echo "Comments: Error when trying to get the comments. <br>";
                    }

                    // Affiche le bouton pour liker
                    echo "<div class='row like-comment'>";
                        echo "<div class='col'></div>"; // Colonne pour centrer les boutons

                        echo    "<div class='col text-right'>";
                        // Crée un formulaire pour liker
                        echo    "<form class='like-form' method='post' action=''>";
                        echo            "<input type='hidden' name='post_id' value='" . $row["id"] . "'>";
                        // echo            "<input type='hidden' name='liking' value='true'>"; Ca essaie sans ca

                        if (mysqli_num_rows($result) > 0) { // Si l'utilisateur a déjà liké
                            // Affiche le bouton de like rempli
                            echo         "<input class='like-button' type='image' src='img/like_filled.png' width='20' height='20'  value='Like'> <div class='like-count' style='display:inline-block'>" . $likes['COUNT(post_id)'] . "</div> <br>";
                        } else { // Si l'utilisateur n'a pas liké
                            // Affiche le bouton de like vide
                            echo         "<input class='like-button' type='image' src='img/like_empty.png' width='20' height='20' value='Like'> <div class='like-count' style='display:inline-block'>" . $likes['COUNT(post_id)'] . " </div><br>";
                        }
                        echo    "</form>";
                        echo   "</div>";

                        $identifiant_comment = uniqid(); // Identifiant unique pour les commentaires

                        echo    "<div class='col text-left'>";
                        // Crée un bouton pour afficher le formulaire de like du post
                        echo    "<button class='comment-button' name='post_id' data-post-id='".$row["id"]."' data-parent-id='NULL' data-identifier-id='".$identifiant_comment."'> <img src='img/comment.png' width='20' height='20'> </button> ". $comments->num_rows ." <br>";
                        echo    "</div>";

                        echo "<div class='col'></div>"; // Colonne pour centrer les boutons
                    echo "</div>";

                echo "</div>"; // Fin de la div container-fluid

            echo "</div>";

            if ($comments->num_rows > 0) { // Si le post a des commentaires
                echo "<button class='show-hidde-comment-button' id='show-button-".$identifiant_comment."' value='".$identifiant_comment."'>Afficher les commentaires</button>";
            }

            echo "<div style='display: none;' id='comment-".$identifiant_comment."'>"; // Crée une div pour afficher les commentaires
                // Affiche les commentaires
                $connexion_mysqli = connexion_mysqli(); // Connexion à la base de données
                $comments = getComments($connexion_mysqli, $row['id'], NULL); // Récupère les commentaires
                displayComments($connexion_mysqli, $comments); // Affiche les commentaires
            echo "</div>";
        }
    } else {
        echo "Aucun résultat trouvé.";
    }
}

/**
 * Fonction qui affiche les utilisateurs qui suivent
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qui follow
 * @param mode Le mode d'affichage 0 pour les followers et 1 pour les following
 * 
 * @return void
 */
function displayFollow($connexion, $username, $mode) {
    // Crée la requête SQL pour récupérer les utilisateurs qui suivent

    if ($mode == 0) {// Si on veut afficher les followers
        $sql = "SELECT * FROM followers WHERE following_id=(SELECT id FROM profil WHERE username = '$username')";
    }else { // Si on veut afficher les following
        $sql = "SELECT * FROM followers WHERE follower_id=(SELECT id FROM profil WHERE username = '$username')";
    }

    try { // Essaie de récupérer les utilisateurs qui suivent
        $result = mysqli_query($connexion, $sql);
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la récupération des utilisateurs qui suivent : " . mysqli_error($connexion) . "</p>";
    }

    if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
        // Affiche les données de chaque ligne
        while ($row = mysqli_fetch_assoc($result)) { // Pour chaque utilisateur qui suit
            // Récupère le nom d'utilisateur qui suit
            if ($mode == 0) { // Si on veut afficher les followers
                $sql = "SELECT * FROM profil WHERE id=" . $row['follower_id'];
            } else { // Si on veut afficher les following
                $sql = "SELECT * FROM profil WHERE id=" . $row['following_id'];
            }

            try { // Essaie de récupérer le nom d'utilisateur qui suit
                $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
            } catch (Exception $e) { // Si ça échoue, affiche une erreur
                echo "Follower: Error when trying to get the name. <br>";
            }

            // Récupère le nombre de follower et following de l'utilisateur qui suit
            $sql = "SELECT COUNT(follower_id) FROM followers WHERE following_id = (SELECT id FROM profil WHERE username = '" . $profil['username'] . "')";
            try { // Essaie de récupérer le nombre de followers
                $follower = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
            } catch (Exception $e) { // Si ça échoue, affiche une erreur
                echo "Follower: Error when trying to get the number of followers. <br>";
            }

            // Récupère le nombre de following de l'utilisateur qui suit
            $sql = "SELECT COUNT(following_id) FROM followers WHERE follower_id = (SELECT id FROM profil WHERE username = '" . $profil['username'] . "')";
            try { // Essaie de récupérer le nombre de following
                $following = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
            } catch (Exception $e) { // Si ça échoue, affiche une erreur
                echo "Follower: Error when trying to get the number of following. <br>";
            }
            

            echo "<div class='follow row'>";

                echo "<div class='follow-info col'>";
                    // Affiche l'avatar et le nom d'utilisateur de l'utilisateur qui suit
                    echo "<a href='home.php?profil_detail=" . urlencode($profil['username']) . "'>"; // Crée un lien vers le profil de l'utilisateur qui suit
                    if ($profil['avatar'] != NULL) { // Si l'utilisateur qui suit a un avatar
                        echo "<img src='pfp/" . $profil['avatar'] . "' alt='avatar' width='50' height='auto' style='border-radius: 50%;border: solid 1px black;'>"; // Affiche l'avatar de l'utilisateur qui suit
                    } else { // Si l'utilisateur qui suit n'a pas d'avatar
                        echo "<img src='img/default_pfp.png' alt='avatar' width='50' height='auto' style='border-radius: 50%;border: solid 1px black;'>"; // Affiche l'avatar par défaut
                    }
                    echo "@" . $profil['username']; // Affiche le nom d'utilisateur qui suit
                    echo "</a>";
                echo "</div>";

                echo "<div class='follow-sub-info col'>";
                    // Affiche le bouton pour follow ou unfollow l'utilisateur qui suit
                    if ($_SESSION['username'] != $profil['username']) { // Si l'utilisateur qui suit n'est pas l'utilisateur connecté
                        if(isFollowing($connexion, $_SESSION['username'], $profil['username'])) {  // Si l'utilisateur connecté follow déjà l'utilisateur qui suit
                            // Affiche un lien pour donner l'option de pouvoir unfollow l'utilisateur qui suit
                            echo "<div class='follow-sub-href'>"; 
                                echo "<a href='home.php?follow=" . urlencode($profil['username']) . "&profil_detail=". urlencode($profil['username']) ."'>Désabonner</a>"; // Affiche un lien pour donner l'option de pouvoir unfollow l'utilisateur qui suit
                            echo "</div>";
                        } else { // Si l'utilisateur connecté ne follow pas l'utilisateur qui suit
                            // Affiche un lien pour donner l'option de pouvoir follow l'utilisateur qui suit
                            echo "<div class='follow-sub-href'>";
                                echo "<a href='home.php?follow=" . urlencode($profil['username']) . "&profil_detail=". urlencode($profil['username']) ."'>S'abonner</a>"; // Affiche un lien pour donner l'option de pouvoir follow l'utilisateur qui suit
                            echo "</div>";
                        }
                    }

                    // Affiche le nombre de followers et following de l'utilisateur qui suit
                    echo "<div class='follow-info-text'><a href='?displayFollower=true&username=". urlencode($profil['username']) ."' >" . $follower['COUNT(follower_id)'] . " Abonnés</a></div>"; // Affiche le nombre de followers de l'utilisateur qui suit
                    echo "<div class='follow-info-text'><a href='?displayFollowing=true&username=". urlencode($profil['username']) ."' >" . $following['COUNT(following_id)'] . " Suivies</a></div>"; // Affiche le nombre de following de l'utilisateur qui suit

                echo "</div>";
            echo "</div>";
        }
    } else {
        echo "Aucun résultat trouvé.";
    }
}

/**
 * Affiche le formulaire de commentaire
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qui commente
 * 
 * @return void
 */
function displayCommentForm($connexion, $username) {
    echo "<form class='comment-form' method='post' action='home.php' enctype='multipart/form-data'>";
        echo "<h4 id='comment-form-title'>Commenter</h4>";
        echo "<input type='hidden' id='comment-post-id' name='post_id' value=''>";
        echo "<input type='hidden' id='comment-parent-id' name='parent_id' value=''>";
        echo "<input type='hidden' id='identifier-id' name='identifier_id' value=''>";
        echo "<input type='hidden' name='commenting' value='true'>";

        echo "<div class='comment-form-input col'>";
            echo "<textarea id='comment-form-textarea' class='row' type='textarea' name='text' placeholder='Commenter'></textarea>";
            echo "<input id='comment-form-file' class='row' type='file' name='comment_images[]' multiple>";
        echo "</div>";

        echo "<button id='close-comment-form' type='button'>Fermer</button>";
        echo "<input type='submit' value='Commenter'>";
    echo "</form>";
}

/**
 * Affiche les logs admins
 * 
 * @param connexion La connexion à la base de données
 * 
 * @return void
 */
function displayLogs($connexion) {
    // Crée la requête SQL pour récupérer les logs
    $sql = "SELECT * FROM admin_logs ORDER BY date DESC";

    try { // Essaie de récupérer les logs
        $result = mysqli_query($connexion, $sql);
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la récupération des logs : " . mysqli_error($connexion) . "</p>";
    }

    if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
        // Affiche les données de chaque ligne
        echo "<div class='table-responsive'>"; 

            echo "<table class='logs container-fluid'>"; 

            echo "<tr>";  // Crée une ligne pour les titres
            echo "<th>Admin</th>"; 
            echo "<th>Utilisateur cible</th>"; 
            echo "<th>Action</th>"; 
            echo "<th>Motif</th>"; 
            echo "<th>Date</th>"; 
            echo "</tr>";

            while ($row = mysqli_fetch_assoc($result)) { // For each log
                // Récupère le profil de l'admin
                $sql = "SELECT * FROM profil WHERE id=" . $row['admin_id'];

                try { // Essaie de récupérer le profil de l'admin
                    $admin = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                } catch (Exception $e) { // Si ça échoue, affiche une erreur
                    echo "Admin: Error when trying to get the name. <br>";
                }

                // Récupère le profil de l'utilisateur ciblé
                $sql = "SELECT * FROM profil WHERE id=" . $row['target_user_id'];

                try { // Essaie de récupérer le profil de l'utilisateur ciblé
                    $target_user = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                } catch (Exception $e) { // Si ça échoue, affiche une erreur
                    echo "Target user: Error when trying to get the name. <br>";
                }

                echo "<tr class='log'>"; // Crée une ligne pour chaque log
                echo "<td>" . $admin['username'] . "</td>"; 
                echo "<td>" . $target_user['username'] . "</td>"; 
                if ($row['action_type'] == 'ban') {
                    echo "<td>" . $row['action_type'] . "<button class='unban-log-button dropdown-item' data-user-id='".$target_user['id']."'>Unban ?</button></td>";
                }else {
                    echo "<td>" . $row['action_type'] . "</td>"; 
                }
                echo "<td>" . $row['reason'] . "</td>"; 
                echo "<td>" . $row['date'] . "</td>"; 
                echo "</tr>"; 
            }
            echo "</table>";

        echo "</div>"; 
    } else {
        echo "Aucun résultat trouvé.";
    }

}

?>
