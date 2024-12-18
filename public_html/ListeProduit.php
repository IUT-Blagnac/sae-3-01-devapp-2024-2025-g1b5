<?php
include 'header.php';
include 'tableauxProduit.php';
?>


<?php

$choix = isset($_POST['choix']) ? $_POST['choix'] : "0";



//fonction qui trie les produits par age par taille par type par promo et par bestsell


//etoile en jaune 



//tri avec des case retourne un tableau de produit trier selont le choix

function triProduit($produit, $choix)
{
    switch ($choix) {
        case 1:
            usort($produit, function ($a, $b) {
                return $a['nomProduit'] <=> $b['nomProduit'];
            });
            break;

        case 2:
            usort($produit, function ($a, $b) {
                return $a['prix'] <=> $b['prix'];
            });
            break;
        case 3:
            usort($produit, function ($a, $b) {
                return $b['prix'] <=> $a['prix'];
            });
            break;
        case 4:
            usort($produit, function ($a, $b) {
                return $a['noteGlobale'] <=> $b['noteGlobale'];
            });
            break;
        case 5:
            usort($produit, function ($a, $b) {
                return $b['noteGlobale'] <=> $a['noteGlobale'];
            });
            break;

    }
    return $produit;
}

//fonction qui affiche sur une tranche de prix donner
function filtrePrix($produit, $prixMin, $prixMax)
{
    $produitFiltre = [];
    foreach ($produit as $p) {
        if ($p['prix'] >= $prixMin && $p['prix'] <= $prixMax) {
            $produitFiltre[] = $p;
        }
    }
    return $produitFiltre;
}


//fonction qui permet dafficher un tableau de produit et appel la fonction afficherProduit pour chaque produit
function afficherTableauProduit($tableauProduit, $nbColonnes = 3)
{
    // Début de la table
    echo '<table class:"table1">';

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

echo '<br>';
$age = isset($_GET['age']) ? $_GET['age'] : null;
$promo = isset($_GET['promo']) ? $produitParPromo : null;
$type = isset($_GET['idCategorie']) ? $_GET['idCategorie'] : null;
$bestS = isset($_GET['bestsell']) ? $produitParBestSell : null;
$recherche = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '-1';

echo '<br>';
//ajouter lidcategorie des sous categorie dune categorie dans un tableau
$tabC = [];
foreach ($scategorie as $s) {
    if ($s['idCategorie'] == $type) {
        $tabC[] = $s['idSousCategorie'];
    }
}

foreach ($tabC as $s) {
    foreach ($scategorie as $sc) {
        if ($sc['idCategorie'] == $s) {
            $tabC[] = $sc['idSousCategorie'];
        }
    }
}
if ($tabC == null) {
    $tabC[] = $type;
}

function triListeProduit($produitAge, $produitType, $idRegroupementPromo, $idRegroupementBS, $recherche, $Allproduit)
{
    $produit = [];

    // Parcours de tous les produits
    foreach ($Allproduit as $p) {
        // Recherche par nom de produit
        if($recherche=='') {
            $produit[]= $p;
        }
        if (!empty($recherche) && stripos($p['nomProduit'], $recherche) !== false) {
            $produit[] = $p;
            
            continue; // On passe au produit suivant
        }

       

        // Filtrer par âge
        if ($produitAge !== null && $p['age'] >= $produitAge) {
            $produit[] = $p;
            continue;
        }

        // Filtrer par type
        if ($produitType !== null && in_array($p['idCategorie'], (array)$produitType)) {
            $produit[] = $p;
            continue;
        }

        // Filtrer par promotion
        if (is_array($idRegroupementPromo) && in_array($p['idProduit'], $idRegroupementPromo)) {
            $produit[] = $p;
            continue;
        }

        // Filtrer par best-sell
        if (is_array($idRegroupementBS) && in_array($p['idProduit'], $idRegroupementBS)) {
            $produit[] = $p;
        }
    }

    return $produit;
}

$produit = triListeProduit($age, $tabC, $promo, $bestS, $recherche, $Allproduit);
$produit = triProduit($produit, isset($_POST['choix']) ? $_POST['choix'] : 0);
$produit = filtrePrix($produit, isset($_POST['prixMin']) ? $_POST['prixMin'] : 0, isset($_POST['prixMax']) ? $_POST['prixMax'] : 100);
//test de la fonction afficherTableauProduit

?>

    <?php
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    if ($search == '') {
     
        echo '<h1  style ="text-align: center">Tous les produits</h1>';
    } else {
        echo '<h1  style ="text-align: center">Résultats de la recherche pour : ' . $_GET['search'] . '</h1>';

    }
} elseif (isset($_GET['idCategorie'])) {
    echo '<h1  style ="text-align: center">Produits de la catégorie : ' . getNomCategorie($_GET['idCategorie'], $categorie) . '</h1>';
} else
if (isset($_GET['age'])) {
    echo '<h2  style ="text-align: center">Produits pour les enfants de ' . $_GET['age'] . ' ans et plus</h2>';
} else if (isset($_GET['promo'])) {
    echo '<h2  style ="text-align: center">Produits en promotion</h2>';
} else if (isset($_GET['bestsell'])) {
    echo '<h2  style ="text-align: center">Produits les plus vendus</h2>';
} else {
    echo '<h1  style ="text-align: center">Tous les produits</h1>';
}
?>
<div class="page-container">
    <div class="sidebar">
        <!-- Section de filtrage -->
        <div class="filtrage-container">
            <?php
            echo '<h2  style ="text-align: center">Filtrer les produits</h2>';
            echo '<br>';
            echo '<form action="" method="post">';
            echo '<select name="choix">';

            // Générer les options avec "selected" pour la valeur choisie
            $options = [
                "0" => "Trie par défaut",
                "1" => "Trie par nom",
                "2" => "Trie par prix croissant",
                "3" => "Trie par prix décroissant",
                "5" => "Trie par note croissante",
                "4" => "Trie par note décroissante",
            ];

            foreach ($options as $key => $value) {
                $selected = ($key == $choix) ? 'selected' : '';
                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
            }

            echo '</select>';

            // Ajout des champs prix min et max
            echo '<br>';

            echo '<span>Prix min </span>';
            echo '<br>';
            echo '<input type="number" name="prixMin" placeholder="Prix min" value="' . (isset($_POST['prixMin']) ? $_POST['prixMin'] : 0) . '" />';
            echo '<br>';
            echo ' <span>Prix max </span>';
            echo '<br>';
            echo '<input type="number" name="prixMax" placeholder="Prix max" value="' . (isset($_POST['prixMax']) ? $_POST['prixMax'] : 100) . '" />';
            echo '<br>';
            echo '<input type="submit" value="Valider" />';
            echo '</form>';
            ?>
        </div>
        <!-- Formulaire ici -->
    </div>
    <div class="main-content">
        <?php
        afficherTableauProduit($produit, 4);
        ?>
        <!-- Section principale pour afficher les produits -->
    </div>
</div>

<?php

include_once('footer.php');
?>