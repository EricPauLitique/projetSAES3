CREATE TABLE theme(
   theme_id INT AUTO_INCREMENT,
   theme_nom VARCHAR(100) NOT NULL,
   PRIMARY KEY(theme_id)
);

CREATE TABLE adresse(
   adr_id INT AUTO_INCREMENT,
   adr_cp INT NOT NULL,
   adr_ville VARCHAR(50) NOT NULL,
   adr_rue VARCHAR(50) NOT NULL,
   adr_num TINYINT,
   PRIMARY KEY(adr_id)
);


CREATE TABLE notification(
   notif_id INT AUTO_INCREMENT,
   notif_contenu VARCHAR(250),
   PRIMARY KEY(notif_id)
);

CREATE TABLE utilisateur(
   user_id INT AUTO_INCREMENT,
   user_mail VARCHAR(100) NOT NULL,
   user_mdp VARCHAR(50) NOT NULL,
   user_prenom VARCHAR(50) NOT NULL,
   user_nom VARCHAR(50) NOT NULL,
   adr_id INT NOT NULL,
   PRIMARY KEY(user_id),
   UNIQUE(user_mail),
   FOREIGN KEY(adr_id) REFERENCES adresse(adr_id)
);

CREATE TABLE groupe(
    grp_id INT AUTO_INCREMENT,
    grp_nom VARCHAR(255) NOT NULL,
    grp_couleur VARCHAR(50),
    grp_img VARCHAR(255),
    grp_lim_an DECIMAL(10, 2) NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY(grp_id),
    FOREIGN KEY(user_id) REFERENCES utilisateur(user_id)
);




CREATE TABLE proposition(
   prop_id INT AUTO_INCREMENT,
   prop_titre VARCHAR(100) NOT NULL,
   prop_desc VARCHAR(255) NOT NULL,
   prop_date_min DATE,
   user_id INT NOT NULL,
   theme_id INT NOT NULL,
   prop_cout DECIMAL(10, 2),
   PRIMARY KEY(prop_id),
   FOREIGN KEY(user_id) REFERENCES utilisateur(user_id),
   FOREIGN KEY(theme_id) REFERENCES theme(theme_id)
);

CREATE TABLE vote(
   vote_id INT AUTO_INCREMENT,
   vote_type_scrutin VARCHAR(50) NOT NULL,
   vote_duree INT NOT NULL,
   vote_valide BOOLEAN,
   prop_id INT NOT NULL,
   PRIMARY KEY(vote_id),
   FOREIGN KEY(prop_id) REFERENCES proposition(prop_id)
);

CREATE TABLE commentaire(
   com_id INT AUTO_INCREMENT,
   com_txt VARCHAR(250) NOT NULL,
   com_date DATETIME NOT NULL,
   user_id INT NOT NULL,
   prop_id INT NOT NULL,
   PRIMARY KEY(com_id),
   FOREIGN KEY(user_id) REFERENCES utilisateur(user_id),
   FOREIGN KEY(prop_id) REFERENCES proposition(prop_id)
);

CREATE TABLE reaction(
    reac_id INT AUTO_INCREMENT,
    reac_img VARCHAR(250),
    prop_id INT, -- Peut être NULL
    com_id INT, -- Peut être NULL
    user_id INT NOT NULL,
    PRIMARY KEY(reac_id),
    FOREIGN KEY(prop_id) REFERENCES proposition(prop_id),
    FOREIGN KEY(com_id) REFERENCES commentaire(com_id),
    FOREIGN KEY(user_id) REFERENCES utilisateur(user_id),
    -- Vérifie que soit prop_id, soit com_id est non NULL (au moins un des deux)
    CONSTRAINT check_reaction CHECK ((prop_id IS NOT NULL AND com_id IS NULL) OR (com_id IS NOT NULL AND prop_id IS NULL))
);


CREATE TABLE signalement(
   sig_id INT AUTO_INCREMENT,
   sig_nature VARCHAR(100) NOT NULL,
   prop_id INT, -- Peut être NULL
   com_id INT, -- Peut être NULL
   user_id INT NOT NULL,
   PRIMARY KEY(sig_id),
   FOREIGN KEY(prop_id) REFERENCES proposition(prop_id),
   FOREIGN KEY(com_id) REFERENCES commentaire(com_id),
   FOREIGN KEY(user_id) REFERENCES utilisateur(user_id),
   -- Vérifie que soit prop_id, soit com_id est non NULL (au moins un des deux)
   CONSTRAINT check_signalement CHECK ((prop_id IS NOT NULL AND com_id IS NULL) OR (com_id IS NOT NULL AND prop_id IS NULL))
);

CREATE TABLE membre(
   user_id INT,
   grp_id INT,
   coche_reac BOOLEAN,
   coche_new_prop BOOLEAN,
   coche_res_vote BOOLEAN,
   role VARCHAR(50),
   PRIMARY KEY(user_id, grp_id),
   FOREIGN KEY(user_id) REFERENCES utilisateur(user_id),
   FOREIGN KEY(grp_id) REFERENCES groupe(grp_id)
);

CREATE TABLE comporte(
   grp_id INT,
   theme_id INT,
   lim_theme DECIMAL(10, 2) NOT NULL,
   PRIMARY KEY(grp_id, theme_id),
   FOREIGN KEY(grp_id) REFERENCES groupe(grp_id),
   FOREIGN KEY(theme_id) REFERENCES theme(theme_id)
);

CREATE TABLE choixVote(
   user_id INT,
   vote_id INT,
   choix_user INT,
   PRIMARY KEY(user_id, vote_id),
   FOREIGN KEY(user_id) REFERENCES utilisateur(user_id),
   FOREIGN KEY(vote_id) REFERENCES vote(vote_id)
);

CREATE TABLE notifUtilisateur(
   user_id INT,
   notif_id INT,
   PRIMARY KEY(user_id, notif_id),
   FOREIGN KEY(user_id) REFERENCES utilisateur(user_id),
   FOREIGN KEY(notif_id) REFERENCES notification(notif_id)
);


