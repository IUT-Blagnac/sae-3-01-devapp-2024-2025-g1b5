<?php
    include "testAdmin.php"; // Connexion à la base de données

    // Suppression d'un produit
    if (isset($_POST['delete'])) {
        $idProduit = $_POST['delete'];
        $query = $conn->prepare("DELETE FROM Produit WHERE idProduit = :idProduit");
        $query->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);
        $query->execute();
        header('Location: gestion-produits.php'); // Redirection après suppression
        exit;
    }

    // Récupération des catégories pour le filtre de recherche
    $query = $conn->prepare("SELECT idCategorie, nomCategorie FROM Categorie");
    $query->execute();
    $categories = $query->fetchAll(PDO::FETCH_ASSOC);

    // Recherche de produits avec les filtres
    $searchNom = $_POST['search_nom'] ?? '';
    $searchCategorie = $_POST['search_categorie'] ?? '';

    // Préparation de la requête SQL avec jointure pour récupérer les produits filtrés
    $query = $conn->prepare("
        SELECT 
            Produit.idProduit, 
            Produit.age, 
            Produit.taille, 
            Produit.nbJoueurMax, 
            Produit.prix, 
            Produit.nomProduit, 
            Produit.noteGlobale, 
            Categorie.nomCategorie
        FROM Produit
        LEFT JOIN Categorie ON Produit.idCategorie = Categorie.idCategorie
        WHERE (:searchNom = '' OR Produit.nomProduit LIKE :searchNomWildcard)
          AND (:searchCategorie = '' OR Produit.idCategorie = :searchCategorie)
    ");
    $query->bindParam(':searchNom', $searchNom, PDO::PARAM_STR);
    $query->bindValue(':searchNomWildcard', '%' . $searchNom . '%', PDO::PARAM_STR);
    $query->bindParam(':searchCategorie', $searchCategorie, PDO::PARAM_INT);
    $query->execute();
    $produits = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits</title>
</head>
<body>

<div class="img-acceuil">
    <img src="images/guirlandes-accueil.png" alt="Image guirlandes page d'acceuil">
</div>

<section class="gestion-produits">
    <h1>Gestion des Produits</h1>

    <!-- Formulaire de recherche -->
    <div class="formulaire-recherche">
        <form method="POST">
            <input type="text" name="search_nom" value="<?= htmlspecialchars($searchNom) ?>" placeholder="Rechercher par nom">
            <select name="search_categorie">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?= htmlspecialchars($categorie['idCategorie']) ?>" <?= $searchCategorie == $categorie['idCategorie'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categorie['nomCategorie']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Rechercher</button>
        </form>
    </div>

    <!-- Tableau dynamique des produits -->
    <div class="tableau-produits">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Âge</th>
                    <th>Taille</th>
                    <th>Nb Joueurs</th>
                    <th>Prix</th>
                    <th>Nom</th>
                    <th>Note</th>
                    <th>Catégorie</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($produits) > 0): ?>
                    <?php foreach ($produits as $produit): ?>
                        <tr>
                            <td><?= htmlspecialchars($produit['idProduit']) ?></td>
                            <td><?= htmlspecialchars($produit['age']) ?></td>
                            <td><?= htmlspecialchars($produit['taille']) ?></td>
                            <td><?= htmlspecialchars($produit['nbJoueurMax']) ?></td>
                            <td><?= htmlspecialchars($produit['prix']) ?> €</td>
                            <td><?= htmlspecialchars($produit['nomProduit']) ?></td>
                            <td><?= htmlspecialchars($produit['noteGlobale']) ?></td>
                            <td><?= htmlspecialchars($produit['nomCategorie'] ?: 'Non spécifiée') ?></td>
                            <td>
                                <!-- Formulaire pour supprimer un produit -->
                                <form method="POST" style="display:inline;">
                                    <button type="submit" name="delete" value="<?= htmlspecialchars($produit['idProduit']) ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</button>
                                </form>
                                <!-- Bouton pour modifier un produit -->
                                <form method="GET" action="modifierProduit.php" style="display:inline;">
                                    <input type="hidden" name="idProduit" value="<?= htmlspecialchars($produit['idProduit']) ?>">
                                    <button type="submit">Modifier</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">Aucun produit trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include "footer.php"; ?>

</body>
</html>
