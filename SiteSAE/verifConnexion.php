<?php

if (!isset($_SESSION['client_email']) && !isset($_COOKIE['CidClient'])) {
    header('Location: connexionCompte.php');
    exit();
}

?>