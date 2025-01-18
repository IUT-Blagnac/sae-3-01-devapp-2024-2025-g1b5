<?php
header('Content-Type: text/html; charset=utf-8');
include "header.php"; 
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Besoin d'aide - Lutin & Co.</title>
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers votre CSS -->
</head>

<body class="background-page">
    <main>
        <!-- Section principale -->
        <section class="pageInfo">
            <h1>Besoin d'aide ?</h1>
            <p>
                Vous avez des questions ou rencontrez un problème avec notre site ou vos commandes ? Voici quelques réponses aux questions fréquentes qui pourraient vous aider.
            </p>

            <!-- Section FAQ -->
            <h2>Questions fréquentes</h2>
            <details>
                <summary>Comment suivre ma commande ?</summary>
                <p>Pour suivre votre commande, vous pouvez vous rendre sur la page <a href="suivi.php">"Suivi de commande"</a> et entrer votre numéro de commande pour obtenir les informations de livraison.</p>
            </details>
            <details>
                <summary>Que faire en cas de retard de livraison ?</summary>
                <p>Si vous constatez un retard de livraison, veuillez vérifier l'état de votre commande sur notre page de suivi. Si le problème persiste, vous pouvez consulter la page <a href="probleme-livraison.php">"Problème sur la livraison"</a> pour plus d'informations.</p>
            </details>
            <details>
                <summary>Comment annuler ou modifier une commande ?</summary>
                <p>Si vous souhaitez annuler ou modifier une commande, cela doit être fait dans les 24 heures suivant la commande. Veuillez consulter notre page <a href="modification-commande.php">"Modification de commande"</a> pour les instructions détaillées.</p>
            </details>
            <details>
                <summary>Comment retourner un article ?</summary>
                <p>Pour retourner un article, consultez notre politique de retour sur la page <a href="retour.php">"Politique de retour"</a> et suivez les étapes indiquées.</p>
            </details>
            <details>
                <summary>Que faire si un produit reçu est endommagé ?</summary>
                <p>Si vous avez reçu un produit endommagé, veuillez nous contacter immédiatement. Consultez notre page <a href="probleme-livraison.php">"Problème sur la livraison"</a> pour signaler un produit endommagé et demander un remplacement.</p>
            </details>
            
            <p>
                Si vous avez plus de questions, nous vous encourageons a aller sur notre <a href="faq.php">FAQ</a> ou bien de <a href="contact.php">nous contacter</a> directement.
            </p>

            <!-- Section dépannage technique -->
            <h2>Problèmes techniques</h2>
            <p>Si vous rencontrez des problèmes techniques sur notre site, essayez les solutions suivantes :</p>
            <ul>
                <li><strong>Vérifiez votre connexion internet.</strong> Un problème de réseau peut empêcher l'affichage correct des pages.</li>
                <li><strong>Essayez un autre navigateur.</strong> Certaines fonctionnalités peuvent ne pas fonctionner sur certains navigateurs ou versions anciennes. Essayez de mettre à jour votre navigateur ou d'en utiliser un autre (Google Chrome, Firefox, Safari, etc.).</li>
                <li><strong>Videz le cache de votre navigateur.</strong> Un cache trop rempli peut causer des bugs d'affichage. Vous pouvez vider le cache dans les paramètres de votre navigateur.</li>
                <li><strong>Des images ou des vidéos ne se chargent pas ?</strong> Vérifiez que vous avez une connexion internet stable et désactivez les bloqueurs de publicités qui peuvent interférer avec le chargement du contenu.</li>
                <li><strong>Le site ne se charge pas correctement ?</strong> Essayez de recharger la page ou de vider les cookies et le cache de votre navigateur pour résoudre ce problème.</li>
                <li><strong>Impossible de vous connecter à votre compte ?</strong> Si vous avez oublié votre mot de passe, cliquez sur le lien "Mot de passe oublié" sur la page de connexion pour le réinitialiser.</li>
                <li><strong>Erreur 404 ou page introuvable ?</strong> Si vous rencontrez cette erreur, vérifiez que l'URL est correcte ou revenez à la page d'accueil pour naviguer à nouveau.</li>
            </ul>

            <!-- Liens vers d'autres ressources -->
            <h2>Ressources supplémentaires</h2>
            <p>Vous trouverez également des informations utiles dans notre section <a href="faq.php">FAQ générale</a> ou sur nos pages de <a href="cgv.php">Conditions générales de vente</a> et <a href="politique-cookies.php">Politique de cookies</a>.</p>

        </section>
    </main>

    <?php include "footer.php"; ?>
</body>

</html>
