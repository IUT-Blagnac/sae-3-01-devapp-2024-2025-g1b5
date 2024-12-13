<?php
ob_start();
include "header.php";
include "Connect.inc.php"; 
require "verifConnexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = htmlspecialchars(trim($_POST['client_id']));

    // Supprimer les informations de la carte bancaire
    $query = $conn->prepare("DELETE FROM CarteBancaire WHERE idClient = :client_id");
    $query->bindParam(':client_id', $client_id);
    $query->execute();

    // Redirection vers la page de détail du compte
    header('Location: detailCompte.php');
    exit();
}

ob_end_flush();
?>