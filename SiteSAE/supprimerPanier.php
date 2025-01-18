<?php

    include "Connect.inc.php";
    session_start();

    if (isset($_POST['idProduit']) and isset($_POST['idClient'])) {
        $req = $conn->prepare("SELECT * FROM Panier_Client WHERE idProduit = ? AND idClient = ? ");
        $req->execute([$_POST['idProduit'], $_POST['idClient']]);
        $produit = $req->fetch();
        $req->closeCursor();

        $quantite = intval($produit['quantite']);

        if ($quantite == 1){
            $res = $conn->prepare("DELETE FROM Panier_Client WHERE idProduit = ? AND idClient = ? ");
            $res->execute([$_POST['idProduit'], $_POST['idClient']]);
            $res->closeCursor();
        } else {
            try{
                $res = $conn->prepare("UPDATE Panier_Client SET quantite = ? WHERE idProduit = ? AND idClient = ?");
                $res->execute([$quantite - 1, $_POST['idProduit'], $_POST['idClient']]);
                $res->closeCursor();
            } catch (PDOException $e) {
                $e->getMessage();
            }
        }

        header("Location: panier.php");

    } elseif (isset($_POST['idProd'])) {
    
        if (isset($_SESSION['panier'][$_POST['idProd']])) {
            $quantite = intval($_SESSION['panier'][$_POST['idProd']]);

            if ($quantite == 1){
                unset($_SESSION['panier'][$_POST['idProd']]); 
            } else {
                // Supprime un produit au panier
                $_SESSION['panier'][$_POST['idProd']] = $quantite - 1;
            }
        }


        header("Location: panier.php");

    } else {
        header("Location: index.php");

    }

?>