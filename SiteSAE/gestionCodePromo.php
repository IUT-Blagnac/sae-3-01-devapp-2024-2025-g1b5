<?php
include "tableauxProduit.php";
include "testAdmin.php";


$searchNom = $_POST['search_nom'] ?? '';

$lister = [];

if(isset($_POST['search_nom'])){
    foreach ($listeCodePromo as $code) {
        if (stripos($code["nomCodePromo"],$searchNom) && $searchNom != '') {
            $lister[] = $code;
        }
    }
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
    <input type="date" name="dateDebut" value="<?php echo date('Y-m-d'); ?>" >
</td>
<td>
    <input type="date" name="dateFin" value="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" >
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
                        <th>Nom Code Promo</th>
                        <th>Reduction</th>
                        <th>Date de debut</th>
                        <th>Date de fin</th>
                        <th>Modifier</th>
                        <th>Supprimer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    foreach ($listeCodePromo as $codePromo) {
                        ?>
                        <tr>
                            <td><?= $codePromo['NomCodePromo'] ?></td>
                            <td><?= $codePromo['reduction'] ?></td>
                            <td><?= $codePromo['dateDebut'] ?></td>
                            <td><?= $codePromo['dateFin'] ?></td>
                            <td><a href="modifierCodePromo.php?idPromo=<?= $codePromo['idPromo'] ?>">Modifier</a>
                            </td>
                            <td><a href="supprimerCodePromo.php?idPromo=<?= $codePromo['idPromo'] ?>">Supprimer</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>

                </tbody>
            </table>

        </div>

        <?PHP
include "footer.php";
?>
</body>

</html>