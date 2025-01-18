<?php

    include "Connect.inc.php"; 
    
    session_start();
    
    if (isset($_SESSION['client_email']) ) {
        
        
        $req = $conn->prepare("SELECT * FROM Client WHERE email = ?");
        $req->execute([$_SESSION['client_email']]);
        $client = $req->fetch();
        $req->closeCursor();
        
        $idClient = $client['idClient'] ;
        $pointFC = $client['pointFidelite'] ;
        $prix=isset($_GET["prix"])?$_GET["prix"]:0;
        //arrondir le prix à l'entier supérieur
        $prix = ceil($prix);
        $pointFC+=$prix ;

        
        // Récupérer l'idAdresse et type de livraison depuis le form
        $idAdresse = isset($_POST['idAdresse']) ? $_POST['idAdresse'] : 0;
        $typeLivraison = isset($_POST['typeLivraison']) ? $_POST['typeLivraison'] : 0;

        try {

            $commande = $conn->prepare("INSERT INTO Commande ( typeLivraison, dateCommande, idClient, idAdresse, statut)  VALUES ( ?, CURRENT_DATE(), ?, ?, ?) ");
            $commande->execute([ $typeLivraison, $idClient, $idAdresse, 'En preparation']);
            $commande->closeCursor();

            $selectCommande = $conn->prepare("SELECT max(idCommande) FROM Commande WHERE idClient = ? AND dateCommande = CURRENT_DATE() ");
            $selectCommande->execute([$idClient]);
            $commandeFaite = $selectCommande -> fetch() ;
            $selectCommande->closeCursor();

            $idCommande = $commandeFaite['max(idCommande)'];

            $panier = $conn->prepare("SELECT * FROM Panier_Client WHERE idClient = ?");
            $panier->execute([$idClient]);
            $requete =$conn->prepare("call ajouterCodesPromoACommande(?,?)");
            $requete->execute([$idCommande, $idClient]);
            

            while( $produit_panier = $panier -> fetch() ) {

                $composer = $conn->prepare("INSERT INTO Composer ( idCommande, idProduit, quantite, idCodePromo)  VALUES ( ?, ?, ?, ?) ");
                $composer->execute([ $idCommande, $produit_panier['idProduit'], $produit_panier['quantite'], null]);
                $composer->closeCursor();

                $remove = $conn->prepare("DELETE FROM Panier_Client WHERE idClient = :idClient AND idProduit = :idProduit ");
                $remove->bindParam(':idClient', $idClient);
                $remove->bindParam(':idProduit', $produit_panier['idProduit']);
                $remove->execute();
                $remove->closeCursor();
            }
            

            $panier->closeCursor();

            // Ajouter les points de fidélité
            $selectCommande = $conn->prepare('Update Client set pointFidelite = ? where idClient = ?');
            $selectCommande->execute([$pointFC, $idClient]);

            unset($_SESSION['numCarte']);
            unset($_SESSION['dateE']);
            unset($_SESSION['cvv']);
            unset($_SESSION['titulaire']);

            //mettre à jour le stock
            

            unset($_SESSION['paypalMail']);
            unset($_SESSION['paypalMdp']);
           header("Location: commande.php?statut=reussi&pointFC=$prix");
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion de la commande !";
            header("Location: commande.php?statut=echoue");
        }
        
    } else {

        header("Location: index.php");

    }

?>