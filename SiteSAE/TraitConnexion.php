<?php
include "Connect.inc.php"; // Fichier contenant les paramètres de connexion à la base de données

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération et sécurisation des entrées utilisateur
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;

    // Vérification des champs vides
    if (empty($email) || empty($password)) {
        header("Location: connexionCompte.php?error=Tous les champs sont obligatoires.");
        exit;
    }

    // Préparation de la requête SQL
    $query = "SELECT * FROM Client WHERE email = :email";
    $result = $conn->prepare($query);
    $result->bindParam(':email', $email);
    $result->execute();
    $client = $result->fetch(PDO::FETCH_ASSOC);

    // Vérification des résultats
    if ($client) {
        // Vérification du mot de passe
        if (password_verify($password, $client['password'])) {
            // Démarrer la session utilisateur
            session_start();
            $_SESSION['idClient'] = $client['id'];
            $_SESSION['client_prenom'] = $client['prenom'];
            $_SESSION['client_email'] = $client['email'];

            // Gestion de la case "Se souvenir de moi"
            if ($remember) {
                setcookie("Cidclient", $client['id'], time() + 60*5); 
                setcookie("ClientEmail", $client['email'], time() + 60*5); // Sauvegarder l'email

            }

            // Redirection vers la page de détail du compte
            header("Location: detailCompte.php");
            exit;
        } else {
            header("Location: connexionCompte.php?error=Mot de passe incorrect.");
            exit;
        }
    } else {
        header("Location: connexionCompte.php?error=Aucun compte trouvé avec cet email.");
        exit;
    }
}
?>