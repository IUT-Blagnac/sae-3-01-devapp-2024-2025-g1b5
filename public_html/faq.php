<?php
header('Content-Type: text/html; charset=utf-8');
include "header.php"; 
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Lutin & Co.</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assurez-vous que ce fichier CSS existe -->
</head>

<body class="background-page">
    <main>
        <!-- Section FAQ -->
        <section class="pageInfo">
            <h1>Foire Aux Questions (FAQ)</h1>
            <p>
                Vous avez des questions ou rencontrez un problème avec notre site ou vos commandes ? Voici quelques réponses aux questions fréquentes qui pourraient vous aider.
            </p>

            <!-- Section Questions Fréquentes -->
            <h2>Questions fréquentes</h2>

            <details>
                <summary>Comment suivre ma commande ?</summary>
                <p>Pour suivre votre commande, vous pouvez vous rendre sur la page <a href="suivi.php">"Suivi de commande"</a> et entrer votre numéro de commande pour obtenir les informations de livraison.</p>
            </details>

            <details>
                <summary>Que faire en cas de retard de livraison ?</summary>
                <p>Si vous constatez un retard de livraison, veuillez vérifier l'état de votre commande sur notre page de suivi. Si le problème persiste, consultez la page <a href="probleme-livraison.php">"Problème sur la livraison"</a>.</p>
            </details>

            <details>
                <summary>Quels sont les délais de livraison ?</summary>
                <p>Nos délais de livraison varient entre 3 à 5 jours ouvrés pour la plupart des commandes. Toutefois, pendant les périodes de fêtes, cela peut aller jusqu’à 7 jours ouvrés.</p>
            </details>

            <details>
                <summary>Comment annuler ou modifier une commande ?</summary>
                <p>Pour annuler ou modifier une commande, vous devez agir dans les 24 heures suivant votre achat. Rendez-vous sur la page <a href="modification-commande.php">"Modification de commande"</a> pour plus d'informations.</p>
            </details>

            <details>
                <summary>Quels moyens de paiement acceptez-vous ?</summary>
                <p>Nous acceptons les cartes Visa, MasterCard, PayPal et les virements bancaires sécurisés. Toutes les transactions sont chiffrées pour assurer votre sécurité.</p>
            </details>

            <details>
                <summary>Comment retourner un article ?</summary>
                <p>Pour retourner un article, consultez notre page <a href="retour.php">"Politique de retour"</a> pour les conditions et les instructions à suivre.</p>
            </details>

            <details>
                <summary>Comment contacter votre service client ?</summary>
                <p>Vous pouvez nous joindre via la page <a href="contact.php">"Contactez-nous"</a>, par e-mail à <strong>contact@lutinco.com</strong> ou par téléphone au +1 234 567 890.</p>
            </details>

            <details>
                <summary>Proposez-vous des livraisons internationales ?</summary>
                <p>Oui, nous livrons dans le monde entier. Les frais de livraison et les délais varient en fonction de votre localisation.</p>
            </details>

            <details>
                <summary>Comment utiliser un code promo ?</summary>
                <p>Pour appliquer un code promo, entrez-le lors du passage en caisse dans le champ "Code Promo". La réduction sera automatiquement appliquée au montant total.</p>
            </details>

            <details>
                <summary>Comment puis-je créer un compte client ?</summary>
                <p>Vous pouvez créer un compte client en cliquant sur "Créer un compte" sur notre page d'accueil et en suivant les instructions. Cela vous permettra de suivre vos commandes et d’accéder à des offres exclusives.</p>
            </details>

            <details>
                <summary>Quelle est votre politique de confidentialité ?</summary>
                <p>Nous respectons votre vie privée. Toutes les informations sont sécurisées conformément à notre <a href="confidentialite.php">"Politique de confidentialité"</a>.</p>
            </details>

            <details>
                <summary>Comment puis-je m'inscrire à votre newsletter ?</summary>
                <p>Vous pouvez vous inscrire à notre newsletter en entrant votre adresse e-mail dans la section dédiée au bas de la page d'accueil. Vous recevrez des offres exclusives et des actualités.</p>
            </details>

            <details>
                <summary>Offrez-vous des cartes cadeaux ?</summary>
                <p>Oui, nous proposons des cartes cadeaux numériques que vous pouvez offrir à vos proches. Elles sont disponibles dans plusieurs montants.</p>
            </details>

            <details>
                <summary>Que faire si je ne reçois pas ma confirmation de commande ?</summary>
                <p>Si vous ne recevez pas de confirmation, vérifiez votre dossier spam. Sinon, contactez notre service client pour résoudre le problème.</p>
            </details>

            <details>
                <summary>Proposez-vous des réductions pour les grands volumes ?</summary>
                <p>Oui, nous offrons des réductions pour les commandes en grande quantité. Veuillez nous contacter pour obtenir un devis personnalisé.</p>
            </details>

        </section>
    </main>

    <?php include "footer.php"; // Inclut le pied de page ?>
</body>

</html>
