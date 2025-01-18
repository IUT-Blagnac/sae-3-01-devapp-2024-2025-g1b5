<?php
session_start();
include "Connect.inc.php"; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $remember = isset($_POST['remember']);

    // Vérifiez les informations de connexion dans la base de données
    $query = $conn->prepare("SELECT * FROM Client WHERE email = :email");
    $query->bindParam(':email', $email);
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        $user = $result[0];

        // Vérifiez le mot de passe
        if (password_verify($password, $user['password'])) {
            // Connexion réussie
            $_SESSION['client_email'] = $user['email'];
            $_SESSION['client_prenom'] = $user['prenom'];
            $_SESSION['client_id'] = $user['idClient']; // Pour la gestion du panier

            // Si "Se souvenir de moi" est coché, créer les cookies
            if ($remember) {
                $time = time() + 60 * 60 * 24; // 1 jour
                setcookie('CidClient', $user['email'], $time, "/", "", false, true); // HttpOnly
                setcookie('last_connexion', date('Y-m-d H:i:s'), $time, "/", "", false, true);
                setcookie('ip_address', $_SERVER['REMOTE_ADDR'], $time, "/", "", false, true);
                setcookie('browser_info', $_SERVER['HTTP_USER_AGENT'], $time, "/", "", false, true);
            } else {
                // Nettoyer les cookies existants
                setcookie('CidClient', '', time() - 3600, "/");
                setcookie('last_connexion', '', time() - 3600, "/");
                setcookie('ip_address', '', time() - 3600, "/");
                setcookie('browser_info', '', time() - 3600, "/");
            }

            // Gestion du panier de la session
            if (isset($_SESSION['panier'])) {
                foreach ($_SESSION['panier'] as $idProd => $quantite) {
                    $appelAjoutPanier = 'CALL AjouterPanier( :idClient, :idProduit, :quantite )';
                    $statement = $conn->prepare($appelAjoutPanier);
                    $statement->bindParam(':idClient', $user['idClient']);
                    $statement->bindParam(':idProduit', $idProd);
                    $statement->bindParam(':quantite', $quantite);
                    $statement->execute();
                    $statement->closeCursor();
                }
                unset($_SESSION['panier']);
            }

            // Redirection vers la page de détail du compte
            header('Location: detailCompte.php');
            exit();
        } else {
            // Mot de passe incorrect
            header('Location: connexionCompte.php?error=Mot de passe incorrect');
            exit();
        }
    } else {
        // Email non trouvé
        header('Location: connexionCompte.php?error=Email non trouvé');
        exit();
    }
} else {
    // Redirection si la méthode n'est pas POST
    header('Location: connexionCompte.php');
    exit();
}
?>
