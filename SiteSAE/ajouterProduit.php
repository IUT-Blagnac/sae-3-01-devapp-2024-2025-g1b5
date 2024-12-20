<?php
	header("Pragma: no-cache");
	ob_start(); // Commence le tampon de sortie

	include "testAdmin.php";

	// Récupération des catégories
	$query = $conn->prepare("SELECT idCategorie, nomCategorie FROM Categorie");
	$query->execute();
	$categories = $query->fetchAll(PDO::FETCH_ASSOC);

	// Récupérer dernier ID produit
	$query = $conn->prepare("SELECT MAX(idProduit) FROM Produit");
	$query->execute();
	$maxID = $query->fetchColumn();
	$idProduit = $maxID + 1;

	// Traitement de la soumission du formulaire
	if (isset($_POST['add'])) {
		try {
			$conn->beginTransaction(); // Démarre une transaction

			// Récupération des données du formulaire
			$nomProduit = $_POST['nomProduit'];
			$prix = $_POST['prix'];
			$age = $_POST['age'];
			$taille = $_POST['taille'];
			$nbJoueurMax = $_POST['nbJoueurMax'];
			$description = $_POST['description'];
			$noteGlobale = $_POST['noteGlobale'];
			$idCategorie = $_POST['idCategorie'];
			$quantiteStock = $_POST['quantiteStock'];

			// Validation des données
			if (!is_numeric($prix) || $prix <= 0) {
				throw new Exception("Le prix doit être un nombre positif.");
			}
			if (!is_numeric($age) || $age < 0) {
				throw new Exception("L'âge doit être un nombre valide.");
			}
			if (!is_numeric($nbJoueurMax) || $nbJoueurMax <= 0) {
				throw new Exception("Le nombre de joueurs max doit être un nombre positif.");
			}
			if (!is_numeric($quantiteStock) || $quantiteStock < 0) {
				throw new Exception("La quantité doit être un nombre non négatif.");
			}

			// Insertion du produit dans la base de données
			$query = $conn->prepare("INSERT INTO Produit 
				(idProduit, nomProduit, prix, age, taille, nbJoueurMax, description, noteGlobale, idCategorie)
				VALUES (:idProduit, :nomProduit, :prix, :age, :taille, :nbJoueurMax, :description, :noteGlobale, :idCategorie)");
			
			$query->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);
			$query->bindParam(':nomProduit', $nomProduit, PDO::PARAM_STR);
			$query->bindParam(':prix', $prix, PDO::PARAM_STR);
			$query->bindParam(':age', $age, PDO::PARAM_INT);
			$query->bindParam(':taille', $taille, PDO::PARAM_STR);
			$query->bindParam(':nbJoueurMax', $nbJoueurMax, PDO::PARAM_INT);
			$query->bindParam(':description', $description, PDO::PARAM_STR);
			$query->bindParam(':noteGlobale', $noteGlobale, PDO::PARAM_STR);
			$query->bindParam(':idCategorie', $idCategorie, PDO::PARAM_INT);
			$query->execute();

			// Ajout de la quantité dans la table Stock
			$queryStock = $conn->prepare("INSERT INTO Stock (idProduit, quantiteStock) VALUES (:idProduit, :quantiteStock)");
			$queryStock->bindParam(':idProduit', $idProduit, PDO::PARAM_INT);
			$queryStock->bindParam(':quantiteStock', $quantiteStock, PDO::PARAM_INT);
			$queryStock->execute();

			// Gestion de l'image
			if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
				$tmpName = $_FILES['file']['tmp_name'];
				$newName = "Prod{$idProduit}.jpg";
				$imagePath = './image_Produit/' . $newName;

				$fileType = mime_content_type($tmpName);
				if (strpos($fileType, 'image') === false) {
					throw new Exception("Le fichier uploadé n'est pas une image valide.");
				} elseif (!move_uploaded_file($tmpName, $imagePath)) {
					throw new Exception("Erreur lors du déplacement du fichier image.");
				}
			} else {
				$imagePath = null;
				if (isset($_FILES['file']['error']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
					throw new Exception("Erreur lors de l'upload de l'image : " . $_FILES['file']['error']);
				}
			}
			$query = $conn->prepare('CALL InsertDerniersProduits();');
			$query->execute();

			$conn->commit(); // Valide la transaction
			header('Location: gestionProduit.php?success=add');
			exit;
		} catch (Exception $e) {
			$conn->rollBack(); // Annule la transaction en cas d'erreur
			echo "<p class='text-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
		}
	}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un produit</title>
</head>
<body>
    <div class="img-acceuil">
        <img src="images/guirlandes-accueil.png" alt="Image guirlandes page d'acceuil">
    </div>

    <section class="modifier-produit">
        <h1>Ajouter un nouveau produit</h1>

        <!-- Formulaire d'ajout -->
        <form method="POST" enctype="multipart/form-data">
		
			<label for="idProduit">ID du produit</label>
            <input type="text" id="idProduit" name="idProduit" value="<?php echo $idProduit ?>" readonly>
            <br>
			
            <label for="nomProduit">Nom du produit</label>
            <input type="text" id="nomProduit" name="nomProduit" required>
            <br>

            <label for="prix">Prix</label>
            <input type="text" id="prix" name="prix" required>
            <br>

            <label for="age">Âge</label>
            <input type="number" id="age" name="age" required>
            <br>

            <label for="taille">Taille</label>
            <input type="text" id="taille" name="taille" required>
            <br>

            <label for="nbJoueurMax">Nombre de joueurs max</label>
            <input type="number" id="nbJoueurMax" name="nbJoueurMax" required>
            <br>

            <label for="description">Description</label>
            <textarea id="description" name="description" required></textarea>
            <br>

            <label for="noteGlobale">Note globale</label>
            <input type="text" id="noteGlobale" name="noteGlobale" readonly value="0">
            <br>

            <label for="quantiteStock">Quantité en stock</label>
            <input type="number" id="quantiteStock" name="quantiteStock" required>
            <br>

            <label for="idCategorie">Catégorie</label>
            <select name="idCategorie" id="idCategorie">
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?= htmlspecialchars($categorie['idCategorie']) ?>">
                        <?= htmlspecialchars($categorie['nomCategorie']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <br>

			<label for="file">Choisir une image au format jpg:</label>
			<input type="file" name="file">
			<br>
			
            <button type="submit" name="add">Ajouter le produit</button>
        </form>
    </section>

<?php include "footer.php"; ?>

</body>
</html>

<?php
ob_end_flush(); // Envoie la sortie tamponnée au navigateur
?>
