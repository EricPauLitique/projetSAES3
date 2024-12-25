<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/utilisateur.php");
require_once("../modele/adresse.php");

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: ../vue/connexion.php");
    exit;
}

$idUtilisateur = htmlspecialchars($_SESSION['id']);

// Connexion à la base de données
Connexion::connect();

try {
    // Récupérer les données du formulaire
    $prenom = htmlspecialchars($_POST['prenom']);
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $ancienPassword = $_POST['ancien_password'];
    $nouveauPassword = $_POST['nouveau_password'];
    $confirmerPassword = $_POST['confirmer_password'];
    $codePostal = htmlspecialchars($_POST['code_postal']);
    $ville = htmlspecialchars($_POST['ville']);
    $numeroRue = htmlspecialchars($_POST['numero_rue']);
    $nomRue = htmlspecialchars($_POST['nom_rue']);

    // Vérification de l'existence de l'email
    if (Utilisateur::emailExists($email, $idUtilisateur)) {
        $_SESSION['messageC'] = "L'email existe déjà dans notre système.";
        header("Location: ../vue/modifCompte.php");
        exit;
    }

    // Vérification de l'existence de prénom + nom
    if (Utilisateur::prenomNomExists($prenom, $nom, $idUtilisateur)) {
        $_SESSION['messageC'] = "Un utilisateur avec le même prénom et nom existe déjà.";
        header("Location: ../vue/modifCompte.php");
        exit;
    }

    // Vérification de l'ancien mot de passe
    $utilisateur = Utilisateur::getUtilisateurByLogin($idUtilisateur);
    if (!password_verify($ancienPassword, $utilisateur->get('user_mdp'))) {
        $_SESSION['messageC'] = "L'ancien mot de passe est incorrect.";
        header("Location: ../vue/modifCompte.php");
        exit;
    }

    // Mettre à jour les informations de l'utilisateur
    $utilisateur->set('user_prenom', $prenom);
    $utilisateur->set('user_nom', $nom);
    $utilisateur->set('user_mail', $email);

    // Mettre à jour le mot de passe si un nouveau mot de passe est fourni
    if (!empty($nouveauPassword)) {
        if ($nouveauPassword !== $confirmerPassword) {
            $_SESSION['messageC'] = "Le nouveau mot de passe et la confirmation ne correspondent pas.";
            header("Location: ../vue/modifCompte.php");
            exit;
        }
        $passwordHashed = password_hash($nouveauPassword, PASSWORD_DEFAULT);
        $utilisateur->set('user_mdp', $passwordHashed);
    }

    Utilisateur::updateUtilisateur($utilisateur);

    // Mettre à jour l'adresse de l'utilisateur
    $adresse = Adresse::getAdresseById($utilisateur->get('adr_id'));
    $adresse->set('adr_cp', $codePostal);
    $adresse->set('adr_ville', $ville);
    $adresse->set('adr_num', $numeroRue);
    $adresse->set('adr_rue', $nomRue);
    Adresse::updateAdresse($adresse);

    $_SESSION['message'] = '<span style="color: green; font-weight: bold;">Votre compte a été modifié avec succès.</span>';

    // Redirection vers la page d'accueil après modification du compte
    header("Location: ../vue/accueil.php");
    exit();

} catch (Exception $e) {
    $_SESSION['messageC'] = '<b><i style="color: red;">Erreur : ' . $e->getMessage() . '</i></b>';
    header("Location: ../vue/modifCompte.php");
    exit();
}
?>