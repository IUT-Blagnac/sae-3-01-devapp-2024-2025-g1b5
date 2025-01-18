<?php
header('Content-Type: text/html; charset=utf-8');
include "header.php"; 
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous - Lutin & Co.</title>
    <link rel="stylesheet" href="styles.css"> <!-- Assurez-vous que ce fichier CSS existe -->
</head>

<body class="background-page">
    <main>
        <!-- Section "Contactez-nous" -->
        <section class="pageInfo">
            <h1>Contactez-nous</h1>
            <p>
                Vous avez une question, un commentaire ou besoin d'aide ? Nous serions ravis de vous aider ! Remplissez simplement le formulaire ci-dessous et notre équipe de lutins vous répondra dans les plus brefs délais.
            </p>

            <h2>Informations de contact</h2>
            <p>
                <b>Adresse :</b><br>
                Lutin & Co., Pôle Nord, 1234, Route du Sapin, 00000 Magie de Noël, Monde entier
            </p>
            <p>
                <b>E-mail :</b> lutin.comp@gmail.com<br>
                <b>Téléphone :</b> +36 30 36 30
            </p>

            <h2>Nos horaires</h2>
            <p>Notre équipe est disponible pour répondre à vos questions :</p>
            <ul>
                <li><strong>Lundi à Vendredi :</strong> 9h00 - 18h00</li>
                <li><strong>Samedi :</strong> 10h00 - 14h00</li>
                <li><strong>Dimanche :</strong> Fermé</li>
            </ul>

            <h2>Notre emplacement</h2>
            <p>Vous pouvez également nous rendre visite à notre adresse physique (si vous vous trouvez au Pôle Nord !)</p>
            <!-- Carte interactive (par exemple, Google Maps) -->
            <iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </section>
    </main>

    <?php include "footer.php"; // Inclut le pied de page ?>
</body>

</html>
