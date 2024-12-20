<?php 

    include "header.php";
    include "Connect.inc.php";

    // Récupérer le staut de la commande (reussi ou non)
    $statut = isset($_GET['statut']) ? $_GET['statut'] : 0;
    
    //si la commande n'est pas passée
    if($statut == 0){
        header('Location: index.php');
        exit();
    }
?>

    <div class="confirmation-container">
        <div class="confirmation-box">
            <img src="https://cdn-icons-png.flaticon.com/512/148/148767.png" alt="Success Icon" class="success-icon">
            <h1>Commande Confirmée !</h1>
            <p>Merci pour votre achat. Votre commande a été passée avec succès !</p>
            <p>Nous vous enverrons un e-mail avec les détails de votre commande sous peu.</p>
            <a href="index.php" class="btn">Retour à l'accueil</a>
        </div>
    </div>



<?php
    include "footer.php";
?>
</body>
</html>