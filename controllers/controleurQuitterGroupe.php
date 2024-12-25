<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/membre.php");
require_once("../modele/groupe.php");

// Vérification si les IDs utilisateur et groupe sont passés dans la requête POST
if (isset($_POST['user_id']) && isset($_POST['grp_id'])) {
    $userId = intval($_POST['user_id']);
    $grpId = intval($_POST['grp_id']);

    // Connexion à la base de données
    Connexion::connect();

    // Récupérer le nom du groupe
    $groupe = Groupe::getGroupByIdUnique($grpId);
    $nomGroupe = $groupe['grp_nom'];

    // Suppression du membre de la base de données
    try {
        $result = Membre::deleteMembre($userId, $grpId);

        if ($result) {
            $_SESSION['message'] = '<span style="color: green; font-weight: bold;">Vous avez quitté le groupe "' . htmlspecialchars($nomGroupe) . '" avec succès.</span>';
        } else {
            $_SESSION['message'] = '<b><i style="color: red;">Erreur lors de la tentative de quitter le groupe "' . htmlspecialchars($nomGroupe) . '".</i></b>';
        }
    } catch (Exception $e) {
        $_SESSION['message'] = '<b><i style="color: red;">Erreur : ' . $e->getMessage() . '</i></b>';
    }

    // Redirection vers la page d'accueil après avoir quitté le groupe
    header("Location: ../vue/accueil.php");
    exit();
} else {
    $_SESSION['message'] = '<b><i style="color: red;">ID utilisateur ou groupe manquant.</i></b>';
    header("Location: ../vue/accueil.php");
    exit();
}