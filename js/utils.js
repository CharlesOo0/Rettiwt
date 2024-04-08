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

//--------- Fonction pour gerer le bouton d'affichage des commentaires

var showButtons = document.querySelectorAll(".show-hidde-comment-button"); // On récupère tous les boutons d'affichage des commentaires

for (var i = 0; i < showButtons.length; i++) { // Pour chaque bouton d'affichage des commentaires
    showButtons[i].addEventListener("click", function(event) { // Quand on clique dessus
        var comment_id = this.value; // On récupère l'id du commentaire
        showComment(comment_id); // On appelle la fonction showComment
    });
}

//--------- Fonction pour gerer le click d'un bouton commentaire

var commentButtons = document.getElementsByClassName("comment-button"); // On récupère tous les boutons commentaire

for (var i = 0; i < commentButtons.length; i++) { // Pour chaque bouton commentaire
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

var closeCommentButton = document.getElementById("close-comment-form"); // On récupère le bouton pour fermer le formulaire

closeCommentButton.addEventListener("click", function(event) { // Quand on clique dessus
    const comment_form = document.getElementsByClassName("comment-form"); // On cache le formulaire
    comment_form[0].style.display = "none";
});

$(document).ready(function() {
    var notificationPellet = document.getElementById("pellet"); // On récupère le pellet de notification

    var notifications = document.querySelectorAll(".unread"); // On récupère les notifications

    if (notifications.length > 0) { // Si il y a des notifications
        notificationPellet.style.display = "block"; // On affiche le pellet
        notificationPellet.innerHTML = notifications.length; // On affiche le nombre de notifications
    }else {
        notificationPellet.style.display = "none"; // Sinon on cache le pellet
    }
        
});
