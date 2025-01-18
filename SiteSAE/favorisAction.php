<?php
include "Connect.inc.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idProduit = isset($_POST['idProduit']) ? intval($_POST['idProduit']) : 0;
    $idClient = isset($_POST['idClient']) ? intval($_POST['idClient']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($idProduit > 0 && $idClient > 0) {
        if ($action == 'ajouter') {
            // Vérifier si le produit est déjà dans les favoris
            $checkFav = $conn->prepare("SELECT * FROM Produit_Favoris WHERE idProduit = ? AND idClient = ?");
            $checkFav->execute([$idProduit, $idClient]);
            if ($checkFav->rowCount() == 0) {
                // Ajouter aux favoris
                $query = $conn->prepare("INSERT INTO Produit_Favoris (idProduit, idClient) VALUES (?, ?)");
                $query->execute([$idProduit, $idClient]);
            }
        } elseif ($action == 'retirer') {
            // Retirer des favoris
            $query = $conn->prepare("DELETE FROM Produit_Favoris WHERE idProduit = ? AND idClient = ?");
            $query->execute([$idProduit, $idClient]);
        }
    }
}
?>
