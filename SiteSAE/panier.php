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
                                    <button type="button" class="delete-btn" onclick="">Supprimer</button>
                            </div>
                        </div>

                    </div> ';

                }

                $panier->closeCursor();

            } else {

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