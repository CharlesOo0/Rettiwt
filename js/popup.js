// https://codehalweb.com/popup-modal-in-html-css-javascript/

// Popup for login
const showPostPopup = document.getElementById("show-post-button");
const popupPostContainer = document.querySelector('.post-form-container');
const closePostBtn = document.getElementById('close-post-button');

showPostPopup.addEventListener('click', () => {
    popupPostContainer.classList.add('active');
    document.body.classList.add('active');
});

closePostBtn.addEventListener('click', () => {
    popupPostContainer.classList.remove('active');
    document.body.classList.remove('active');
});

// Popup for notification

const showNotificationPopup = document.getElementById("show-notification-button");
const popupNotificationContainer = document.querySelector('.notification-container');
const closeNotificationBtn = document.getElementById('close-notification-button');

showNotificationPopup.addEventListener('click', () => {
    popupNotificationContainer.classList.add('active');
    document.body.classList.add('active');
});

closeNotificationBtn.addEventListener('click', () => {
    popupNotificationContainer.classList.remove('active');
    document.body.classList.remove('active');
});