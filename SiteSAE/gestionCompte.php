<?php
	include "testAdmin.php";

	// Suppression d'un client
	if (isset($_POST['delete'])) {
		$idClient = $_POST['delete'];
		$query = $conn->prepare("DELETE FROM Commande WHERE idClient = :idClient");
		$query->bindParam(':idClient', $idClient, PDO::PARAM_INT);
		$query->execute();
		$query = $conn->prepare("DELETE FROM CarteBancaire WHERE idClient = :idClient");
		$query->bindParam(':idClient', $idClient, PDO::PARAM_INT);
		$query->execute();
		$query = $conn->prepare("DELETE FROM Avis WHERE idClient = :idClient");
		$query->bindParam(':idClient', $idClient, PDO::PARAM_INT);
		$query->execute();
		$query = $conn->prepare("DELETE FROM Client WHERE idClient = :idClient");
		$query->bindParam(':idClient', $idClient, PDO::PARAM_INT);
		$query->execute();
		echo "<script>alert('Le compte client a été supprimé avec succès.');</script>";
	}

	// Promouvoir un client au rôle Admin
	if (isset($_POST['promote'])) {
		$idClient = $_POST['promote'];
		$query = $conn->prepare("UPDATE Client SET role = 'A' WHERE idClient = :idClient");
		$query->bindParam(':idClient', $idClient, PDO::PARAM_INT);
		$query->execute();
		echo "<script>alert('L'utilisateur séléctionné est bien passé administrateur.');</script>";
	}

	// Recherche de clients
	$searchNom = $_POST['search_nom'] ?? '';
	$searchPrenom = $_POST['search_prenom'] ?? '';

	// Préparation de la requête SQL
	$query = $conn->prepare("
		SELECT idClient, nom, prenom, email, numTel, role 
		FROM Client
		WHERE (:searchNom = '' OR nom LIKE :searchNomWildcard)
		  AND (:searchPrenom = '' OR prenom LIKE :searchPrenomWildcard)
	");
	$query->bindParam(':searchNom', $searchNom, PDO::PARAM_STR);
	$query->bindValue(':searchNomWildcard', '%' . $searchNom . '%', PDO::PARAM_STR);
	$query->bindParam(':searchPrenom', $searchPrenom, PDO::PARAM_STR);
	$query->bindValue(':searchPrenomWildcard', '%' . $searchPrenom . '%', PDO::PARAM_STR);
	$query->execute();
	$clients = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="img-acceuil">
  <img src="images/guirlandes-accueil.png" alt="Image guirlandes page d'acceuil">
</div>

<section class="supp-compte">
  <div class="formulaire-recherche">
    <form method="POST">
      <label for="search_nom">Nom :</label>
      <input type="text" name="search_nom" id="search_nom" value="<?= htmlspecialchars($searchNom) ?>" placeholder="Rechercher par nom">
      <label for="search_prenom">Prénom :</label>
      <input type="text" name="search_prenom" id="search_prenom" value="<?= htmlspecialchars($searchPrenom) ?>" placeholder="Rechercher par prénom">
      <button type="submit">Rechercher</button>
    </form>
  </div>

  <div class="tableau-clients">
    <table border="1" cellpadding="10" cellspacing="0">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Email</th>
          <th>Téléphone</th>
          <th>Rôle</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($clients) > 0): ?>
          <?php foreach ($clients as $client): ?>
            <tr>
              <td><?= htmlspecialchars($client['idClient']) ?></td>
              <td><?= htmlspecialchars($client['nom']) ?></td>
              <td><?= htmlspecialchars($client['prenom']) ?></td>
              <td><?= htmlspecialchars($client['email']) ?></td>
              <td><?= htmlspecialchars($client['numTel']) ?></td>
              <td><?= htmlspecialchars($client['role']) ?></td>
              <td>
                <!-- Bouton pour supprimer -->
                <form method="POST" style="display:inline;">
                  <button type="submit" name="delete" value="<?= htmlspecialchars($client['idClient']) ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?');">Supprimer</button>
                </form>
                
                <!-- Bouton pour promouvoir en admin -->
                <?php if ($client['role'] !== 'A'): ?>
                  <form method="POST" style="display:inline;">
                    <button type="submit" name="promote" value="<?= htmlspecialchars($client['idClient']) ?>" onclick="return confirm('Êtes-vous sûr de vouloir ajouter cette administrateur ?');">Promouvoir en Admin</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7">Aucun client trouvé.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</section>

<?php
include "footer.php";
?>
</body>
</html>
