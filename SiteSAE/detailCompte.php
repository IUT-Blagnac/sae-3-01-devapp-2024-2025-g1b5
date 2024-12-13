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
$idAdresse = $client['idAdresse'];

// Récupérer les informations de l'adresse
$query = $conn->prepare("SELECT * FROM Adresse WHERE idAdresse = :idAdresse");
$query->bindParam(':idAdresse', $idAdresse);
$query->execute();
$adresse = $query->fetch(PDO::FETCH_ASSOC);
$rue = $adresse ? $adresse['rue'] : '';
$ville = $adresse ? $adresse['ville'] : '';
$codePostal = $adresse ? $adresse['codePostal'] : '';
$pays = $adresse ? $adresse['pays'] : '';
$adresseComplete = $rue ? "$rue, $ville, $codePostal, $pays" : 'Adresse non définie';

// Récupérer les informations de la carte bancaire
$query = $conn->prepare("SELECT * FROM CarteBancaire WHERE idClient = :client_id");
$query->bindParam(':client_id', $client_id);
$query->execute();
$carte = $query->fetch(PDO::FETCH_ASSOC);
$numCarte = $carte ? $carte['numCarte'] : 'Carte bancaire non définie';

// Fonction pour masquer les numéros de la carte bancaire sauf les 4 derniers chiffres
function masquerNumCarte($numCarte) {
    return str_repeat('*', strlen($numCarte) - 4) . substr($numCarte, -4);
}

$numCarteMasque = $carte ? masquerNumCarte($numCarte) : 'Non définie';

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
            <ul>
                <?php foreach ($commandes as $commande): ?>
                    <li>Commande #<?php echo htmlspecialchars($commande['idCommande']); ?> - Statut : <?php echo htmlspecialchars($commande['statut']); ?> - Date : <?php echo htmlspecialchars($commande['dateCommande']); ?></li>
                <?php endforeach; ?>
            </ul>
            <button class="button">Voir toutes les commandes</button>
        </div>
    </section>
</main>

<?php
include "footer.php";
?>
</body>
</html>