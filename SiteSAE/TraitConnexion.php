<?php
session_start();
include "Connect.inc.php"; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $remember = isset($_POST['remember']); // Détermine si "Se souvenir de moi" est coché

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

            // Si "Se souvenir de moi" est coché, créer les cookies
            if ($remember) {
                setcookie('CidClient', $user['email'], time() + 60*60*24, "/"); // 1 jour
                setcookie('last_connexion', date('Y-m-d H:i:s'), time() + 60*60*24, "/"); // Date actuelle
                setcookie('ip_address', $_SERVER['REMOTE_ADDR'], time() + 60*60*24, "/"); // Adresse IP
                setcookie('browser_info', $_SERVER['HTTP_USER_AGENT'], time() + 60*60*24, "/"); // Infos navigateur
            } else {
                // Sinon, supprimer les cookies existants
                if (isset($_COOKIE['CidClient'])) {
                    setcookie('CidClient', '', time() - 3600, "/"); 
                    setcookie('last_connexion', '', time() - 3600, "/");
                    setcookie('ip_address', '', time() - 3600, "/");
                    setcookie('browser_info', '', time() - 3600, "/");
                }
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
