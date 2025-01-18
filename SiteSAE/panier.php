<?php
    require_once "header.php";
    include "Connect.inc.php";

    //session_start();

        if (isset($_SESSION['client_email']) ) {

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
	// Cette fonction permettra de demander la connexion a son compte client
	function alertConnexion() {
		if(confirm("Il faut être connecté, voulez-vous vous connecter ?")){
			document.location.href = "connexionCompte.php";
		} 
	}
</script>

<section class="panier">

    <div class="votre-panier">
        <h1>Votre Panier</h1>

        <?php

            if ( $idClient != 0 ) {

                $panier = $conn->prepare("SELECT * FROM Panier_Client pc, Produit p WHERE pc.idProduit = p.idProduit AND idClient = ?");
                $panier->execute([$idClient]);
                $panierDuClient =[];
              
                if ( $panier -> rowCount() >= 1 ){

                    while( $produit_panier = $panier -> fetch() ) {
                        $panierDuClient[] = $produit_panier;

                        echo '<div class="produit-panier"> ';
                        echo  '<img src="image_Produit/Prod'.$produit_panier['idProduit'].'.jpg" alt="'.$produit_panier['nomProduit'].'"  >';
    
                        echo '  <div class="info-produit-panier">
                                    <p> ' . $produit_panier['nomProduit'] . '</p>
                                    <div class="prix">
                                        <p> '. $produit_panier['prix'] .' </p>
                                        <p> '. $produit_panier['quantite'] .' </p>

                                        <form action="supprimerPanier.php" method="POST" style="display: inline;">
                                            <input type="text" value="'. $produit_panier['idProduit'] . '" name="idProduit" hidden>
                                            <input type="text" value="'. $idClient . '" name="idClient" hidden>
                                            <button type="submit" class="quantity-btn minus-btn" ></button>
                                        </form>

                                        <form action="ajouterPanier.php" method="get" style="display: inline;">
                                            <input type="text" value="'. $produit_panier['idProduit'] . '" name="idProduit" hidden>
                                            <input type="number" value="1" name="quantite" hidden>
                                            <input type="number" value="1" name="confirmation" hidden>
                                            <button type="submit" class="quantity-btn plus-btn" ></button>
                                        </form>
                                </div>
                            </div>
    
                        </div> ';
    
                    }
    
                    $panier->closeCursor();

                } else {
                    echo 'Votre panier est vide !<br>Remplissez-le !';
                }
                

            } else {

                if (isset($_SESSION['panier']) ) {

                    foreach ($_SESSION['panier'] as $idProd => $quantite) {

                        $res = $conn->prepare("SELECT * FROM Produit WHERE idProduit = ?");
                        $res->execute([$idProd]);
                        $prod = $res->fetch();
                        $res->closeCursor();
                        
                        echo '<div class="produit-panier"> ';
                        echo  '<img src="image_Produit/Prod'.$idProd.'.jpg" alt="'.$prod['nomProduit'].'"  >';

                        echo '  <div class="info-produit-panier">
                                    <p> ' . $prod['nomProduit'] . '</p>
                                    <div class="prix">
                                        <p> '. $prod['prix'] .' €</p>
                                        <p> Quantite : '. $quantite .' </p>
                                        
                                        <form action="supprimerPanier.php" method="POST" style="display: inline;">
                                            <input type="text" value="'. $idProd . '" name="idProd" hidden>
                                            <button type="submit" class="quantity-btn minus-btn" ></button>
                                        </form>

                                        <form action="ajouterPanier.php" method="get" style="display: inline;">
                                            <input type="text" value="'. $idProd . '" name="idProduit" hidden>
                                            <input type="number" value="1" name="quantite" hidden>
                                            <button type="submit" class="quantity-btn plus-btn" ></button>
                                        </form>
                                </div>
                            </div>

                        </div> ';

                    }

                } else {
                    echo 'Votre panier est vide !<br>Remplissez-le !';
                }

            }

        ?>

    </div>

        <?php
       $pClient = []; 
       $idCodePromoP =[];
       $code = $conn->prepare("SELECT cp.idPromo,NomCodePromo,CodePromo,reduction,dateFin,dateDebut FROM codePromotion cp ,Panier_Client_Promo pcp WHERE pcp.idClient = ? AND cp.idPromo = pcp.idPromo");

       $code->execute([$idClient]);
       $reduc = 0;
       // Utiliser une autre variable dans la boucle pour éviter d'écraser $code
       while ($row = $code->fetch(PDO::FETCH_ASSOC)) {
        if (!in_array($row, $pClient)) {
            if ($row['dateFin'] >= date("Y-m-d") && $row['dateDebut'] <= date("Y-m-d")) {

           $pClient[] = $row;
           $reduc += $row['reduction'];
           $idCodePromoP []= $row['idPromo'];
        }
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
                            echo"<br>";
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

            $panier = $conn->prepare("SELECT * FROM Panier_Client pc, Produit p 
                        WHERE pc.idProduit = p.idProduit AND idClient = ?");
            $panier->execute([$idClient]);
                
            if ( $panier -> rowCount() >= 1 ){


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

            }
                        
            echo '
                <div class="recap-panier">
                    <p>Produits (' . $quantite . ')</p>
                   
                    <p>Sous-Total : ' . $prix . ' €</p>
                </div>
            ';

            echo '<form action="commande-choix.php" method="POST">
                    <input type="hidden" name="quantite" value="' . $quantite . '">';

            if ($quantite != 0) {
                echo '<button type="submit" class="valider-panier">Valider mon Panier</button>';
            } else {
                echo '<button type="submit" class="valider-panier" disabled>Valider mon Panier</button>';
            }

            echo '</form>';

        } else {

            if (isset($_SESSION['panier']) ) {
                $qteTotale = 0;
                $prixTotal = 0;

                foreach ($_SESSION['panier'] as $idProd => $qteProd) {
                    //Permet de calculer le prix du produit du tableau de la session
                    $res = $conn->prepare("SELECT * FROM Produit WHERE idProduit = ?");
                    $res->execute([$idProd]);
                    $prod = $res->fetch();
                    $res->closeCursor();

                    $qteTotale += $qteProd;
                    $prixTotal += $prod['prix'] * $qteProd;
                }

                echo '
                    <div class="recap-panier">
                        <p>Produits (' . $qteTotale. ') </p>
                        <p>Sous-Total : ' . $prixTotal . ' €</p>
                    </div>

                    ';

                echo ' <a href="javascript:alertConnexion()"><button type="button" class="valider-panier" >Valider mon Panier</button></a> ';
            }

        }

    ?>


    </div>

</section>

<?php
    include "footer.php";
?>
</body>
</html>