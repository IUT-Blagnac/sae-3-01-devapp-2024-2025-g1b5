<?php
include 'Connect.inc.php';
$produit=[];
$prodRecherche = [];
$prodAge = [];
$prodPromo = [];
$prodType = [];
$prodBestSeller = [];

$donneeProd=$pdo->prepare("SELECT * FROM Produits");
if($donneeProd->execute()){
    foreach($donneeProd as $prod){
        $produit[]=[
            'idProduit'=>$prod['idProduit'],
            'idCategorie'=>$prod['idCategorie'],
            'nomProduit'=>$prod['nomProduit'],
            'prixProduit'=>$prod['prixProduit']
        ];
    }
}

function getProduit($rechercheProduit){
    global $produit;
    foreach($produit as $prod){
        if($rechercheProduit.contains($prod['nomProduit']) || $rechercheProduit.contains($prod['idProduit'])){ 
            $prodRecherche[] = $prod;
        }
    }
    return null;
}
function getProduitByCategorie($idCategorie){
    global $produit;
    foreach($produit as $prod){
        if($prod['idCategorie']==$idCategorie){
            $prodType[] = $prod;
        }
    }
    return $tab;
}






