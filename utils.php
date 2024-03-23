<?php

/**
 * Vérifie si l'utilisateur est connecté
 * 
 * @return void
 */
function checkCreds() {
    if (!isset($_SESSION['username'])) { // Vérifie si l'utilisateur est connecté a travers le cookie qui devrait être set
        $_SESSION['error'] = "You need to be logged in to access this page."; // Stocke un message d'erreur
        header('Location: logout.php'); // Redirige vers la page de déconnexion
        exit();
    }
}

/**
 * Vérifie si l'utilisateur follow déjà
 * 
 * @param connexion La connexion à la base de données
 * @param username Le nom d'utilisateur qui follow
 * @param followed Le nom d'utilisateur qui est follow
 * 
 * @return bool
 */
function isFollowing($connexion, $username, $followed) {
    $sql = "SELECT * FROM followers WHERE follower_id=(SELECT id FROM profil WHERE username = '$username') AND following_id=(SELECT id FROM profil WHERE username = '$followed')";

    try { // Essaie de récupérer les followers
        $result = mysqli_query($connexion, $sql);
        if (mysqli_num_rows($result) > 0) { // Vérifie si la requête a retourné des lignes
            return true; // Si oui, l'utilisateur follow déjà
        }
    } catch (Exception $e) { // Si ça échoue, affiche une erreur
        echo "<p> Erreur lors de la vérification du follow : " . mysqli_error($connexion) . "</p>";
    }

    return false; // Si la requête n'a pas retourné de lignes, l'utilisateur ne follow pas
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
                    echo "<a href='profil.php?profil_detail=" . urlencode($username) . "'>"; // Crée un lien vers le profil de l'auteur
                    if ($rowProfil['avatar'] != NULL) { // Si l'utilisateur a un avatar
                        echo "<img src='img/" . $rowProfil['avatar'] . "' alt='avatar' width='64' height='64' style='border-radius: 50%;border: solid 1px black;' id='avatar'> <br>";
                    } else { // Si l'utilisateur n'a pas d'avatar
                        echo "<img src='img/default_pfp.png' alt='avatar' width='64' height='64' style='border-radius: 50%;border: solid 1px black;' id='avatar'> <br>";
                    }
                    echo "</a>";
                echo "</div>";

                echo "<div id='pseudo-follow' class='col'>"; // Affiche le nom d'utilisateur et le nombre de followers et following
                    echo "<div id='pseudo'><a href='profil.php?profil_detail=" . urlencode($username) . "'>@" . $rowProfil["username"] . "</a></div>";
                    echo "<div id='sub'><a href='?displayFollower=true&username=". urlencode($username) ."' >" . $rowFollower["COUNT(follower_id)"] . " Followers</a> </div>";
                    echo "<div id='follow'><a href='?displayFollowing=true&username=". urlencode($username) ."' >". $rowFollowing["COUNT(following_id)"] ." Suivies</a> </div>";
                echo "</div>";

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
        $sql = "SELECT * FROM post ORDER BY date DESC"; // Crée la requête SQL pour récupérer tous les posts
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

            echo "<div class='post'>";
                // Récupère le nom de l'auteur
                $sql = "SELECT * FROM profil WHERE id=" . $row['author'];
                try { // Essaie de récupérer le nom de l'auteur
                    $profil = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                } catch (Exception $e) { // Si ça échoue, affiche une erreur
                    echo "Author: Error when trying to get the name. <br>";
                }

                echo "<div class='row'>";

                        //  Affiche l'avatar et le nom d'utilisateur de l'auteur
                        echo "<div class='col' id='post-avatar-username'>";
                            echo "<a href='profil.php?profil_detail=" . urlencode($profil['username']) . "'>"; // Crée un lien vers le profil de l'auteur
                            if ($profil['avatar'] != NULL) {
                                echo "<img src='img/" . $profil['avatar'] . "' alt='avatar' width='50' height='auto' style='border-radius: 50%;border: solid 1px black;'>"; // Affiche l'avatar de l'auteur
                            } else {
                                echo "<img src='img/default_pfp.png' alt='avatar' width='50' height='auto' style='border-radius: 50%;border: solid 1px black;'>"; // Affiche l'avatar par défaut
                            }
                            
                            echo "@" . $profil['username']; 
                            echo "</a>";
                        echo "</div>";
                        
                        echo "<div class='col' id='post-date'>";
                        echo $row["date"]; // Affiche la date du post
                        echo "</div>";

                echo "</div>";

                echo "<div class='container-fluid'>";
                    // Affiche les informations du post
                    echo "<div class='col' id='post-text'>" . $row["text"] . "</div>";

                    // Récupère le nombre de likes
                    $sql = "SELECT COUNT(post_id) FROM likes WHERE post_id=" . $row['id'];
                    try { // Essaie de récupérer le nombre de likes
                        $likes = mysqli_fetch_assoc(mysqli_query($connexion, $sql));
                    } catch (Exception $e) { // Si ça échoue, affiche une erreur
                        echo "Likes: Error when trying to get the number of likes. <br>";
                    }

                    // Récupère le nombre de commentaires
                    $sql = "SELECT * FROM likes WHERE post_id = " . $row["id"] . " AND user_id=(SELECT id FROM profil WHERE username = '" . $_SESSION['username'] . "')";

                    try { // Essaie de récupérer le like
                        $result = mysqli_query($connexion, $sql);
                    } catch (Exception $e) { // Si ça échoue, affiche une erreur
                        echo "<p> Erreur lors de la récupération du like : " . mysqli_error($connexion) . "</p>";
                    }

                    // Affiche le bouton pour liker
                    echo "<div class='row' id='like-comment'>";
                        echo "<div class='col'></div>";

                        echo    "<div class='col text-right'>";
                        // Crée un formulaire pour liker
                        echo    "<form method='post' action=''>";
                        echo            "<input type='hidden' name='post_id' value='" . $row["id"] . "'>";
                        echo            "<input type='hidden' name='liking' value='true'>";

                        if (mysqli_num_rows($result) > 0) { // Si l'utilisateur a déjà liké
                            // Affiche le bouton de like rempli
                            echo         "<input class='like-button' type='image' src='img/like_filled.png' width='20' height='20'  value='Like'> " . $likes['COUNT(post_id)'] . " <br>";
                        } else { // Si l'utilisateur n'a pas liké
                            // Affiche le bouton de like vide
                            echo         "<input class='like-button' type='image' src='img/like_empty.png' width='20' height='20' value='Like'> " . $likes['COUNT(post_id)'] . " <br>";
                        }
                        echo    "</form>";
                        echo   "</div>";


                        echo    "<div class='col text-left'>";
                        // Crée un formulaire pour commenter
                        echo    "<form method='post' action=''>";
                        echo            "<input type='hidden' name='post_id' value='" . $row["id"] . "'>";
                        echo            "<input type='hidden' name='commenting' value='true'>";
                        echo            "<input class='like-button' type='image' src='img/comment.png' width='20' height='20' value='Comment'> X <br>";
                        
                        echo    "</form>";
                        echo   "</div>";

                        echo "<div class='col'></div>";
                    echo "</div>";

                echo "</div>";

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
                    echo "<a href='profil.php?profil_detail=" . urlencode($profil['username']) . "'>"; // Crée un lien vers le profil de l'utilisateur qui suit
                    if ($profil['avatar'] != NULL) { // Si l'utilisateur qui suit a un avatar
                        echo "<img src='img/" . $profil['avatar'] . "' alt='avatar' width='50' height='auto' style='border-radius: 50%;border: solid 1px black;'>"; // Affiche l'avatar de l'utilisateur qui suit
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

?>