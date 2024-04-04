$(document).ready(function() { // Quand le document est prêt
    
    // ------------------------ Handle le like
    $(".like-form").submit(function(e) { // Quand le formulaire est soumis
        e.preventDefault(); // Empêcher le comportement par défaut du formulaire
        var form = $(this);  // Récupérer le formulaire

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
            }
        });
    });

    // ------------------------ Handle le signalement
    const popupAdminContainer = document.querySelector('.admin-container');  // Conteneur du popup

    $("#admin-form").submit(function(e) { // Quand le formulaire est soumis
        e.preventDefault(); // Empêcher le comportement par défaut du formulaire
        var form = $(this);  // Récupérer le formulaire
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

        // ------------------------ Handle l'unban d'un utilisateur
        $(".unban-log-button").click(function() { // Quand le bouton est cliqué
            e.preventDefault(); // Empêcher le comportement par défaut du formulaire
            var userId = $(this).attr('data-user-id'); // Récupérer l'id de l'utilisateur
            var form = $("#admin-form"); // Récupérer le formulaire
            form.find('input[name="user_id"]').val(userId); // Mettre l'id de l'utilisateur dans le formulaire
            form.find('input[name="post_id"]').val(NULL); // Mettre l'action unban dans le formulaire
            form.find('input[name="action"]').val("unban"); // Mettre l'action unban dans le formulaire
            form.submit(); // Soumettre le formulaire
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