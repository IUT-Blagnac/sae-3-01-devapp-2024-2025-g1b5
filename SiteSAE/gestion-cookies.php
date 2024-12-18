<?php
ob_start(); // Mise en mémoire tampon
header('Content-Type: text/html; charset=utf-8');
include "header.php";

// Vérifie si une session est active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Suppression des cookies si le bouton est cliqué
if (isset($_POST['delete_cookies'])) {
    setcookie('CidClient', '', time() - 3600, "/");
    setcookie('last_connexion', '', time() - 3600, "/");
    setcookie('browser_info', '', time() - 3600, "/");
    setcookie('ip_address', '', time() - 3600, "/");
    header("Location: gestion-cookies.php");
    exit();
}

// Vérifie si les cookies existent
$cid_client = isset($_COOKIE['CidClient']) ? htmlspecialchars($_COOKIE['CidClient']) : null;
$last_connexion = isset($_COOKIE['last_connexion']) ? htmlspecialchars($_COOKIE['last_connexion']) : null;
$browser_info = isset($_COOKIE['browser_info']) ? htmlspecialchars($_COOKIE['browser_info']) : null;
$ip_address = isset($_COOKIE['ip_address']) ? htmlspecialchars($_COOKIE['ip_address']) : null;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Cookies</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body class="background-page">
    <main>
        <section class="pageInfo">
            <h1>Gestion des Cookies</h1>

            <?php if ($cid_client): ?>
                <p>Voici les informations liées à votre dernière connexion :</p>
                <ul>
                    <li><strong>Adresse e-mail :</strong> <?php echo $cid_client; ?></li>
                    <li><strong>Date de dernière connexion :</strong> <?php echo $last_connexion; ?></li>
                    <li><strong>Adresse IP :</strong> <?php echo $ip_address; ?></li>
                    <li><strong>Informations du navigateur :</strong> <?php echo $browser_info; ?></li>
                </ul>
            <?php else: ?>
                <p>Aucune information n'est disponible car vous n'avez pas choisi de sauvegarder vos cookies.</p>
            <?php endif; ?>

            <!-- Bouton pour détruire les cookies -->
            <?php if ($cid_client): ?>
                <form method="post" action="">
                    <button type="submit" name="delete_cookies" class="btn btn-danger" style="background-color: #FF1F11; color: #fff; border: none; padding: 10px 20px; margin-top: 20px; border-radius: 5px;">
                        Détruire les cookies
                    </button>
                </form>
            <?php endif; ?>
        </section>
    </main>

    <?php include "footer.php"; ?>
</body>

</html>

<?php
ob_end_flush(); // Envoie le contenu de la mémoire tampon
?>
