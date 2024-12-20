<?php
include "header.php";
include "Connect.inc.php";
include "tableauxProduit.php";

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

// Vérifier si le client a commandé le produit
$commande = false;
if ($idClient > 0 && $idProduit > 0) {
    $query = $conn->prepare("SELECT COUNT(*) FROM Commande WHERE idClient = ?");
    $query->execute([$idClient]);
    $commande = $query->fetchColumn() > 0;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['deleteAvis']) && $_POST['deleteAvis'] == 1) {
        // Handle delete review
        $idProduit = $_POST['idProduit'];
        $idClient = $_POST['idClient'];
        $stmt = $conn->prepare("CALL SupprimerAvisEtMettreAJourNoteGlobale(?, ?)");
        $stmt->execute([$idProduit, $idClient]);

    } 

    elseif (isset($_POST['submitReponse'])) {
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
    } elseif (isset($_POST['deleteAvisAdmin'])) {
        // Handle delete review
        $idProduit = $_GET['idProduit'];
        $idClientAvis = $_GET['idClientAvis'];
        echo'<script>alert("'.$idClientAvis.'")</script>';

        // Delete review from the database
        try {
            $stmt = $conn->prepare("CALL SupprimerAvisEtMettreAJourNoteGlobale(?, ?)");
            $stmt->execute([$idProduit, $idClientAvis]);
        } catch (PDOException $e) {
            echo "". $e->getMessage() ."";
        }
    } else {
        // Handle add review
        if (isset($_POST['idProduit']) && isset($_POST['idClient'])) {
        $idProduit = $_POST['idProduit'];
        $idClient = $_POST['idClient'];
        $note = $_POST['note'];
        $contenu = $_POST['contenu'];
        $dateAvis = date('Y-m-d H:i:s');
        try {
            $stmt = $conn->prepare("CALL AjouterAvisEtMettreAJourNoteGlobale(?, ?, ?, ?)");
            $stmt->execute([$idProduit, $idClient, $contenu, $note]);
        } catch (PDOException $e) {
            echo "". $e->getMessage() ."";
        }
    }
    }
    
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
            <h2> <?php 
            $r=0;
            foreach($produitParPromo as $r1){
                if($r1['idProduit']==$produit['idProduit']){
                    
                    $r=$produit['prix']/(1-$r1['reduction']);
                    $r = number_format($r, 2);
                }
            }
            
            if($r!= 0  ){
                echo '<p class="prix-produit" style="margin:5px 0; color:red; font-size:1em; text-decoration: line-through;">Prix : ' . htmlspecialchars($r) . ' €</p>';
                echo '<center><p class="prix-produit" style="margin:5px 0; color:#007BFF; font-size:0.9em; font-weight: bold;">Promo : ' . htmlspecialchars($produit['prix']) . ' €</p></center>';

            
            }else{
                echo '<p class="prix-produit" style="margin:5px 0; color:#007BFF; font-size:1em;">Prix : ' . htmlspecialchars($produit['prix']) . ' €</p>';
            
            }
             ?> </h2>
        </div>

        <form action="ajouterPanier.php" method="get">
            <input type="text" value="<?php echo $produit['idProduit']; ?>" name="idProduit" hidden>
            <button type="submit" class="button">Ajouter au panier</button>
            <input type="number" id="quantite" value="1" name="quantite" min="1" oninput="ajusterQuantite()">
        </form>
		<?php
		// Récupérer l'ID du client et du produit
		$idClient = isset($client['idClient']) ? $client['idClient'] : 0;
		$idProduit = isset($_GET['idProduit']) ? intval($_GET['idProduit']) : 0;

		$isFavorite = false; // Valeur par défaut

		if ($idClient > 0 && $idProduit > 0) {
			// Vérifier si ce produit est déjà dans les favoris
			$checkFav = $conn->prepare("SELECT * FROM Produit_Favoris WHERE idProduit = ? AND idClient = ?");
			$checkFav->execute([$idProduit, $idClient]);
			$isFavorite = $checkFav->rowCount() > 0; // True si le produit est déjà dans les favoris
		}
		?>

		<button type="button" class="butFavoris" id="favButton">
			<img src="<?php echo $isFavorite ? 'images/petit-coeur-plein.png' : 'images/petit-coeur-rouge.png'; ?>" alt="petit coeur" width="20px" id="favImage">
		</button>


	<script>
	// Fonction pour afficher un message temporaire
	function afficherMessage(message) {
		var msgElement = document.createElement("p");
		msgElement.textContent = message;
		document.getElementById("message-container").appendChild(msgElement);
		setTimeout(function() { msgElement.remove(); }, 2000);
	}

	// Vérifier si l'utilisateur est connecté
	var isUserLoggedIn = <?php echo isset($_SESSION['client_email']) ? 'true' : 'false'; ?>;

	document.getElementById('favButton').addEventListener('click', function() {
		if (!isUserLoggedIn) {
			afficherMessage("Vous devez être connecté pour ajouter aux favoris.");
			return; // Ne pas exécuter le reste du code si l'utilisateur n'est pas connecté
		}

		var img = document.getElementById('favImage');
		var idProduit = <?php echo $idProduit; ?>;
		var idClient = <?php echo $idClient; ?>;

		// Ajout ou retrait du produit dans les favoris
		if (img.src.includes('petit-coeur-rouge.png')) {
			img.src = 'images/petit-coeur-plein.png'; // Le coeur devient plein (favori ajouté)
			var action = 'ajouter';
			afficherMessage("Ajouté aux favoris");
		} else {
			img.src = 'images/petit-coeur-rouge.png'; // Le coeur devient vide (favori retiré)
			var action = 'retirer';
			afficherMessage("Retiré des favoris");
		}

		// Requête AJAX pour ajouter ou retirer du favori
		var xhr = new XMLHttpRequest();
		xhr.open("POST", "favorisAction.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send("idProduit=" + idProduit + "&idClient=" + idClient + "&action=" + action);
	});
	</script>

	<div id="message-container"></div>

 
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
    if ($commande):
        echo '<button type="button" class="button-avis" onclick="blocAvis()">Ajouter un avis</button>';
    endif;
    ?>

    <!-- Ajouter des nouvelles avis Form -->
    <section class="evaluation" id="avisForm" style="display: none;">
        <?php if ($idClient): ?>
            <form action="descriptionDetail.php?idProduit=<?php echo $idProduit; ?>" method="post">
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
                    <br>
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
            $nom = isset($reponse['nom']) ? $reponse['nom'] : 'Xmas';
            echo '<p class="date-reponse">Réponse du <strong>' . $dateReponse . '</strong> par ' . $prenom . ' ' . $nom . '</p>';
            echo '<form action="descriptionDetail.php?idProduit=' . $idProduit . '&idClientAvis=' . $avis['idClient'] . '" method="post" style="display:inline;">
                    <textarea name="contenuReponse" required>' . $reponse['contenu'] . '</textarea>
                    <button type="submit" name="updateReponse">Modifier</button>
                    <button type="submit" name="deleteReponse">Supprimer</button>
                  </form>';
            echo '</div>';
        } else {
            
            // Formulaire de réponse pour l'entreprise
            if ($client_role !== null) {
                echo '<form action="descriptionDetail.php?idProduit=' . $idProduit . '&idClientAvis=' . $avis['idClient'] . '" method="post" style="display:inline;">
                        <textarea name="contenuReponse" required></textarea>
                        <button type="submit" name="submitReponse">Répondre</button>
                      </form>';
            }
        }

        // button de suppression de l'avis pour l'entreprise
        if ($client_role !== null) {
            echo '<form action="descriptionDetail.php?idProduit=' . $idProduit . '&idClientAvis=' . $avis['idClient'] . '" method="post" style="display:inline;">
                    <button type="submit" name="deleteAvisAdmin">Supprimer Avis</button>
                  </form>';
        }

        // Rajout le button de suppression de l'avis pour le client
        if ($avis['idClient'] == $idClient) {
            echo '<form action="descriptionDetail.php?idProduit=' . $idProduit . '" method="post" style="display:inline;">
                    <input type="hidden" name="deleteAvis" value="1">
                    <input type="hidden" name="idProduit" value="' . $idProduit . '">
                    <input type="hidden" name="idClient" value="' . $avis['idClient'] . '">
                    <button type="submit" class="delete-button">Supprimer mon Avis</button>
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