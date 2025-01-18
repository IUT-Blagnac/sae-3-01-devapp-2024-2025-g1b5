<?php
$cookie_name = "CidClient";
session_start();
if (isset($_COOKIE[$cookie_name])) {
    $_SESSION['client_email'] = "OK";
} else if (!isset($_SESSION['client_email']) || $_SESSION['client_email'] != 'OK') {
    header('Location: connexionCompte.php');
}

unset($_SESSION['client_email']);  
// Rediriger vers la page de connexion
header('Location: connexionCompte.php');
exit();
?>