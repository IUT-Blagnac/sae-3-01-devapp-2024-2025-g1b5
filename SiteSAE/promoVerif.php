<?php
require_once('Connect.inc.php'); // Connexion à la base de données
// Récupérer l'URL de la dernière page visitée
$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'panier.php';
//retirer les paramètres de l'url
if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']) {
    $previousPage = explode('?', $previousPage)[0];
}
$listeCodePromo = [];
$req = $conn->prepare("SELECT * FROM codePromotion");
$req->execute();
while ($codePromo = $req->fetch(PDO::FETCH_ASSOC)) {
    $listeCodePromo[] = $codePromo;
}

// Extraction des codes promo existants
$codeP = array_column($listeCodePromo, 'CodePromo');

// Fonction pour vérifier si une date est valide pour un code promo
function verifDatev($dateDebut, $dateFin) {
    $dateDebut = new DateTime($dateDebut);
    $dateFin = new DateTime($dateFin);
    $dateActuelle = new DateTime();
    return ($dateActuelle >= $dateDebut && $dateActuelle <= $dateFin);
}

// Récupération de l'ID client
$idClient = $_GET["idClient"] ?? null;

// Vérifier si un code promo a été soumis
if ((isset($_POST['promocode']) ||$_GET["promocode"])&& $idClient) {
    if(isset($_POST['promocode'])){
        $codePromo = $_POST['promocode'];
    }else{
        $codePromo = $_GET["promocode"];
    }


    // Vérifier si le code promo existe
    if (in_array($codePromo, $codeP)) {
        // Trouver le code promo correspondant
        $codePromoDetails = null;
        foreach ($listeCodePromo as $code) {
            if ($code['CodePromo'] === $codePromo) {
                $codePromoDetails = $code;
                break;
            }
        }

        // Vérifier si le code promo a été trouvé et si ses dates sont valides
        if ($codePromoDetails && verifDatev($codePromoDetails['dateDebut'], $codePromoDetails['dateFin'])) {
            // Calculer la réduction totale
            $reduc = 0; // La variable pour accumuler la réduction

            // Exemple : additionner toutes les réductions déjà appliquées au client
            // Vous pouvez adapter ce calcul en fonction de vos données
            $check = $conn->prepare("SELECT SUM(reduction) AS total_reduc FROM Panier_Client_Promo pcp
                                     JOIN codePromotion cp ON pcp.idPromo = cp.idPromo
                                     WHERE pcp.idClient = ?");
            $check->execute([$idClient]);
            $reductionExistante = $check->fetch(PDO::FETCH_ASSOC)['total_reduc'];
            $reduc = $reductionExistante + $codePromoDetails['reduction'];

            // Vérifier si la réduction totale dépasse la limite de 0.6
            if ($reduc > 0.5) {
                // Limite de réduction atteinte
                header("Location: $previousPage?error=La limite de réduction sera depassée");
                exit;
            } else {
                try {
                    // Vérifier si le code promo est déjà appliqué
                    $check = $conn->prepare("SELECT * FROM Panier_Client_Promo WHERE idPromo = ? AND idClient = ?");
                    $check->execute([$codePromoDetails['idPromo'], $idClient]);

                    if ($check->rowCount() > 0) {
                        // Si le code promo est déjà appliqué
                        header("Location: $previousPage?error=Code promo déjà appliqué");
                        exit;
                    } else {
                        // Ajouter le code promo au panier
                        $req = $conn->prepare("INSERT INTO Panier_Client_Promo (idPromo, idClient) VALUES (?, ?)");
                        $req->execute([$codePromoDetails['idPromo'], $idClient]);
                        header("Location: $previousPage?success=Code promo appliqué");
                        exit;
                    }
                } catch (PDOException $e) {
                    // Gestion des erreurs PDO
                    header("Location: $previousPage?error=Erreur lors de l'application du code promo");
                    exit;
                }
            }
        } else {
            // Dates invalides ou code non trouvé
            header("Location: $previousPage?error=Code promo expiré ou invalide");
            exit;
        }
    } else {
        // Le code promo n'existe pas
        header("Location: $previousPage?error=Code promo invalide");
        exit;
    }
} else {
    // Code promo ou utilisateur invalide
    header("Location: $previousPage?error=Code promo non fourni ou utilisateur non valide");
    exit;
}
