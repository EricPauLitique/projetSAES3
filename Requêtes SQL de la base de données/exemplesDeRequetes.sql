-- * (X) s’agit le numéro des clé primaire
--Afficher le nombre de membres du groupe (Association sportive d’Evry)
select count(*) as nbMembreGrp
from utilisateur u
inner join membre m on (u.user_id = m.user_id)
inner join groupe g on (m.grp_id = g.grp_id)
where g.grp_nom = 'Association sportive d’Evry';
--Il y a 4 membres du groupe ‘Association sportive d’Evry’

-- Afficher l’origine de la personne qui a proposé au sujet de la Création d’un terrain de foot :
select prop_titre, user_prenom, user_nom
from proposition
natural join utilisateur
where prop_titre = 'Création d’un terrain de foot';
-- C’est Julie Dupont qui a proposé ce sujet de la ‘Création d’un terrain de foot’ 

-- Le nombre votant à la proposition de Création d’un terrain de foot: 
select count(*) as nbVotant
from proposition p
inner join vote v on (p.prop_id = v.prop_id)
inner join choixVote c on (c.vote_id = v.vote_id)
where p.prop_titre = 'Création d’un terrain de foot';

-- Il y a eu 2 votants qui ont voté la proposition de Création d’un terrain de foot

-- Quel thème représente le sujet Organiser des cours de soutien gratuits : 
select theme_nom, prop_titre 
from proposition 
natural join theme
where prop_titre = 'Organiser des cours de soutien gratuits';

--Le sujet ‘Organiser des cours de soutien gratuits’ fait partie du thème ‘Education’.

-- L'utilisateur Léa Moreau est membre de quel groupe : 
select user_prenom as prenom, user_nom as nom, grp_nom as groupe
from utilisateur u
inner join membre m on (u.user_id = m.user_id)
inner join groupe g on (m.grp_id = g.grp_id)
where user_nom='Moreau' and user_prenom='Léa';

-- Léa Moreau est membre du groupe ‘Association des parents d’élèves’’

-- Quel groupe peut voter cette proposition création d’un terrain de foot (1): 
select prop_titre as titreProposition, g.grp_nom as groupeConcerner
from proposition
natural join theme
natural join comporte c
Inner join groupe g on (c.grp_id = g.grp_id)
where prop_id = 1;

--L’Association sportive d’Evry peut voter cette proposition création d’un terrain de foot.
-- La membre Marie Martin aurait-t-il été informé par mail qu’il a bien été ajouté dans le groupe ?
select user_prenom, user_nom, user_mail, notif_contenu 
from notification
natural join notifUtilisateur
natural join utilisateur
where user_mail = 'marie.martin@gmail.com';

/*Oui Marie Martin a reçu un mail et a reçu ‘Nouveau membre ajouté au groupe’ */

--  Afficher les commentaires du sujet de la proposition Organiser des cours de soutien gratuits (3) :
select user_prenom as prenom, user_nom as nom, com_date as date, com_txt as commentaires 
from commentaire c
inner join utilisateur u on (c.user_id = u.user_id)
inner join proposition p on (p.prop_id = c.prop_id)
where p.prop_id = 3;

/* Pierre Durand a commenté ‘Cela pourrait vraiment aider les enfants.
 cette proposition le 27 octobre 2024 à 14:10:05*/

/*Nicolas Fabre a commenté la proposition le 29 octobre 2024 à 12h38, en exprimant son avis que cela pourrait vraiment motiver les jeunes : "Cela pourrait vraiment motiver les jeunes.*/

/*Sophie Laroche, le 30 octobre 2024 à 13h10, a partagé son point de vue en disant qu'elle pensait que cela valait la peine d’essayer : "Je pense que cela vaut la peine d’essayer."*/

-- Qui a réagi dans le commentaire ‘Je soutiens à 100% cette initiative.’ (4):
	select u.user_prenom as prenom, u.user_nom as nom, c.com_txt as reagit, p.prop_titre as sujet
	from utilisateur u
 	inner join reaction r on (u.user_id = r.user_id)
	inner join commentaire c on (c.com_id = r.com_id)
	inner join proposition p on (c.prop_id = p.prop_id)
	where c.com_id = 4;
	
/*C’est Léa Moreau qui a réagit ce commentaire ‘Je soutiens à 100% cette initiative.’ au sujet de la proposition ‘Création d’un terrain de foot’.*/

-- Voir les détails du sujet ‘Création d’une piste cyclable’ (5)
select prop_titre as titre, prop_desc as description
from proposition
where prop_id = 5;

/*Le sujet Création d’une piste cyclable explique en détail sur ‘Une piste cyclable serait construite le long de l’avenue principale pour sécuriser les déplacements à vélo.’*/


     -- Afficher les membres d’un groupe (Association des parents d’élèves’):
select prop_titre as titre, prop_desc as description
from proposition
where prop_id = 5;
