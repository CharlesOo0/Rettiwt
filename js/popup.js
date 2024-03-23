// CREDIT : https://codehalweb.com/popup-modal-in-html-css-javascript/

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