<?php
session_start();
require_once(__DIR__ . "/../config/connexion.php");
require_once("../modele/theme.php");
require_once("../modele/comporte.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    header("Location: ../vue/connexion.php");
    exit;
}

// Récupérer les données du formulaire
$themeId = $_POST['theme_id'] ?? null;
$nomTheme = $_POST['nom_theme'] ?? null;
$prixTheme = $_POST['prix_theme'] ?? null;
$groupId = $_POST['group_id'] ?? null;

if ($themeId && $nomTheme && $prixTheme && $groupId) {
    // Vérifier si le nom du thème est inconnu
    $existingTheme = Theme::getThemeByName($nomTheme);
    if (!$existingTheme) {
        // Ajouter le nouveau thème
        $newThemeId = Theme::createTheme($nomTheme);
        $themeId = $newThemeId;
    } else {
        $themeId = $existingTheme->get('theme_id');
    }

    // Mettre à jour le thème dans la table comporte
    $updateSuccess = Comporte::updateTheme($themeId, $groupId, $prixTheme);

    if ($updateSuccess) {
        $_SESSION['message'] = '<p style="color: green; font-weight: bold;"><b>Le thème a été modifié avec succès.</b></p>';
    } else {
        $_SESSION['messageC'] = '<p style="color: red; font-weight: bold;"><b>Erreur lors de la mise à jour du thème.</b></p>';
    }
} else {
    $_SESSION['messageC'] = '<p style="color: red; font-weight: bold;"><b>Erreur : données invalides.</b></p>';
}

header("Location: ../vue/groupe.php?id=" . $groupId);
exit;
?>