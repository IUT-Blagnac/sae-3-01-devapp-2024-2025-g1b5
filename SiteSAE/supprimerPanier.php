<?php

    include "Connect.inc.php";
    session_start();

    if (isset($_POST['idProduit']) and isset($_POST['idClient'])) {
        $res = $conn->prepare("DELETE FROM Panier_Client WHERE idProduit = ? AND idClient = ? ");
        $res->execute([$_POST['idProduit'], $_POST['idClient']]);
        $prod = $res->fetch();
        $res->closeCursor();

        header("Location: panier.php");

    } elseif (isset($_POST['idProd'])) {
    
        if (isset($_SESSION['panier'][$_POST['idProd']])) {
            unset($_SESSION['panier'][$_POST['idProd']]); 
            // Supprime l'article du panier de la session s'il existe 
        }

        header("Location: panier.php");

    } else {
        header("Location: index.php");

    }

?>