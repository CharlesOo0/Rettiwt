$(document).ready(function() {

    var closeCommentButton = document.getElementById("close-comment-form"); // On récupère le bouton pour fermer le formulaire

    closeCommentButton.addEventListener("click", function(event) { // Quand on clique dessus
        const comment_form = document.getElementsByClassName("comment-form"); // On cache le formulaire
        comment_form[0].style.display = "none";
    });

    
    var notificationPellet = document.getElementById("pellet"); // On récupère le pellet de notification

    var notifications = document.querySelectorAll(".unread"); // On récupère les notifications

    if (notifications.length > 0) { // Si il y a des notifications
        notificationPellet.style.display = "block"; // On affiche le pellet
        notificationPellet.innerHTML = notifications.length; // On affiche le nombre de notifications
    }else {
        notificationPellet.style.display = "none"; // Sinon on cache le pellet
    }
        
});
