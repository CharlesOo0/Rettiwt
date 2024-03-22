// https://codehalweb.com/popup-modal-in-html-css-javascript/
const showPopup = document.getElementById("show-post-button");
const popupContainer = document.querySelector('.post-form-container');
const closeBtn = document.getElementById('close-post-button');

showPopup.addEventListener('click', () => {
    console.log('clicked');
    popupContainer.classList.add('active');
    document.body.classList.add('active');
});

closeBtn.addEventListener('click', () => {
    popupContainer.classList.remove('active');
    document.body.classList.remove('active');
});
