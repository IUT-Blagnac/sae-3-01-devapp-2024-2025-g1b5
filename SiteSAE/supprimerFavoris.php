<?php
include "Connect.inc.php";
include "verifConnexion.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idProduit'])) {
    $idProduit = intval($_POST['idProduit']);

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['client_email'])) {
        header("Location: connexion.php");
        exit();
    }

    // Récupérer l'ID du client
    $client_email = $_SESSION['client_email'];
    $query = $conn->prepare("SELECT idClient FROM Client WHERE email = ?");
    $query->execute([$client_email]);
    $client = $query->fetch();
    $idClient = $client['idClient'];

    // Supprimer le produit des favoris
    $deleteQuery = $conn->prepare("DELETE FROM Produit_Favoris WHERE idClient = ? AND idProduit = ?");
    $deleteQuery->execute([$idClient, $idProduit]);

    // Rediriger vers la page des favoris
    header("Location: favoris.php");
    exit();
}
?>
