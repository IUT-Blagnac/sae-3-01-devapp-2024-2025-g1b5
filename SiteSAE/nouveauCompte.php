<?php
include "header.php";
include "Connect.inc.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation et assainissement des entrées utilisateur
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $date_naissance = htmlspecialchars(trim($_POST['date-naissance']));
    $num_telephone = htmlspecialchars(trim($_POST['num-telephone']));
    $genre = htmlspecialchars(trim($_POST['genre']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];

    // Vérification des mots de passe
    if ($password !== $confirm_password) {
        echo "Les mots de passe ne correspondent pas.";
    } else {
        // Hachage du mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Conversion du genre
        if ($genre === "homme") {
            $genre = "H";
        } elseif ($genre === "femme") {
            $genre = "F";
        }

        try {
            // Requête préparée pour éviter les injections SQL
            $client = $conn->prepare("INSERT INTO Client (email, password, prenom, nom, numTel, genreC, dateNaissance) VALUES (:email, :password, :prenom, :nom, :num_telephone, :genre, :date_naissance)");
            $client->bindParam(':prenom', $prenom);
            $client->bindParam(':nom', $nom);
            $client->bindParam(':date_naissance', $date_naissance);
            $client->bindParam(':num_telephone', $num_telephone);
            $client->bindParam(':genre', $genre);
            $client->bindParam(':email', $email);
            $client->bindParam(':password', $hashed_password);
            $client->execute();
            echo "Compte créé avec succès!";
            header("Location: connexionCompte.php");
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage();
        }
    }
}
?>

<main class="d-flex justify-content-center align-items-center"
    style="height: 150vh; background-color: #f9f9f9; margin-top: 0;">
    <div class="card p-4 shadow" style="width: 350px;">
        <h2 class="text-center mb-4" style="color: #FF1F11;">Créer compte</h2>
        <form method="post">
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" placeholder="Entrez votre prénom" required>
            </div>
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" placeholder="Entrez votre nom" required>
            </div>
            <div class="mb-3">
                <label for="date-naissance" class="form-label">Date de naissance</label>
                <input type="date" class="form-control" id="date-naissance" name="date-naissance" required>
            </div>
            <div class="mb-3">
                <label for="num-telephone" class="form-label">Numéro de téléphone</label>
                <input type="tel" class="form-control" id="num-telephone" name="num-telephone" placeholder="Entrez votre numéro de téléphone" required>
            </div>
            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <select id="genre" name="genre" class="form-select" required>
                    <option value="" disabled selected>Choisissez votre genre</option>
                    <option value="homme">Homme</option>
                    <option value="femme">Femme</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Saisir votre mot de passe" required>
            </div>
            <div class="mb-3">
                <label for="confirm-password" class="form-label">Confirmez votre mot de passe</label>
                <input type="password" class="form-control" id="confirm-password" name="confirm-password" placeholder="Ressaisir votre mot de passe" required>
            </div>
            <button type="submit" class="btn btn-danger w-100" style="background-color: #FF1F11;">Créer Compte</button>
            <div class="mt-3 text-center">
                <a href="connexionCompte.php" class="text-muted" style="font-size: 14px;">Se connecter</a>
            </div>
        </form>
    </div>
</main>
<?php
include "footer.php";
?>
</body>
</html>