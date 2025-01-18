DELIMITER $$

CREATE PROCEDURE recapPanier(
    IN idC INT, 
    OUT totalQuantite INT, 
    OUT totalPrix DECIMAL(10,2)
)
BEGIN
    -- Initialisation des variables
    DECLARE totalQuantiteTemp INT DEFAULT 0;
    DECLARE totalPrixTemp DECIMAL(10,2) DEFAULT 0;

    -- Calcul de la quantité totale et du prix total
    SELECT SUM(pc.quantite), SUM(pc.quantite * p.prix)
    INTO totalQuantiteTemp, totalPrixTemp
    FROM Panier_Client pc
    JOIN Produit p ON pc.idProduit = p.idProduit
    WHERE pc.idClient = idC;

    -- Assigner les résultats aux variables de sortie
    SET totalQuantite = totalQuantiteTemp;
    SET totalPrix = totalPrixTemp;

END$$

DELIMITER ;
