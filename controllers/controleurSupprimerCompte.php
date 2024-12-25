<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/membre.php");
require_once("../modele/utilisateur.php");
require_once("../modele/groupe.php");

// Vérification si l'ID utilisateur est passé dans la requête POST
if (isset($_SESSION['id'])) {
    $idUtilisateur = htmlspecialchars($_SESSION['id']);

    // Connexion à la base de données
    Connexion::connect();

    // Vérification si l'utilisateur est membre ou propriétaire de groupes
    if (Membre::siMembreAgrp($idUtilisateur) > 0 || Groupe::siProprioAgrp($idUtilisateur) > 0) {
        // Redirige vers la page d'accueil si l'utilisateur n'est pas membre du groupe
        $_SESSION['messageC'] = "Erreur, Vous devez d'abord supprimer/quitter vos groupes avant de supprimer votre compte.";
        header("Location: ../vue/accueil.php");
        exit;
    }

    if (isset($_SESSION['messageC'])) {
        echo $_SESSION['messageC'];
        unset($_SESSION['messageC']);
    }
    else {
        echo Membre::siMembreAgrp($idUtilisateur) . " " . Groupe::siProprioAgrp($idUtilisateur);
        echo "Compte supprimé avec succès.";
    }
    

/*
    // Suppression de l'utilisateur de la base de données
    try {
        $result = Utilisateur::deleteUtilisateur($userId);

        if ($result) {
            $_SESSION['message'] = '<span style="color: green; font-weight: bold;">Votre compte a été supprimé avec succès.</span>';
        } else {
            $_SESSION['message'] = '<b><i style="color: red;">Erreur lors de la suppression du compte.</i></b>';
        }
    } catch (Exception $e) {
        $_SESSION['message'] = '<b><i style="color: red;">Erreur : ' . $e->getMessage() . '</i></b>';
    }

    // Redirection vers la page d'accueil après suppression du compte
    header("Location: ../vue/accueil.php");*/
    exit();
} else {
    $_SESSION['message'] = '<b><i style="color: red;">ID utilisateur manquant.</i></b>';
    header("Location: ../vue/accueil.php");
    exit();
}

?>