<?php
require_once 'Connect.inc.php'; // Connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and assign POST variables
    $id = htmlspecialchars($_POST["idCodePromo"]);
    $nom = htmlspecialchars($_POST["nomCodePromo"]);
    $reduction = htmlspecialchars($_POST["reduction"]);
    $dateDebut = htmlspecialchars($_POST["dateDebut"]);
    $dateFin = htmlspecialchars($_POST["dateFin"]);
    var_dump($_POST);
    // Action pour "Modifier"
    if (isset($_POST["Modifier"])) {
        $requete = $conn->prepare("UPDATE codePromotion 
                              SET NomCodePromo = :nom, 
                                  Reduction = :reduction, 
                                  DateDebut = :dateDebut, 
                                  DateFin = :dateFin 
                              WHERE idPromo = :id");
        $requete->execute([
            "nom" => $nom,
            "reduction" => $reduction,
            "dateDebut" => $dateDebut,
            "dateFin" => $dateFin,
            "id" => $id
        ]);

        // Redirection après modification
        header("Location: gestionCodePromo.php?success=modifier");
        exit();
    }

    // Action pour "Supprimer"
    if (isset($_POST["Supprimer"])) {
        try {
            // Préparer la requête pour suppression
            $requete = $conn->prepare("DELETE FROM codePromotion WHERE idPromo = :id");
            $requete->execute(["id" => $id]);

            // Redirection après suppression
            header("Location: gestionCodePromo.php?success=supprimer");
            exit();
        } catch (PDOException $e) {
            // Gestion des erreurs lors de la suppression
            header("Location: gestionCodePromo.php?error=supprimer");
            exit();
        }
    }
}
?>
