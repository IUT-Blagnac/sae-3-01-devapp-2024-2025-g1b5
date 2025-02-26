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

<section class="panier">

    <div class="votre-panier">
        <h1>Votre Panier</h1>

        <?php

            if ( $idClient != 0 ) {

                $panier = $conn->prepare("SELECT * FROM Panier_Client pc, Produit p WHERE pc.idProduit = p.idProduit AND idClient = ?");
                $panier->execute([$idClient]);
                $panierDuClient =[];


                while( $produit_panier = $panier -> fetch() ) {
                    $panierDuClient[] = $produit_panier;

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
                                    <div class='paypal-info'>
                                        <input type='radio' id='carteBancaire' name='methodePaiement' value='carteBancaire' checked>
                                        <label for='carteBancaire' >Utiliser la carte Bancaire</label>
                                    </div>
                                
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
                                <input type="text" name="carteActuelle" value="', $numCarte,'" hidden>                
                                <button type="submit" class="valider-panier">Modifier la carte enregistrée</button>
                            </form>
                        ';

                        if (isset($_SESSION['paypalMail']) && isset($_SESSION['paypalMdp']) ) {

                            echo '
                                <div class="paypal-box">
                                    <div class="paypal-info">
                                        <input type="radio" id="paypal" name="methodePaiement" value="paypal">
                                        <label for="paypal">Utiliser PayPal</label>
                                    </div>
                                    <p class="paypal-description">Vous êtes connecté votre compte PayPal avec le mail '. $_SESSION['paypalMail'] .'</p>
                                </div>
    
                                <form class="choix-adresse" action="choisirPaypal.php" method="POST">   
                                    <button type="submit" class="valider-panier">Choisir PayPal</button>
                                </form>
                            ' ;
                            
                        } else {

                            echo '
                                <div class="paypal-box">
                                    <div class="paypal-info">
                                        <input type="radio" id="paypal" name="methodePaiement" value="paypal">
                                        <label for="paypal">Utiliser PayPal</label>
                                    </div>
                                    <p class="paypal-description">Connectez-vous à votre compte PayPal pour payer.</p>
                                </div>
    
                                <form class="choix-adresse" action="choisirPaypal.php" method="POST">   
                                    <button type="submit" class="valider-panier">Choisir PayPal</button>
                                </form>
                            ' ;
                        }
                        
                      
                    } else {

                        if (isset($_SESSION['numCarte']) && isset($_SESSION['cvv']) ) {

                            $numCarte = $_SESSION['numCarte'] ;
                            $dateCarte = $_SESSION['dateE'] ;
                            $cvv = $_SESSION['cvv'] ;

                            $dernierNumCarte = substr($numCarte, -4);

                            echo "<p>Votre carte ne sera pas enregistrée !</p><br>";

                            echo "                            
                                    <div class='payment-box'>
                                        <div class='paypal-info'>
                                            <input type='radio' id='carteBancaire' name='methodePaiement' value='carteBancaire' checked>
                                            <label for='carteBancaire' >Utiliser la carte Bancaire</label>
                                        </div>
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
                                    <input type="text" name="carteActuelle" value="', $numCarte,'" hidden>                
                                    <button type="submit" class="valider-panier">Modifier la carte enregistrée</button>
                                </form>
                            ';

                            if (isset($_SESSION['paypalMail']) && isset($_SESSION['paypalMdp']) ) {

                                echo '
                                    <div class="paypal-box">
                                        <div class="paypal-info">
                                            <input type="radio" id="paypal" name="methodePaiement" value="paypal">
                                            <label for="paypal">Utiliser PayPal</label>
                                        </div>
                                        <p class="paypal-description">Vous êtes connecté votre compte PayPal avec le mail '. $_SESSION['paypalMail'] .'</p>
                                    </div>
        
                                    <form class="choix-adresse" action="choisirPaypal.php" method="POST">   
                                        <button type="submit" class="valider-panier">Choisir PayPal</button>
                                    </form>
                                ' ;
                                
                            } else {
    
                                echo '
                                    <div class="paypal-box">
                                        <div class="paypal-info">
                                            <input type="radio" id="paypal" name="methodePaiement" value="paypal">
                                            <label for="paypal">Utiliser PayPal</label>
                                        </div>
                                        <p class="paypal-description">Connectez-vous à votre compte PayPal pour payer.</p>
                                    </div>
        
                                    <form class="choix-adresse" action="choisirPaypal.php" method="POST">   
                                        <button type="submit" class="valider-panier">Choisir PayPal</button>
                                    </form>
                                ' ;
                            }
                            
                        } else {
                       
                            echo "Aucun moyen de paiement est enregistré !<br>
                                Ajoutez-en un !" ;

                            echo '
                                <form class="choix-adresse" action="modifCarteBancaire.php" method="POST">                   
                                    <button type="submit" class="valider-panier">Ajouter une carte</button>
                                </form>
                            ';

                            if (isset($_SESSION['paypalMail']) && isset($_SESSION['paypalMdp']) ) {

                                echo '
                                    <div class="paypal-box">
                                        <div class="paypal-info">
                                            <input type="radio" id="paypal" name="methodePaiement" value="paypal" checked>
                                            <label for="paypal">Utiliser PayPal</label>
                                        </div>
                                        <p class="paypal-description">Vous êtes connecté votre compte PayPal avec le mail '. $_SESSION['paypalMail'] .'</p>
                                    </div>
        
                                    <form class="choix-adresse" action="choisirPaypal.php" method="POST">   
                                        <button type="submit" class="valider-panier">Choisir PayPal</button>
                                    </form>
                                ' ;
                                
                            } else {
    
                                echo '
                                    <div class="paypal-box">
                                        <div class="paypal-info">
                                            <input type="radio" id="paypal" name="methodePaiement" value="paypal">
                                            <label for="paypal">Utiliser PayPal</label>
                                        </div>
                                        <p class="paypal-description">Connectez-vous à votre compte PayPal pour payer.</p>
                                    </div>
        
                                    <form class="choix-adresse" action="choisirPaypal.php" method="POST">   
                                        <button type="submit" class="valider-panier">Choisir PayPal</button>
                                    </form>
                                ' ;
                            }

                        }

                    }


                

            } 

        ?>

    </div>

    <?php
       $pClient = []; 
       $idCodePromoP =[];
       $code = $conn->prepare("SELECT cp.idPromo,NomCodePromo,CodePromo,reduction FROM codePromotion cp ,Panier_Client_Promo pcp WHERE pcp.idClient = ? AND cp.idPromo = pcp.idPromo");

       $code->execute([$idClient]);
       $reduc = 0;
       // Utiliser une autre variable dans la boucle pour éviter d'écraser $code
       while ($row = $code->fetch(PDO::FETCH_ASSOC)) {
        if (!in_array($row, $pClient)) {
           $pClient[] = $row;
           $reduc += $row['reduction'];
           $idCodePromoP []= $row['idPromo'];
       }}
       
       //recuperer les code promo du client
       $codepduClient =$conn->prepare("SELECT * FROM codePromotion WHERE idClient = ?");
         $codepduClient->execute([$idClient]);
       //recuperer les code promo actif du client
       while ($row = $codepduClient->fetch(PDO::FETCH_ASSOC)) {
              if($row['dateFin'] >= date("Y-m-d") && $row['dateDebut'] <= date("Y-m-d")){
                $codepC[] = $row;
              }
        }
        $codepduClient->closeCursor();

        ?>
    <div class="recapitulatif">
        <h1>Récapitulatif</h1>

        <div class="recap-panier">
            <?php
            if(!empty($codepC)&& !empty($panierDuClient)
            ){
                echo"<h3>Mes Code promo</h3>";
                foreach ($codepC as $row) {
                    if(!in_array($row["idPromo"], $idCodePromoP)){
                        echo "<p style='margin-bottom: 1rem; background-color: #f8d7da; padding: 5px; border-radius: 5px;'>
                                 Code promo " . $row['NomCodePromo'] . " ";
                        echo " <a href='promoVerif.php?idClient=$idClient&promocode=" . $row['CodePromo'] . "'>
                                <button type='button' class='valider-panier'>Utiliser</button></a></p>";
                    }
                }
                    
            }
            ?>
            <div class="code-promo">
                <label for="code-promo">Code Promo :</label>
                <form action="promoVerif.php?idClient=<?php echo $idClient; ?>" method="POST">
                    <input type="text" name="promocode" id="promocode" placeholder="Entrez votre code promo">
                    <?php if (isset($_GET['error'])) { ?>
                        <p style="color: red;"><?php echo $_GET['error']; ?></p>
                    <?php } 
                            if(count($pClient) >0){
                                if(empty($panierDuClient)){
                                    echo '<button type="submit" class="valider-panier" disabled>Valider </button>';
                                }else{

                            echo '<button type="submit" class="valider-panier">Valider </button>';
                                }
                       foreach ($pClient as $row) {
                            $t=$row['reduction'];
                            $t=$t*100;
                            echo"<br>";
                            echo"<br>";
                            echo"<span>Code promo  ".$row['NomCodePromo']." : ".$row['CodePromo']." -".$t."%</span>";
                            echo '<a href="supprimerCPanierPromo.php?idPromo='.$row['idPromo'].'&idClient='.$idClient.'" 
                            style="display: inline-block; background-color:rgb(255, 0, 0); color: white; text-decoration: none; padding: 5px 10px; border-radius: 5px; text-align: center;">
                            Supprimer
                         </a>';
                    
                       }

                    }else{
                        if($idClient != 0){
                            if(empty($panierDuClient)){
                                echo '<button type="submit" class="valider-panier" disabled>Valider </button>';
                            }else{

                        echo '<button type="submit" class="valider-panier">Valider </button>';
                            }
                        }else{
                echo ' <center><a href="javascript:alertConnexion()"><button type="button" class="valider-panier" >Valider</button></a> </center>';

                        }
                    }
                    ?>
                    
                </form>
            </div>
        </div>


    <?php

        if ( $idClient != 0 ) {

            //on définit les paramètres retour
            $prix = 0;
            $quantite = 0;

            // on définit la requete d'appel de la procédure stockée 
            $recapPanier = 'CALL RecapPanier( :idClient, @quantiteTotale, @prixTotal )';
            try {

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
            
            } catch (PDOException $e) {
                echo "Erreur lors de l'appel de la procédure stockée !";
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

                <form action="ajouterCommande.php?prix='.$prix.'" method="POST" >

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
                                <div class="livraison-header">Livraison express <span class="prix payant">Gratuit</span></div>
                                    <div class="livraison-details">
                                        <p>Livraison prévue le '. strftime("%e %B", $datePlus2Jours) .'</p>
                                    </div>
                            </div>
                        </label>
                    </div>

                    <input type="text" name="idAdresse" value="', $idAdresse,'" hidden>  
            ';
                    
                if ( $idAdresse && ($carteBancaire || isset($_SESSION['numCarte']) ) ) {
                    echo ' <button type="submit" class="valider-panier" >Payer</button>';
                } else {
                    echo ' <button type="submit" class="valider-panier" disabled>Payer</button>';
                }
                    
                echo'</form>';

        } 

    ?>


    </div>

</section>

<?php
    include "footer.php";
?>
</body>
</html>