<?php
include 'Connect.inc.php';


$categorie =[];

// Requête SQL pour récupérer les catégories de la table categorie
$sql = "SELECT * FROM Categorie";
$stmt = $conn->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $categorie[] = $row;
}
// Affichage des catégories
foreach ($categorie as $cat) {
    //echo "<br> Id: " . $cat['idCategorie'] . " Nom: " . $cat['nomCategorie'];
}
$sql = "SELECT * FROM SousCategorie";
$stmt = $conn->query($sql);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $scategorie[] = $row;
}
// Affichage des sous catégories
foreach ($scategorie as $cat) {
    //echo "<br> Id: " . $cat['idCategorie'] . " Nom: " . $cat['idSousCategorie'];
}


function afficherCategories($tabCategNV1, $tabCategNV2, $tabCategNV3, $listeCategorie) {
    $affichage = '<ul class="menu-categories">';

    // Parcourir les catégories de niveau 1
    foreach ($tabCategNV1 as $idCategorie1) {
        $affichage .= '<li class="menu-item">';
        $affichage .= '<a href="ListeProduit.php?idCategorie=' . $idCategorie1 . '">' 
                    . getNomCategorie($idCategorie1, $listeCategorie) 
                    . '</a>';
        
        // Sous-catégories de niveau 2
        $affichage .= '<ul class="submenu">';
        foreach ($tabCategNV2 as $idCategorie2 => $parent) {
            if ($parent == $idCategorie1) {
                $affichage .= '<li class="submenu-item">';
                $affichage .= '<a href="ListeProduit.php?idCategorie=' . $idCategorie2 . '">' 
                            . getNomCategorie($idCategorie2, $listeCategorie) 
                            . '</a>';
                
                // Sous-catégories de niveau 3
                $affichage .= '<ul class="submenu">';
                foreach ($tabCategNV3 as $idCategorie3 => $parent2) {
                    if ($parent2 == $idCategorie2) {
                        $affichage .= '<li class="submenu-item">';
                        $affichage .= '<a href="ListeProduit.php?idCategorie=' . $idCategorie3 . '">' 
                                    . getNomCategorie($idCategorie3, $listeCategorie) 
                                    . '</a>';
                        $affichage .= '</li>';
                    }
                }
                $affichage .= '</ul>'; // Fin niveau 3
                $affichage .= '</li>';
            }
        }
        $affichage .= '</ul>'; // Fin niveau 2
        $affichage .= '</li>';
    }

    $affichage .= '</ul>'; // Fin niveau 1
    return $affichage;
}


function getNomCategorie($idCategorie, $categorie) {
    foreach ($categorie as $cat) {
        if ($cat['idCategorie'] == $idCategorie) {
            return $cat['nomCategorie'];
        }
    }
}

//fonction qui separer les categories et les sous categories
function separateur($categEnfant, $categParent) {
    $tabCategNV1 = [];
    $tabCategNV2 = [];
    $tabCategNV3 = [];
    $dump = [];

    foreach ($categParent as $value=>$key) {
        //print_r($key);
        //echo"<br>";
       

        if(!in_array($key['idCategorie'], $tabCategNV1)) {
            $tabCategNV1[] = $key['idCategorie'];
        }
        if(!in_array($key['idSousCategorie'], $tabCategNV2)) {
            $tabCategNV2[$key['idSousCategorie']] = $key['idSousCategorie'];
            $tabCategNV2b[$key['idSousCategorie']] = $key['idCategorie'];
        }
        if(in_array($key['idCategorie'], $tabCategNV1)&& in_array($key['idCategorie'], $tabCategNV2)) {
            $dump[] = $key['idCategorie'];
        }
        
        
        if(in_array($key['idCategorie'], $dump)) {
            $dump[] = $key['idSousCategorie'];
            $tabCategNV3[$key['idSousCategorie']] = $key['idCategorie'];

        }
       
        
    }
    //supprimer les doublons sans les valeurs de la table dump
    $tabCategNV1 = array_diff($tabCategNV1, $dump);
    foreach ($tabCategNV2b as $key => $value) {
        if(in_array($key, $dump)&& in_array($value, $dump)) {
            unset($tabCategNV2b[$key]);
        }
    }
    $affichage = '';
    echo afficherCategories($tabCategNV1, $tabCategNV2b, $tabCategNV3,$categEnfant) ;
    
    
}



//separateur($categorie, $scategorie);
















?>

