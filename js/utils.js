//--------- Fonction pour gerer le click d'un bouton commentaire

var commentButtons = document.getElementsByClassName("comment-button"); // On récupère tous les boutons commentaire

for (var i = 0; i < commentButtons.length; i++) { // Pour chaque bouton commentaire
    commentButtons[i].addEventListener("click", function(event) { // Quand on clique dessus
        var post_id = this.value; // On récupère l'id du post
        document.getElementById("comment-post-id").value = post_id; // On met cet id dans le champ caché du formulaire
        
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