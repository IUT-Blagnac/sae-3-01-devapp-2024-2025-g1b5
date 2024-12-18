<?php
ob_start(); // Démarre la mise en tampon de sortie

// Définir l'en-tête pour le bon encodage
header('Content-Type: text/html; charset=utf-8');
include "header.php";

// Récupérer les informations actuelles de l'utilisateur
$date_actuelle = time(); // Date et heure actuelle en timestamp
$ip_utilisateur = $_SERVER['REMOTE_ADDR'] ?? 'IP inconnue'; // Adresse IP de l'utilisateur
$user_agent_actuel = $_SERVER['HTTP_USER_AGENT'] ?? 'Non disponible'; // Informations du navigateur

// Effacer les cookies si l'utilisateur le demande
$confirmation_suppression = false;

if (isset($_POST['effacer_cookies'])) {
    setcookie('DerniereConnexion', '', time() - 3600, "/");
    setcookie('UserAgent', '', time() - 3600, "/");
    $confirmation_suppression = true;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Cookies</title>
    <link rel="stylesheet" href="styles.css"> <!-- Lien vers votre CSS -->
</head>
<body class="background-page">
    <main>
        <section class="pageInfo">
            <h1>Gestion des informations de session</h1>
            <p>
                Voici les informations actuelles liées à votre session. Ces données sont temporaires et ne sont pas stockées de manière permanente.
            </p>

            <!-- Informations de la session actuelle -->
            <h2>Informations de la session actuelle</h2>
            <p><strong>Date et heure :</strong> <?php echo date("d/m/Y H:i:s", $date_actuelle); ?></p>
            <p><strong>Adresse IP :</strong> <?php echo htmlspecialchars($ip_utilisateur); ?></p>
            <p><strong>Informations sur votre navigateur :</strong> <?php echo htmlspecialchars($user_agent_actuel); ?></p>

            <!-- Message de confirmation pour suppression des cookies -->
            <?php if ($confirmation_suppression): ?>
                <p class="alert alert-success">
                    Les cookies ont été effacés avec succès. Les informations ci-dessus sont celles de votre session actuelle.
                </p>
            <?php endif; ?>

            <!-- Bouton pour effacer les cookies -->
            <form method="post" action="">
                <button type="submit" name="effacer_cookies" class="btn btn-danger">Effacer les cookies</button>
            </form>
        </section>
    </main>

    <?php include "footer.php"; ?>
</body>
</html>

<?php
ob_end_flush(); // Envoie la sortie finale au navigateur
?>
