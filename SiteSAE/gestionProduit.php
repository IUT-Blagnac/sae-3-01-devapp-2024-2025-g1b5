<?php
header("Pragma: no-cache");
ob_start(); // Commence le tampon de sortie
include "testAdmin.php"; // Connexion à la base de données
include "tableauxProduit.php";

// Suppression d'un produit
if (isset($_POST['delete']) && is_numeric($_POST['delete'])) {
    $idProduit = $_POST['delete'];

    try {
        // Récupérer le chemin de l'image associée au produit
        $imagePath = "./image_Produit/Prod{$idProduit}.jpg";

        // Supprimer l'image si elle existe
        if (file_exists($imagePath)) {
            unlink($imagePath); // Supprime le fichier
        }

        // Préparer la requête pour appeler la procédure de suppression
        $stmt = $conn->prepare("CALL SupprimerProduit(?)");
        $stmt->execute([$idProduit]);


        // Rediriger après la suppression
        header('location: gestionProduit.php?success=supp');
        exit;

    } catch (Exception $e) {
        // En cas d'erreur, gérer l'exception
        header('location: gestionProduit.php?error=supp_error');
        exit;
    }
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
                <input type="text" name="search_nom" value="<?= htmlspecialchars($searchNom) ?>"
                    placeholder="Rechercher par nom">
                <select name="search_categorie">
                    <option value="">Toutes les catégories</option>
                    <?php foreach ($categories as $categorie): ?>
                        <option value="<?= htmlspecialchars($categorie['idCategorie']) ?>"
                            <?= $searchCategorie == $categorie['idCategorie'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categorie['nomCategorie']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Rechercher</button>
            </form>
            <div>
                <a href="ajouterProduit.php"><button type="submit">Ajouter produit</button></a>
            </div>
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
                        <th>Quantité</th>
                        <th>Image</th>
                        <th>Actions</th>
                        <th>Promotion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($produits) > 0): ?>
                        <?php foreach ($produits as $produit):
                            $red = 0;
                            foreach ($produitParPromo as $produitPromo) {
                                if ($produitPromo['idProduit'] == $produit['idProduit']) {
                                    $red = $produitPromo['reduction'];
                                }
                            }
                            // Récupération de la quantité actuelle pour chaque produit
                            $queryStock = $conn->prepare("SELECT quantiteStock FROM Stock WHERE idProduit = :idProduit");
                            $queryStock->bindParam(':idProduit', $produit['idProduit'], PDO::PARAM_INT);
                            $queryStock->execute();
                            $stock = $queryStock->fetch(PDO::FETCH_ASSOC);
                            $quantiteStock = $stock ? $stock['quantiteStock'] : 0;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($produit['idProduit']) ?></td>
                                <td><?= htmlspecialchars($produit['age']) ?></td>
                                <td><?= htmlspecialchars($produit['taille']) ?></td>
                                <td><?= htmlspecialchars($produit['nbJoueurMax']) ?></td>
                                <td><?= htmlspecialchars($produit['prix']) ?> €</td>
                                <td><?= htmlspecialchars($produit['nomProduit']) ?></td>
                                <td><?= htmlspecialchars($produit['noteGlobale']) ?></td>
                                <td><?= htmlspecialchars($produit['nomCategorie'] ?: 'Non spécifiée') ?></td>
                                <td><?= htmlspecialchars($quantiteStock) ?></td>
                                <td><img src='./image_Produit/Prod<?php echo $produit['idProduit']; ?>.jpg?<?php echo time(); ?>'
                                        width="50%"></td>
                                <td>
                                    <!-- Formulaire pour supprimer un produit -->
                                    <form method="POST" style="display:inline;">
                                        <button type="submit" name="delete"
                                            value="<?= htmlspecialchars($produit['idProduit']) ?>"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</button>
                                    </form>
                                    <!-- Bouton pour modifier un produit -->
                                    <form method="GET" action="modifierProduit.php" style="display:inline;">
                                        <input type="hidden" name="idProduit"
                                            value="<?= htmlspecialchars($produit['idProduit']) ?>">
                                        <button type="submit">Modifier</button>
                                    </form>
                                </td>
                                <td>
                                    <form method="POST" action="ajouterPromotion.php?idProduit=<?= $produit['idProduit'] ?>">
                                        
                                        <input type="text" name="reduction"
                                            value="<?php echo isset($red) ? htmlspecialchars($red) : ''; ?>"
                                            id="reductionInput">
                                        <br>

                                        <?php
                                        if (isset($_GET['error']) && $_GET['error'] == $produit['idProduit']) {
                                            echo '<p style ="color:red">veuillez saisir une réduction valide (0.01 à 1.00) ';
                                            echo'<br>';
                                        } 
                                        
                                        $listeProduitPromo = array_column($produitParPromo, 'idProduit');
                                        if (in_array($produit['idProduit'], $listeProduitPromo)) {
                                            echo '<button type="submit">Supprimer Promotion</button>';
                                        } else {
                                            echo '<button type="submit">Ajouter Promotion</button>';
                                        }
                                        ?>

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
    <script>
        function showNotification(message) {
            alert(message);
        }

        // Vérification de l'URL pour un paramètre de succès
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');

        if (success === 'add') {
            showNotification('Le produit a été ajouté avec succès !');
        } else if (success === 'edit') {
            showNotification('Le produit a été modifié avec succès !');
        }
        else if (success === 'supp') {
            showNotification('Le produit a été supprimé avec succès !');
        }

        // Optionnel : Supprimez le paramètre `success` de l'URL après l'affichage du message
        if (success) {
            const cleanUrl = window.location.origin + window.location.pathname;
            window.history.replaceState({}, document.title, cleanUrl);
        }
    </script>
    <?php include "footer.php"; ?>

</body>

</html>

<?php

ob_end_flush(); // Envoie la sortie tamponnée au navigateur
?>


<script>
    function validateReduction() {
        var reductionValue = document.getElementById('reductionInput').value;

        // Vérifier si le champ est vide
        if (reductionValue.trim() === "") {
            alert("Le champ de réduction ne peut pas être vide.");
            return false; // Empêche la soumission du formulaire
        }

        // Vérifier si la valeur est un nombre valide (entre 0 et 1, avec deux chiffres après la virgule)
        var regex = /^(0(\.\d{1,2})?|1(\.00?)?)$/;

        if (!regex.test(reductionValue)) {
            alert("La réduction doit être un nombre inférieur ou égal à 1, avec deux chiffres après la virgule.");
            return false; // Empêche la soumission du formulaire
        }

        return true; // Si la valeur est valide, le formulaire est soumis
    }
</script>