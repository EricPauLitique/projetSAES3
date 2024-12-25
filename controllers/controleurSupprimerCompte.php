<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/membre.php");
require_once("../modele/utilisateur.php");
require_once("../modele/groupe.php");
require_once("../modele/adresse.php");

// Vérification si l'ID utilisateur est passé dans la requête POST
if (isset($_SESSION['id'])) {
    $idUtilisateur = htmlspecialchars($_SESSION['id']);
    $prenom = htmlspecialchars($_SESSION['prenom']);
    $nom = htmlspecialchars($_SESSION['nom']);

    // Connexion à la base de données
    Connexion::connect();

    // Vérification si l'utilisateur est membre ou propriétaire de groupes
    if (Membre::siMembreAgrp($idUtilisateur) > 0 || Groupe::siProprioAgrp($idUtilisateur) > 0) {
        // Redirige vers la page d'accueil si l'utilisateur est membre ou propriétaire d'au moins un groupe
        $_SESSION['messageC'] = "Erreur, Vous devez d'abord supprimer/quitter vos groupes avant de supprimer votre compte.";
        header("Location: ../vue/accueil.php");
        exit;
    }

    // Suppression de l'utilisateur de la base de données
    try {
        // Récupérer l'adresse de l'utilisateur
        $utilisateur = Utilisateur::getUtilisateurByLogin($idUtilisateur);
        $adrId = $utilisateur->get('adr_id');

        // Supprimer l'utilisateur
        $result = Utilisateur::deleteUtilisateur($idUtilisateur);

        if ($result) {
            // Supprimer l'adresse associée
            Adresse::deleteAdresse($adrId);

            $message = '<span style="color: green; font-weight: bold;">Votre compte (' . $prenom . ' ' . $nom . ') a été supprimé avec succès.</span>';
            session_destroy(); // Détruire la session après suppression du compte
            session_start(); // Redémarrer la session pour afficher le message
            $_SESSION['message'] = $message;
        } else {
            $_SESSION['message'] = '<b><i style="color: red;">Erreur lors de la suppression du compte.</i></b>';
        }
    } catch (Exception $e) {
        $_SESSION['message'] = '<b><i style="color: red;">Erreur : ' . $e->getMessage() . '</i></b>';
    }

    // Redirection vers la page de connexion après suppression du compte
    header("Location: ../vue/connexion.php");
    exit();
} else {
    $_SESSION['message'] = '<b><i style="color: red;">ID utilisateur manquant.</i></b>';
    header("Location: ../vue/accueil.php");
    exit();
}

?>