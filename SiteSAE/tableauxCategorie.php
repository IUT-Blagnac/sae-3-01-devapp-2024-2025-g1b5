<?php
include 'Connect.inc.php';


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
