-- Après avoir fait des requêtes exemplaire, il pourrait mieux de créer des tables de vue pour les raccourcirs

-- La table de vuePropositionsUtilisateurs affiche la liste des utilisateurs qui ont proposé les sujets du sondage :
create view vuePropositionsUtilisateurs as
select user_id as id, user_prenom as prenom, user_nom as nom, prop_titre as titre, prop_desc as description
from proposition
natural join utilisateur;


-- On va créer plusieurs table de vue pour afficher la liste des membres par groupe : 
-- Table vue ASmembresEvry (1)

create view ASmembresEvry as
select m.user_id, user_prenom as prenom, user_nom as nom, role, grp_nom as groupe
from groupe g
inner join membre m on (m.grp_id = g.grp_id)
inner join utilisateur u on (u.user_id = m.user_id)
where g.grp_id = 1

-- Table vue membresCollectifEnvironnement (2)
create view membresCollectifEnvironnement as
select m.user_id, user_prenom as prenom, user_nom as nom, role, grp_nom as groupe
from groupe g
inner join membre m on (m.grp_id = g.grp_id)
inner join utilisateur u on (u.user_id = m.user_id)
where g.grp_id = 2;

-- Table vue membresAssoParentsEleve (3)
create view membresAssoParentsEleve as
select m.user_id, user_prenom as prenom, user_nom as nom, role, grp_nom as groupe
from groupe g
inner join membre m on (m.grp_id = g.grp_id)
inner join utilisateur u on (u.user_id = m.user_id)
where g.grp_id = 3;




-- La table PropositionsNBVotants affiche la liste des proposition affichant leurs nombres total de vote par proposition trier par groupe`

CREATE VIEW PropositionsNBVotants AS
SELECT p.prop_titre AS proposition,
    g.grp_nom AS groupe,
    COUNT(*) AS nbVotants
FROM proposition p
INNER JOIN vote v ON p.prop_id = v.prop_id
INNER JOIN choixVote cv ON v.vote_id = cv.vote_id
INNER JOIN membre m ON cv.user_id = m.user_id
INNER JOIN groupe g ON m.grp_id = g.grp_id
GROUP BY p.prop_titre, g.grp_nom;

-- La table nbReactionParCommentaire permet d’afficher le nb de reactions par commentaire : 
CREATE VIEW nbReactionParCommentaire AS
SELECT 
    p.prop_titre AS titre_proposition,
    c.com_txt AS texte_commentaire,
    COUNT(r.reac_id) AS nb_reactions,
    p.prop_id
FROM proposition p
INNER JOIN commentaire c ON p.prop_id = c.prop_id
INNER JOIN reaction r ON c.com_id = r.com_id
GROUP BY p.prop_titre, c.com_txt;

-- La table listeCommentaireParProposition permet d’afficher la liste commentaire par proposition : 
CREATE VIEW listeCommentaireParProposition AS
select p.prop_id, prop_titre as proposition, com_txt as Commentaire
from proposition p
inner join commentaire c on (p.prop_id = c.prop_id)


