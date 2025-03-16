-- Insertion de données dans la table theme
INSERT INTO theme (theme_id, theme_nom) VALUES
(1, 'Santé'),
(2, 'Environnement'),
(3, 'Education');

-- Insertion de données dans la table adresse
INSERT INTO adresse (adr_id, adr_cp, adr_ville, adr_rue, adr_num) VALUES
(1, 91400, 'Orsay', 'rue Archangé', 11),
(2, 75015, 'Paris', 'rue Lecourbe', 22),
(3, 69001, 'Lyon', 'rue de la République', 45),
(4, 33000, 'Bordeaux', 'rue Sainte-Catherine', 100);

-- Insertion de données dans la table notification
INSERT INTO notification (notif_id, notif_contenu) VALUES
(1, 'Vous avez: 1 réaction à votre proposition ainsi qu’une invitation à rejoindre un groupe !'),
(2, 'Nouveau commentaire sur votre proposition'),
(3, 'Nouveau membre ajouté au groupe');

-- Insertion de données dans la table utilisateur
INSERT INTO utilisateur (user_id, user_mail, user_mdp, user_prenom, user_nom, adr_id) VALUES
(1, 'chris54@gmail.com', 'jiedjif95!995', 'Christophe', 'Carresta', 1),
(2, 'julie.dupont@outlook.fr', 'mdpJuillet2023!', 'Julie', 'Dupont', 2),
(3, 'marie.martin@gmail.com', 'Motdepasse!23', 'Marie', 'Martin', 3),
(4, 'pierre.durand@free.fr', 'PierreD2023*', 'Pierre', 'Durand', 4);

-- Insertion de données dans la table groupe
INSERT INTO groupe (grp_id, grp_nom, grp_couleur, grp_img, grp_lim_an, user_id) VALUES
(1, 'Association sportive d’Evry', 'vert émeraude', 'images/public/logo.png', 100000.00, 1),
(2, 'Collectif pour l’environnement', 'bleu marine', 'images/public/environnement.png', 50000.00, 2),
(3, 'Association des parents d’élèves', 'rouge bordeaux', 'images/public/ape.png', 30000.00, 3);

-- Insertion de données dans la table proposition
INSERT INTO proposition (prop_id, prop_titre, prop_desc, prop_date_min, user_id, theme_id, prop_cout) VALUES
(1, 'Création d’un terrain de foot', 'On souhaite construire un terrain de foot de 90 m de longueur et 45 m de largeur. Il serait construit à ville nouvelle et serait accessible par tous gratuitement.', '2024-11-21', 2, 1, 130000.00),
(2, 'Installation de poubelles de tri', 'Installation de poubelles de tri dans tous les espaces publics de la ville.', '2024-12-05', 3, 2, 12500.00),
(3, 'Organiser des cours de soutien gratuits', 'Proposer des cours de soutien gratuits dans .les quartiers défavorisés.', '2024-11-15', 4, 3, 25000.00);

-- Insertion de données dans la table vote
INSERT INTO vote (vote_id, vote_type_scrutin, vote_duree, vote_valide, prop_id) VALUES
(1, 'pour/contre', 72, TRUE, 1),
(2, 'jugement majoritaire', 120, TRUE, 2),
(3, 'majorité simple', 48, FALSE, 3);

-- Insertion de données dans la table commentaire
INSERT INTO commentaire (com_id, com_txt, com_date, user_id, prop_id) VALUES
(1, 'Trop bien, moi je suis pour :)', '2024-10-25 17:32:41', 1, 1),
(2, 'Je pense que c’est une bonne idée.', '2024-10-26 08:45:20', 3, 2),
(3, 'Cela pourrait vraiment aider les enfants.', '2024-10-27 14:10:05', 4, 3);


-- Insertion de données dans la table reaction
INSERT INTO reaction (reac_id, reac_type, reac_img, prop_id, com_id, user_id) VALUES
(1, TRUE, 'images/public/poucehaut.png', 1, NULL, 1),
(2, FALSE, 'images/public/poucebas.png', 2, NULL, 3),
(3, TRUE, 'images/public/poucehaut.png', NULL, 1, 4);

-- Insertion de données dans la table comporte
INSERT INTO comporte (grp_id, theme_id, lim_theme) VALUES
(1, 1, 14000.00),
(2, 2, 10000.00),
(3, 3, 15000.00);

-- Insertion de données dans la table choixVote
INSERT INTO choixVote (user_id, vote_id, choix_user) VALUES
(1, 1, TRUE),
(2, 1, FALSE),
(3, 2, TRUE),
(4, 3, TRUE);

-- Insertion de données dans la table notifUtilisateur
INSERT INTO notifUtilisateur (user_id, notif_id) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 1);

-- Ajout d'autres utilisateurs (membres) avec des adresses différentes
INSERT INTO utilisateur (user_id, user_mail, user_mdp, user_prenom, user_nom, adr_id) VALUES
(5, 'emma.bouvier@outlook.fr', 'EmB@vier!92', 'Emma', 'Bouvier', 1),
(6, 'lucas.robert@gmail.com', 'LucasR*123', 'Lucas', 'Robert', 2),
(7, 'lea.moreau@free.fr', 'LMoreau!2024', 'Léa', 'Moreau', 3),
(8, 'nicolas.fabre@yahoo.fr', 'NicoFab89', 'Nicolas', 'Fabre', 4),
(9, 'juliette.martin@laposte.net', 'Juliette123#', 'Juliette', 'Martin', 2),
(10, 'sophie.laroche@gmail.com', 'SophieRoche95', 'Sophie', 'Laroche', 3);

-- Ajout des membres dans différents groupes
INSERT INTO membre (user_id, grp_id, coche_reac, coche_new_prop, coche_res_vote, role) VALUES
(1, 1, TRUE, TRUE, FALSE, 'Créateur'),
(2, 1, FALSE, TRUE, TRUE, 'Membre'),
(3, 2, TRUE, FALSE, FALSE, 'Modérateur'),
(4, 3, TRUE, TRUE, TRUE, 'Membre'),
(5, 1, TRUE, TRUE, TRUE, 'Membre'),
(6, 2, TRUE, FALSE, TRUE, 'Membre'),
(7, 3, TRUE, TRUE, TRUE, 'Membre'),
(8, 2, FALSE, TRUE, FALSE, 'Membre'),
(9, 1, TRUE, TRUE, TRUE, 'Membre'),
(10, 3, TRUE, FALSE, TRUE, 'Membre');

-- Ajout de nouveaux commentaires pour chaque membre
INSERT INTO commentaire (com_id, com_txt, com_date, user_id, prop_id) VALUES
(4, 'Je soutiens à 100% cette initiative.', '2024-10-28 09:12:34', 5, 1),
(5, 'Il est temps que notre ville investisse dans le sport.', '2024-10-28 15:45:02', 6, 1),
(6, 'Une excellente idée pour améliorer la qualité de vie.', '2024-10-29 10:22:11', 7, 2),
(7, 'Cela pourrait vraiment motiver les jeunes.', '2024-10-29 12:38:45', 8, 3),
(8, 'Je suis un peu sceptique sur l’utilité.', '2024-10-30 08:50:32', 9, 2),
(9, 'Je pense que cela vaut la peine d’essayer.', '2024-10-30 13:10:54', 10, 3);

-- Ajout de réactions pour chaque membre
INSERT INTO reaction (reac_id, reac_type, reac_img, prop_id, com_id, user_id) VALUES
(4, TRUE, 'images/public/poucehaut.png', 1, NULL, 5),
(5, FALSE, 'images/public/poucebas.png', 2, NULL, 6),
(6, TRUE, 'images/public/poucehaut.png', NULL, 4, 7),
(7, TRUE, 'images/public/poucehaut.png', NULL, 5, 8),
(8, TRUE, 'images/public/poucehaut.png', 3, NULL, 9),
(9, FALSE, 'images/public/poucebas.png', NULL, 6, 10);

-- Ajout de nouvelles propositions
INSERT INTO proposition (prop_id, prop_titre, prop_desc, prop_date_min, user_id, theme_id, prop_cout) VALUES
(4, 'Amélioration du parc municipal', 'Nous proposons d’ajouter des bancs et des poubelles dans le parc principal de la ville.', '2024-12-10', 5, 2, 10000.00),
(5, 'Création d’une piste cyclable', 'Une piste cyclable serait construite le long de l’avenue principale pour sécuriser les déplacements à vélo.', '2024-11-18', 6, 2, 200000.00),
(6, 'Installation de bornes de recharge pour voitures électriques', 'Ajouter des bornes de recharge pour voitures électriques dans les parkings publics.', '2024-11-25', 7, 1, 75000.00);

-- Ajout de nouveaux commentaires pour les nouvelles propositions
INSERT INTO commentaire (com_id, com_txt, com_date, user_id, prop_id) VALUES
(10, 'Bonne idée, cela manque cruellement dans notre ville.', '2024-10-31 15:20:12', 8, 4),
(11, 'Cela encouragera l’utilisation des vélos en ville.', '2024-10-31 18:34:47', 9, 5),
(12, 'Indispensable pour une ville moderne.', '2024-11-01 09:15:22', 10, 6),
(13, 'Caca', '2024-11-07 19:32:45',2,2);

-- Ajout de nouvelles réactions pour les nouveaux commentaires
INSERT INTO reaction (reac_id, reac_type, reac_img, prop_id, com_id, user_id) VALUES
(10, TRUE, 'images/public/poucehaut.png', 4, NULL, 8),
(11, TRUE, 'images/public/poucehaut.png', NULL, 10, 9),
(12, FALSE, 'images/public/poucebas.png', 5, NULL, 10);



-- Ajout d’un signalement 
INSERT INTO signalement (sig_nature, prop_id, com_id, user_id)
VALUES (1,'inapproprié', NULL, 13, 2);


