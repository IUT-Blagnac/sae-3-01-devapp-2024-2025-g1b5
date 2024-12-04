<?php
include "header.php";

$email_value = isset($_COOKIE['ClientEmail']) ? htmlspecialchars($_COOKIE['ClientEmail']) : '';
?>

<main class="d-flex justify-content-center align-items-center" style="height: 80vh; background-color: #f9f9f9;">
    <div class="card p-4 shadow" style="width: 350px;">
        <h2 class="text-center mb-4" style="color: #FF1F11;">Connexion</h2>
        <?php
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger" role="alert">' . htmlspecialchars($_GET['error']) . '</div>';
        }
        ?>
        <form method="post" action="TraitConnexion.php">
            <div class="mb-3">
                <label for="email" class="form-label">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre email" value="<?php echo $email_value; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Entrez votre mot de passe" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember" value="remember">
                <label class="form-check-label" for="remember">Se souvenir de moi</label>
            </div>
            <button type="submit" class="btn btn-danger w-100" style="background-color: #FF1F11;">Se connecter</button>
            <div class="mt-3 text-center">
                <a href="nouveauCompte.php" class="text-muted" style="font-size: 14px;">Créer un nouveau compte</a>
            </div>
        </form>
    </div>
</main>

<?php
include "footer.php";
?>
</body>

</html>