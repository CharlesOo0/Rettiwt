<?php

function checkCreds() {
    if (!isset($_SESSION['username'])) {
        $_SESSION['error'] = "You need to be logged in to access this page.";
        header('Location: logout.php');
        exit();
    }
}

?>