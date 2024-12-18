<?php
    echo '<link rel="stylesheet" href="commande.css">';

    include "header.php";
    include "Connect.inc.php";

    //session_start();

        if (isset($_SESSION['client_email']) || isset($_COOKIE['CidClient'])) {

            $req = $conn->prepare("SELECT * FROM Client WHERE email = ?");
            $req->execute([$_SESSION['client_email']]);
            $client = $req->fetch();
            $req->closeCursor();

            $idClient = $client['idClient'] ;
        } else {
            $idClient = 0 ;
        }

?>


<section class="panier">

    <div class="votre-panier">
        <h1>Votre Panier</h1>

        <?php

            if ( $idClient != 0 ) {

                $adr = $conn->prepare("SELECT * FROM Client WHERE idClient = ?");
                $adr->execute([$idClient]);
                $adresse = $adr -> fetch();
                $adr->closeCursor();
                
                
                
                
                $panier = $conn->prepare("SELECT * FROM Panier_Client pc, Produit p WHERE pc.idProduit = p.idProduit AND idClient = ?");
                $panier->execute([$idClient]);
                
                while( $produit_panier = $panier -> fetch() ) {
                    $nomProduit = $produit_panier['nomProduit'];
                    $idProduit = $produit_panier['idProduit'];
                    
                    echo '<div class="rappel-produit-panier" >';
                        echo  '<img src="image_Produit/Prod'.$idProduit.'.jpg" alt="'.$nomProduit.'"  >';
                        echo $nomProduit;
                    echo '</div>';
                }
                
                $idAdresse = $client['idAdresse'] ;
                $panier->closeCursor();
            

                $adre = $conn->prepare("SELECT * FROM Adresse WHERE idAdresse = ?");
                $adre->execute([$idAdresse]);
                $adresseClient = $adre -> fetch();
                $adre->closeCursor();
                
                
                

            } 

        ?>

    </div>

    <div class="recapitulatif">
        <h1>Récapitulatif</h1>

        <div class="recap-panier">
            <div class="code-promo">
                <label for="code-promo">Code Promo :</label>
                <input type="text" class="promo" placeholder="Ecrivez le ici ...">
            </div>
        </div>

    <?php

        if ( $idClient != 0 ) {

            //on définit les paramètres retour
            $prix = 0;
            $quantite = 0;

            // on définit la requete d'appel de la procédure stockée 
            $recapPanier = 'CALL RecapPanier( :idClient, @quantiteTotale, @prixTotal )';

            // Préparer et exécuter l'appel de la procédure
            $statement = $conn->prepare($recapPanier);
            $statement->bindParam(':idClient', $idClient);
            $statement->execute();
            $statement->closeCursor();

            // Récupérer directement les valeurs des variables OUT
            $query = 'SELECT @quantiteTotale AS quantiteTotale, @prixTotal AS prixTotal';
            $result = $conn->query($query);

            // Récupérer les résultats
            if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $quantite = $row['quantiteTotale'];
                $prix = $row['prixTotal'];
            }
                        
            echo '
                <div class="recap-panier">
                    <p>Produits (' . $quantite . ') </p>
                    <p>Sous-Total : ' . $prix . ' €</p>
                </div>

                <button type="button" class="valider-panier" onclick="">Valider mon Panier</button>
            ';

        } 

    ?>


    </div>

</section>

<?php
    include "footer.php";
?>
</body>
</html>