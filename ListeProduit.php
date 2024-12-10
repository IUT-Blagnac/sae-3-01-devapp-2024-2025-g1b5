<?php
include 'header.php';
include 'tableauxProduit.php';


echo '<br>';
$choix = isset($_POST['choix']) ? $_POST['choix'] : "0";

echo '<form action="ListeProduit.php" method="post">';
echo '<select name="choix">';

// Générer les options avec "selected" pour la valeur choisie
$options = [
    "0" => "Trie par défaut",
    "1" => "Trie par nom",
    "2" => "Trie par prix croissant",
    "3" => "Trie par prix décroissant",
    "4" => "Trie par note croissante",
    "5" => "Trie par note décroissante",
];

foreach ($options as $key => $value) {
    // Vérifie si la clé correspond à la valeur postée
    $selected = ($key == $choix) ? 'selected' : '';
    echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
}


echo '</select>';
//prix min et max
echo '<input type="number" name="prixMin" placeholder="Prix min" />';
echo '<input type="number" name="prixMax" placeholder="Prix max" />';

echo '<input type="submit" value="Valider" />';
echo '</form>';

/*Bouton déroulant de choix d'affichage
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
            $html .= '<span style="color: yellow; font-size: 1.5em;">★</span>';
        } else {
            $html .= '<span style ="font-size: 1.5em">☆</span>';
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
//http://193.54.227.208/~R2024SAE3010/descriptionDetail.php?idProduit=2

    // Affichage de la cellule
    echo '<td style="text-align:center; width:' . $largeurCellule . '%; vertical-align:top;">';
    echo '<a href="descriptionDetail.php?idProduit='.$produit['id'].'" style="text-decoration:none; color:black;">';
    
    // Conteneur pour garantir un ratio carré
    echo '<div style="width:100%; height:0; padding-bottom:100%; position:relative;">';
    echo '<img src="' . htmlspecialchars($produit['image']) . '" alt="image" style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;">';
    echo '</div>';
    
    // Affichage des détails du produit
    echo '<p style="margin:5px 0; font-weight:bold;">' . htmlspecialchars($produit['nom']) . '</p>';
    echo '<p style="margin:5px 0; color:gray;">Prix : ' . htmlspecialchars($produit['prix']) . ' €</p>';
    echo '<p style="margin:5px 0;">' . afficherEtoiles($produit['note']) . '</p>';
    echo '</td>';
    echo '</a>';
}

//tri avec des case retourne un tableau de produit trier selont le choix

function triProduit($produit,$choix){
    switch($choix){
        case 1:
            usort($produit, function($a, $b) {
                return $a['nom'] <=> $b['nom'];
            });
            break;
            
        case 2:
            usort($produit, function($a, $b) {
                return $a['prix'] <=> $b['prix'];
            });
            break;
        case 3:
            usort($produit, function($a, $b) {
                return $b['prix'] <=> $a['prix'];
            });
            break;
        case 4:
            usort($produit, function($a, $b) {
                return $a['note'] <=> $b['note'];
            });
            break;
        case 5:
            usort($produit, function($a, $b) {
                return $b['note'] <=> $a['note'];
            });
            break;
        
    }
    return $produit;
}

//fonction qui affiche sur une tranche de prix donner
function filtrePrix($produit,$prixMin,$prixMax){
    $produitFiltre = [];
    foreach($produit as $p){
        if($p['prix'] >= $prixMin && $p['prix'] <= $prixMax){
            $produitFiltre []= $p;
        }
    }
    return $produitFiltre;
}


//fonction qui permet dafficher un tableau de produit et appel la fonction afficherProduit pour chaque produit
function afficherTableauProduit($tableauProduit, $nbColonnes = 3) {
    // Début de la table
    echo '<table style="width:100%; text-align:center;">';
    
    // Initialisation de l'indice pour suivre la position
    $compteur = 0;

    foreach ($tableauProduit as $produit) {
        // Ouvrir une nouvelle rangée au début ou après chaque ligne complète
        if ($compteur % $nbColonnes == 0) {
            echo '<tr>';
        }

        // Afficher le produit
        afficherProduit($produit, $nbColonnes);

        $compteur++;

        // Fermer la rangée après un certain nombre de colonnes
        if ($compteur % $nbColonnes == 0) {
            echo '</tr>';
        }
    }

    // Compléter la dernière rangée si elle est incomplète
    if ($compteur % $nbColonnes != 0) {
        $casesRestantes = $nbColonnes - ($compteur % $nbColonnes);
        for ($i = 0; $i < $casesRestantes; $i++) {
            echo '<td></td>'; // Cases vides
        }
        echo '</tr>'; // Fermer la dernière rangée
    }

    // Fin de la table
    echo '</table>';
}


//test de la fonction de tri
$produit = defProduit(20);
$produit = triProduit($produit,isset($_POST['choix']) ? $_POST['choix'] : 0);
$produit = filtrePrix($produit,isset($_POST['prixMin']) ? $_POST['prixMin'] : 0,isset($_POST['prixMax']) ? $_POST['prixMax'] : 100);
afficherTableauProduit($produit,5);
//test de la fonction afficherTableauProduit


?>


    

