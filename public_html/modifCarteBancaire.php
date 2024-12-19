<?php

    
    include "Connect.inc.php";
    session_start() ;

    if (isset($_POST['modif'])) {

        $query = $conn->prepare("UPDATE Adresse SET codePostal = :codePostal, ville = :ville, rue = :rue, pays = :pays WHERE idAdresse = :idAdresse");
        $query->bindParam(':codePostal', $_POST['codePostal']);
        $query->bindParam(':ville', $_POST['ville']);
        $query->bindParam(':rue', $_POST['rue']);
        $query->bindParam(':pays', $_POST['pays']);
        $query->bindParam(':idAdresse', $_POST['idAdresse']);
        $query->execute();

        header('Location: commande-choix.php');
        
    } elseif (isset($_POST['ajouter'])) {

        $req = $conn->prepare("SELECT * FROM Client WHERE email = ?");
        $req->execute([$_SESSION['client_email']]);
        $client = $req->fetch();
        $req->closeCursor();

        $idClient = $client['idClient'] ;

        $query = $conn->prepare("INSERT INTO Adresse (codePostal, ville, rue, pays) VALUES (:codePostal, :ville, :rue, :pays)");
        $query->bindParam(':codePostal', $_POST['codePostal']);
        $query->bindParam(':ville', $_POST['ville']);
        $query->bindParam(':rue', $_POST['rue']);
        $query->bindParam(':pays', $_POST['pays']);
        $query->execute();

        $idAdresse = $conn->lastInsertId();

        // Mettre à jour l'ID de l'adresse dans la table Client
        $query = $conn->prepare("UPDATE Client SET idAdresse = :idAdresse WHERE idClient = :client_id");
        $query->bindParam(':idAdresse', $idAdresse);
        $query->bindParam(':client_id', $idClient);
        $query->execute();

        header('Location: commande-choix.php');
    }


    include "header.php";


        if (isset($_SESSION['client_email']) ) {

            if (isset($_POST['adresseActuelle'])) {

                $adre = $conn->prepare("SELECT * FROM Adresse WHERE idAdresse = ?");
                $adre->execute([$_POST['adresseActuelle']]);
                $adresseClient = $adre -> fetch();
                $adre->closeCursor();

                $codePostal = $adresseClient['codePostal'];
                $ville = $adresseClient['ville'];
                $rue = $adresseClient['rue'];
                $pays = $adresseClient['pays'];

                echo ' 
                    <section class="changement-adresse" >
                        <form method="POST">
                            <label for="rue">Rue :</label>
                            <input type="text" name="rue" value="'. $rue . '">

                            <label for="ville">Ville :</label>
                            <input type="text" name="ville" value="'. $ville . '">

                            <label for="codePostal">Code Postal :</label>
                            <input type="text" name="codePostal" value="'. $codePostal . '">

                            <label for="pays">Pays :</label>
                            <input type="text" name="pays" value="'. $pays . '">

                            <input type="text" name="idAdresse" value="'. $_POST['adresseActuelle'] . '" hidden>
                            <button type="submit" name="modif" >Modifier cette adresse</button>
                        </form>
                    </section>
                ';

            } else {

                echo '
                    <section class="changement-adresse" >
                        <form method="POST">
                            <label for="rue">Rue :</label>
                            <input type="text" name="rue">

                            <label for="ville">Ville :</label>
                            <input type="text" name="ville">

                            <label for="codePostal">Code Postal :</label>
                            <input type="text" name="codePostal">

                            <label for="pays">Pays :</label>
                            <input type="text" name="pays">

                            <button type="submit" name="ajouter" >Modifier cette adresse</button>
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
