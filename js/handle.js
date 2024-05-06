import { initialisePopup } from './popup.js';

/**
 * Fonction qui affiche ou cache les commentaires
 * 
 * @param commentId L'id du commentaire à afficher ou cacher
 * 
 * @return void
 */
function showComment(commentId) {
    var commentElement = document.querySelector('#comment-' + commentId); // Récupère l'élément du commentaire
    var showButton = document.querySelector('#show-button-' + commentId); // Récupère le bouton pour afficher ou cacher les commentaires
    if (commentElement.style.display === "none") { // Si les commentaires sont cachés
        commentElement.style.display = "block";  // Affiche les commentaires
        showButton.innerHTML = "Cacher les commentaires"; // Change le texte du bouton
    } else { // Si les commentaires sont affichés
        commentElement.style.display = "none"; // Cache les commentaires
        showButton.innerHTML = "Afficher les commentaires"; // Change le texte du bouton
    }
}


function initialise() {
    console.log("Initialisation des fonctions");
    // ------------------------ Handle le like
    $(".like-form").submit(function(e) { // Quand le formulaire est soumis
        e.preventDefault(); // Empêcher le comportement par défaut du formulaire
        var form = $(this);  // Récupérer le formulaire

        if (!form.data().hasOwnProperty('eventBound')) { // Si l'évènement n'est pas bind
            form.data('eventBound', true); // On bind l'évènement

            $.ajax({ // Fait une requête AJAX
                type: "POST",  
                url: "ajax_request/handleLike.php",
                data: form.serialize(),
                success: function(data) { // Quand la requête est terminée
                    if (data == 1) { // Si la requête a réussi
                        var likeCount = form.find(".like-count"); 
                        var newCount = parseInt(likeCount.text()) + 1; // Ajoute 1 au texte de l'élément 
                        likeCount.text(newCount); // Met à jour le texte de l'élément
                        form.find(".like-button").attr("src", "img/like_filled.png")
                    } else { // Si la requête a échoué
                        var likeCount = form.find(".like-count");
                        var newCount = parseInt(likeCount.text()) - 1; // Enlève 1 au texte de l'élément
                        likeCount.text(newCount); // Met à jour le texte de l'élément
                        form.find(".like-button").attr("src", "img/like_empty.png")
                    }

                    form.removeData('eventBound');
                }
            });
        }
    });

    // ------------------------ Handle le signalement
    const popupAdminContainer = document.querySelector('.admin-container');  // Conteneur du popup

    $("#admin-form").submit(function(e) { // Quand le formulaire est soumis
        e.preventDefault(); // Empêcher le comportement par défaut du formulaire
        var form = $(this);  // Récupérer le formulaire

        if (!form.data().hasOwnProperty('eventBound')) { // Si l'évènement n'est pas bind
            form.data('eventBound', true); // On bind l'évènement
        
            var action = form.find('input[name="action"]').val(); // Récupérer la valeur de l'action

            $.ajax({ // Fait une requête AJAX
                type: "POST",  
                url: "ajax_request/handleAdmin.php",
                data: form.serialize(),
                success: function(data) { // Quand la requête est terminée
                    if (data == 1) { // Si la requête a réussi
                        popupAdminContainer.classList.remove('active'); // On enlève la classe active pour cacher le popup
                        document.body.classList.remove('active'); // On enlève la classe active pour débloquer le scroll

                        if (action == "flag") { // Si l'action est de flagger un post
                            var warnButton = document.getElementById('warn-' + form.find('input[name="post_id"]').val()); // On cache le post

                            if (warnButton.innerHTML == "Flag") { // Si le bouton est "Flag"
                                warnButton.innerHTML = "Unflag"; // On change le texte du bouton
                            } else { // Si le bouton est "Unflag"
                                warnButton.innerHTML = "Flag"; // On change le texte du bouton
                            }
                        }


                        if (action == "delete-admin") { // Si l'action est de supprimer un post
                            $('#post-' + form.find('input[name="post_id"]').val()).hide(); // On cache le post
                        }

                    }
                }
            });
        }
        
    });

    //--------- Fonction pour gerer le bouton d'affichage des commentaires

    var showButtons = document.querySelectorAll(".show-hidde-comment-button"); // On récupère tous les boutons d'affichage des commentaires

    for (var i = 0; i < showButtons.length; i++) { // Pour chaque bouton d'affichage des commentaires
        if (!showButtons[i].hasAttribute("data-eventBound")) { // Si l'évènement n'est pas bind
            showButtons[i].setAttribute("data-eventBound", true); // On bind l'évènement

            showButtons[i].addEventListener("click", function(event) { // Quand on clique dessus
                var comment_id = this.value; // On récupère l'id du commentaire
                showComment(comment_id); // On appelle la fonction showComment
            });
        }
    }

    //--------- Fonction pour gerer le click d'un bouton commentaire

    var commentButtons = document.getElementsByClassName("comment-button"); // On récupère tous les boutons commentaire

    for (var i = 0; i < commentButtons.length; i++) { // Pour chaque bouton commentaire
        if (!commentButtons[i].hasAttribute("data-eventBound")) { // Si l'évènement n'est pas bind
            commentButtons[i].setAttribute("data-eventBound", true); // On bind l'évènement

            commentButtons[i].addEventListener("click", function(event) { // Quand on clique dessus
                var post_id = this.dataset.postId; // On récupère l'id du post
                var parent_id = this.dataset.parentId; // On récupère l'id du commentaire parent
                var identifier_id = this.dataset.identifierId; // On récupère l'id de l'élément qui a été cliqué

                document.getElementById("comment-post-id").value = post_id; // On met cet id dans le champ caché du formulaire
                document.getElementById("comment-parent-id").value = parent_id; // On met cet id dans le champ caché du formulaire
                document.getElementById("identifier-id").value = identifier_id; // On met cet id dans le champ caché du formulaire
                    
                const comment_form = document.getElementsByClassName("comment-form"); // On affiche le formulaire

                if (comment_form[0].style.display == "block") { // Si le formulaire est déjà affiché
                    comment_form[0].style.display = "none"; // On le cache
                } else {
                    comment_form[0].style.display = "block"; // Sinon on l'affiche
                }
                
            });
        }
    }

}

$(document).ready(function() { // Quand le document est prêt
    sessionStorage.setItem('depth', '1'); // Initialisation de la profondeur a 0

    initialise(); // Initialisation des fonctions


    // ------------------------ Handle l'unban d'un utilisateur
    $(".unban-log-button").click(function() { // Quand le bouton est cliqué

        var userId = $(this).attr('data-user-id'); // Récupérer l'id de l'utilisateur
        var form = $("#admin-form"); // Récupérer le formulaire
        form.find('input[name="user_id"]').val(userId); // Mettre l'id de l'utilisateur dans le formulaire
        form.find('input[name="post_id"]').val(null); // Mettre l'action unban dans le formulaire
        form.find('input[name="action"]').val("unban"); // Mettre l'action unban dans le formulaire
            
        form.submit(); // Soumettre le formulaire
    });


    // ------------------------ Handle le fait de supprimer une notification
    $(".delete-notification-button").click(function() { // Quand le bouton est cliqué
        var notificationId = $(this).attr('data-notification-id'); // Récupérer l'id de la notification
        $.ajax({ // Fait une requête AJAX
            type: "POST",  
            url: "ajax_request/handleDeleteNotification.php",
            data: {notification_id: notificationId},
            success: function(data) { // Quand la requête est terminée
                if (data == 1) { // Si la requête a réussi
                    $('#notification-' + notificationId).hide(); // On cache la notification
                }
            }
        });
        
    });

    $("#show-notification-button").click(function() { // Quand le bouton est cliqué   
        $.ajax({ // Fait une requête AJAX pour mettre les notifications de l'utilisateur comme lu
            type: "POST",
            url: "ajax_request/handleReadNotification.php",
            data: {},
            success: function(data) { // Quand la requête est terminée
                $("#pellet").hide(); // On cache le pellet
            }
        });
    });

    // ------------------------ Handle le bouton de chargement de plus de post
    $("#load-more-button").click(function() { // Quand le bouton est cliqué
        if (sessionStorage.getItem('depth') == null) { // Si la profondeur n'est pas défini
            sessionStorage.setItem('depth', '1'); // On l'initialise
            var depth = 1; // On initialise la profondeur
        }else {
            var depth = parseInt(sessionStorage.getItem('depth')); // On récupère la profondeur
        }
        
        var username = $(this).attr('data-username'); // Récupérer le nom d'utilisateur
        var sub = $(this).attr('data-sub'); // Récupérer le nom du sub
        var search = $(this).attr('data-search'); // Récupérer le nom de la recherche

        $.ajax({ // Fait une requête AJAX
            type: "POST",
            url: "ajax_request/HandleLoadingPost.php",
            data: {depth: depth, username: username, sub: sub, search: search},
            success: function(data) { // Quand la requête est terminée
                $('#no-more-post').remove(); // On enlève le message "Pas plus de post"
                sessionStorage.setItem('depth', parseInt(depth) + 1); // On incrémente l'offset                
                $(data).insertBefore('#load-more-button'); // On ajoute les posts après le bouton

                initialise(); // On réinitialise les fonctions
                initialisePopup(); // On réinitialise les popups
            }
        });
    });


    // $(".comment-form").submit(function(e) { PROTOTYPE AJOUT COMMENTAIRE AVEC AJAX
    //     e.preventDefault();
    //     var formData = new FormData(this);

    //     // Log FormData entries
    //     for (var pair of formData.entries()) {
    //         console.log(pair[0]+ ', ' + pair[1]); 
    //     }

    //     if (formData.get("text") == "") { // Si le texte est vide
    //             return;
    //     }

    //     if (formData.get("text").length > 270) { // Si le texte est trop long
    //         if (document.querySelector(".error-post") == null) { // Si il n'y a pas déjà un message d'erreur
    //             content = "<div class='error-post'> Le texte ne peux pas dépasser 270 caractères </div>"; // Créer un message d'erreur
    //             document.getElementById("profil").insertAdjacentHTML('beforeend', content);
    //         } else { // Si il y a déjà un message d'erreur
    //             document.querySelector(".error-post").remove(); // Enlever le message d'erreur
    //             content = "<div class='error-post'> Le texte ne peux pas dépasser 270 caractères </div>";
    //             document.getElementById("profil").insertAdjacentHTML('beforeend', content);
    //         }

    //         return;
    //     }


    //     if (formData.getAll("comment_images[]").length > 3) { // Si il y a plus de 3 images

    //         if (document.querySelector(".error-post") == null) { // Si il n'y a pas déjà un message d'erreur
    //             content = "<div class='error-post'> On ne peux pas mettre plus de 3 images dans un post </div>"; // Créer un message d'erreur
    //             document.getElementById("profil").insertAdjacentHTML('beforeend', content);
    //         } else { // Si il y a déjà un message d'erreur
    //             document.querySelector(".error-post").remove(); // Enlever le message d'erreur
    //             content = "<div class='error-post'> On ne peux pas mettre plus de 3 images dans un post </div>";
    //             document.getElementById("profil").insertAdjacentHTML('beforeend', content);
    //         }

    //         return;
    //     }

    //     if (formData.get("comment_images[]").size > 1000000) { // Si la taille de l'image est supérieur à 1Mo

    //         if (document.querySelector(".error-post") == null) { // Si il n'y a pas déjà un message d'erreur
    //             content = "<div class='error-post'> La taille cumulé des images ne peux pas dépasser 1 Mo </div>"; // Créer un message d'erreur
    //             document.getElementById("profil").insertAdjacentHTML('beforeend', content);
    //         } else { // Si il y a déjà un message d'erreur
    //             document.querySelector(".error-post").remove(); // Enlever le message d'erreur
    //             content = "<div class='error-post'> La taille cumulé des images ne peux pas dépasser 1 Mo </div>";
    //             document.getElementById("profil").insertAdjacentHTML('beforeend', content);
    //         }

    //         return;
    //     }

    //     $.ajax({
    //         type: "POST",
    //         url: "ajax_request/handleComment.php",
    //         data: formData,
    //         processData: false,  
    //         contentType: false, 
    //         success: function(data) {
    //             var blocAjoutCommentaire = document.getElementById("comment-" + formData.get("identifier_id"));

    //             if (blocAjoutCommentaire != null) {
    //                 content = "<div class='comment' id='comment-" + data + "'>";
    //             }
    //         }
    //     });
    // });
});