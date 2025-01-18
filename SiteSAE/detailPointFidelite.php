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
            <h3>Points de fidélité</h3>
            <p>Vous avez actuellement <strong><?php echo $client['pointFidelite']; ?></strong> points de fidélité.</p>
        </div>
        <!-- Boutons pour la conversion des points de fidélité -->
        <div class="info-section">
            <h3>Convertir les points de fidélité</h3>

            <p style="margin-bottom: 1rem; background-color: #f8d7da; padding: 5px; border-radius: 5px;">
                Réduction de 10% pour 200 points de fidélité
                <?php
                if ($client['pointFidelite'] >= 200) {
                    $reduction = 0.1; // Exemple
                    $point = 200;
                    $nomCodePromo = 'Client' . $client_id . 'Fidele200'; // Exemple
                
                    echo '<a href="ajouterCodePromo.php?idClient=' . urlencode($client_id) .
                        '&reduction=' . urlencode($reduction) .
                        '&nomCodePromo=' . urlencode($nomCodePromo) .
                        '&point=' . urlencode($point) .
                        '
     " class="btn">
     <button class="valider-panier">Convertir</button>
     </a>';
                }
                ?>
            </p>

            <p style="margin-bottom: 1rem; background-color: #f8d7da; padding: 5px; border-radius: 5px;">
                Réduction de 30% pour 400 points de fidélité
                <?php
                if ($client['pointFidelite'] >= 400) {
                    $reduction = 0.3; // Exemple
                    $point = 400;
                    $nomCodePromo = 'Client' . $client_id . 'Fidele400'; // Exemple
                
                    echo '<a href="ajouterCodePromo.php?idClient=' . urlencode($client_id) .
                        '&reduction=' . urlencode($reduction) .
                        '&nomCodePromo=' . urlencode($nomCodePromo) .
                        '&point=' . urlencode($point) .
                        '
     " class="btn">
     <button class="valider-panier">Convertir</button>
     </a>';
                }
                ?>
            </p>

        </div>
            <!--Liste de mes CodePromo

        -->
        <div class="info-section">
            <h3>Mes codes promo</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Code</th>
                        <th>Réduction</th>
                        <th>Date de début</th>
                        <th>Date de fin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = $conn->prepare("SELECT * FROM codePromotion WHERE idClient = :client_id");
                    $query->bindParam(':client_id', $client_id);
                    $query->execute();
                    $codesPromo=[]; 
                    //verifier si les code non pas expirer
                    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                        if ($row['dateFin'] > date("Y-m-d")) {
                            $codesPromo[] = $row;
                        }
                    }
                  
                    foreach ($codesPromo as $codePromo) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($codePromo['NomCodePromo']) . '</td>';
                        echo '<td>' . htmlspecialchars($codePromo['CodePromo']) . '</td>';
                        echo '<td>' . htmlspecialchars($codePromo['reduction']) . '</td>';
                        echo '<td>' . htmlspecialchars($codePromo['dateDebut']) . '</td>';
                        echo '<td>' . htmlspecialchars($codePromo['dateFin']) . '</td>';
                        echo '</tr>';
                    }
                    
                    ?>
                </tbody>
            </table>
    </section>
</main>

<?php
include "footer.php";
?>
</body>

</html>