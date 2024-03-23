document.addEventListener("DOMContentLoaded", function() { // Quand le DOM est chargé
    if (sessionStorage.getItem('scrollPosition') !== null) { // Si on a une position de scroll enregistrée
        $(document).ready(function() { // On attend que le document soit chargé
            window.scrollTo({ // On scroll jusqu'à la position enregistrée
                behavior: 'instant',
            },sessionStorage.getItem('scrollPosition')); 
        });

        sessionStorage.removeItem('scrollPosition'); // On retire la position de scroll enregistrée
    }

    var likeButtons = document.getElementsByClassName("like-button"); // On récupère tous les boutons like

    for (var i = 0; i < likeButtons.length; i++) { // Pour chaque bouton like
        likeButtons[i].addEventListener("click", function(event) { // Quand on clique dessus
            sessionStorage.setItem('scrollPosition', $(window).scrollTop()); // On enregistre la position de scroll
        });
    }
});