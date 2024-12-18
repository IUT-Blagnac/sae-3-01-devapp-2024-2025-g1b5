<?php
include "header.php";
include "Connect.inc.php";

// Récupérer l'email du client
$client_email = isset($_SESSION['client_email']) ? $_SESSION['client_email'] : '';

if ($client_email):
    // Récupérer les informations du client
    $query = $conn->prepare("SELECT * FROM Client WHERE email = :client_email");
    $query->bindParam(':client_email', $client_email);
    $query->execute();
    $client = $query->fetch(PDO::FETCH_ASSOC);

    $idClient = $client['idClient'];
    $client_role = $client['role'];
    $client_prenom = $client['prenom'];
    $client_nom = $client['nom'];
else:
    $idClient = 0;
    $client_role = null;
    $client_prenom = '';
    $client_nom = '';
endif;

// Récupérer l'idProduit depuis l'URL
$idProduit = isset($_GET['idProduit']) ? intval($_GET['idProduit']) : 0;

// Si l'idProduit est valide, récupérer les détails du produit
if ($idProduit > 0) {
    $prod = $conn->prepare("SELECT * FROM Produit WHERE idProduit = ?");
    $prod->execute([$idProduit]);
    $produit = $prod->fetch();
    $prod->closeCursor();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['deleteAvis']) && $_POST['deleteAvis'] == 1) {
        // Handle delete review
        $idProduit = $_POST['idProduit'];
        $idClient = $_POST['idClient'];
        $res = $conn->prepare("DELETE FROM Avis WHERE idProduit = ? AND idClient = ?");
        $res->execute([$idProduit, $idClient]);
    } elseif (isset($_POST['submitReponse'])) {
        // Handle add response
        $idProduit = $_GET['idProduit'];
        $idClientAvis = $_GET['idClientAvis'];
        $contenuReponse = $_POST['contenuReponse'];
        $dateReponse = date('Y-m-d H:i:s');

        // Store response in a session or a file
        $_SESSION['reponses'][$idProduit][$idClientAvis] = [
            'contenu' => $contenuReponse,
            'date' => $dateReponse,
            'prenom' => $client_prenom,
            'nom' => $client_nom
        ];
    } elseif (isset($_POST['updateReponse'])) {
        // Handle update response
        $idProduit = $_GET['idProduit'];
        $idClientAvis = $_GET['idClientAvis'];
        $contenuReponse = $_POST['contenuReponse'];
        $dateReponse = date('Y-m-d H:i:s');

        // Update response in a session or a file
        $_SESSION['reponses'][$idProduit][$idClientAvis] = [
            'contenu' => $contenuReponse,
            'date' => $dateReponse,
            'prenom' => $client_prenom,
            'nom' => $client_nom
        ];
    } elseif (isset($_POST['deleteReponse'])) {
        // Handle delete response
        $idProduit = $_GET['idProduit'];
        $idClientAvis = $_GET['idClientAvis'];

        // Delete response from a session or a file
        unset($_SESSION['reponses'][$idProduit][$idClientAvis]);
    } elseif (isset($_POST['deleteAvis'])) {
        // Handle delete review
        $idProduit = $_GET['idProduit'];
        $idClientAvis = $_GET['idClientAvis'];

        // Delete review from the database
        $res = $conn->prepare("DELETE FROM Avis WHERE idProduit = ? AND idClient = ?");
        $res->execute([$idProduit, $idClientAvis]);
    } else {
        // Handle add review
        if (isset($_POST['idProduit']) && isset($_POST['idClient'])) {
            $idProduit = $_POST['idProduit'];
            $idClient = $_POST['idClient'];
            $note = $_POST['note'];
            $contenu = $_POST['contenu'];
            $dateAvis = date('Y-m-d H:i:s');

            $res = $conn->prepare("INSERT INTO Avis (idProduit, idClient, contenu, note, dateAvis) VALUES (?, ?, ?, ?, ?)");
            $res->execute([$idProduit, $idClient, $contenu, $note, $dateAvis]);
        }
    }
    
}

//etoile en jaune 
function afficherEtoiles($note, $maxEtoiles = 5)
{
    $html = '';
    for ($i = 1; $i <= $maxEtoiles; $i++) {
        if ($i <= $note) {
            $html .= '<span style="color: yellow; font-size:1.5em" ; >★</span>';
        } else {
            $html .= '<span style = "font-size:1.5em" >☆</span>';
        }
    }
    return $html;
}

//recupere le nb max de produit en stock
$req = $conn->prepare("SELECT quantiteStock FROM Stock WHERE idProduit = ?");
$req->execute([$idProduit]);
$stock_max = $req->fetchColumn();
$req->closeCursor();

?>

<!-- Script js qui permet de choisir une bonne quantite de produit -->
<script>
    function ajusterQuantite() {
        var quantite = document.getElementById("quantite");
        var stockMax = <?php echo $stock_max; ?>

        if (quantite.value < 1) {
            quantite.value = 1;
        }
        if (quantite.value > stockMax) {
            quantite.value = stockMax;
        }
    }
</script>

<section class="presentation">
    <img src="image_Produit/Prod<?php echo $produit['idProduit']; ?>.jpg" width="50%" alt="<?php echo $produit['nomProduit']; ?>">
    <div>
        <h1> <?php echo $produit['nomProduit']; ?> </h1>
        <p>Ref : 00<?php echo $produit['idProduit']; ?> </p>
        <p>Age : <?php echo $produit['age']; ?> ans</p>

        <div class="prixDescription">
            <h2> <?php echo $produit['prix']; ?> €</h2>
        </div>

        <form action="ajouterPanier.php" method="get">
            <input type="text" value="<?php echo $produit['idProduit']; ?>" name="idProduit" hidden>
            <button type="submit" class="button">Ajouter au panier</button>
            <input type="number" id="quantite" value="1" name="quantite" min="1" oninput="ajusterQuantite()">
        </form>
        <button type="button" class="butFavoris" id="favButton">
            <img src="images/petit-coeur-rouge.png" alt="petit coeur" width="20px" id="favImage">
        </button>

        <!-- Intégration du JavaScript -->
        <script>
            document.getElementById('favButton').addEventListener('click', function() {
                var img = document.getElementById('favImage');

                if (img.src.includes('petit-coeur-rouge.png')) {
                    img.src = 'images/petit-coeur-plein.png'; // Remplacez ceci par le chemin de la deuxième image
                } else {
                    img.src = 'images/petit-coeur-rouge.png'; // Remplacez ceci par le chemin de l'image initiale
                }
            });
        </script>
    </div>
</section>

<section class="description">

    <div class="texteDescriptif">

        <h2>Description</h2>

        <p>
            <?php echo $produit['description']; ?>
        </p>

        <h2>Caractéristiques</h2>

        <ul style="list-style-type: none">
            <li><u>Âge</u> : <?php echo $produit['age']; ?> ans </li>
            <li><u>Dimensions</u> : <?php echo $produit['taille']; ?> cm </li>
            <li><u>Type</u> : construction</li>
            <li><u>Nombre de joueurs</u> : <?php echo $produit['nbJoueurMax']; ?> </li>
        </ul>
    </div>
</section>

<section class="avis">

    <h2>Avis</h2>
    <?php
    if ($client_email):
        echo '<button type="button" class="button-avis" onclick="blocAvis()">Ajouter un avis</button>';
    endif;
    ?>

    <!-- Form to add a new avis -->
    <section class="evaluation" id="avisForm" style="display: none;">
        <?php if ($idClient): ?>
            <form action="descriptionDetailcopie.php?idProduit=<?php echo $idProduit; ?>" method="post">
                <input type="hidden" name="idProduit" value="<?php echo $idProduit; ?>">
                <input type="hidden" name="idClient" value="<?php echo $idClient; ?>">
                <div>
                    <label for="note">Note:</label>
                    <select name="note" id="note" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div>
                    <label for="contenu">Contenu:</label>
                    <textarea name="contenu" id="contenu" required></textarea>
                </div>
                <div>
                    <button type="submit">Soumettre</button>
                </div>
            </form>
        <?php endif; ?>
    </section>

    <?php

    //recupere les avis client pour le produit consulte
    $res = $conn->prepare("SELECT * FROM Avis WHERE idProduit = ? ORDER BY dateAvis DESC");
    $res->execute([$idProduit]);

    $nbAvis = 0;

    while ($avis = $res->fetch()) {
        // Récupère le nom du client qui a écrit l'avis
        $req = $conn->prepare("SELECT * FROM Client WHERE idClient = ?");
        $req->execute([$avis['idClient']]);
        $client = $req->fetch();
        $req->closeCursor();

        $displayStyle = $nbAvis < 3 ? 'block' : 'none';

        echo '<section class="evaluation" style="display: ' . $displayStyle . ';" id="avis-' . $nbAvis . '">';
        echo '<div class="notes">
                <button type="button" class="butAvatar" onclick=" "> <img src="images/perso-avatar.png" alt="avatar"> </button>';
        echo '<h3>' . $client['nom'] . " " . $client['prenom'] . '</h3>';
        echo afficherEtoiles($avis['note']);
        echo '<h3>' . $avis['note'] . '/5</h3>
              </div>';
        echo '<div class="eval-perso">';
        echo '<p>' . $avis['contenu'] . '</p>';

        $date1 = $avis['dateAvis'];
        $date = strftime("%d/%m/%Y", strtotime($date1));
        echo '<p class="date-avis">Avis du <strong>' . $date . '</strong></p>';

        //afficher les reponses de l'entreprise
        if (isset($_SESSION['reponses'][$idProduit][$avis['idClient']])) {
            $reponse = $_SESSION['reponses'][$idProduit][$avis['idClient']];
            echo '<div class="reponse">';
            echo '<p>' . $reponse['contenu'] . '</p>';
            $dateReponse = strftime("%d/%m/%Y", strtotime($reponse['date']));
            $prenom = isset($reponse['prenom']) ? $reponse['prenom'] : 'Lutin & Companny';
            $nom = isset($reponse['nom']) ? $reponse['nom'] : 'Lutin & Companny';
            echo '<p class="date-reponse">Réponse du <strong>' . $dateReponse . '</strong> par ' . $prenom . ' ' . $nom . '</p>';
            echo '<form action="descriptionDetailcopie.php?idProduit=' . $idProduit . '&idClientAvis=' . $avis['idClient'] . '" method="post" style="display:inline;">
                    <textarea name="contenuReponse" required>' . $reponse['contenu'] . '</textarea>
                    <button type="submit" name="updateReponse">Modifier</button>
                    <button type="submit" name="deleteReponse">Supprimer</button>
                  </form>';
            echo '</div>';
        } else {
            // Formulaire de réponse pour l'entreprise
            if ($client_role !== null) {
                echo '<form action="descriptionDetailcopie.php?idProduit=' . $idProduit . '&idClientAvis=' . $avis['idClient'] . '" method="post" style="display:inline;">
                        <textarea name="contenuReponse" required></textarea>
                        <button type="submit" name="submitReponse">Répondre</button>
                      </form>';
            }
        }

        // Display delete button for admins
        if ($client_role !== null) {
            echo '<form action="descriptionDetailcopie.php?idProduit=' . $idProduit . '&idClientAvis=' . $avis['idClient'] . '" method="post" style="display:inline;">
                    <button type="submit" name="deleteAvis">Supprimer Avis</button>
                  </form>';
        }

        // Add delete button if the avis belongs to the logged-in user
        if ($avis['idClient'] == $idClient) {
            echo '<form action="descriptionDetail.php?idProduit=' . $idProduit . '" method="post" style="display:inline;">
                    <input type="hidden" name="deleteAvis" value="1">
                    <input type="hidden" name="idProduit" value="' . $idProduit . '">
                    <input type="hidden" name="idClient" value="' . $avis['idClient'] . '">
                    <button type="submit" class="delete-button">Supprimer Avis</button>
                  </form>';
        }

        echo '</div>';
        echo '</section>';

        $nbAvis++;
    }

    echo '<button type="button" class="button-afficherAvis" onclick="plusAvis()">Afficher tout les Avis</button>';

    ?>
</section>

<script>
    var montrerAvis = false;
    function plusAvis() {
        var nbAvis = <?php echo $nbAvis; ?>;
        var button = document.querySelector('.button-afficherAvis');

        if (montrerAvis) {
            for (var i = 3; i < nbAvis; i++) {
                var avisSection = document.getElementById('avis-' + i);
                if (avisSection) {
                    avisSection.style.display = 'none';
                }
            }
            button.textContent = 'Afficher tout les Avis';
        } else {
            for (var i = 3; i < nbAvis; i++) {
                var avisSection = document.getElementById('avis-' + i);
                if (avisSection) {
                    avisSection.style.display = 'block';
                }
            }
            button.textContent = 'Réduire les Avis';
        }

        montrerAvis = !montrerAvis;
    }
    function blocAvis() {
        var form = document.getElementById('avisForm');
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
</script>

<?php
include "footer.php";
?>

</body>
</html>