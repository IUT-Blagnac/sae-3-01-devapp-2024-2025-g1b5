<?php
include "header.php";
?>


<section class="panier">

    <div class="votre-panier">
        <h1>Votre Panier</h1>

        <div class="produit-panier">
            <img src="images/kit-batteries-enfant.png" alt="Image kit de batteries pour enfant"  >

            <div class="info-produit-panier">
                <p>Kit batteries pour enfant grosse caisse + tambours + cymbale</p>
                <div class="prix">
                    <p>39,99 €</p>
                    <button type="button" class="delete-btn" onclick="">Supprimer</button>
                </div>
            </div>
            
        </div>

        <div class="produit-panier">
            <img src="images/pistolet-enfant-16-flechettes.png" alt="Image pistolet enfant"  >

            <div class="info-produit-panier">
                <p>Pistolet enfant + 16 fléchettes en mousses</p>
                <div class="prix">
                    <p>34,99 €</p>
                    <button type="button" class="delete-btn" onclick="">Supprimer</button>
                </div>                
            </div>
            
        </div>


    </div>

    <div class="recapitulatif">
        <h1>Récapitulatif</h1>

        <div class="recap-panier">
            <div class="code-promo">
                <label for="code-promo">Code Promo :</label>
                <input type="text" class="promo" placeholder="Ecrivez le ici ...">
            </div>
        </div>

        <div class="recap-panier">
            <p>Produits (2) </p>
            <p>Sous-Total : 74,98 €</p>
        </div>

        <button type="button" class="valider-panier" onclick="">Valider mon Panier</button>
       

    </div>

</section>

<?php
include "footer.php";
?>
</body>
</html>