<?php
require_once('tableauxProduit.php');

$codeP=array_column($listeCodePromo,'CodePromo');
var_dump($codeP);
var_dump($_POST);

function verifDatev($dateDebut,$dateFin){
    $dateDebut = new DateTime($dateDebut);
    $dateFin = new DateTime($dateFin);
    $dateActuelle = new DateTime();
    if($dateActuelle>=$dateDebut && $dateActuelle<=$dateFin){
        return true;
    }else{
        return false;
    }
}
$idClient=$_GET["idClient"];
if(isset($_POST['promocode'])){
    $codePromo=$_POST['promocode'];
    if(in_array($codePromo,$codeP)){
        foreach($listeCodePromo as $code){
            if($code['CodePromo']==$codePromo && verifDatev($code['dateDebut'],$code['dateFin'])){
                $req = $conn->prepare("INSERT INTO Panier_Client_Promo (idPromo ,idClient) VALUES (?,?) ");
                $req->execute([$code['idPromo'] ,$idClient]);
                header("Location: panier.php");
            }
        }
    }else{
       header("Location: panier.php?error=Code promo invalide");
    }
}