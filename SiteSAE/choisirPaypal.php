<?php

    
    include "Connect.inc.php";
    session_start() ;

    if (isset($_POST['paypalEmail'])){

        //je crée une session pour garder les infos le temps de cette commande
        $_SESSION['paypalMail'] = $_POST['paypalEmail'] ;
        $_SESSION['paypalMdp'] = $_POST['paypalPassword'] ;

        header('Location: commande-choix.php');
    }

    include "header.php";


        if (isset($_SESSION['client_email']) ) {

            echo '
                <form class="paypal-login" action="choisirPaypal.php" method="POST" style="border: 1px solid #ddd; border-radius: 8px; width: 400px; margin: 50px auto; padding: 20px; background-color: #fff;">
                    <div style="text-align: center;">
                        <img src="images/paypal-logo.jpg" alt="PayPal" style="width: 100px; margin-bottom: 20px;">
                        <h3 style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 10px;">Payez avec PayPal</h3>
                    </div>
                    <div class="form-group">
                        <label for="paypal-email" style="font-size: 14px; font-weight: bold; color: #333;  margin-top: 20px;">Adresse email</label>
                        <input type="email" id="paypal-email" name="paypalEmail" placeholder="Adresse email" style="width: calc(100% - 20px); padding: 10px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px;" required>
                    </div>
                    <div class="form-group">
                        <label for="paypal-password" style="font-size: 14px; font-weight: bold; color: #333; margin-top: 20px;">Mot de passe</label>
                        <input type="password" id="paypal-password" name="paypalPassword" placeholder="Mot de passe" style="width: calc(100% - 20px); padding: 10px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px;" required>
                    </div>
                    <button type="submit" style="width: 100%; background-color: #0070ba; color: white; font-size: 14px; padding: 10px; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s; margin-top: 50px;">Connexion</button>
                </form>

            ';


        } else {
            header('Location: index.php');
        }






    include "footer.php";
?>
</body>
</html>
