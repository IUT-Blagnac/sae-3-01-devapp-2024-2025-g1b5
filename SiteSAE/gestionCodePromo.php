<?php
ob_start(); // Commence le tampon de sortie
require_once "tableauxProduit.php";
require_once "testAdmin.php";



$searchNom = $_POST['search_nom'] ?? '';

$lister = [];

if (isset($_POST['search_nom']) && !empty($_POST['search_nom'])) {
    foreach ($listeCodePromo as $code) {
        if (stripos($code["NomCodePromo"], $searchNom) && $searchNom != '') {
            $lister[] = $code;
        }
    }
    if (count($lister) > 0) {
        $listeCodePromo1 = $listeCodePromo;
    } else {
        $listeCodePromo1 = $lister;
    }
} else {
    $listeCodePromo1 = $listeCodePromo;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Codes Promo</title>
</head>

<body>

    <div class="img-acceuil">
        <img src="images/guirlandes-accueil.png" alt="Image guirlandes page d'acceuil">
    </div>

    <section class="gestion-produits">
        <h1>Gestion Code Promo</h1>
        <!-- Formulaire de recherche -->
        <div class="formulaire-recherche">
            <form method="POST">
                <input type="text" name="search_nom" value="<?= htmlspecialchars($searchNom) ?>"
                    placeholder="Rechercher par nom">
                <input type="submit" value="Rechercher">
            </form>
        </div>
        <h2>Ajouter code promo</h2>

        <div class="tableau-produits">
            <!-- ajouterCodePromo-->
            <form method="POST" action="ajouterCodePromo.php">
                <table>
                    <thead>
                        <tr>
                            <th>Nom Code Promo</th>
                            <th>Reduction</th>
                            <th>Date de debut</th>
                            <th>Date de fin</th>
                            <th>Ajouter</th>
                        </tr>
                    </thead>
                    <tbody>

                        <form action="ajouterCodePromo.php" method="POST">
                            <tr>
                                <td><input type="text" name="nomCodePromo" required></td>
                                <!-- verification du chiffre compris entre 0 et 1 .0 non inclue-->
                                <td><input type="number" name="reduction" step="0.01" min="0" max="1" required></td>
                                <td>
                                    <input type="date" name="dateDebut" value="<?php echo date('Y-m-d'); ?>">
                                </td>
                                <td>
                                    <input type="date" name="dateFin"
                                        value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>">
                                </td>

                                <td><input type="submit" value="Ajouter"></td>
                            </tr>

                        </form>
                    </tbody>
                </table>
            </form>

        </div>
        <br>
        <h2>Liste des codes promo</h2>
        <div class="tableau-produits">
            <table>
                <thead>
                    <tr>
                        <th>id Code Promo</th>
                        <th>Nom Code Promo</th>
                        <th>Code Promo</th>
                        <th>Reduction</th>
                        <th>Date de debut</th>
                        <th>Date de fin</th>
                        <th>Modifier</th>
                        <th>Supprimer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    foreach ($listeCodePromo1 as $codePromo) {
                        ?>
                        <form action="modifPromo.php" method="POST">
                            <tr>
                                <td>
                                    <input type="hidden" name="idCodePromo"
                                        value="<?= htmlspecialchars($codePromo['idPromo']) ?>">
                                    <?= htmlspecialchars($codePromo['idPromo']) ?>
                                </td>
                                <td><input type="text" name="nomCodePromo" value="<?= $codePromo['NomCodePromo'] ?>"
                                        required></td>
                                <!-- verification du chiffre compris entre 0 et 1 .0 non inclue-->
                                <td><input type="hidden" name="codePromo" value="<?= $codePromo['CodePromo'] ?>" >
                                    <?= htmlspecialchars($codePromo['CodePromo']) ?>
                                </td>
                                <td><input type="number" name="reduction" value="<?= $codePromo['reduction'] ?>" step="0.01"
                                        min="0" max="1" required></td>
                                <td>
                                    <input type="date" name="dateDebut" value="<?= $codePromo['dateDebut'] ?>">
                                </td>
                                <td>
                                    <input type="date" name="dateFin" value="<?= $codePromo['dateFin'] ?>">
                                </td>
                                <td><input type="submit" name = 'Modifier' value="Modifier"></td>
                                <td><input type="submit" name = 'Supprimer' value="Supprimer"></td>
                            </tr>

                        </form>

                        <?php
                    }
                    ?>

                </tbody>
            </table>

        </div>

        <?PHP


        require_once "footer.php";
        ob_end_flush()
            ?>
</body>

</html>