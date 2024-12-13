<?php
ob_start();  // Commence le tampon de sortie

include "testAdmin.php";

// Récupération de l'ID du produit à modifier
if (isset($_GET['idProduit'])) {
    $idProduit = $_GET['idProduit'];

    // Récupération des détails du produit
    $query = $conn->prepare("SELECT * FROM Produit WHERE idProduit = :idProduit");
    $query->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);
    $query->execute();
    $produit = $query->fetch(PDO::FETCH_ASSOC);

    if (!$produit) {
        echo "<p>Le produit n'a pas été trouvé.</p>";
        exit;
    }
} else {
    echo "<p>Produit introuvable.</p>";
    exit;
}

// Récupération des catégories
$query = $conn->prepare("SELECT idCategorie, nomCategorie FROM Categorie");
$query->execute();
$categories = $query->fetchAll(PDO::FETCH_ASSOC);

// Traitement de la soumission du formulaire
if (isset($_POST['edit'])) {
    $nomProduit = $_POST['nomProduit'];
    $prix = $_POST['prix'];
    $age = $_POST['age'];
    $taille = $_POST['taille'];
    $nbJoueurMax = $_POST['nbJoueurMax'];
    $noteGlobale = $_POST['noteGlobale'];
    $idCategorie = $_POST['idCategorie'];

    // Mise à jour du produit dans la base de données
    $query = $conn->prepare("UPDATE Produit SET 
        nomProduit = :nomProduit,
        prix = :prix,
        age = :age,
        taille = :taille,
        nbJoueurMax = :nbJoueurMax,
        noteGlobale = :noteGlobale,
        idCategorie = :idCategorie
    WHERE idProduit = :idProduit");

    $query->bindParam(':nomProduit', $nomProduit, PDO::PARAM_STR);
    $query->bindParam(':prix', $prix, PDO::PARAM_STR);
    $query->bindParam(':age', $age, PDO::PARAM_INT);
    $query->bindParam(':taille', $taille, PDO::PARAM_STR);
    $query->bindParam(':nbJoueurMax', $nbJoueurMax, PDO::PARAM_INT);
    $query->bindParam(':noteGlobale', $noteGlobale, PDO::PARAM_STR);
    $query->bindParam(':idCategorie', $idCategorie, PDO::PARAM_INT);
    $query->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);

    $query->execute();
    
    // Déplacez le header avant toute sortie HTML
    header('Location: gestionProduit.php');
    exit;  // Toujours appeler exit après un header pour empêcher d'autres sorties.
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>
</head>
<body>
    <div class="img-acceuil">
        <img src="images/guirlandes-accueil.png" alt="Image guirlandes page d'acceuil">
    </div>

    <section class="modifier-produit">
        <h1>Modifier le produit</h1>

        <!-- Formulaire de modification -->
        <form method="POST">
            <input type="hidden" name="idProduit" value="<?= htmlspecialchars($produit['idProduit']) ?>">

            <label for="nomProduit">Nom du produit</label>
            <input type="text" id="nomProduit" name="nomProduit" value="<?= htmlspecialchars($produit['nomProduit']) ?>" required>
            <br>

            <label for="prix">Prix</label>
            <input type="text" id="prix" name="prix" value="<?= htmlspecialchars($produit['prix']) ?>" required>
            <br>

            <label for="age">Âge</label>
            <input type="number" id="age" name="age" value="<?= htmlspecialchars($produit['age']) ?>" required>
            <br>

            <label for="taille">Taille</label>
            <input type="text" id="taille" name="taille" value="<?= htmlspecialchars($produit['taille']) ?>" required>
            <br>

            <label for="nbJoueurMax">Nombre de joueurs max</label>
            <input type="number" id="nbJoueurMax" name="nbJoueurMax" value="<?= htmlspecialchars($produit['nbJoueurMax']) ?>" required>
            <br>

            <label for="noteGlobale">Note globale</label>
            <input type="text" id="noteGlobale" name="noteGlobale" value="<?= htmlspecialchars($produit['noteGlobale']) ?>" readonly>
            <br>

            <label for="idCategorie">Catégorie</label>
            <select name="idCategorie" id="idCategorie">
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?= htmlspecialchars($categorie['idCategorie']) ?>" <?= $produit['idCategorie'] == $categorie['idCategorie'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categorie['nomCategorie']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>

            <button type="submit" name="edit">Modifier le produit</button>
            <br>
        </form>
    </section>

<?php include "footer.php"; ?>

</body>
</html>

<?php
ob_end_flush();  // Envoie la sortie tamponnée au navigateur
?>
