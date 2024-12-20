<?php
require_once "tableauxProduit.php";



$Nomp=html_entity_decode(strip_tags($_POST["nomCodePromo"]));
$reductionp=$_POST['reduction'];
$dateDebutp=$_POST['dateDebut'];
$dateFinp=$_POST['dateFin'];
$codePromo = strtoupper($faker->regexify('[A-Z0-9]{10}'));

//mettre au format date sql
$dateDebutp = date("Y-m-d", strtotime($dateDebutp));
$dateFinp = date("Y-m-d", strtotime($dateFinp));

$listeC= array_column($listeCodePromo,'CodePromo');
while(in_array($codePromo,$listeC)){
$codePromo = strtoupper($faker->regexify('[A-Z0-9]{10}'));
}
    // Préparer la requête pour appeler la procédure stockée
    $requete = $conn->prepare("CALL addCodePromo(?, ?, ?, ?, ?)");

    // Exécuter la requête avec les paramètres
    $requete->execute([$Nomp, $codePromo, $reductionp, $dateDebutp, $dateFinp]);

    $t=true;

// Toujours au début, évitez les espaces ou HTML ici

// Votre code PHP ici

header("location:gestionCodePromo.php?success=add");
?>



