

-- Creation du Trigger verif_prop_cout, on vérifie que si la personne veut insérer une valeur, ne doit pas dépasser la limite du fond monétaire et empechera d’y insérer les données :
 
DELIMITER $$

CREATE OR REPLACE TRIGGER verif_prop_cout
BEFORE INSERT ON proposition
FOR EACH ROW
BEGIN
    DECLARE lim_theme DECIMAL(10,2);
    DECLARE grp_lim_an DECIMAL(10,2);

    -- Récupérer la limite du thème
    SELECT lim_theme
    INTO lim_theme
    FROM theme t
    NATURAL JOIN comporte
    WHERE t.theme_id = NEW.theme_id;


    -- Récupérer la limite annuelle du groupe auquel appartient l'utilisateur
    SELECT g.grp_lim_an
    INTO grp_lim_an
    FROM groupe g
    INNER JOIN membre m ON g.grp_id = m.grp_id
    WHERE m.user_id = NEW.user_id
    LIMIT 1;

    -- Vérifier si prop_cout dépasse l'une des limites
    IF NEW.prop_cout > lim_theme OR NEW.prop_cout > grp_lim_an THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le prop_cout dépasse lim_theme ou grp_lim_an. Insertion interdite.';
    END IF;

    -- Vérifier si lim_theme dépasse grp_lim_an
    IF lim_theme > grp_lim_an THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La lim_theme dépasse grp_lim_an. Insertion interdite.';
    END IF;
END$$

DELIMITER ;



-- Pareil, lorsque qu’on modifie les données : 

DELIMITER $$

CREATE OR REPLACE TRIGGER verif_prop_cout_update
BEFORE UPDATE ON proposition
FOR EACH ROW
BEGIN
    DECLARE lim_theme DECIMAL(10,2);
    DECLARE grp_lim_an DECIMAL(10,2);

    -- Récupérer la limite du thème
    SELECT lim_theme
    INTO lim_theme
    FROM theme t
    NATURAL JOIN comporte
    WHERE t.theme_id = NEW.theme_id;

    -- Récupérer la limite annuelle du groupe auquel appartient l'utilisateur
    SELECT g.grp_lim_an
    INTO grp_lim_an
    FROM groupe g
    INNER JOIN membre m ON g.grp_id = m.grp_id
    WHERE m.user_id = NEW.user_id
    LIMIT 1;

    -- Vérifier si prop_cout dépasse l'une des limites
    IF NEW.prop_cout > lim_theme OR NEW.prop_cout > grp_lim_an THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Le prop_cout dépasse lim_theme ou grp_lim_an. Mise à jour interdite.';
    END IF;

    -- Vérifier si lim_theme dépasse grp_lim_an
    IF lim_theme > grp_lim_an THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La lim_theme dépasse grp_lim_an. Mise à jour interdite.';
    END IF;
END$$

DELIMITER ;

-- Création du déclencheur verif_limite_theme_comporte : nous vérifions que si une personne souhaite insérer une valeur, celle-ci ne doit pas excéder la limite du fonds monétaire du groupe, sinon l'insertion des données sera bloquée.
DELIMITER $$

CREATE OR REPLACE TRIGGER verif_limite_theme_comporte_insert
BEFORE INSERT ON comporte
FOR EACH ROW
BEGIN
    DECLARE total_lim_theme DECIMAL(10,2);
    DECLARE grp_lim_an_val DECIMAL(10,2);

    -- Calculer la somme des limites du groupe (avant l'insertion de la nouvelle ligne)
    SELECT SUM(lim_theme) + NEW.lim_theme
    INTO total_lim_theme
    FROM comporte
    WHERE grp_id = NEW.grp_id;

    -- Récupérer la limite annuelle du groupe
    SELECT grp_lim_an
    INTO grp_lim_an_val
    FROM groupe
    WHERE grp_id = NEW.grp_id;

    -- Vérifier si la somme totale dépasse la limite annuelle du groupe
    IF total_lim_theme > grp_lim_an_val THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La somme des lim_theme dépasse grp_lim_an dans la table comporte. Insertion interdite.';
    END IF;
END$$

DELIMITER ;





-- Pareil, lorsque qu’on modifie les données : 

DELIMITER $$

CREATE OR REPLACE TRIGGER verif_limite_theme_comporte_update
BEFORE UPDATE ON comporte
FOR EACH ROW
BEGIN
    DECLARE total_lim_theme DECIMAL(10,2);
    DECLARE grp_lim_an_val DECIMAL(10,2);

    -- Calculer la nouvelle somme des limites du groupe (y compris la mise à jour en cours)
    SELECT SUM(lim_theme) - OLD.lim_theme + NEW.lim_theme
    INTO total_lim_theme
    FROM comporte
    WHERE grp_id = NEW.grp_id;

    -- Récupérer la limite annuelle du groupe
    SELECT grp_lim_an
    INTO grp_lim_an_val
    FROM groupe
    WHERE grp_id = NEW.grp_id;

    -- Vérifier si la somme totale dépasse la limite annuelle du groupe
    IF total_lim_theme > grp_lim_an_val THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La somme des lim_theme dépasse grp_lim_an dans la table comporte. Mise à jour interdite.';
    END IF;
END$$

DELIMITER ;
