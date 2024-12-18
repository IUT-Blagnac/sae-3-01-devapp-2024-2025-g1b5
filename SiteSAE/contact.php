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
                <b>E-mail :</b> contact@lutinco.com<br>
                <b>Téléphone :</b> +1 234 567 890
            </p>

            <h2>Nos horaires</h2>
            <p>Notre équipe est disponible pour répondre à vos questions :</p>
            <ul>
                <li><strong>Lundi à Vendredi :</strong> 9h00 - 18h00</li>
                <li><strong>Samedi :</strong> 10h00 - 14h00</li>
                <li><strong>Dimanche :</strong> Fermé</li>
            </ul>
            <h2>Notre emplacement</h2>
            <p>Vous pouvez également nous rendre visite à notre adresse physique (si vous vous trouvez en Laponie !)</p>
            
            <!-- Carte interactive avec un marqueur pour l'emplacement du Père Noël -->
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2316.727502631433!2d25.8533!3d66.5325!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4685025069e18463%3A0xf8c7b46093f72db8!2sSanta%20Claus%20Village!5e0!3m2!1sen!2sfi!4v1603199435914!5m2!1sen!2sfi" 
                width="600" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>


        </section>
    </main>

    <?php include "footer.php"; // Inclut le pied de page ?>
</body>

</html>
