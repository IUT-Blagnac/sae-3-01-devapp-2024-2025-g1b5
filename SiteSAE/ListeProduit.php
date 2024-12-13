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
echo '<input type="number" name="prixMin" placeholder="Prix min" value="' . (isset($_POST['prixMin']) ? $_POST['prixMin'] : 0) . '" />';
echo '<input type="number" name="prixMax" placeholder="Prix max" value="' . (isset($_POST['prixMax']) ? $_POST['prixMax'] : 100) . '" />';

echo '<input type="submit" value="Valider" />';
echo '</form>';

//Bouton déroulant de choix d'affichage
$Allproduit=[] ;
$produitParAge =[];
$produitParTaille =[];
$produitParType =[];


$produitParPromo =[];
$produitParBestSell =[];

$req = $conn->prepare("SELECT idProduit FROM Regroupement,Produit_Regroupement WHERE nomRegroupement='Promotion' AND Regroupement.idRegroupement=Produit_Regroupement.idRegroupement");
$req->execute();
while ($row = $req->fetch(PDO::FETCH_ASSOC))
{
    $produitParPromo[] = $row;
}
$req = $conn->prepare("SELECT idProduit FROM Regroupement,Produit_Regroupement WHERE nomRegroupement='Promotion' AND Regroupement.idRegroupement=Produit_Regroupement.idRegroupement");
$req->execute();
while ($row = $req->fetch(PDO::FETCH_ASSOC))
{
    $produitParBestSell[] = $row;
}
//recuperer tout les produits de la base de donnée
$req = $conn->prepare("SELECT * FROM Produit");
$req->execute();
while ($row = $req->fetch(PDO::FETCH_ASSOC))
{
    $Allproduit[] = $row;
}

//fonction qui trie les produits par age par taille par type par promo et par bestsell

 
//etoile en jaune 
function afficherEtoiles($note, $maxEtoiles = 5) {
    $html = '';
    $entier = floor($note); // Partie entière de la note
    $decimal = $note - $entier; // Partie décimale

    // Affichage des étoiles pleines
    for ($i = 1; $i <= $entier; $i++) {
        $html .= '<span style="color: yellow; font-size: 1.5em;">★</span>';
    }

    // Affichage d'une demi-étoile si la partie décimale est supérieure ou égale à 0.5
    if ($decimal >= 0.5) {
        $html .= '<span style="color: yellow; font-size: 1.5em;">☆</span>';
    }

    // Compléter avec des étoiles vides si nécessaire
    for ($i = $entier + ($decimal >= 0.5 ? 1 : 0); $i < $maxEtoiles; $i++) {
        $html .= '<span style="font-size: 1.5em">☆</span>';
    }

    return $html;
}

echo '<br>';
echo '<br>';
echo '<br>';
//Affichage des produits avec les informations de chaque produit image puis en dessous le nom puis le prix et en dessous la note en etoile
//image adaptable en taille
function afficherProduit($produit, $nbColonnes) {
    // Calcul de la largeur d'une cellule en pourcentage
    $largeurCellule = 100 / $nbColonnes;

    // Affichage de la cellule
    echo '<td style="text-align:center; width:' . $largeurCellule . '%; vertical-align:top;">';
    echo '<a href="descriptionDetail.php?idProduit='.$produit['idProduit'].'" style="text-decoration:none; color:black;">';
    
    // Conteneur pour garantir un ratio carré
    echo '<div style="width:100%; height:0; padding-bottom:100%; position:relative;">';
    echo '<img src=" alt="image" style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0;">';
    echo '</div>';
    
    // Affichage des détails du produit
    echo '<p style="margin:5px 0; font-weight:bold;">' . htmlspecialchars($produit['nomProduit']) . '</p>';
    echo '<p style="margin:5px 0; color:gray;">Prix : ' . htmlspecialchars($produit['prix']) . ' €</p>';
    echo '<p style="margin:5px 0;">' . afficherEtoiles($produit['noteGlobale']) . '</p>';
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

$age = isset($_GET['age']) ? $_GET['age'] : null;
$promo = isset($_GET['promo']) ? $produitParPromo : null;
$type = isset($_GET['idCategorie']) ? $_GET['idCategorie'] : null;
$bestS = isset($_GET['bestsell']) ? $produitParBestSell : null;
echo '<br>';
echo '<br>';
//ajouter lidcategorie des sous categorie dune categorie dans un tableau
$tabC=[];
print_r($type);
foreach($scategorie as $s){
    if($s['idCategorie'] == $type){
        $tabC[] = $s['idSousCategorie'];
    }
}

foreach($tabC as $s){
    foreach($scategorie as $sc){
        if($sc['idCategorie'] == $s){
            $tabC[] = $sc['idSousCategorie'];
        }
    }
}
if($tabC==null){
    $tabC[] = $type;
}
function triListeProduit($produitAge, $produitType, $idRegroupementPromo, $IdregroupementBS, $Allproduit) {
    $produit = [];
    

    // Récupération des paramètres POST
  
    // Parcours de tous les produits
    foreach ($Allproduit as $p) {
        // Filtrer par âge
        if ($produitAge !== null && $p['age'] >= $produitAge) {
            $produit[] = $p;
        } 
        // Filtrer par type
        elseif ($produitType !== null && in_array($p['idCategorie'],$produitType)) {
            $produit[] = $p;
        }
        // Filtrer par promo
        elseif (is_array($idRegroupementPromo) && in_array($p['idProduit'], $idRegroupementPromo)) {
            $produit[] = $p;
        }
        // Filtrer par best-sell
        elseif (is_array($IdregroupementBS) && in_array($p['idProduit'], $IdregroupementBS)) {
            $produit[] = $p;
        }
       
    }

    return $produit;
}
$produit = triListeProduit($age,$tabC,$promo,$bestS,$Allproduit);
$produit = triProduit($produit,isset($_POST['choix']) ? $_POST['choix'] : 0);
$produit = filtrePrix($produit,isset($_POST['prixMin']) ? $_POST['prixMin'] : 0,isset($_POST['prixMax']) ? $_POST['prixMax'] : 100);
afficherTableauProduit($produit,5);
//test de la fonction afficherTableauProduit

?>
