<?php
include "header.php";

?>

<main class="main-container">
    <nav class="side-menu">
        <ul>
            <li><a href="#">Informations personnelles</a></li>
            <li><a href="#">Commandes récentes</a></li>
            <li><a href="#">Préférences</a></li>
            <li><a href="deconnexion.php">Déconnexion</a></li>
            <?php
            if (isset($_COOKIE['CidClient'])) {
                echo '<li class="nav-item">
                        <a class="nav-link" href="DetruireCookie.php">Détruire Cookie</a>
                      </li>';
            }
            ?>
        </ul>
    </nav>
    <section class="client-info">
        <h2>Bienvenue, <?php echo isset($_SESSION['client_prenom']) ? htmlspecialchars($_SESSION['client_prenom']) : 'Invité'; ?></h2>
        <div class="info-section">
            <h3>Informations personnelles</h3>
            <p>Prenom : <?php echo isset($_SESSION['client_prenom']) ? htmlspecialchars($_SESSION['client_prenom']) : 'Non défini'; ?></p>
            <p>Email : <?php echo isset($_SESSION['client_email']) ? htmlspecialchars($_SESSION['client_email']) : 'Non défini'; ?></p>
            <p>Adresse :<?php echo isset($_SESSION['client_adresse']) ? htmlspecialchars($_SESSION['client_adresse']) : 'Non défini'; ?></p>
            <button class="button">Modifier</button>
        </div>
        <div class="info-section">
            <h3>Commandes récentes</h3>
            <ul>
                <li>Commande #12345 - Statut : Livrée</li>
                <li>Commande #12346 - Statut : En cours</li>
                <li>Commande #12347 - Statut : Annulée</li>
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