$(document).ready(function() { // Quand le document est prêt
    
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