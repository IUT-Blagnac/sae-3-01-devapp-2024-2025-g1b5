<?PHP
require_once "header.php";
require_once "tableauxProduit.php";
?>
<div class="img-acceuil">
  <img src="images/guirlandes-accueil.png" alt="Image guirlandes page d'acceuil">
</div>
<?php
require_once "Connect.inc.php";
?>
<br>
<br>
<h1> Produits à la une </h1>
<section class="produits-accueil">
  <?php
    afficherLProduitsA(getproduit($Allproduit,$produitAlaUne),$produitParPromo);
    /*
    <div>
      <a href="descriptionProduit.php"> <img src="images/circuit-a-bille-138-pieces.png" alt="Image circuit de bille"> </a>
      <p>Circuit à bille 138 pièces + 30 billes</p>
      <div class="prix">
        <p>49,99 €</p>
      </div>
      <button type="button" class="button" onclick="">Ajouter au panier</button>
    </div>
    
    <div>
      <img src="images/kit-batteries-enfant.png" alt="Image kit de batteries pour enfant" width="70%">
      <p>Kit batteries pour enfant grosse caisse + tambours + cymbale</p>
      <div class="prix">
        <p>39,99 €</p>
      </div>
      <button type="button" class="button" onclick="">Ajouter au panier</button>
    </div>
    
    <div>
      <p>Pistolet enfant + 16 fléchettes en mousses</p>
      <div class="prix">
        <p>34,99 €</p>
      </div>
      <button type="button" class="button" onclick="">Ajouter au panier</button>
    </div>
    */
      ?>

</section>


<?PHP
include "footer.php";
?>
</body>

</html>