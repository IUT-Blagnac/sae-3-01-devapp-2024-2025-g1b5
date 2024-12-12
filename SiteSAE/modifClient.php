<?php
include "header.php";
include "Connect.inc.php"; 
require "verifConnexion.php";

// Récupérer l'email du client
$client_email = isset($_SESSION['client_email']) ? $_SESSION['client_email'] : '';


// Récupérer les informations du client
$query = $conn->prepare("SELECT * FROM Client WHERE email = :client_email");
$query->bindParam(':client_email', $client_email);
$query->execute();
$client = $query->fetch(PDO::FETCH_ASSOC);
$client_id = $client['idClient'];
$prenom = $client['prenom'];
$nom = $client['nom'];
$email = $client['email'];
$numTel = $client['numTel'];
$genreC = $client['genreC'];
$dateNaissance = $client['dateNaissance'];
$idAdresse = $client['idAdresse'];

// Récupérer les informations de l'adresse
$query = $conn->prepare("SELECT * FROM Adresse WHERE idAdresse = :idAdresse");
$query->bindParam(':idAdresse', $idAdresse);
$query->execute();
$adresse = $query->fetch(PDO::FETCH_ASSOC);
$codePostal = $adresse ? $adresse['codePostal'] : '';
$ville = $adresse ? $adresse['ville'] : '';
$rue = $adresse ? $adresse['rue'] : '';
$pays = $adresse ? $adresse['pays'] : '';

// Récupérer les informations de la carte bancaire
$query = $conn->prepare("SELECT * FROM CarteBancaire WHERE idClient = :client_id");
$query->bindParam(':client_id', $client_id);
$query->execute();
$carte = $query->fetch(PDO::FETCH_ASSOC);
$numCarte = $carte ? $carte['numCarte'] : '';
$dateExpiration = $carte ? $carte['dateExpiration'] : '';
$codeCarte = $carte ? $carte['codeCarte'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $numTel = htmlspecialchars(trim($_POST['numTel']));
    $numTel = str_replace(' ', '', $numTel); // Retirer les espaces
    $genreC = htmlspecialchars(trim($_POST['genreC']));
    $dateNaissance = htmlspecialchars(trim($_POST['dateNaissance']));
    $codePostal = htmlspecialchars(trim($_POST['codePostal']));
    $ville = htmlspecialchars(trim($_POST['ville']));
    $rue = htmlspecialchars(trim($_POST['rue']));
    $pays = htmlspecialchars(trim($_POST['pays']));
    $numCarte = htmlspecialchars(trim($_POST['numCarte']));
    $dateExpiration = htmlspecialchars(trim($_POST['dateExpiration']));
    $codeCarte = htmlspecialchars(trim($_POST['codeCarte']));

    // Conversion du genre pour la BD (H ou F)
    if ($genreC === "homme") {
        $genreC = "H";
    } elseif ($genreC === "femme") {
        $genreC = "F";
    }

    // Mettre à jour les informations du client dans la base de données
    $query = $conn->prepare("UPDATE Client SET prenom = :prenom, nom = :nom, email = :email, numTel = :numTel, genreC = :genreC, dateNaissance = :dateNaissance WHERE idClient = :client_id");
    $query->bindParam(':prenom', $prenom);
    $query->bindParam(':nom', $nom);
    $query->bindParam(':email', $email);
    $query->bindParam(':numTel', $numTel);
    $query->bindParam(':genreC', $genreC);
    $query->bindParam(':dateNaissance', $dateNaissance);
    $query->bindParam(':client_id', $client_id);
    $query->execute();

    // Mettre à jour ou insérer les informations de l'adresse dans la base de données
    if ($codePostal || $ville || $rue || $pays) {
        if ($adresse) {
            $query = $conn->prepare("UPDATE Adresse SET codePostal = :codePostal, ville = :ville, rue = :rue, pays = :pays WHERE idAdresse = :idAdresse");
            $query->bindParam(':codePostal', $codePostal);
            $query->bindParam(':ville', $ville);
            $query->bindParam(':rue', $rue);
            $query->bindParam(':pays', $pays);
            $query->bindParam(':idAdresse', $idAdresse);
        } else {
            $query = $conn->prepare("INSERT INTO Adresse (codePostal, ville, rue, pays) VALUES (:codePostal, :ville, :rue, :pays)");
            $query->bindParam(':codePostal', $codePostal);
            $query->bindParam(':ville', $ville);
            $query->bindParam(':rue', $rue);
            $query->bindParam(':pays', $pays);
            $query->execute();
            $idAdresse = $conn->lastInsertId();

            // Mettre à jour l'ID de l'adresse dans la table Client
            $query = $conn->prepare("UPDATE Client SET idAdresse = :idAdresse WHERE idClient = :client_id");
            $query->bindParam(':idAdresse', $idAdresse);
            $query->bindParam(':client_id', $client_id);
        }
        $query->execute();
    } else {
        // Si les informations d'adresse sont vides, définissez idAdresse à NULL
        $query = $conn->prepare("UPDATE Client SET idAdresse = NULL WHERE idClient = :client_id");
        $query->bindParam(':client_id', $client_id);
        $query->execute();
    }

    // Mettre à jour ou insérer les informations de la carte bancaire dans la base de données
    if ($numCarte || $dateExpiration || $codeCarte) {
        if ($carte) {
            $query = $conn->prepare("UPDATE CarteBancaire SET numCarte = :numCarte, dateExpiration = :dateExpiration, codeCarte = :codeCarte WHERE idClient = :client_id");
            $query->bindParam(':numCarte', $numCarte);
            $query->bindParam(':dateExpiration', $dateExpiration);
            $query->bindParam(':codeCarte', $codeCarte);
            $query->bindParam(':client_id', $client_id);
        } else {
            $query = $conn->prepare("INSERT INTO CarteBancaire (numCarte, dateExpiration, codeCarte, idClient) VALUES (:numCarte, :dateExpiration, :codeCarte, :client_id)");
            $query->bindParam(':numCarte', $numCarte);
            $query->bindParam(':dateExpiration', $dateExpiration);
            $query->bindParam(':codeCarte', $codeCarte);
            $query->bindParam(':client_id', $client_id);
        }
        $query->execute();
    } else {
        // Si les informations de la carte bancaire sont vides, supprimez l'entrée existante
        $query = $conn->prepare("DELETE FROM CarteBancaire WHERE idClient = :client_id");
        $query->bindParam(':client_id', $client_id);
        $query->execute();
    }

    // Redirection vers la page de détail du compte
    header('Location: detailCompte.php');
    exit();
}

// Fonction pour formater le numéro de téléphone
function formatNumTel($numTel) {
    // Supprimer tous les espaces existants
    $numTel = str_replace(' ', '', $numTel);

    // Ajouter un espace après chaque deux chiffres
    return preg_replace('/(\d{2})(?=\d)/', '$1 ', $numTel);
}

// Formater le numéro de téléphone avant de l'afficher
$numTel = formatNumTel($numTel);
?>

<main class="main-container">
    <nav class="side-menu">
        <ul>
            <li><a href="detailCompte.php">Informations personnelles</a></li>
            <li><a href="deconnexion.php">Déconnexion</a></li>
        </ul>
    </nav>
    <section class="client-info">
        <h2>Modifier les informations du compte</h2>
        <form method="post" action="modifClient.php">
            <div class="info-section">
                <h3>Informations personnelles</h3>
                <br><br>
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required>
                <br><br>
                
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($nom); ?>" required>
                <br><br>
                
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <br><br>
                
                <label for="numTel">Numéro de téléphone :</label>
                <input type="tel" id="numTel" name="numTel" value="<?php echo htmlspecialchars($numTel); ?>" required oninput="numTelrespect()">
                <div id="numTel-respect-message" style="color: red;"></div>
                <br><br>
                
                <label for="genreC">Genre :</label>
                <select id="genreC" name="genreC" required>
                    <option value="" disabled <?php echo ($genreC == '') ? 'selected' : ''; ?>>Choisissez votre genre</option>
                    <option value="homme" <?php echo ($genreC == 'H') ? 'selected' : ''; ?>>Homme</option>
                    <option value="femme" <?php echo ($genreC == 'F') ? 'selected' : ''; ?>>Femme</option>
                </select>
                <br><br>
                
                <label for="dateNaissance">Date de naissance :</label>
                <input type="date" id="dateNaissance" name="dateNaissance" value="<?php echo htmlspecialchars($dateNaissance); ?>" required>
                <br><br>
            </div>
            <div class="info-section">
                <h3>Adresse</h3>
                <label for="codePostal">Code postal :</label>
                <input type="text" id="codePostal" name="codePostal" value="<?php echo htmlspecialchars($codePostal); ?>" oninput="validateCodePostal()">
                <div id="codePostal-respect-message" style="color: red;"></div>
                <br>
                
                <label for="ville">Ville :</label>
                <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($ville); ?>">
                <br><br>
                
                <label for="rue">Rue :</label>
                <input type="text" id="rue" name="rue" value="<?php echo htmlspecialchars($rue); ?>">
                <br><br>
                
                <label for="pays">Pays :</label>
                <input type="text" id="pays" name="pays" value="<?php echo htmlspecialchars($pays); ?>">
                <br><br>
            </div>
            <div class="info-section">
                <h3>Informations de paiement</h3>
                <label for="numCarte">Numéro de carte bancaire :</label>
                <input type="text" id="numCarte" name="numCarte" value="<?php echo htmlspecialchars($numCarte); ?>">
                <br><br>
                
                <label for="dateExpiration">Date d'expiration :</label>
                <input type="date" id="dateExpiration" name="dateExpiration" value="<?php echo htmlspecialchars($dateExpiration); ?>">
                <br><br>
                
                <label for="codeCarte">CVV :</label>
                <input type="text" id="codeCarte" name="codeCarte" value="<?php echo htmlspecialchars($codeCarte); ?>">
                <br><br>
            </div>
            <button type="submit" class="button">Enregistrer les modifications</button>
        </form>
    </section>
</main>

<?php
include "footer.php";
?>
<script>
function formatNumTel(numTel) {
    // Supprimer tous les espaces existants
    numTel = numTel.replace(/\s+/g, '');

    // Ajouter un espace après chaque deux chiffres
    return numTel.replace(/(\d{2})(?=\d)/g, '$1 ');
}

function numTelrespect() {
    var numTelInput = document.getElementById("numTel");
    var numTel = numTelInput.value.replace(/\s+/g, ''); // Supprimer les espaces pour la validation
    var message = document.getElementById("numTel-respect-message");

    if (numTel === "") {
        message.textContent = "";
    } else if (!/^0\d{9}$/.test(numTel)) {
        message.textContent = "Le numéro de téléphone doit contenir exactement 10 chiffres et doit commencer par 0.";
    } else {
        message.textContent = "";
        numTelInput.value = formatNumTel(numTel); // Formater le numéro de téléphone
    }
}

function validateCodePostal() {
    var codePostalInput = document.getElementById("codePostal");
    var codePostal = codePostalInput.value;
    var message = document.getElementById("codePostal-respect-message");

    if (codePostal === "") {
        message.textContent = "";
    } else if (!/^\d{5}$/.test(codePostal)) {
        message.textContent = "Le code postal doit contenir exactement 5 chiffres.";
    } else {
        message.textContent = "";
    }
}
</script>
</body>
</html>