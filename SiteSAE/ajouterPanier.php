<?php

    include "Connect.inc.php"; 

    // Récupérer l'idProduit depuis l'URL
    $idProduit = isset($_GET['idProduit']) ? intval($_GET['idProduit']) : 0;


    session_start();

    if (isset($_SESSION['client_email']) or isset($_COOKIE['CidClient'])) {

        $client = $conn->prepare("SELECT * FROM Client WHERE email = ?");
        $client->execute([$_SESSION['client_email']]);
        $idClient = $client->fetch();
        $client->closeCursor();

        echo $idClient['idClient'] ;
        

        try {
            
            $produit = $conn->prepare("INSERT INTO Panier (idClient, idProduit) VALUES (?, ?)");
            $produit->execute( $idClient, $idProduit);

            $stmt = $pdo->prepare("INSERT INTO panier (idClient, idProduit) VALUES (:idClient, :idProduit)");
            $stmt->execute([':idClient' => $idClient, ':idProduit' => $idProduit]);


            echo "Produit ajouté dans le panier!";
            header("Location: descriptionDetail.php?idProduit=$idProduit");
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion du produit dans le panier !";
        }
        
    } else {
        echo 'Non mon gars connecte toi';
    }

?>