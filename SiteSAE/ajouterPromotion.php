<?php
include("tableauxProduit.php");


//verifier que reduction est bien un nombre plus petit ou egal a 1 avec 2 chiffres apres la virgule
$listeProduitPromo = array_column($produitParPromo, 'idProduit');
$r = $_POST['reduction'];
var_dump($listeProduitPromo);
if(isset($_POST)){
    if(preg_match('/^(0\.[0-9][1-9]?|1(\.00?)?)$/', $_POST['reduction'])&& $_GET['idProduit'] > 0 && !in_array($_GET['idProduit'], $listeProduitPromo)){
        $reduction = $_POST['reduction'];
        $idProduit = $_GET['idProduit'];
        $stmt = $conn->prepare(query: "CALL addReduction(?, ?)");
        $stmt->execute([$idProduit, $reduction]);
        echo "Promotion ajoutée avec succès";
        header("Location: gestionProduit.php?test=0");

    } else if(in_array($_GET["idProduit"], $listeProduitPromo)){
        $idProduit = $_GET['idProduit'];
        try{
            $stmt = $conn->prepare(query: "CALL deleteReduction(?)");
            $stmt->execute([$idProduit]);
            echo "Promotion supprimée avec succès";
            header("Location: gestionProduit.php?test=$idProduit");
        }catch(PDOException $e){
            echo $e->getMessage();
        }

        
    }else{
        header("Location: gestionProduit.php?error=$_GET[idProduit]");
    }
    var_dump($_POST);
}