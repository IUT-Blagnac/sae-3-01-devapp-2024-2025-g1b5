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
            <img src="images/coeur.jpg" alt="Favoris"> 
            <a href="panier.php"><img src="images/cart.jpg" alt="Cart"></a>
            <a href="connexionCompte.php"><img src="images/user.jpg" alt="User"></a>
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
                <a href="#">3 ans et +</a>
                <a href="#">5 ans et +</a>
                <a href="#">7 ans et +</a>
                <a href="#">10 ans et +</a>
            </div> 
        </div>

        <div class="menu-taille">
            <button class="deroulant">Taille</button> 
            <div class="liste-deroulant">
                <a href="#">-</a>
            </div>
        </div>

        <div class="menu-type">
            <button class="deroulant">Type</button> 
            <div class="liste-deroulant">
                <a href="#">Educatif</a>
                <a href="#">Scientifique</a>
                <a href="#">Decouverte</a>
                <a href="#">Figurines</a>
                <a href="#">Exterieur</a>
                <a href="#">Musicale</a>
                <a href="#">Construction</a>
                <a href="#">Eveil</a>
                <a href="#">Guerre</a>
            </div>
        </div>

        <div class="menu-promo">
            <button class="deroulant">Promotions</button> 
        </div>

        <div class="menu-meilleur-ventes">
            <button class="deroulant">Nos meilleures ventes</button> 
        </div>

    </section> 

</header>