<?php

    include "Connect.inc.php"; 

    // Récupérer l'idProduit depuis l'URL
    $idProduit = isset($_GET['idProduit']) ? intval($_GET['idProduit']) : 0;

    // Récupérer l'idProduit depuis l'URL
    $quantite = isset($_GET['quantite']) ? intval($_GET['quantite']) : 0;


    session_start();

    if (isset($_SESSION['client_email']) or isset($_COOKIE['CidClient'])) {

        $req = $conn->prepare("SELECT * FROM Client WHERE email = ?");
        $req->execute([$_SESSION['client_email']]);
        $client = $req->fetch();
        $req->closeCursor();

        $idClient = $client['idClient'] ;
        

        try {

            // on définit la requete d'appel de la procédure stockée 

            $appelAjoutPanier = 'CALL AjouterPanier( :idClient, :idProduit, :quantite )';

            $statement = $conn->prepare($appelAjoutPanier);
            $statement->bindParam(':idClient', $idClient);
            $statement->bindParam(':idProduit', $idProduit);
            $statement->bindParam(':quantite', $quantite);
            $statement->execute();
            $statement->closeCursor();

            header("Location: descriptionDetail.php?idProduit=$idProduit");
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion du produit dans le panier !";
        }
        
    } else {

         // Si l'utilisateur n'est pas connecté, on gère le panier avec la session
         if (!isset($_SESSION['panier'])) {
              // Si le panier n'existe pas encore, on initialise un tableau vide
              $_SESSION['panier'] = [];
         }

         // Ajouter le produit au panier
         if (isset($_SESSION['panier'][$idProduit])) {
             $_SESSION['panier'][$idProduit] += $quantite;
         } else {
             $_SESSION['panier'][$idProduit] = $quantite;
         }

         header("Location: descriptionDetail.php?idProduit=$idProduit");

    }

?>