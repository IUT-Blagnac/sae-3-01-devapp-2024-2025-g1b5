<?php

require_once('Connect.inc.php');

$idClient = $_GET['idClient'];
$idPromo = $_GET['idPromo'];
$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'panier.php';
//retirer les paramètres de l'url
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
    $previousPage = explode('?', $previousPage)[0];
}
// Requête SQL pour supprimer l'association entre client et promotion
$requete = "DELETE FROM Panier_Client_Promo WHERE idClient = ? AND idPromo = ?";
$res = $conn->prepare($requete);

// Exécution de la requête avec des paramètres ordonnés
$res->execute([$idClient, $idPromo]);

if ($res->rowCount() > 0) {
    echo "L'association entre le client et la promotion a été supprimée avec succès.";
    header("Location: $previousPage");
} else {
    header("Location: $previousPage?error=Echec de la suppression ");
}
