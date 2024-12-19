<?php

    include "header.php";
    include "Connect.inc.php";

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

<script>
        // Variables dynamiques
        const cardNumber = "1111111111116026"; // Numéro de carte complet
        const cardExpiry = "12/2026";         // Date d'expiration
        const cardCVV = "123";               // Code CVV

        // Initialisation
        document.getElementById('card-last-digits').textContent = cardNumber.slice(-4);
        document.getElementById('card-expiry').textContent = cardExpiry;

        let isVisible = false;

        // Fonction pour afficher/masquer les informations
        function toggleVisibility() {
            const cardInfo = document.getElementById('card-number');
            const cardCVVElement = document.getElementById('card-cvv');

            if (isVisible) {
                // Masquer les informations
                cardInfo.innerHTML = `<span class="hidden">•••• •••• ••••</span> ${cardNumber.slice(-4)}`;
                cardCVVElement.textContent = "•••";
                cardCVVElement.classList.add('hidden');
            } else {
                // Afficher les informations complètes
                cardInfo.textContent = cardNumber.replace(/(\d{4})/g, '$1 ').trim();
                cardCVVElement.textContent = cardCVV;
                cardCVVElement.classList.remove('hidden');
            }

            isVisible = !isVisible;
        }
    </script>

<section class="panier">

    <div class="votre-panier">
        <h1>Votre Panier</h1>

        <?php

            if ( $idClient != 0 ) {

                $panier = $conn->prepare("SELECT * FROM Panier_Client pc, Produit p WHERE pc.idProduit = p.idProduit AND idClient = ?");
                $panier->execute([$idClient]);
                
                echo 'nb Produits : ';

                while( $produit_panier = $panier -> fetch() ) {
                    $nomProduit = $produit_panier['nomProduit'];
                    $idProduit = $produit_panier['idProduit'];
                    
                    echo '<div class="rappel-produit-panier" >';
                        echo  '<img src="image_Produit/Prod'.$idProduit.'.jpg" alt="'.$nomProduit.'"  >';
                        echo $nomProduit;
                    echo '</div>';
                }
                
                $panier->closeCursor();
            
                $adr = $conn->prepare("SELECT * FROM Client WHERE idClient = ?");
                $adr->execute([$idClient]);
                $adresse = $adr -> fetch();
                $adr->closeCursor();
                
                $idAdresse = $client['idAdresse'] ;
                $nomClient = $client['nom'] ;
                $prenomClient = $client['prenom'] ;
                $telClient = $client['numTel'] ;
                

                if ($idAdresse != null) {
                    $adre = $conn->prepare("SELECT * FROM Adresse WHERE idAdresse = ?");
                    $adre->execute([$idAdresse]);
                    $adresseClient = $adre -> fetch();
                    $adre->closeCursor();

                    $codePostal = $adresseClient['codePostal'];
                    $ville = $adresseClient['ville'];
                    $rue = $adresseClient['rue'];
                    $pays = $adresseClient['pays'];
                    
                    echo '<h2>Votre moyen de Livraison</h2>';

                    echo $nomClient ,' ', $prenomClient ,'<br>
                        ', $rue ,'<br>
                        ', $codePostal ,' ', $ville ,'<br>
                        ', $pays ,'<br>
                        ', $telClient ;
                    
                    echo '
                        <form class="choix-adresse" action="modifAdresse.php" method="POST">
                            <input type="text" name="adresseActuelle" value="', $idAdresse,'" hidden>                    
                            <button type="submit" class="valider-panier" >Modifier mon Adresse</button>
                        </form>
                    ';
                  

                } else {

                    echo '<h2>Votre moyen de Livraison</h2>';

                    echo '
                        <form class="choix-adresse" action="modifAdresse.php" method="POST">                 
                            <button type="submit" class="valider-panier" >Ajouter mon Adresse</button>
                        </form>
                    ';
                }

                    echo '<h2>Votre moyen de Paiement</h2>';

                    $res = $conn->prepare("SELECT * FROM CarteBancaire WHERE idClient = ?");
                    $res->execute([$idClient]);
                    $carteBancaire = $res -> fetch();
                    $res->closeCursor();

                    if ($carteBancaire != null) {

                        $numCarte = $carteBancaire['numCarte'] ;
                        $dateCarte = $carteBancaire['dateExpiration'] ;
                        $cvv = $carteBancaire['codeCarte'] ;

                        $dernierNumCarte = substr($numCarte, -4);

                        echo "                            
                                <div class='payment-box'>
                                    <div class='card-info'>
                                        <span>•••• •••• •••• ".$dernierNumCarte."</span>
                                    </div>
                                    <div class='details'>
                                        <p>Date d'expiration : <span id='card-expiry'>".$dateCarte."</span></p>
                                        <p>CVV : <span> •••</span></p>
                                    </div>
                                </div>
                        ";

                        echo '
                            <form class="choix-adresse" action="modifCarteBancaire.php" method="POST">                   
                                <button type="submit" class="valider-panier">Changer de carte</button>
                            </form>
                        ';
                        
                    } else {
                       
                        echo'<section class="paiement-container">
                                <h2>Renseignez votre carte de paiement</h2>
                                
                                <form>
                                    <!-- Numéro de carte -->
                                    <div class="input-group">
                                        <label for="card-number">Numéro de carte</label>
                                        <input type="text" id="card-number" placeholder="1234 5678 9012 3456" required>
                                    </div>
    
                                
                                    <div class="input-row">
                                        <div class="input-group">
                                            <label for="expiration">Expiration</label>
                                            <input type="text" id="expiration" placeholder="MM/AA" required>
                                        </div>
                                        <div class="input-group">
                                            <label for="ccv">CCV</label>
                                            <input type="text" id="ccv" placeholder="3 chiffres" required>
                                        </div>
                                    </div>
    
                                    <div class="input-group">
                                        <label for="cardholder">Titulaire de la carte</label>
                                        <input type="text" id="cardholder" value="ADRIEN THEOPHILE" required>
                                    </div>
    
                                
                                    <div class="input-group checkbox">
                                        <input type="checkbox" id="save-card">
                                        <label for="save-card"><strong>Enregistrer ma carte bancaire</strong><br>Pour faciliter mes prochains achats</label>
                                    </div>
    
                                    <button type="submit" class="submit-btn">Enregistrer</button>
                                </form>
                            </section>';

                    }


                

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

            setlocale(LC_TIME, 'fr_FR.UTF-8'); 

            // Ajoute 2 jours (2 * 24 * 60 * 60 secondes) à la date actuelle
            $datePlus2Jours = time() + (2 * 24 * 60 * 60);

            $datePlus5Jours = time() + (5 * 24 * 60 * 60);
            $datePlus7Jours = time() + (7 * 24 * 60 * 60);
                        
            echo '
                <div class="recap-panier">
                    <p>Produits (' . $quantite . ') </p>
                    <p>Sous-Total : ' . $prix . ' €</p>
                </div>

                <form action="ajouterCommande.php" method="POST" >

                    <div class="livraison-container" >
                        <input type="radio" id="Standard" name="typeLivraison" value="Standard" checked>
                        <label class="livraison-card" for="Standard">
                            <div class="livraison-content">
                                <div class="livraison-header">Livraison standard <span class="prix gratuit">Gratuit</span></div>
                                    <div class="livraison-details">
                                        <p>Livraison entre le '.strftime("%e", $datePlus5Jours) .' et le '. strftime("%e %B", $datePlus7Jours) .'</p>
                                    </div>
                            </div>
                        </label>

                        <input type="radio" id="Express" name="typeLivraison" value="Express" >
                        <label class="livraison-card" for="Express">
                            <div class="livraison-content">
                                <div class="livraison-header">Livraison express <span class="prix payant">3,99€</span></div>
                                    <div class="livraison-details">
                                        <p>Livraison prévue le '. strftime("%e %B", $datePlus2Jours) .'</p>
                                    </div>
                            </div>
                        </label>
                    </div>

                    <input type="text" name="idAdresse" value="', $idAdresse,'" hidden>  
                    <button type="submit" class="valider-panier" >Payer</button>
                </form>
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