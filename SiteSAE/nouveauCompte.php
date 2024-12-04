<?php
include "header.php";
?>

    <main class="d-flex justify-content-center align-items-center" 
      style="height: 150vh; background-color: #f9f9f9; margin-top: 0;">
    <div class="card p-4 shadow" style="width: 350px;">
        <h2 class="text-center mb-4" style="color: #FF1F11;">Créer compte</h2>
        <form>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" placeholder="Entrez votre prénom" required>
            </div>
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" placeholder="Entrez votre nom" required>
            </div>
            <div class="mb-3">
                <label for="date-naissance" class="form-label">Date de naissance</label>
                <input type="date" class="form-control" id="date-naissance" required>
            </div>
            <div class="mb-3">
                <label for="num-telephone" class="form-label">Numéro de téléphone</label>
                <input type="tel" class="form-control" id="num-telephone" placeholder="Entrez votre numéro de téléphone" required>
            </div>
            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <select id="genre" class="form-select" required>
                    <option value="" disabled selected>Choisissez votre genre</option>
                    <option value="homme">Homme</option>
                    <option value="femme">Femme</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" placeholder="Entrez votre email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" placeholder="Saisir votre mot de passe" required>
            </div>
            <div class="mb-3">
                <label for="confirm-password" class="form-label">Confirmez votre mot de passe</label>
                <input type="password" class="form-control" id="confirm-password" placeholder="Ressaisir votre mot de passe" required>
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