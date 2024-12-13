<?php
    include "header.php";
    include "Connect.inc.php";

    //session_start();

        if (isset($_SESSION['client_email']) or isset($_COOKIE['CidClient'])) {

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

                while( $produit_panier = $panier -> fetch() ) {

                    echo '<div class="produit-panier"> ';
                    echo  '<img src="images/produits/Prod'.$produit_panier['idProduit'].'.jpg" alt="'.$produit_panier['nomProduit'].'"  >';

                    echo '  <div class="info-produit-panier">
                                <p> ' . $produit_panier['nomProduit'] . '</p>
                                <div class="prix">
                                    <p> '. $produit_panier['prix'] .' </p>
                                    <p> '. $produit_panier['quantite'] .' </p>

                                    <form action="supprimerPanier.php" method="POST">
                                        <input type="text" value="'. $produit_panier['idProduit'] . '" name="idProduit" hidden>
                                        <input type="text" value="'. $idClient . '" name="idClient" hidden>
                                        <button type="submit" class="delete-btn" >Supprimer</button>
                                    </form>
                            </div>
                        </div>

                    </div> ';

                }

                $panier->closeCursor();

            } else {

                if (isset($_SESSION['panier']) ) {

                    foreach ($_SESSION['panier'] as $idProd => $quantite) {

                        $res = $conn->prepare("SELECT * FROM Produit WHERE idProduit = ?");
                        $res->execute([$idProd]);
                        $prod = $res->fetch();
                        $res->closeCursor();
                        
                        echo '<div class="produit-panier"> ';
                        echo  '<img src="images/produits/Prod'.$idProd.'.jpg" alt="'.$prod['nomProduit'].'"  >';

                        echo '  <div class="info-produit-panier">
                                    <p> ' . $prod['nomProduit'] . '</p>
                                    <div class="prix">
                                        <p> '. $prod['prix'] .' </p>
                                        <p> '. $quantite .' </p>
                                        <form action="supprimerPanier.php" method="POST">
                                            <input type="text" value="'. $idProd . '" name="idProd" hidden>
                                            <button type="submit" class="delete-btn" >Supprimer</button>
                                        </form>
                                </div>
                            </div>

                        </div> ';

                    }

                } else {
                    echo 'Votre panier est vide !';
                    echo '<br>Remplissez-le !';
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
            $res = $conn->prepare("SELECT sum(quantite), sum(prix) FROM Panier_Client pc, Produit p WHERE pc.idProduit = p.idProduit AND idClient = ?");
            $res->execute([$idClient]);
            $prix_qte = $res->fetch();
            $res->closeCursor();

            echo '
                <div class="recap-panier">
                    <p>Produits (' . $prix_qte['sum(quantite)'] . ') </p>
                    <p>Sous-Total : ' . $prix_qte['sum(prix)'] . ' €</p>
                    <p>Regler le probleme avec une procedure pour le prix</p>
                </div>

                <button type="button" class="valider-panier" onclick="">Valider mon Panier</button>
            ';

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

                    <button type="button" class="valider-panier" onclick="">Valider mon Panier</button>
                ';
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