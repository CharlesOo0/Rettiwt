document.addEventListener("DOMContentLoaded", function() {
    if (sessionStorage.getItem('scrollPosition') !== null) {
        $(document).ready(function() {
            window.scrollTo({
                behavior: 'instant',
            },sessionStorage.getItem('scrollPosition'));
        });

    }

    var likeButtons = document.getElementsByClassName("like-button");

    for (var i = 0; i < likeButtons.length; i++) {
        likeButtons[i].addEventListener("click", function(event) {
 
            sessionStorage.setItem('scrollPosition', $(window).scrollTop());

        });
    }
});