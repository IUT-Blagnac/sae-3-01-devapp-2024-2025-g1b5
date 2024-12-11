<?php
include "header.php";
include "Connect.inc.php"; 

if (!isset($_SESSION['client_email']) && !isset($_COOKIE['CidClient'])) {
    header('Location: connexionCompte.php');
    exit();
}

// Récupérer l'ID du client
$client_id = isset($_SESSION['client_id']) ? $_SESSION['client_id'] : '';

// Récupérer les commandes récentes du client
$query = $conn->prepare("SELECT * FROM Commande WHERE idClient = :client_id ORDER BY dateCommande DESC LIMIT 5");
$query->bindParam(':client_id', $client_id);
$query->execute();
$commandes = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="main-container">
    <nav class="side-menu">
        <ul>
            <li><a href="#">Informations personnelles</a></li>
            <li><a href="#">Commandes récentes</a></li>
            <li><a href="#">Préférences</a></li>
            <li><a href="deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
    <section class="client-info">
        <h2>Bienvenue, <?php echo isset($_SESSION['client_prenom']) ? htmlspecialchars($_SESSION['client_prenom']) : 'Invite'; ?></h2>
        <div class="info-section">
            <h3>Informations personnelles</h3>
            <p>Prenom : <?php echo isset($_SESSION['client_prenom']) ? htmlspecialchars($_SESSION['client_prenom']) : 'Non défini'; ?></p>
            <p>Email : <?php echo isset($_SESSION['client_email']) ? htmlspecialchars($_SESSION['client_email']) : 'Non défini'; ?></p>
            <p>Adresse :<?php echo isset($_SESSION['client_adresse']) ? htmlspecialchars($_SESSION['client_adresse']) : ' Non défini'; ?></p>
            <button class="button">Modifier</button>
        </div>
        <div class="info-section">
            <h3>Commandes récentes</h3>
            <ul>
                <?php foreach ($commandes as $commande): ?>
                    <li>Commande #<?php echo htmlspecialchars($commande['id']); ?> - Statut : <?php echo htmlspecialchars($commande['statut']); ?> - Date : <?php echo htmlspecialchars($commande['date_commande']); ?></li>
                <?php endforeach; ?>
            </ul>
            <button class="button">Voir toutes les commandes</button>
        </div>
        <div class="info-section">
            <h3>Préférences</h3>
            <p>Langue : Français</p>
            <p>Notifications : Activées</p>
            <button class="button">Modifier</button>
        </div>
    </section>
</main>

<?php
include "footer.php";
?>
</body>
</html>