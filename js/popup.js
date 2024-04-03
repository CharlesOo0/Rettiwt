// CREDIT : https://codehalweb.com/popup-modal-in-html-css-javascript/
// Tutoriel utiliser pour la structure principale des popups

$(document).ready(function() {

    // Popup pour login

    const showPostPopup = document.getElementById("show-post-button"); // Bouton pour afficher le popup
    const popupPostContainer = document.querySelector('.post-form-container'); // Conteneur du popup
    const closePostBtn = document.getElementById('close-post-button'); // Bouton pour fermer le popup
    
    showPostPopup.addEventListener('click', () => { // Quand on clique sur le bouton
        popupPostContainer.classList.add('active'); // On ajoute la classe active au popup pour l'afficher
        document.body.classList.add('active'); // On ajoute la classe active au body pour bloquer le scroll
    });

    closePostBtn.addEventListener('click', () => { // Quand on clique sur le bouton pour fermer le popup
        popupPostContainer.classList.remove('active'); // On retire la classe active pour cacher le popup
        document.body.classList.remove('active'); // On retire la classe active pour débloquer le scroll
    });

    // Popup pour notification

    const showNotificationPopup = document.getElementById("show-notification-button"); // Bouton pour afficher le popup
    const popupNotificationContainer = document.querySelector('.notification-container');  // Conteneur du popup
    const closeNotificationBtn = document.getElementById('close-notification-button');  // Bouton pour fermer le popup

    showNotificationPopup.addEventListener('click', () => { // Quand on clique sur le bouton
        popupNotificationContainer.classList.add('active'); // On ajoute la classe active au popup pour l'afficher
        document.body.classList.add('active');  // On ajoute la classe active au body pour bloquer le scroll
    });

    closeNotificationBtn.addEventListener('click', () => { // Quand on clique sur le bouton pour fermer le popup
        popupNotificationContainer.classList.remove('active'); // On retire la classe active pour cacher le popup
        document.body.classList.remove('active'); // On retire la classe active pour débloquer le scroll
    });

    // Popup pour les bans, warns, et suppression de post

    const showBanPopup = document.querySelectorAll(".ban-post-button"); // Bouton pour afficher le popup
    const showWarnPopup = document.querySelectorAll(".warn-post-button"); // Bouton pour afficher le popup
    const showDeletePopup = document.querySelectorAll(".delete-post-button"); // Bouton pour afficher le popup
    const showDeleteAdminPopup = document.querySelectorAll(".delete-admin-post-button"); // Bouton pour afficher le popup
    const showFlagPopup = document.querySelectorAll(".flag-post-button"); // Bouton pour afficher le popup

    const popupAdminContainer = document.querySelector('.admin-container');  // Conteneur du popup
    const closeAdminBtn = document.getElementById('close-admin-button');  // Bouton pour fermer le popup
    
    var adminTypeForm = document.getElementById('admin-type-form'); // Titre du formulaire
    var adminTypeHiddenInput = document.getElementById('type-of-input-admin'); // Input caché pour le type de formulaire
    var adminPostIdInput = document.getElementById('admin-post-id'); // Input caché pour l'id du post
    var adminUsernameInput = document.getElementById('admin-username'); // Input caché pour l'username du post
    var adminTypeSubmit = document.getElementById('type-of-submit-admin'); // Bouton pour soumettre le formulaire
    var dateInput = document.getElementById('date-input-admin'); // On récupère l'input pour la date

    showBanPopup.forEach((button) => { // Pour chaque bouton de ban
        button.addEventListener('click', () => { // Quand on clique sur le bouton
            adminTypeForm.innerHTML = "Formulaire de banissement"; // On change le titre du formulaire
            adminTypeSubmit.value = "Ban"; // On change le texte du bouton de soumission
            adminTypeHiddenInput.value = "ban"; // On change la valeur de l'input caché
            adminPostIdInput.value = button.getAttribute('data-post-id'); // On change la valeur de l'input caché pour l'id du post
            adminUsernameInput.value = button.getAttribute('data-username'); // On change la valeur de l'input caché pour l'username du post

            dateInput.type = "date"; // On change le type de l'input pour la date
            dateInput.required = true; // On change la valeur de l'input pour la date

            popupAdminContainer.classList.add('active'); // On ajoute la classe active au popup pour l'afficher
            document.body.classList.add('active');  // On ajoute la classe active au body pour bloquer le scroll
        });
    });

    showWarnPopup.forEach((button) => { // Pour chaque bouton de warn
        button.addEventListener('click', () => { // Quand on clique sur le bouton
            adminTypeForm.innerHTML = "Formulaire d'avertissement"; // On change le titre du formulaire
            adminTypeSubmit.value = "Avertir"; // On change le texte du bouton de soumission
            adminTypeHiddenInput.value = "warn"; // On change la valeur de l'input caché
            adminPostIdInput.value = button.getAttribute('data-post-id'); // On change la valeur de l'input caché pour l'id du post
            adminUsernameInput.value = button.getAttribute('data-username'); // On change la valeur de l'input caché pour l'username du post

            dateInput.type = "hidden"; // On change le type de l'input pour la date
            dateInput.required = false; // On change la valeur de l'input pour la date

            popupAdminContainer.classList.add('active'); // On ajoute la classe active au popup pour l'afficher
            document.body.classList.add('active');  // On ajoute la classe active au body pour bloquer le scroll
        });
    });

    showDeleteAdminPopup.forEach((button) => { // Pour chaque bouton de suppression par un admin
        button.addEventListener('click', () => { // Quand on clique sur le bouton
            adminTypeForm.innerHTML = "Formulaire de supression de post"; // On change le titre du formulaire
            adminTypeSubmit.value = "Supprimer"; // On change le texte du bouton de soumission
            adminTypeHiddenInput.value = "delete-admin"; // On change la valeur de l'input caché
            adminPostIdInput.value = button.getAttribute('data-post-id'); // On change la valeur de l'input caché pour l'id du post
            adminUsernameInput.value = button.getAttribute('data-username'); // On change la valeur de l'input caché pour l'username du post


            dateInput.type = "hidden"; // On change le type de l'input pour la date
            dateInput.required = false; // On change la valeur de l'input pour la date

            popupAdminContainer.classList.add('active'); // On ajoute la classe active au popup pour l'afficher
            document.body.classList.add('active');  // On ajoute la classe active au body pour bloquer le scroll
        });
    });

    showDeletePopup.forEach((button) => { // Pour chaque bouton de suppression 
        button.addEventListener('click', () => { // Quand on clique sur le bouton
            var data = {
                'action': 'delete', // On envoie l'action 'delete-post
                'post_id': button.getAttribute('data-post-id'),
                'user_id': button.getAttribute('data-username')
            }

            $.ajax({ // Fait une requête AJAX
                type: "POST",  
                url: "ajax_request/handleAdmin.php",
                data: data,
                success: function(data) { // Quand la requête est terminée
                    if (data == 1) { // Si la requête a réussi
                        $('#post-' + button.getAttribute('data-post-id')).hide();
                    }
                }
            });
        });
    });

    showFlagPopup.forEach((button) => { // Pour chaque bouton de signalement
        button.addEventListener('click', () => { // Quand on clique sur le bouton
            adminTypeForm.innerHTML = "Formulaire de signalement"; // On change le titre du formulaire
            adminTypeSubmit.value = "Signaler"; // On change le texte du bouton de soumission
            adminTypeHiddenInput.value = "flag"; // On change la valeur de l'input caché
            adminPostIdInput.value = button.getAttribute('data-post-id'); // On change la valeur de l'input caché pour l'id du post
            adminUsernameInput.value = button.getAttribute('data-username'); // On change la valeur de l'input caché pour l'username du post
            

            dateInput.type = "hidden"; // On change le type de l'input pour la date
            dateInput.required = false; // On change la valeur de l'input pour la date

            popupAdminContainer.classList.add('active'); // On ajoute la classe active au popup pour l'afficher
            document.body.classList.add('active');  // On ajoute la classe active au body pour bloquer le scroll
        });
    });

    closeAdminBtn.addEventListener('click', () => { // Quand on clique sur le bouton pour fermer le popup
        popupAdminContainer.classList.remove('active'); // On retire la classe active pour cacher le popup
        document.body.classList.remove('active'); // On retire la classe active pour débloquer le scroll
    });

});