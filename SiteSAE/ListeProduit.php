<?php
include 'header.php';
include 'tableauxProduit.php';

if($_GET['idCategorie']){
    $idCategorie = $_GET['idCategorie'];
    $produit = getProduitByCategorie($idCategorie);
}
if($_GET['rechercheProduit']){
    $rechercheProduit = $_GET['rechercheProduit'];
    $produit = getProduit($rechercheProduit);
}
if($_GET['age']){
    $age = $_GET['age'];
    $produit = getProduitByAge($age);
}

if($_GET['promo']){
    $promo = $_GET['promo'];
    $produit = getProduitByPromo($promo);
}


?>


    

