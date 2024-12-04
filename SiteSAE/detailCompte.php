<?php
include "header.php";
?>

<main class="main-container">
    <nav class="side-menu">
        <ul>
            <li><a href="#">Informations personnelles</a></li>
            <li><a href="#">Commandes récentes</a></li>
            <li><a href="#">Préférences</a></li>
            <li><a href="#">Déconnexion</a></li>
        </ul>
    </nav>
    <section class="client-info">
        <h2>Bienvenue, [Nom de l'utilisateur]</h2>
        <div class="info-section">
            <h3>Informations personnelles</h3>
            <p>Nom : [Nom]</p>
            <p>Email : [Email]</p>
            <p>Adresse : [Adresse]</p>
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
