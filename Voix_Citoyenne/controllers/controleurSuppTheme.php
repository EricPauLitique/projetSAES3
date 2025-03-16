<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/comporte.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    header("Location: ../vue/connexion.php");
    exit;
}

// Récupérer les données du formulaire
$themeId = $_POST['theme_id'] ?? null;
$idGroupe = $_POST['group_id'] ?? null;

Connexion::connect();

if ($themeId && $idGroupe) {
    // Supprimer le thème
    $deleteSuccess = Comporte::deleteThemeGrp($themeId, $idGroupe);

    if ($deleteSuccess) {
        $_SESSION['message'] = '<p style="color: green; font-weight: bold;"><b>Le thème a été supprimé avec succès.</b></p>';
    } else {
        $_SESSION['messageC'] = '<p style="color: red; font-weight: bold;"><b>Erreur lors de la suppression du thème.</b></p>';
    }
} else {
    $_SESSION['messageC'] = '<p style="color: red; font-weight: bold;"><b>Erreur : ID du thème ou groupe n\'est pas reconnu.</b></p>';
}

header("Location: ../vue/groupe.php?id=" . $idGroupe);
exit;
?>