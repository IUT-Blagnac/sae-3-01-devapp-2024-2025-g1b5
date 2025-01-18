<?php

    
    include "Connect.inc.php";
    session_start() ;

    if (isset($_SESSION['client_email']) ) {
        $req = $conn->prepare("SELECT * FROM Client WHERE email = ?");
        $req->execute([$_SESSION['client_email']]);
        $client = $req->fetch();
        $req->closeCursor();
        $idClient = $client['idClient'] ;
    }

    //On impose le regex pour les infos bancaires
    $regexCarte = '/^\d{16}$/'; // 16 chiffres
    $regexCVV = '/^\d{3}$/'; // 3 chiffres
    $regexDate = '/^(0[1-9]|1[0-2])\/\d{4}$/'; // format MM/AAAA

    if (isset($_POST['modif'])) {

        
        
        $numCarte = str_replace(" ", "", $_POST['card-number']); 
        
        if ( preg_match($regexCarte, $numCarte) && preg_match($regexDate, $_POST['expiration']) && preg_match($regexCVV, $_POST['cvv'] )  ) {
            
            list($mois, $annee) = explode("/", $_POST['expiration']);
            $dateFormat = "$annee-$mois-01"; // Ajouter le jour "01" par défaut
            
            $query = $conn->prepare("UPDATE CarteBancaire SET numCarte = :numCarte, dateExpiration = :dateExpiration, codeCarte = :codeCarte WHERE idClient = :idClient");
            $query->bindParam(':numCarte', $numCarte);
            $query->bindParam(':dateExpiration', $dateFormat);
            $query->bindParam(':codeCarte', $_POST['cvv']);
            $query->bindParam(':idClient', $idClient);
            $query->execute();
            
            header('Location: commande-choix.php');
        } else {
            $erreurRegex = '<center><h3>Les valeurs sont incorrectes !<br>Veuillez réessayer !</h3></center>';
        }
            
    } elseif (isset($_POST['ajout'])) {

        if (isset($_POST['save-card'])) {

            try {
               
                
                $numCarte = str_replace(" ", "", $_POST['card-number']); 
                
                if ( preg_match($regexCarte, $numCarte) && preg_match($regexDate, $_POST['expiration']) && preg_match($regexCVV, $_POST['cvv'] )  ) {
                    
                    list($mois, $annee) = explode("/", $_POST['expiration']);
                    $dateFormat = "$annee-$mois-01"; // Ajouter le jour "01" par défaut

                    $query = $conn->prepare("INSERT INTO CarteBancaire ( numCarte, dateExpiration, codeCarte, idClient ) VALUES ( ?, ?, ?, ? ) ");
                    $query->execute([ $numCarte, $dateFormat, $_POST['cvv'], $idClient]);
                    header('Location: commande-choix.php');

                } else {
                    $erreurRegex = '<center><h3>Les valeurs sont incorrectes !<br>Veuillez réessayer !</h3></center>';
                }

            } catch (PDOException $e) {
                echo 'Erreur : insertion échouée';
                header('Location: index.php');
            }

        } else {
            
            
            $numCarte = str_replace(" ", "", $_POST['card-number']); 
            
            if ( preg_match($regexCarte, $numCarte) && preg_match($regexDate, $_POST['expiration']) && preg_match($regexCVV, $_POST['cvv'] )  ) {

                list($mois, $annee) = explode("/", $_POST['expiration']);
                $dateFormat = "$annee-$mois-01"; // Ajouter le jour "01" par défaut

                //je crée une session pour garder la carte le temps de cette commabde seulement
                $_SESSION['numCarte'] = $numCarte ;
                $_SESSION['dateE'] = $dateFormat ;
                $_SESSION['cvv'] = $_POST['cvv'] ;
                $_SESSION['titulaire'] = $_POST['titulaire'] ;
                header('Location: commande-choix.php');

            } else {
                $erreurRegex = '<center><h3>Les valeurs sont incorrectes !<br>Veuillez réessayer !</h3></center>';
            }

        }

    }


    include "header.php";


        if (isset($_SESSION['client_email']) ) {

            if (isset($erreurRegex)) { echo $erreurRegex ; }

            if (isset($_POST['carteActuelle'])) {

                echo'
                    <section class="paiement-container">
                        <h2>Renseignez votre carte de paiement</h2>
                                
                        <form method="POST">
                            <div class="input-group">
                                <label for="card-number">Numéro de carte</label>
                                <input type="text" name="card-number" id="card-number" placeholder="1234 5678 9012 3456" required>
                            </div>
    
                                
                            <div class="input-row">
                                <div class="input-group">
                                    <label for="expiration">Expiration</label>
                                    <input type="text" name="expiration" id="expiration" placeholder="MM/AAAA" required>
                                </div>
                                <div class="input-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" name="cvv" id="cvv" placeholder="3 chiffres" required>
                                </div>
                            </div>
    
                            <div class="input-group">
                                <label for="titulaire">Titulaire de la carte</label>
                                <input type="text" name="titulaire" id="titulaire" placeholder="Exemple" required>
                            </div>
    
                            <button type="submit" name="modif" class="submit-btn">Enregistrer</button>
                        </form>
                    </section>
                ';

            } else {

                echo'
                    <section class="paiement-container">
                        <h2>Renseignez votre carte de paiement</h2>
                                
                        <form method="POST">
                            <div class="input-group">
                                <label for="card-number">Numéro de carte</label>
                                <input type="text" name="card-number" id="card-number" placeholder="1234 5678 9012 3456" required>
                            </div>
    
                                
                            <div class="input-row">
                                <div class="input-group">
                                    <label for="expiration">Expiration</label>
                                    <input type="text" name="expiration" id="expiration" placeholder="MM/AAAA" required>
                                </div>
                                <div class="input-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" name="cvv" id="cvv" placeholder="3 chiffres" required>
                                </div>
                            </div>
    
                            <div class="input-group">
                                <label for="titulaire">Titulaire de la carte</label>
                                <input type="text" name="titulaire" id="titulaire" placeholder="Exemple" required>
                            </div>
    
                                
                            <div class="input-group checkbox">
                                <label for="save-card"><strong>Enregistrer ma carte bancaire</strong><br>Pour faciliter mes prochains achats</label>
                                <input type="checkbox" name="save-card" id="save-card" >
                            </div>
    
                            <button type="submit" name="ajout" class="submit-btn">Enregistrer</button>
                        </form>
                    </section>
                ';

            }

            


        } else {
            header('Location: index.php');
        }






    include "footer.php";
?>
</body>
</html>
