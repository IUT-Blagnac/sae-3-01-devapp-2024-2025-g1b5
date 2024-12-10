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
function afficherEtoiles($note, $maxEtoiles = 5) {
    // Initialisation des étoiles
    $etoiles = '';
    
    // Ajouter les étoiles pleines
    for ($i = 1; $i <= $note; $i++) {
        $etoiles .= '★';
    }

    // Ajouter les étoiles vides
    for ($i = $note + 1; $i <= $maxEtoiles; $i++) {
        $etoiles .= '☆';
    }

    return $etoiles;
}
//Affichage des produits avec les informations de chaque produit image puis en dessous le nom puis le prix et en dessous la note en etoile
//image adaptable en taille
$produit = defProduit(20);
function afficherProduit($produit){
    echo '<td>';
    echo '<img src="'.$produit['image'].'">';
    echo '<p>'.$produit['nom'].'</p>';
    echo '<p>'.$produit['prix'].'</p>';
    echo '<p>'.afficherEtoiles($produit['note']).'</p>';
    echo '</td>';
}

//fonction qui permet dafficher un tableau de produit et appel la fonction afficherProduit pour chaque produit
function afficherTableauProduit($tableauProduit){
    echo '<table>';

    echo '</tr >';
    foreach($tableauProduit as $produit){
        if($produit['id']%5==1){
            echo '<tr>';
        }
        afficherProduit($produit);
        if($produit['id']%5==0){
            echo '</tr>';
        }
    }
    echo '</table>';
}

//test de la fonction afficherTableauProduit
afficherTableauProduit($produit);


?>


    

