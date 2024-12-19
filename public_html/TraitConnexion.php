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

            // Si "Se souvenir de moi" est coché, créer les cookies
            if ($remember) {
                setcookie('CidClient', $user['email'], time() + 60*60*24, "/"); // 1 jour
            }

            // gérer le panier de la session quand on se connecte si un panier de session existait
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