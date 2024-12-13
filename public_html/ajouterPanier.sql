CREATE OR REPLACE PROCEDURE AjouterPanier
    (
        idC     Panier_Client.idClient%TYPE, 
        idP     Panier_Client.idProduit%TYPE,
        qte     Panier_Client.quantite%TYPE
    )
IS
    fk_erreur EXCEPTION;
    PRAGMA EXCEPTION_INIT(fk_erreur, -2291); 
    ck_erreur EXCEPTION;
    PRAGMA EXCEPTION_INIT(ck_erreur, -2290);

    qteDisponible int;
    nbProdPanier int ;
    qteActuellePanier int ;

BEGIN

    IF qte < 1 THEN
        RAISE_APPLICATION_ERROR (-20001, 'Erreur : la quantité est nulle !');
    END IF;

    SELECT quantiteStock INTO qteDisponible FROM Stock WHERE idProduit = idP;

    IF qteDisponible < qte THEN    
        RAISE_APPLICATION_ERROR (-20002, 'Erreur : la quantité du stock est trop faible !');
    END IF;

    -- On regarde si le produit appartient deja au panier
    SELECT COUNT(*) INTO nbProdPanier FROM Panier_Client
    WHERE idProduit = idP AND idClient = idC 

    IF nbProdPanier = 0 THEN 
            -- Insertion dans le panier
            INSERT INTO Panier_Client (idClient, idProduit, quantite)
            VALUES (idC, idP, qte);
            COMMIT; 

        ELSE 

            SELECT quantite INTO qteActuellePanier FROM Panier_Client WHERE idProduit = idP ;

            UPDATE Panier_Client
            SET quantite := qteActuellePanier + qte
            WHERE idProduit = idP;
            COMMIT; 

    END IF;

    
    DBMS_OUTPUT.PUT_LINE('Produit ajouté au panier.');

EXCEPTION
    
    -- Si une erreur de cle etrangere survient (idClient ou idProduit ne correspondant pas a une valeur existante)
    WHEN fk_erreur THEN
        DBMS_OUTPUT.PUT_LINE('ERREUR : Client ou produit invalide !');
    
    -- Si une erreur de contrainte CHECK survient (par exemple, une valeur invalide dans le panier)
    WHEN ck_erreur THEN
        DBMS_OUTPUT.PUT_LINE('ERREUR : Violation de contrainte !');
   

END;
/


--------------------------------------------------------------------------------------


DELIMITER $$

CREATE PROCEDURE AjouterPanier(
    IN idC INT, 
    IN idP INT,
    IN qte INT
)

BEGIN
    DECLARE qteDisponible INT;
    DECLARE nbProdPanier INT;
    DECLARE qteActuellePanier INT;

    IF qte < 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erreur : la quantité est nulle !';
    END IF;

    SELECT quantiteStock INTO qteDisponible 
    FROM Stock 
    WHERE idProduit = idP;

    IF qteDisponible < qte THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erreur : la quantité du stock est trop faible !';
    END IF;

    -- On regarde si le produit existe deja dans le panier
    SELECT COUNT(*) INTO nbProdPanier 
    FROM Panier_Client
    WHERE idProduit = idP AND idClient = idC;

    IF nbProdPanier = 0 THEN
        -- Si le produit n'y est pas, on l'ajoute
        INSERT INTO Panier_Client (idClient, idProduit, quantite)
        VALUES (idC, idP, qte);
    ELSE
        -- Sinon, on met a jour la quantite
        SELECT quantite INTO qteActuellePanier 
        FROM Panier_Client
        WHERE idProduit = idP AND idClient = idC;

        UPDATE Panier_Client
        SET quantite = qteActuellePanier + qte
        WHERE idProduit = idP AND idClient = idC;
    END IF;

    -- Affichage d'un message de confirmation
    SELECT 'Produit ajouté au panier.' AS message;

END$$

DELIMITER ;
