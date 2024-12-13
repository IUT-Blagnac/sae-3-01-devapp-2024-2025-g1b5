<?php
include "header.php";
include 'Connect.inc.php';

// Récupérer l'idProduit depuis l'URL
$idProduit = isset($_GET['idProduit']) ? intval($_GET['idProduit']) : 0;

// Si l'idProduit est valide, récupérer les détails du produit
if ($idProduit > 0) {
    $prod = $conn->prepare("SELECT * FROM Produit WHERE idProduit = ?");
    $prod->execute([$idProduit]);
    $produit = $prod->fetch();
    $prod->closeCursor();
}

//etoile en jaune 
function afficherEtoiles($note, $maxEtoiles = 5) {
    $html = '';
    for ($i = 1; $i <= $maxEtoiles; $i++) {
        if ($i <= $note) {
            $html .= '<span style="color: yellow; font-size:1.5em" ; >★</span>';
        } else {
            $html .= '<span style = "font-size:1.5em" >☆</span>';
        }
    }
    return $html;
}

//recupere le nb max de produit en stock
$req = $conn->prepare("SELECT quantiteStock FROM Stock WHERE idProduit = ?");
$req->execute([$idProduit]);
$stock_max = $req->fetchColumn();
$req->closeCursor();

?>

<!-- Script js qui permet de choisir une bonne quantite de produit -->
<script>
    function ajusterQuantite() {
        var quantite = document.getElementById("quantite");
        var stockMax = <?php echo $stock_max; ?>

        if (quantite.value < 1) {
            quantite.value = 1;
        }
        if (quantite.value > stockMax ) {
            quantite.value = stockMax ; 
        }
    }
</script>

<section class="presentation">
    <img src="images/produits/Prod<?php echo $produit['idProduit'] ; ?>.jpg"  width="50%" alt="<?php echo $produit['nomProduit'] ; ?>">
    <div>
        <h1>  <?php echo $produit['nomProduit'] ; ?> </h1>
        <p>Ref : 8172347</p>
        <p>Age :  <?php echo $produit['age'] ; ?>  ans</p>

        <div class="prixDescription">
            <h2>  <?php echo $produit['prix'] ; ?> €</h2>
        </div>
 
        <form action="ajouterPanier.php" method="get">
            <input type="text" value="<?php echo $produit['idProduit'] ; ?>" name="idProduit" hidden>
            <button type="submit" class="button" >Ajouter au panier</button>
            <input type="number"  id="quantite" value="1" name="quantite" min="1" oninput="ajusterQuantite()" >
        </form>
        <button type="button" class="butFavoris"> <img src="images/petit-coeur-rouge.png" alt="petit coeur" width="20px"> </button>

    </div>
</section>

<section class="description">

    <div class="texteDescriptif">

        <h2>Description</h2>

        <p>
            <?php echo $produit['description'] ; ?>
        </p>

        <h2>Caractéristiques</h2>

        <ul style="list-style-type: none">
            <li><u>Âge</u> : <?php echo $produit['age'] ; ?> ans  </li>
            <li><u>Dimensions</u> : <?php echo $produit['taille'] ; ?>  cm </li>
            <li><u>Type</u> : construction</li>
            <li><u>Nombre de joueurs</u> : <?php echo $produit['nbJoueurMax'] ; ?>  </li>
        </ul>


    </div>

</section>


<section class="avis">

    <h2>Avis</h2>
    
    <?php
        
        //recupere les avis client pour le produit consulte
        $res = $conn->prepare("SELECT * FROM Avis WHERE idProduit = ? ORDER BY dateAvis DESC");
        $res->execute([$idProduit]);

        $nbAvis = 0 ;

        while( ($avis = $res -> fetch()) and $nbAvis < 3) {

            //recupere le nom du client qui a ecrit l'avis
            $req = $conn->prepare("SELECT * FROM Client WHERE idClient = ?");
            $req->execute([$avis['idClient']]);
            $client = $req->fetch();
            $req->closeCursor();

            echo '<section class="evaluation">';

            echo '<div class="notes">
                    <button type="button" class="butAvatar" onclick=" "> <img src="images/perso-avatar.png" alt="avatar"> </button>' ;
            echo '<h3>'. $client['nom'] . " " . $client['prenom'] .'</h3>';
            echo afficherEtoiles($avis['note']) ; //appel de fonction affcherEtoiles
            echo '<h3>'. $avis['note'] .'/5</h3>
                </div>';


            echo '<div class="eval-perso">';

                echo '<p>' . $avis['contenu'] . '</p>';

                $date1 = $avis['dateAvis'];
                setlocale(LC_TIME, "fr_FR");
                $date = strftime("%d/%m/%Y", strtotime($date1));

                echo '<p class="date-avis">
                    Avis du <strong>' . $date    . '</strong>
                    </p>

                </div>';

            echo '</section>' ;

            $nbAvis++;

        }

        $res->closeCursor();


    ?>

</section>
    
    


<?php
    include "footer.php";
?>

</body>

</html>