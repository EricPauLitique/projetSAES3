
FONCTIONS :



– Création de la fonction nbMembres donc en entrée id du groupe


DELIMITER $$

CREATE FUNCTION nbMembresParGrp (id INT)
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE nbTotal INT;

    SELECT COUNT(*) INTO nbTotal
    FROM groupe g
    INNER JOIN membre m ON m.grp_id = g.grp_id
    INNER JOIN utilisateur u ON u.user_id = m.user_id
    WHERE g.grp_id = id;

    RETURN nbTotal; -- Retourne la valeur de la variable
END$$

DELIMITER ;

– Création de la fonction liste_com_poucehaut retourne la liste des membre qui ont réagi avec un pouce vers le haut à un commentaire donné en entrée

DELIMITER $$

CREATE FUNCTION liste_com_poucehaut(
    p_com_id INT       -- Identifiant du commentaire
)
RETURNS TEXT
DETERMINISTIC
BEGIN
    -- Déclare une variable pour stocker la liste des utilisateurs
    DECLARE user_list TEXT;

    -- Récupérer les noms ou identifiants des utilisateurs qui ont réagi avec 'type_1'
    SELECT GROUP_CONCAT(u.user_nom SEPARATOR ', ')
    INTO user_list
    FROM reaction r
    JOIN utilisateur u ON r.user_id = u.user_id
    WHERE r.com_id = p_com_id AND r.reac_type = 'type_1';

    -- Retourner la liste des utilisateurs
    RETURN IFNULL(user_list, 'Aucun utilisateur trouvé');
END$$

DELIMITER ;

– Création de la fonction liste_prop_poucebas retourne la liste des membre qui ont réagi avec un pouce vers le bas à une proposition donné en entrée.


DELIMITER $$

CREATE FUNCTION liste_prop_poucebas(
    p_prop_id INT       -- Identifiant de la proposition
)
RETURNS TEXT
DETERMINISTIC
BEGIN
    -- Déclare une variable pour stocker la liste des utilisateurs
    DECLARE user_list TEXT;

    -- Récupérer les noms ou identifiants des utilisateurs qui ont réagi avec 'type_0'
    SELECT GROUP_CONCAT(u.user_nom SEPARATOR ', ')
    INTO user_list
    FROM reaction r
    JOIN utilisateur u ON r.user_id = u.user_id
    WHERE r.prop_id = p_prop_id AND r.reac_type = 'type_0';

    -- Retourner la liste des utilisateurs
    RETURN IFNULL(user_list, 'Aucun utilisateur trouvé');
END$$

DELIMITER ;



– Procédure pour mettre à jour le prénom d’un utilisateur s’il le change 

DELIMITER $$

CREATE PROCEDURE change_prenom(
    IN p_user_id INT,          -- Identifiant de l'utilisateur
    IN p_new_firstname VARCHAR(50)  -- Nouveau prénom
)
BEGIN
    -- Vérifier si l'utilisateur existe
    IF EXISTS (SELECT 1 FROM utilisateur WHERE user_id = p_user_id) THEN
        -- Mettre à jour le prénom
        UPDATE utilisateur
        SET user_prenom = p_new_firstname
        WHERE user_id = p_user_id;

        -- Vérifier si la mise à jour a réussi
        IF ROW_COUNT() > 0 THEN
            SELECT CONCAT('Prénom mis à jour avec succès pour l\'utilisateur ID ', p_user_id, '.') AS Result;
        ELSE
            SELECT 'Échec : Le prénom n\'a pas pu être mis à jour.' AS Result;
        END IF;
    ELSE
        -- Si l'utilisateur n'existe pas
        SELECT CONCAT('Échec : Aucun utilisateur trouvé avec l\'ID ', p_user_id, '.') AS Result;
    END IF;
END$$

DELIMITER ;

– En saisissant call change_user_prenom(8,"Jules"), le prénom changera pour l'utilisateur n°8.


– Procédure pour mettre à jour le nom d’un groupe 


DELIMITER $$

CREATE PROCEDURE change_groupe_nom(
    IN p_grp_id INT,             -- Identifiant du groupe
    IN p_new_name VARCHAR(255)   -- Nouveau nom du groupe
)
BEGIN
    -- Vérifier si le groupe existe
    IF EXISTS (SELECT 1 FROM groupe WHERE grp_id = p_grp_id) THEN
        -- Mettre à jour le nom du groupe
        UPDATE groupe
        SET grp_nom = p_new_name
        WHERE grp_id = p_grp_id;

        -- Vérifier si la mise à jour a réussi
        IF ROW_COUNT() > 0 THEN
            SELECT CONCAT('Nom du groupe mis à jour avec succès pour le groupe ID ', p_grp_id, '.') AS Result;
        ELSE
            SELECT 'Échec : Le nom n\'a pas pu être mis à jour.' AS Result;
        END IF;
    ELSE
        -- Si le groupe n'existe pas
        SELECT CONCAT('Échec : Aucun groupe trouvé avec l\'ID ', p_grp_id, '.') AS Result;
    END IF;
END$$

DELIMITER ;

–call change_group_name(3, "APE") va changer le nom du groupe qui a pour id du groupe 3

– Procédure pour mettre à jour la couleur d’un groupe 

DELIMITER $$

CREATE PROCEDURE change_groupe_couleur(
    IN p_grp_id INT,             -- Identifiant du groupe
    IN p_new_color VARCHAR(50)   -- Nouvelle couleur du groupe
)
BEGIN
    -- Vérifier si le groupe existe
    IF EXISTS (SELECT 1 FROM groupe WHERE grp_id = p_grp_id) THEN
        -- Mettre à jour la couleur du groupe
        UPDATE groupe
        SET grp_couleur = p_new_color
        WHERE grp_id = p_grp_id;

        -- Vérifier si la mise à jour a réussi
        IF ROW_COUNT() > 0 THEN
            SELECT CONCAT('Couleur du groupe mise à jour avec succès pour le groupe ID ', p_grp_id, '.') AS Result;
        ELSE
            SELECT 'Échec : La couleur n\'a pas pu être mise à jour.' AS Result;
        END IF;
    ELSE
        -- Si le groupe n'existe pas
        SELECT CONCAT('Échec : Aucun groupe trouvé avec l\'ID ', p_grp_id, '.') AS Result;
    END IF;
END$$

DELIMITER ;
– call change_group_color(2, "rouge") change la couleur du groupe n°2 : Collectif pour l’environnement

– Procédure pour mettre à jour l’image de profil d’un groupe 

DELIMITER $$

CREATE PROCEDURE change_groupe_image(
    IN p_grp_id INT,              -- Identifiant du groupe
    IN p_new_image VARCHAR(255)   -- Nouveau chemin de l'image
)
BEGIN
    -- Vérifier si le groupe existe
    IF EXISTS (SELECT 1 FROM groupe WHERE grp_id = p_grp_id) THEN
        -- Mettre à jour l'image du groupe
        UPDATE groupe
        SET grp_img = p_new_image
        WHERE grp_id = p_grp_id;

        -- Vérifier si la mise à jour a réussi
        IF ROW_COUNT() > 0 THEN
            SELECT CONCAT('Image du groupe mise à jour avec succès pour le groupe ID ', p_grp_id, '.') AS Result;
        ELSE
            SELECT 'Échec : L\'image n\'a pas pu être mise à jour.' AS Result;
        END IF;
    ELSE
        -- Si le groupe n'existe pas
        SELECT CONCAT('Échec : Aucun groupe trouvé avec l\'ID ', p_grp_id, '.') AS Result;
    END IF;
END$$

DELIMITER ;
– call change_group_image(1, "images/public/ASE.png") modifie l’image dans le groupe n°1 : Association sportive d’Evry


– Procédure pour mettre à jour la limite monétaire par an d’un groupe 

DELIMITER $$

CREATE PROCEDURE change_groupe_lim_an(
    IN p_grp_id INT,         -- Identifiant du groupe
    IN p_new_lim_an DECIMAL(10,2)  -- Nouvelle limite annuelle
)
BEGIN
    -- Vérifier si le groupe existe
    IF EXISTS (SELECT 1 FROM groupe WHERE grp_id = p_grp_id) THEN
        -- Mettre à jour la limite annuelle
        UPDATE groupe
        SET grp_lim_an = p_new_lim_an
        WHERE grp_id = p_grp_id;

        -- Vérifier si la mise à jour a réussi
        IF ROW_COUNT() > 0 THEN
            SELECT CONCAT('Limite annuelle mise à jour avec succès pour le groupe ID ', p_grp_id, '.') AS Result;
        ELSE
            SELECT 'Échec : La limite annuelle n\'a pas pu être mise à jour.' AS Result;
        END IF;
    ELSE
        -- Si le groupe n'existe pas
        SELECT CONCAT('Échec : Aucun groupe trouvé avec l\'ID ', p_grp_id, '.') AS Result;
    END IF;
END$$

DELIMITER ;
– call change_group_lim_an(3, 40010.98) modifie le fond monétaire pour le groupe n°3


– Procédure pour mettre à jour la limite monétaire d’un theme par propostion 


DELIMITER $$

CREATE PROCEDURE change_lim_theme(
    IN p_grp_id INT,          -- Identifiant du groupe
    IN p_theme_id INT,        -- Identifiant du thème
    IN p_new_lim_theme DECIMAL(10,2)  -- Nouvelle limite pour le thème
)
BEGIN
    -- Vérifier si la relation entre le groupe et le thème existe
    IF EXISTS (
        SELECT 1 
        FROM comporte 
        WHERE grp_id = p_grp_id AND theme_id = p_theme_id
    ) THEN
        -- Mettre à jour la limite du thème
        UPDATE comporte
        SET lim_theme = p_new_lim_theme
        WHERE grp_id = p_grp_id AND theme_id = p_theme_id;

        -- Vérifier si la mise à jour a réussi
        IF ROW_COUNT() > 0 THEN
            SELECT CONCAT('La limite du thème (ID ', p_theme_id, ') a été mise à jour avec succès pour le groupe ID ', p_grp_id, '.') AS Result;
        ELSE
            SELECT 'Échec : La limite du thème n\'a pas pu être mise à jour.' AS Result;
        END IF;
    ELSE
        -- Si la relation entre le groupe et le thème n'existe pas
        SELECT CONCAT('Échec : Aucun lien trouvé entre le groupe ID ', p_grp_id, ' et le thème ID ', p_theme_id, '.') AS Result;
    END IF;
END$$

DELIMITER ;
– La commande change_lim_theme(3, 3, 200000) va ajuster la limite du fonds monétaire pour le groupe numéro 3 ainsi que celle du thème numéro 3.


– Procédure pour mettre à jour le rôle d’un membre


DELIMITER $$

CREATE PROCEDURE change_role (
    IN p_user_id INT,             -- Identifiant de l'utilisateur
    IN p_new_role VARCHAR(255)    -- Nouveau rôle à attribuer
)
BEGIN
    -- Mettre à jour le rôle dans la table membre
    UPDATE membre
    SET role = p_new_role
    WHERE user_id = p_user_id;

    -- Optionnel : gérer les cas où aucune ligne n'est mise à jour
    IF ROW_COUNT() = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Aucun membre trouvé avec cet ID.';
    END IF;
END$$

DELIMITER ;



– Procedure pour mettre à jour l’adresse d’un utilisateur

DELIMITER $$

CREATE PROCEDURE change_user_adresse(
    p_user_id INT,
    p_new_cp INT,              -- Code postal (int(5))
    p_new_ville VARCHAR(50),   -- Ville (varchar(50))
    p_new_rue VARCHAR(50),     -- Rue (varchar(50))
    p_new_num TINYINT          -- Numéro (tinyint(4))
)
BEGIN
    -- Variable pour stocker le message de retour
    DECLARE result_message VARCHAR(255);

    -- Vérifier si l'utilisateur existe et récupérer son adresse
    IF EXISTS (SELECT 1 FROM utilisateur WHERE user_id = p_user_id) THEN
        -- Mettre à jour l'adresse liée à cet utilisateur
        UPDATE adresse
        SET adr_cp = p_new_cp,
            adr_ville = p_new_ville,
            adr_rue = p_new_rue,
            adr_num = p_new_num
        WHERE adr_id = (SELECT adr_id FROM utilisateur WHERE user_id = p_user_id);

  -- Vérifier si la mise à jour a réussi
        IF ROW_COUNT() > 0 THEN
            SELECT CONCAT(Adresse mis à jour avec succès pour l\'utilisateur ID ', p_user_id, '.') AS Result;
        ELSE
            SELECT 'Échec : L’adresse n\'a pas pu être mis à jour.' AS Result;
        END IF;
    ELSE
        -- Si l'utilisateur n'existe pas
        SELECT CONCAT('Échec : Aucun utilisateur trouvé avec l\'ID ', p_user_id, '.') AS Result;

    END IF;

END$$

DELIMITER ;

– Si on exécute SELECT change_user_addresse(1, 93160, 'Noisy-le-grand', 'place', 9); elle modifiera l’adresse de utilisateur 1 Elle changera l'adresse de l'utilisateur 1 si celle-ci est présente, sinon un message d'erreur sera affiché.




























