<?php
include "header.php";
include "Connect.inc.php";
require "verifConnexion.php";

?>

<main class="main-container">
    <nav class="side-menu">
        <ul>
            <li><a href="#">Informations personnelles</a></li>
            <li><a href="modifClient.php">Modifier Compte</a></li>
            <li><a href="#">Commandes récentes</a></li>
            <li><a href="deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
    <section class="client-info">
        <h2>Bienvenue, <?php echo htmlspecialchars($client['prenom']); ?></h2>
        <div class="info-section">
            <h3>Informations personnelles</h3>
            <p>Prénom : <?php echo htmlspecialchars($client['prenom']); ?></p>
            <p>Email : <?php echo htmlspecialchars($client['email']); ?></p>
            <p>Adresse : <?php echo htmlspecialchars($adresseComplete); ?></p>
            <p>Carte Bancaire : <?php echo htmlspecialchars($numCarteMasque); ?>
            </p>
            <a href="modifClient.php"><button class="button">Modifier</button></a>
        </div>
        <div class="info-section">
            <h3>Commandes récentes</h3>
            <?php if($commandes && count($commandes) > 0) : ?>
            <ul>
                <?php foreach ($commandes as $commande): ?>
                    <li>Commande #<?php echo htmlspecialchars($commande['idCommande']); ?> 
                    - Type de Livraison : <?php echo htmlspecialchars($commande['typeLivraison']); ?> 
                    - Statut : <?php echo htmlspecialchars($commande['statut']); ?> 
                    - Date : <?php echo htmlspecialchars((new DateTime($commande['dateCommande']))->format('d/m/Y')); ?></li>
                <?php endforeach; ?>
                </ul>
                <a href="detailCommandeClient.php"><button class="button">Voir toutes les commandes</button></a>
            <?php else: ?>
                <p>Aucune commande encore effectuée. Il n'est jamais trop tard pour se faire plaisir. <br>
                    Découvrez nos offres et laissez-vous tenter dès maintenant!
                </p>
                <a href="ListeProduit.php?promo=1"><button class="button">Découvrir nos offres</button></a>


            <?php endif; ?>
        </div>
    </section>
</main>

<?php
include "footer.php";
?>
</body>
</html>