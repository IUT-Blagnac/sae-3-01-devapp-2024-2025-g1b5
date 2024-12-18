<?php
include "header.php";
include "Connect.inc.php";
include "verifConnexion.php";

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client_email'])) {
    echo "<p>Vous devez être connecté pour voir vos produits favoris.</p>";
    include "footer.php";
    exit();
}

// Récupérer l'ID du client
$client_email = $_SESSION['client_email'];
$query = $conn->prepare("SELECT idClient FROM Client WHERE email = ?");
$query->execute([$client_email]);
$client = $query->fetch();
$idClient = $client['idClient'];

// Récupérer les produits favoris
$query = $conn->prepare("
    SELECT P.idProduit, P.nomProduit, P.prix, P.description
    FROM Produit_Favoris PF
    JOIN Produit P ON PF.idProduit = P.idProduit
    WHERE PF.idClient = ?
");
$query->execute([$idClient]);
$favoris = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="favoris.css">
<main class="main-container">
    <section class="favoris">
        <h1>Vos Produits Favoris</h1>
        <?php if (count($favoris) > 0): ?>
            <div class="produits-favoris">
                <?php foreach ($favoris as $produit): ?>
                    <div class="produit">
                        <img src="image_Produit/Prod<?php echo $produit['idProduit']; ?>.jpg" alt="<?php echo htmlspecialchars($produit['nomProduit']); ?>" width="150px">
                        <h2><?php echo htmlspecialchars($produit['nomProduit']); ?></h2>
                        <p><?php echo htmlspecialchars($produit['description']); ?></p>
                        <p><strong>Prix : </strong><?php echo number_format($produit['prix'], 2); ?> €</p>

                        <!-- Formulaire pour ajouter au panier -->
                        <form action="ajouterPanier.php" method="get" style="display: inline-block;">
                            <input type="hidden" name="idProduit" value="<?php echo $produit['idProduit']; ?>">
                            <label for="quantite_<?php echo $produit['idProduit']; ?>">Quantité :</label>
                            <input type="number" id="quantite_<?php echo $produit['idProduit']; ?>" name="quantite" value="1" min="1" style="width: 50px;">
                            <button type="submit" class="button">Ajouter au panier</button>
                        </form>

                        <!-- Formulaire pour supprimer des favoris -->
                        <form action="supprimerFavoris.php" method="post" style="display: inline-block;">
                            <input type="hidden" name="idProduit" value="<?php echo $produit['idProduit']; ?>">
                            <button type="submit" class="button-delete">Supprimer des favoris</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Vous n'avez aucun produit dans vos favoris.</p>
        <?php endif; ?>
    </section>
</main>

<?php
include "footer.php";
?>
