<?php
include "header.php";
include "Connect.inc.php";
require "verifConnexion.php";

// Récupérer l'email du client
$client_email = isset($_SESSION['client_email']) ? $_SESSION['client_email'] : '';

// Récupérer les informations du client
$query = $conn->prepare("SELECT * FROM Client WHERE email = :client_email");
$query->bindParam(':client_email', $client_email);
$query->execute();
$client = $query->fetch(PDO::FETCH_ASSOC);

$client_id = $client['idClient'];

// Récupérer toutes les commandes du client
$query = $conn->prepare("SELECT * FROM Commande WHERE idClient = :client_id ORDER BY dateCommande DESC");
$query->bindParam(':client_id', $client_id);
$query->execute();
$commandes = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="main-container">
<nav class="side-menu">
        <ul>
        <li><a href="detailCompte.php">Informations personnelles</a></li>

            <li><a href="modifClient.php">Modifier Compte</a></li>
            <li><a href="detailPointFidelite.php">Points de fidélité</a></li>
            <li><a href="detailCommandeClient.php">Toutes les commandes</a></li>
            <li><a href="deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
    <section class="client-info">
        <h2>Bienvenue, <?php echo htmlspecialchars($client['prenom']); ?></h2>
        <div class="info-section">
            <h3>Toutes les commandes</h3>
            <?php if($commandes && count($commandes) > 0) : ?>
            <ul>
                <?php foreach ($commandes as $commande): ?>
                    <li>Commande #<?php echo htmlspecialchars($commande['idCommande']); ?> 
                    - Type de Livraison : <?php echo htmlspecialchars($commande['typeLivraison']); ?> 
                    - Statut : <?php echo htmlspecialchars($commande['statut']); ?> 
                    - Date : <?php echo htmlspecialchars((new DateTime($commande['dateCommande']))->format('d/m/Y')); ?></li>
                <?php endforeach; ?>
            </ul>
            <a href="detailCompte.php"><button class="button">Retour</button></a>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
include "footer.php";
?>
</body>
</html>