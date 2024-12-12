<?php
include 'Connect.inc.php';

//recuperer categorie et sous categorie
$categorie =[];
$souscategorie = [];

$afficheCategorie=[];
$request1="SELECT * FROM Categorie";
$request2= "SELECT * FROM SousCategorie";

//recuperer les categories et sous categories
try{
    $result1 = $conn->query($request1);
    $result2 = $conn->query($request2);
    $i=0;
    while($row = $result1->fetch(PDO::FETCH_ASSOC)){
        $categorie[$i] = $row;
        $i++;
    }
    $i=0;
    while($row = $result2->fetch(PDO::FETCH_ASSOC)){
        $souscategorie[$i] = $row;
        $i++;
    }
}
catch (PDOException $e){
    echo "Erreur: ".$e->getMessage()."<br>";
    die() ;
}

//ajouter une les categories et les sous categories dans un sous tableau de categorie

foreach($categorie as $key => $value){
    $afficheCategorie[$key] = $value;
    $afficheCategorie[$key]['souscategorie'] = [];
    foreach($souscategorie as $key2 => $value2){
        if($value['idCategorie'] == $value2['idCategorie']){
            $afficheCategorie[$key]['souscategorie'][] = $value2;
        }
    }
}

var_dump($afficheCategorie);