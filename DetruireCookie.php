<?php
session_start();

// Nom du cookie à détruire
$cookie_name = "CidClient";

// Vérifiez si le cookie existe et le détruire
if (isset($_COOKIE[$cookie_name])) {
    // Détruire le cookie en définissant une date d'expiration passée
    setcookie($cookie_name, "", time() - 3600, "/");
}
// Redirection vers la page de détail du compte
header('Location: detailCompte.php');
exit();
?>
