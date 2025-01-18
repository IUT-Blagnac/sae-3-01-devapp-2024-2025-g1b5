<?php
require_once "tableauxProduit.php";

if (isset($_GET) && !empty($_GET)) {
    $idClient = $_GET["idClient"];
    $reduction = $_GET["reduction"];
    $point = $_GET["point"];
    var_dump($_GET);
    $nomCodePromo = $_GET['nomCodePromo'];

    $dateDebut = date("Y-m-d");
    $dateFin = date("Y-m-d", strtotime("+1 year"));
    $codePromo = strtoupper($faker->regexify('[A-Z0-9]{10}'));

    $listeC = array_column($listeCodePromo, 'CodePromo');
    while (in_array($codePromo, $listeC)) {
        $codePromo = strtoupper($faker->regexify('[A-Z0-9]{10}'));

    }
    $pointClient = $conn->prepare("SELECT pointFidelite FROM Client WHERE idClient = ?");
    $pointClient->execute(array($idClient));
    $pointc = $pointClient->fetch(PDO::FETCH_ASSOC);
    var_dump($pointc);
    $pointc = $pointc['pointFidelite'] - $point;

    $requete = $conn->prepare("INSERT INTO codePromotion (NomCodePromo, CodePromo, reduction, dateDebut, dateFin,idClient) VALUES (?, ?, ?, ?, ?, ?)");
    $requete->execute(array($nomCodePromo, $codePromo, $reduction, $dateDebut, $dateFin, $idClient));
    $requete2 = $conn->prepare("UPDATE Client SET pointFidelite = ? WHERE idClient = ?");
    $requete2->execute(array($pointc, $idClient));
    header("location:detailPointFidelite.php");


} else {

    $Nomp = html_entity_decode(strip_tags($_POST["nomCodePromo"]));
    $reductionp = $_POST['reduction'];
    $dateDebutp = $_POST['dateDebut'];
    $dateFinp = $_POST['dateFin'];
    $codePromo = strtoupper($faker->regexify('[A-Z0-9]{10}'));

    //mettre au format date sql
    $dateDebutp = date("Y-m-d", strtotime($dateDebutp));
    $dateFinp = date("Y-m-d", strtotime($dateFinp));

    $listeC = array_column($listeCodePromo, 'CodePromo');
    while (in_array($codePromo, $listeC)) {
        $codePromo = strtoupper($faker->regexify('[A-Z0-9]{10}'));
    }
    // Préparer la requête pour appeler la procédure stockée
    try {
    $requete = $conn->prepare("CALL addCodePromo(?, ?, ?, ?, ?)");

    // Exécuter la requête avec les paramètres
    $requete->execute([$Nomp, $codePromo, $reductionp, $dateDebutp, $dateFinp]);
    } catch (PDOException $e) {
        header("location:gestionCodePromo.php?error=Erreur lors de l'insertion du code promo !");
    }

    $t = true;

    // Toujours au début, évitez les espaces ou HTML ici

    // Votre code PHP ici

    header("location:gestionCodePromo.php?success=add");
}
?>