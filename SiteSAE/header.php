<?php
$test="";
    if (session_status() === PHP_SESSION_NONE) {
		session_start();
	}
    include("gestioncategorie.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil Lutin & Co.</title>
    <link rel="stylesheet" href="style1.css">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header> 
    <div class="header">
        <a href="index.php"><img src="images/logo-entreprise.png" alt="Logo"></a> 

        <div class="search-bar">
            <input type="text" placeholder="Recherche...">
            <img src="images/loupe.png" alt="Search"> 
        </div>
        
        <div class="icons">
			<div>
			<?php
				if (isset($_SESSION['client_email']) || isset($_COOKIE['CidClient'])) {
					    $email = isset($_SESSION['client_email']) ? $_SESSION['client_email'] : $_COOKIE['CidClient'];
						$query = $conn->prepare("SELECT role FROM Client WHERE email = :email");
						$query->bindParam(':email', $email);
						$query->execute();
						$result = $query->fetch(PDO::FETCH_ASSOC);
					if (isset($result['role']) || $result['role'] !== null) {
						echo "<a href='menuAdmin.php'><button class='espaceAdmin'>Espace Admin</button></a>";
					}
				}		
			?>
			</div>
            <img src="images/coeur.jpg" alt="Favoris"> 
            <a href="panier.php"><img src="images/cart.jpg" alt="Cart"></a>
            <?php
            if (isset($_SESSION['client_email']) || isset($_COOKIE['CidClient'])) {
                echo '<a href="detailCompte.php"><img src="images/user.jpg" alt="User"></a>';
            } else {
                echo '<a href="connexionCompte.php"><img src="images/user.jpg" alt="User"></a>';
            }
            ?>
            <div class="langue">
                <span>Langue :</span>
                <img src="images/france.png" alt="Langue"> 
            </div>
        </div>
    </div>
    
    <section class="menu">

        <div class="menu-age">
            <button class="deroulant">Âge</button> 
            <div class="liste-deroulant">
            <a href="ListeProduit.php?age=0" >Tout âge</a>

                <a href="ListeProduit.php?age=3">3 ans et +</a>
                <a href="ListeProduit.php?age=5">5 ans et +</a>
                <a href="ListeProduit.php?age=8">8 ans et +</a>
                <a href="ListeProduit.php?age=12">12 ans et +</a>
            </div> 
        </div>

        <div class="menu-type">
            <button class="deroulant">Type</button> 
            <div class="liste-deroulant">
            <?php
            separateur($categorie, $scategorie);
            ?>
                

            </div>
        </div>

        <div class="menu-promo">
            <a href="ListeProduit.php?promo=1" style="text-decoration: none;">Promotions</a>
        </div>

        <div class="menu-meilleur-ventes">
            <a href="ListeProduit.php?bestsell=1"style="text-decoration: none;">Nos Meilleures ventes</a>
        </div>

    </section> 

</header>