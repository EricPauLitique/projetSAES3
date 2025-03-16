## Voix Citoyenne - S301 

## Durée : Septembre 2024 - Janvier 2025

## Remerciements
- **SE Eric**
- **Gensbittel Raphaël**
- **Bigirimana Jean Joris**

## Description du projet
Voix Citoyenne est une plateforme permettant aux utilisateurs de s'exprimer et de voter sur diverses propositions au sein de groupes dédiés. L'application offre un environnement structuré pour la gestion des discussions citoyennes et facilite l'organisation démocratique des décisions.

## Aperçu du site
<img width="1511" alt="Capture d’écran 2025-03-16 à 11 11 25" src="https://github.com/user-attachments/assets/0897344d-1e73-41b4-bb83-077c22cc60bc" />
<img width="1512" alt="Capture d’écran 2025-03-16 à 11 12 11" src="https://github.com/user-attachments/assets/9ecfd967-7621-49f5-863e-04bff513d3d8" />



## Fonctionnalités principales
### Gestion des utilisateurs
- Inscription et connexion sécurisées
- Modification des informations de compte

### Gestion des groupes
- Création, modification et suppression de groupes
- Invitation et gestion des membres des groupes

### Gestion des thèmes et propositions
- Création, modification et suppression de thèmes
- Création et gestion des propositions
- Vote sur les propositions
- Affichage des résultats des votes

### Notifications et interactions
- Affichage des notifications
- Réactions aux propositions (commentaires, likes)
- Signalement de contenus inappropriés

## Technologies utilisées
### Développement Web
- **Langages** : PHP, HTML, CSS, JavaScript
- **Frameworks & Bibliothèques** : Bootstrap, jQuery

### Base de données
- **Système** : MySQL
- **Modélisation** : MCD, Schéma relationnel, Dictionnaire de données, Graphe des Dépendances Fonctionnelles (GDF)
- **Accès et gestion** : SQL

### Serveurs
- **Base de données et web** : Raspberry Pi et serveur de l'IUT
- **Protocoles utilisés** : HTTP/HTTPS, SSH, FTPS, MySQL, SFTP

### Envoi d’emails
- **Outil** : PHPMailer

### Développement d’API
- **Technologies** : JDBC, REST

### Application complémentaire
- **Langage** : Java

## Gestion du projet
### Analyse du projet
- Identification des besoins utilisateurs
- Définition des fonctionnalités
- Planification des étapes de développement

### Modélisation des données
- Conception du **Modèle Conceptuel des Données (MCD)**
![MCD](https://github.com/user-attachments/assets/c46f47bb-4a9d-4be1-a5b4-24dd2b92078a)
- Création du **Schéma relationnel**
![SR](https://github.com/user-attachments/assets/c4b9c2ef-6c1b-4e77-9ed9-bd46ef9b2eb9)
- Définition du **Dictionnaire de données**
- Mise en place du **Graphe des Dépendances Fonctionnelles (GDF)**
![GDF](https://github.com/user-attachments/assets/b19a8d20-890e-44de-992c-b209c857161c)
- Élaboration du **Modèle Conceptuel de Traitement (MCT)**
- Élaborer un **diagramme de classe (UML)** puis le transcrire en code JAVA.
![diagramme de classe](https://github.com/user-attachments/assets/de0175b0-1071-40bd-88c4-013a68a6d53e)

### Planification
- Élaboration du **Product Breakdown Structure (PBS)**
- Construction du **Work Breakdown Structure (WBS)**
- Liste détaillée des tâches
- Diagramme de **PERT**
- Diagramme de **GANTT**

## Déploiement
1. **Installation des dépendances**  
   - PHP et MySQL  
   - Serveur Apache  
   - PHPMailer  
2. **Configuration de la base de données**  
   - Importer le script SQL  
3. **Lancement du site web**  
   - Hébergement sur Raspberry Pi ou serveur de l'IUT  

## Ports utilisés
- **Web** : HTTP/HTTPS
- **SSH** : Accès distant au serveur
- **FTPS** : Transfert sécurisé de fichiers
- **MySQL** : Gestion des bases de données
- **SFTP** : Accès sécurisé aux fichiers
