<?php
include 'header.php';
include 'tableauxProduit.php';

/*Bouton déroulant de choix d'affichage

echo '<form action="ListeProduit.php" method="post">';

echo '<select name="choix">';
echo '<option value="1">Tous les produits</option>';
echo '<option value="2">Produits en stock</option>';

echo '</select>';
echo '<input type="submit" value="Valider" />';
echo '</form>';

fonction qui retourne une case de tableau qui contient les informations d'un produit sont nom limage associer sont prix et sa note en etoile 
*/

//fonction qui donne un tableau de produit fictif avec faker

function defProduit($nbProduit){
    $faker = Faker\Factory::create();
    $produit = [];
    for($i=0;$i<$nbProduit;$i++){
        $produit []= array('id' => $i+1, 'nom' => $faker->name, 'prix' => $faker->randomFloat(2,0,100) , 'note' => $faker->numberBetween(0,5), 'image' => $faker->imageUrl(100,100));
    }
    return $produit;
}
//etoile en jaune 
function afficherEtoiles($note, $maxEtoiles = 5) {
    $html = '';
    for ($i = 1; $i <= $maxEtoiles; $i++) {
        if ($i <= $note) {
            $html .= '<span style="color: yellow;">★</span>';
        } else {
            $html .= '<span>☆</span>';
        }
    }
    return $html;
}
echo '<br>';
echo '<br>';
echo '<br>';
//Affichage des produits avec les informations de chaque produit image puis en dessous le nom puis le prix et en dessous la note en etoile
//image adaptable en taille
$produit = defProduit(20);
function afficherProduit($produit, $nbColonnes) {
    // Calcul de la largeur d'une cellule en pourcentage
    $largeurCellule = 100 / $nbColonnes;

    // Affichage de la cellule
    echo '<td style="text-align:center; width:' . $largeurCellule . '%; vertical-align:top;">';
    
    // Conteneur pour garantir un ratio carré
    echo '<div style="width:100%; height:0; padding-bottom:100%; position:relative;">';
    echo '<img src="' . htmlspecialchars($produit['image']) . '" alt="image" style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;">';
    echo '</div>';
    
    // Affichage des détails du produit
    echo '<p style="margin:5px 0; font-weight:bold;">' . htmlspecialchars($produit['nom']) . '</p>';
    echo '<p style="margin:5px 0; color:gray;">Prix : ' . htmlspecialchars($produit['prix']) . ' €</p>';
    echo '<p style="margin:5px 0;">' . afficherEtoiles($produit['note']) . '</p>';
    echo '</td>';
}



//fonction qui permet dafficher un tableau de produit et appel la fonction afficherProduit pour chaque produit
function afficherTableauProduit($tableauProduit,$nbcase){
    //table adapter a la taille de l'ecran
    echo '<table style="width:100%">';

    echo '</tr >';
    foreach($tableauProduit as $produit){
        if($produit['id']%$nbcase==1){
            echo '<tr>';
        }
        afficherProduit($produit,$nbcase);
        if($produit['id']%$nbcase==0){
            echo '</tr>';
        }
    }
    echo '</table>';
}

//test de la fonction afficherTableauProduit
afficherTableauProduit($produit,5);


?>


    

